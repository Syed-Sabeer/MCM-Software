<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Settings\PipelineDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\PipelineForm;
use Webkul\Lead\Repositories\PipelineRepository;
use Webkul\Lead\Repositories\StageRepository;

class PipelineController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected PipelineRepository $pipelineRepository,
        protected StageRepository $stageRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(PipelineDataGrid::class)->process();
        }

        return view('admin::settings.pipelines.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $pipeline = request('pipeline_id')
            ? $this->pipelineRepository->find(request('pipeline_id'))
            : $this->pipelineRepository->getDefaultPipeline();

        $pipeline?->load(['stages' => fn ($query) => $query->withCount('leads')]);

        return view('admin::settings.pipelines.create', compact('pipeline'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PipelineForm $request): RedirectResponse
    {
        $request->validated();

        $request->merge([
            'is_default' => $request->boolean('is_default') ? 1 : 0,
        ]);

        Event::dispatch('settings.pipeline.create.before');

        $pipeline = $this->pipelineRepository->create($request->all());

        Event::dispatch('settings.pipeline.create.after', $pipeline);

        session()->flash('success', trans('admin::app.settings.pipelines.index.create-success'));

        return redirect()->route('admin.settings.pipelines.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $pipeline = $this->pipelineRepository->findOrFail($id);

        $pipeline->load(['stages' => fn ($query) => $query->withCount('leads')]);

        return view('admin::settings.pipelines.edit', compact('pipeline'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PipelineForm $request, int $id): RedirectResponse
    {
        $request->validated();

        $isDefault = $request->boolean('is_default') ? 1 : 0;

        if (! $isDefault) {
            $defaultCount = $this->pipelineRepository->findWhere(['is_default' => 1])->count();

            $pipeline = $this->pipelineRepository->find($id);

            if (
                $defaultCount === 1
                && $pipeline->is_default
            ) {
                session()->flash('error', trans('admin::app.settings.pipelines.index.default-required'));

                return redirect()->back();
            }
        }

        $request->merge(['is_default' => $isDefault]);

        Event::dispatch('settings.pipeline.update.before', $id);

        $pipeline = $this->pipelineRepository->update($request->all(), $id);

        Event::dispatch('settings.pipeline.update.after', $pipeline);

        session()->flash('success', trans('admin::app.settings.pipelines.index.update-success'));

        return redirect()->route('admin.settings.pipelines.index');
    }

    /**
     * Rename the pipeline without leaving the edit screen.
     */
    public function rename(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'name' => [
                'required',
                Rule::unique('lead_pipelines', 'name')->ignore($id),
            ],
        ]);

        $pipeline = $this->pipelineRepository->findOrFail($id);

        $pipeline->update(['name' => $data['name']]);

        return response()->json([
            'message'  => trans('admin::app.settings.pipelines.edit.rename-success'),
            'pipeline' => $pipeline->fresh(),
        ]);
    }

    /**
     * Add a stage immediately.
     */
    public function storeStage(Request $request, int $id): JsonResponse
    {
        $pipeline = $this->pipelineRepository->findOrFail($id);

        $data = $request->validate([
            'name'        => [
                'required',
                Rule::unique('lead_pipeline_stages', 'name')->where('lead_pipeline_id', $pipeline->id),
            ],
            'probability' => 'nullable|integer|min:0|max:100',
        ]);

        $stage = $this->stageRepository->create([
            'lead_pipeline_id' => $pipeline->id,
            'name'             => $data['name'],
            'code'             => $this->uniqueStageCode($data['name'], $pipeline->id),
            'probability'      => $data['probability'] ?? 100,
            'sort_order'       => ($pipeline->stages()->max('sort_order') ?? 0) + 1,
        ]);

        $stage->leads_count = 0;

        return response()->json([
            'message' => trans('admin::app.settings.pipelines.edit.stage-create-success'),
            'stage'   => $stage,
        ]);
    }

    /**
     * Rename a stage immediately.
     */
    public function updateStage(Request $request, int $id, int $stageId): JsonResponse
    {
        $pipeline = $this->pipelineRepository->findOrFail($id);
        $stage = $pipeline->stages()->whereKey($stageId)->firstOrFail();

        $data = $request->validate([
            'name'        => [
                'required',
                Rule::unique('lead_pipeline_stages', 'name')
                    ->where('lead_pipeline_id', $pipeline->id)
                    ->ignore($stage->id),
            ],
            'probability' => 'nullable|integer|min:0|max:100',
        ]);

        $stage->update([
            'name'        => $data['name'],
            'code'        => $this->uniqueStageCode($data['name'], $pipeline->id, $stage->id),
            'probability' => $data['probability'] ?? $stage->probability,
        ]);

        return response()->json([
            'message' => trans('admin::app.settings.pipelines.edit.stage-update-success'),
            'stage'   => $stage->fresh()->loadCount('leads'),
        ]);
    }

    /**
     * Persist drag-and-drop stage order immediately.
     */
    public function reorderStages(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'stage_ids'   => 'required|array|min:1',
            'stage_ids.*' => 'integer',
        ]);

        DB::transaction(function () use ($id, $data) {
            $pipeline = $this->pipelineRepository->findOrFail($id);
            $stages = $pipeline->stages()->lockForUpdate()->pluck('id')->map(fn ($stageId) => (int) $stageId);
            $stageIds = collect($data['stage_ids'])->map(fn ($stageId) => (int) $stageId);

            if (
                $stages->sort()->values()->all() !== $stageIds->sort()->values()->all()
            ) {
                throw ValidationException::withMessages([
                    'stage_ids' => trans('admin::app.settings.pipelines.edit.stage-order-stale'),
                ]);
            }

            foreach ($stageIds->values() as $index => $stageId) {
                $this->stageRepository->update(['sort_order' => $index + 1], $stageId);
            }
        });

        return response()->json([
            'message' => trans('admin::app.settings.pipelines.edit.stage-order-success'),
        ]);
    }

    /**
     * Delete a stage and safely move its leads to the nearest available stage.
     */
    public function destroyStage(int $id, int $stageId): JsonResponse
    {
        $result = DB::transaction(function () use ($id, $stageId) {
            $pipeline = $this->pipelineRepository->findOrFail($id);
            $stages = $pipeline->stages()->lockForUpdate()->get(['id', 'name', 'sort_order']);

            if ($stages->count() <= 1) {
                throw ValidationException::withMessages([
                    'stage' => trans('admin::app.settings.pipelines.edit.stage-delete-last-error'),
                ]);
            }

            $removedStage = $stages->firstWhere('id', $stageId);

            if (! $removedStage) {
                abort(404);
            }

            $replacementStage = $stages
                ->where('id', '<>', $removedStage->id)
                ->where('sort_order', '>', $removedStage->sort_order)
                ->sortBy('sort_order')
                ->first()
                ?? $stages
                    ->where('id', '<>', $removedStage->id)
                    ->where('sort_order', '<', $removedStage->sort_order)
                    ->sortByDesc('sort_order')
                    ->first();

            $leadsCount = $pipeline->leads()
                ->where('lead_pipeline_stage_id', $removedStage->id)
                ->count();

            if ($replacementStage) {
                $pipeline->leads()
                    ->where('lead_pipeline_stage_id', $removedStage->id)
                    ->update(['lead_pipeline_stage_id' => $replacementStage->id]);
            }

            $this->stageRepository->delete($removedStage->id);

            $pipeline->stages()->orderBy('sort_order')->get()->values()->each(function ($stage, $index) {
                $stage->update(['sort_order' => $index + 1]);
            });

            return [
                'leads_count' => $leadsCount,
                'replacement' => $replacementStage,
            ];
        });

        return response()->json([
            'message'     => trans('admin::app.settings.pipelines.edit.stage-delete-success'),
            'leads_count' => $result['leads_count'],
            'replacement' => $result['replacement'],
        ]);
    }

    /**
     * Generate a stage code that respects the per-pipeline unique index.
     */
    protected function uniqueStageCode(string $name, int $pipelineId, ?int $ignoreStageId = null): string
    {
        $baseCode = Str::slug($name) ?: 'stage';
        $code = $baseCode;
        $suffix = 1;

        while (
            DB::table('lead_pipeline_stages')
                ->where('lead_pipeline_id', $pipelineId)
                ->where('code', $code)
                ->when($ignoreStageId, fn ($query) => $query->where('id', '<>', $ignoreStageId))
                ->exists()
        ) {
            $code = $baseCode.'-'.$suffix++;
        }

        return $code;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $pipeline = $this->pipelineRepository->findOrFail($id);

        if ($pipeline->is_default) {
            return response()->json([
                'message' => trans('admin::app.settings.pipelines.index.default-delete-error'),
            ], 400);
        } else {
            $defaultPipeline = $this->pipelineRepository->getDefaultPipeline();

            $pipeline->leads()->update([
                'lead_pipeline_id'       => $defaultPipeline->id,
                'lead_pipeline_stage_id' => $defaultPipeline->stages()->first()->id,
            ]);
        }

        try {
            Event::dispatch('settings.pipeline.delete.before', $id);

            $this->pipelineRepository->delete($id);

            Event::dispatch('settings.pipeline.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.settings.pipelines.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.settings.pipelines.index.delete-failed'),
            ], 400);
        }

        return response()->json([
            'message' => trans('admin::app.settings.pipelines.index.delete-failed'),
        ], 400);
    }
}
