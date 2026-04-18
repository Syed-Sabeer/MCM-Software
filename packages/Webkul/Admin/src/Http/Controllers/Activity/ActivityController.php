<?php

namespace Webkul\Admin\Http\Controllers\Activity;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Activity\Repositories\ActivityRepository;
use Webkul\Activity\Repositories\FileRepository;
use Webkul\Admin\Http\Resources\OrganizationResource;
use Webkul\Admin\Http\Resources\PersonResource;
use Webkul\Admin\DataGrids\Activity\ActivityDataGrid;
use Webkul\Admin\DataGrids\Activity\TaskDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Requests\MassUpdateRequest;
use Webkul\Admin\Http\Resources\ActivityResource;
use Webkul\Admin\Http\Resources\UserResource;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Contact\Repositories\OrganizationRepository;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\User\Repositories\UserRepository;

class ActivityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected ActivityRepository $activityRepository,
        protected FileRepository $fileRepository,
        protected AttributeRepository $attributeRepository,
        protected OrganizationRepository $organizationRepository,
        protected PersonRepository $personRepository,
        protected UserRepository $userRepository,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('admin::activities.index');
    }

    /**
     * Display activities calendar page.
     */
    public function calendar(): View
    {
        return view('admin::activities.calendar');
    }

    /**
     * Show assigned tasks page.
     */
    public function myTasks(): View
    {
        $userId = auth()->guard('user')->id();
        $today = now()->toDateString();

        $allTasksCount = $this->assignedTasksQuery($userId)->get()->count();

        $upcomingTasksCount = $this->assignedTasksQuery($userId)
            ->where('activities.is_done', 0)
            ->whereDate('activities.schedule_from', '>=', $today)
            ->get()
            ->count();

        $overdueTasksCount = $this->assignedTasksQuery($userId)
            ->where('activities.is_done', 0)
            ->whereDate('activities.schedule_from', '<', $today)
            ->get()
            ->count();

        return view('admin::activities.my-tasks', [
            'upcomingTasksCount' => $upcomingTasksCount,
            'overdueTasksCount'  => $overdueTasksCount,
            'allTasksCount'      => $allTasksCount,
        ]);
    }

    /**
     * Get datagrid response for assigned tasks page.
     */
    public function myTasksData(): JsonResponse
    {
        return datagrid(TaskDataGrid::class)->process();
    }

    /**
     * Get summary for floating task widget.
     */
    public function myTasksSummary(): JsonResponse
    {
        $userId = auth()->guard('user')->id();

        $today = now()->toDateString();

        $query = $this->assignedTasksQuery($userId)
            ->where('activities.is_done', 0)
            ->whereDate('activities.schedule_from', '>=', $today);

        $tasks = (clone $query)
            ->orderBy('activities.schedule_from')
            ->limit(5)
            ->get();

        $badgeCount = (clone $query)->get()->count();

        $overdueCount = $this->assignedTasksQuery($userId)
            ->where('activities.is_done', 0)
            ->whereDate('activities.schedule_from', '<', $today)
            ->get()
            ->count();

        return response()->json([
            'tasks' => $tasks,
            'badge_count' => $badgeCount,
            'overdue_count' => $overdueCount,
            'view_all_url' => route('admin.activities.my_tasks'),
        ]);
    }

    /**
     * Assigned tasks base query for current user.
     */
    protected function assignedTasksQuery(int $userId)
    {
        return DB::table('activities')
            ->leftJoin('activity_participants', 'activities.id', '=', 'activity_participants.activity_id')
            ->select(
                'activities.id',
                'activities.title',
                'activities.comment',
                'activities.schedule_from',
                'activities.schedule_to',
                'activities.is_done',
                'activities.created_at'
            )
            ->where('activities.type', 'task')
            ->where(function ($query) use ($userId) {
                $query->where('activities.user_id', $userId)
                    ->orWhere('activity_participants.user_id', $userId);
            })
            ->groupBy(
                'activities.id',
                'activities.title',
                'activities.comment',
                'activities.schedule_from',
                'activities.schedule_to',
                'activities.is_done',
                'activities.created_at'
            );
    }

    /**
     * Returns a listing of the resource.
     */
    public function get(): JsonResponse
    {
        if (! request()->has('view_type')) {
            return datagrid(ActivityDataGrid::class)->process();
        }

        $startDate = request()->get('startDate')
            ? Carbon::createFromTimeString(request()->get('startDate').' 00:00:01')
            : Carbon::now()->startOfWeek()->format('Y-m-d H:i:s');

        $endDate = request()->get('endDate')
            ? Carbon::createFromTimeString(request()->get('endDate').' 23:59:59')
            : Carbon::now()->endOfWeek()->format('Y-m-d H:i:s');

        $activities = $this->activityRepository->getActivities([$startDate, $endDate])->toArray();

        return response()->json([
            'activities' => $activities,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): RedirectResponse|JsonResponse
    {
        $this->validate(request(), [
            'type'          => 'required|in:call,meeting,note,file,task',
            'title'         => 'required_unless:type,file|max:200',
            'comment'       => 'required_if:type,note',
            'schedule_from' => 'required_if:type,meeting,task',
            'schedule_to'   => 'required_if:type,meeting',
            'file'          => 'required_if:type,file',
            'organization_id' => 'required_if:type,meeting,task|nullable|exists:organizations,id',
        ]);

        if (request('type') === 'task' && ! request()->filled('schedule_to') && request()->filled('schedule_from')) {
            request()->merge([
                'schedule_to' => request('schedule_from'),
            ]);
        }

        if (request('type') === 'meeting') {
            /**
             * Check if meeting is overlapping with other meetings.
             */
            $isOverlapping = $this->activityRepository->isDurationOverlapping(
                request()->input('schedule_from'),
                request()->input('schedule_to'),
                request()->input('participants'),
                request()->input('id')
            );

            if ($isOverlapping) {
                if (request()->ajax()) {
                    return response()->json([
                        'message' => trans('admin::app.activities.overlapping-error'),
                    ], 400);
                }

                session()->flash('success', trans('admin::app.activities.overlapping-error'));

                return redirect()->back();
            }
        }

        Event::dispatch('activity.create.before');

        $payload = request()->all();

        if (empty($payload['entity_type']) && empty($payload['entity_id'])) {
            if (! empty($payload['organization_id'])) {
                $payload['entity_type'] = 'organizations';
                $payload['entity_id'] = $payload['organization_id'];
            } elseif (! empty($payload['person_id'])) {
                $payload['entity_type'] = 'persons';
                $payload['entity_id'] = $payload['person_id'];
            } elseif (! empty($payload['lead_id'])) {
                $payload['entity_type'] = 'leads';
                $payload['entity_id'] = $payload['lead_id'];
            }
        }

        $activity = $this->activityRepository->create(array_merge($payload, [
            'is_done' => request('type') == 'note' ? 1 : 0,
            'user_id' => auth()->guard('user')->user()->id,
        ]));

        if (array_key_exists('lead_id', $payload)) {
            $activity->leads()->sync(
                ! empty($payload['lead_id'])
                    ? [$payload['lead_id']]
                    : []
            );
        }

        if (array_key_exists('organization_id', $payload)) {
            $activity->organizations()->sync(
                ! empty($payload['organization_id'])
                    ? [$payload['organization_id']]
                    : []
            );
        }

        if (array_key_exists('person_id', $payload)) {
            $activity->persons()->sync(
                ! empty($payload['person_id'])
                    ? [$payload['person_id']]
                    : []
            );
        }

        Event::dispatch('activity.create.after', $activity);

        if (request()->ajax()) {
            return response()->json([
                'data'    => new ActivityResource($activity),
                'message' => trans('admin::app.activities.create-success'),
            ]);
        }

        session()->flash('success', trans('admin::app.activities.create-success'));

        return redirect()->back();
    }

    /**
     * Search organizations for activity related-to selector.
     */
    public function searchOrganizations(): AnonymousResourceCollection
    {
        $query = trim((string) request()->input('query', ''));

        $organizations = $this->organizationRepository
            ->scopeQuery(function ($builder) use ($query) {
                $builder = $builder->orderBy('name');

                if ($query !== '') {
                    $builder->where('name', 'like', '%'.$query.'%');
                }

                return $builder->limit(3);
            })
            ->all();

        return OrganizationResource::collection($organizations);
    }

    /**
     * Search contacts for activity contact selectors.
     */
    public function searchPersons(): AnonymousResourceCollection
    {
        $query = trim((string) request()->input('query', ''));

        $persons = $this->personRepository
            ->scopeQuery(function ($builder) use ($query) {
                if ($query !== '') {
                    $builder->where(function ($personQuery) use ($query) {
                        $personQuery->whereRaw("LOWER(TRIM(CONCAT_WS(' ', first_name, last_name))) LIKE ?", ['%' . mb_strtolower($query) . '%'])
                            ->orWhereRaw("LOWER(name) LIKE ?", ['%' . mb_strtolower($query) . '%'])
                            ->orWhereRaw("LOWER(email) LIKE ?", ['%' . mb_strtolower($query) . '%']);
                    });
                }

                return $builder->orderBy('first_name')->orderBy('last_name')->limit(3);
            })
            ->all();

        return PersonResource::collection($persons);
    }

    /**
     * Search assignable employee users.
     */
    public function searchEmployeeUsers(): AnonymousResourceCollection
    {
        $query = trim((string) request()->input('query', ''));

        $users = $this->userRepository
            ->scopeQuery(function ($builder) use ($query) {
                $builder
                    ->select('users.*')
                    ->join('roles', 'roles.id', '=', 'users.role_id')
                    ->where('users.status', 1)
                    ->where('roles.description', 'like', '%Employee%');

                if ($query !== '') {
                    $builder->where(function ($userQuery) use ($query) {
                        $userQuery->where('users.name', 'like', '%'.$query.'%')
                            ->orWhere('users.email', 'like', '%'.$query.'%');
                    });
                }

                return $builder->orderBy('users.name')->limit(3);
            })
            ->all();

        return UserResource::collection($users);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $activity = $this->activityRepository->findOrFail($id);

        $leadId = old('lead_id') ?? optional($activity->leads()->first())->id;

        $lookUpEntityData = $this->attributeRepository->getLookUpEntity('leads', $leadId);

        return view('admin::activities.edit', compact('activity', 'lookUpEntityData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id): RedirectResponse|JsonResponse
    {
        Event::dispatch('activity.update.before', $id);

        $data = request()->all();

        $activity = $this->activityRepository->update($data, $id);

        /**
         * We will not use `empty` directly here because `lead_id` can be a blank string
         * from the activity form. However, on the activity view page, we are only updating the
         * `is_done` field, so `lead_id` will not be present in that case.
         */
        if (isset($data['lead_id'])) {
            $activity->leads()->sync(
                ! empty($data['lead_id'])
                    ? [$data['lead_id']]
                    : []
            );
        }

        Event::dispatch('activity.update.after', $activity);

        if (request()->ajax()) {
            return response()->json([
                'data'    => new ActivityResource($activity),
                'message' => trans('admin::app.activities.update-success'),
            ]);
        }

        session()->flash('success', trans('admin::app.activities.update-success'));

        return redirect()->route('admin.activities.index');
    }

    /**
     * Mass Update the specified resources.
     */
    public function massUpdate(MassUpdateRequest $massUpdateRequest): JsonResponse
    {
        $activities = $this->activityRepository->findWhereIn('id', $massUpdateRequest->input('indices'));

        foreach ($activities as $activity) {
            Event::dispatch('activity.update.before', $activity->id);

            $activity = $this->activityRepository->update([
                'is_done' => $massUpdateRequest->input('value'),
            ], $activity->id);

            Event::dispatch('activity.update.after', $activity);
        }

        return response()->json([
            'message' => trans('admin::app.activities.mass-update-success'),
        ]);
    }

    /**
     * Download file from storage.
     */
    public function download(int $id): StreamedResponse
    {
        try {
            $file = $this->fileRepository->findOrFail($id);

            return Storage::download($file->path);
        } catch (\Exception $exception) {
            abort(404);
        }
    }

    /*
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $activity = $this->activityRepository->findOrFail($id);

        try {
            Event::dispatch('activity.delete.before', $id);

            $activity?->delete($id);

            Event::dispatch('activity.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.activities.destroy-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.activities.destroy-failed'),
            ], 400);
        }
    }

    /**
     * Mass Delete the specified resources.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $activities = $this->activityRepository->findWhereIn('id', $massDestroyRequest->input('indices'));

        try {
            foreach ($activities as $activity) {
                Event::dispatch('activity.delete.before', $activity->id);

                $this->activityRepository->delete($activity->id);

                Event::dispatch('activity.delete.after', $activity->id);
            }

            return response()->json([
                'message' => trans('admin::app.activities.mass-destroy-success'),
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.activities.mass-delete-failed'),
            ], 400);
        }
    }
}
