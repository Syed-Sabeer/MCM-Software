<?php

namespace Webkul\Lead\Repositories;

use Carbon\Carbon;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\Core\Eloquent\Repository;
use Webkul\Lead\Contracts\Lead;

class LeadRepository extends Repository
{
    /**
     * Searchable fields.
     */
    protected $fieldSearchable = [
        'title',
        'lead_value',
        'status',
        'user_id',
        'user.name',
        'person_id',
        'person.name',
        'lead_source_id',
        'lead_type_id',
        'lead_pipeline_id',
        'lead_pipeline_stage_id',
        'created_at',
        'closed_at',
        'expected_close_date',
    ];

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected StageRepository $stageRepository,
        protected PersonRepository $personRepository,
        protected ProductRepository $productRepository,
        protected AttributeRepository $attributeRepository,
        protected AttributeValueRepository $attributeValueRepository,
        Container $container
    ) {
        parent::__construct($container);
    }

    /**
     * Specify model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return Lead::class;
    }

    /**
     * Get the next case number. Finds max numeric value of existing case_no and increments by 1.
     * Format: 5 digits with leading zeros (e.g. 00001, 50014).
     */
    public function getNextCaseNo(): string
    {
        $max = $this->model
            ->whereNotNull('case_no')
            ->pluck('case_no')
            ->map(fn ($v) => (int) $v)
            ->filter()
            ->push(0)
            ->max();

        $next = $max + 1;

        return str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get leads query.
     *
     * @param  int  $pipelineId
     * @param  int  $pipelineStageId
     * @param  string  $term
     * @param  string  $createdAtRange
     * @return mixed
     */
    public function getLeadsQuery($pipelineId, $pipelineStageId, $term, $createdAtRange)
    {
        return $this->with([
            'attribute_values',
            'pipeline',
            'stage',
        ])->scopeQuery(function ($query) use ($pipelineId, $pipelineStageId, $term, $createdAtRange) {
            return $query->select(
                'leads.id as id',
                'leads.created_at as created_at',
                'title',
                'lead_value',
                'persons.name as person_name',
                'leads.person_id as person_id',
                'lead_pipelines.id as lead_pipeline_id',
                'lead_pipeline_stages.name as status',
                'lead_pipeline_stages.id as lead_pipeline_stage_id'
            )
                ->addSelect(DB::raw('DATEDIFF('.DB::getTablePrefix().'leads.created_at + INTERVAL lead_pipelines.rotten_days DAY, now()) as rotten_days'))
                ->leftJoin('persons', 'leads.person_id', '=', 'persons.id')
                ->leftJoin('lead_pipelines', 'leads.lead_pipeline_id', '=', 'lead_pipelines.id')
                ->leftJoin('lead_pipeline_stages', 'leads.lead_pipeline_stage_id', '=', 'lead_pipeline_stages.id')
                ->where('title', 'like', "%$term%")
                ->where('leads.lead_pipeline_id', $pipelineId)
                ->where('leads.lead_pipeline_stage_id', $pipelineStageId)
                ->when($createdAtRange, function ($query) use ($createdAtRange) {
                    return $query->whereBetween('leads.created_at', $createdAtRange);
                })
                ->where(function ($query) {
                    if ($userIds = bouncer()->getAuthorizedUserIds()) {
                        $query->whereIn('leads.user_id', $userIds);
                    }
                });
        });
    }

    /**
     * Create.
     *
     * @return \Webkul\Lead\Contracts\Lead
     */
    public function create(array $data)
    {
        $persons = $this->resolvePersonsFromInput($data);

        /**
         * If a person is provided (with id or name), create or update the person and set the `person_id`.
         * Contact is optional - leads can be created without a person.
         */
        if (! empty($persons)) {
            $person = $persons[0];
            $data['person_id'] = $person->id;
        }

        if (empty($data['organization_id'])) {
            $data['organization_id'] = $this->firstPersonInputValue($data, 'organization_id')
                ?? (isset($person) ? $person->organization_id : null);
        }

        if (empty($data['expected_close_date'])) {
            $data['expected_close_date'] = null;
        }

        $this->normalizeNullableFields($data);

        // Convert empty strings to NULL for optional foreign key fields
        unset($data['person'], $data['person_ids']);

        $lead = parent::create(array_merge([
            'lead_pipeline_id'       => 1,
            'lead_pipeline_stage_id' => 1,
        ], $data));

        if (! empty($persons)) {
            $lead->persons()->sync(collect($persons)->pluck('id')->all());
        }

        $this->attributeValueRepository->save(array_merge($data, [
            'entity_id' => $lead->id,
        ]));

        if (isset($data['products'])) {
            foreach ($data['products'] as $product) {
                $this->productRepository->create(array_merge($product, [
                    'lead_id' => $lead->id,
                    'amount'  => $product['price'] * $product['quantity'],
                ]));
            }
        }

        return $lead;
    }

    /**
     * Update.
     *
     * @param  int  $id
     * @param  array|\Illuminate\Database\Eloquent\Collection  $attributes
     * @return \Webkul\Lead\Contracts\Lead
     */
    public function update(array $data, $id, $attributes = [])
    {
        $shouldSyncPersons = array_key_exists('person', $data)
            || array_key_exists('person_id', $data)
            || array_key_exists('person_ids', $data);

        /**
         * If a person is provided, create or update the person and set the `person_id`.
         * Be cautious, as a lead can be updated without providing person data.
         * For example, in the lead Kanban section, when switching stages, only the stage will be updated.
         */
        if ($shouldSyncPersons && ! empty($persons = $this->resolvePersonsFromInput($data))) {
            $person = $persons[0];
            $data['person_id'] = $person->id;
        }

        if (empty($data['organization_id']) && isset($person)) {
            $data['organization_id'] = $person->organization_id;
        }

        unset($data['person'], $data['person_ids']);

        if (isset($data['lead_pipeline_stage_id'])) {
            $stage = $this->stageRepository->find($data['lead_pipeline_stage_id']);

            if (in_array($stage->code, ['won', 'lost'])) {
                $data['closed_at'] = $data['closed_at'] ?? Carbon::now();
            } else {
                $data['closed_at'] = null;
            }
        }

        if (empty($data['expected_close_date'])) {
            $data['expected_close_date'] = null;
        }

        $this->normalizeNullableFields($data);

        $lead = parent::update($data, $id);

        if ($shouldSyncPersons && isset($persons)) {
            $lead->persons()->sync(collect($persons)->pluck('id')->all());
        }

        /**
         * If attributes are provided, only save the provided attributes and return.
         * A collection of attributes may also be provided, which will be treated as valid,
         * regardless of whether it is empty or not.
         */
        if (! empty($attributes)) {
            /**
             * If attributes are provided as an array, then fetch the attributes from the database;
             * otherwise, use the provided collection of attributes.
             */
            if (is_array($attributes)) {
                $conditions = ['entity_type' => $data['entity_type']];

                if (isset($data['quick_add'])) {
                    $conditions['quick_add'] = 1;
                }

                $attributes = $this->attributeRepository->where($conditions)
                    ->whereIn('code', $attributes)
                    ->get();
            }

            $this->attributeValueRepository->save(array_merge($data, [
                'entity_id' => $lead->id,
            ]), $attributes);

            return $lead;
        }

        $this->attributeValueRepository->save(array_merge($data, [
            'entity_id' => $lead->id,
        ]));

        $previousProductIds = $lead->products()->pluck('id');

        if (isset($data['products'])) {
            foreach ($data['products'] as $productId => $productInputs) {
                if (Str::contains($productId, 'product_')) {
                    $this->productRepository->create(array_merge([
                        'lead_id' => $lead->id,
                    ], $productInputs));
                } else {
                    if (is_numeric($index = $previousProductIds->search($productId))) {
                        $previousProductIds->forget($index);
                    }

                    $this->productRepository->update($productInputs, $productId);
                }
            }
        }

        foreach ($previousProductIds as $productId) {
            $this->productRepository->delete($productId);
        }

        return $lead;
    }

    /**
     * Resolve the submitted contact rows into person models.
     */
    protected function resolvePersonsFromInput(array $data): array
    {
        $personInputs = $this->normalizePersonInputs($data);
        $persons = [];
        $seen = [];

        foreach ($personInputs as $personInput) {
            if (! empty($personInput['id'])) {
                $person = $this->personRepository->findOrFail($personInput['id']);
            } elseif (! empty(trim($personInput['name'] ?? ''))) {
                $personData = array_merge($personInput, [
                    'entity_type'     => 'persons',
                    'organization_id' => $personInput['organization_id'] ?? $data['organization_id'] ?? null,
                ]);

                if (empty($personData['name'])) {
                    $orgId = $personData['organization_id'] ?? null;
                    $orgName = $orgId
                        ? optional(app(\Webkul\Contact\Repositories\OrganizationRepository::class)->find($orgId))->name
                        : null;
                    $personData['name'] = $orgName ? "Contact from {$orgName}" : 'New Contact';
                }

                $person = $this->personRepository->create($personData);
            } else {
                continue;
            }

            if (isset($seen[$person->id])) {
                continue;
            }

            $seen[$person->id] = true;
            $persons[] = $person;
        }

        return $persons;
    }

    /**
     * Normalize person, person[0], person[1] and person_id inputs into rows.
     */
    protected function normalizePersonInputs(array $data): array
    {
        $inputs = [];
        $raw = $data['person'] ?? null;

        if (is_array($raw)) {
            if (! empty($raw['id']) || ! empty(trim($raw['name'] ?? ''))) {
                $inputs[] = collect($raw)
                    ->reject(fn ($value, $key) => is_int($key))
                    ->all();
            }

            foreach ($raw as $key => $value) {
                if (is_int($key) && is_array($value)) {
                    $inputs[] = $value;
                }
            }
        }

        foreach ((array) ($data['person_ids'] ?? []) as $personId) {
            if (! empty($personId)) {
                $inputs[] = ['id' => $personId];
            }
        }

        if (! empty($data['person_id'])) {
            array_unshift($inputs, ['id' => $data['person_id']]);
        }

        return $inputs;
    }

    /**
     * Get a value from the first submitted person row.
     */
    protected function firstPersonInputValue(array $data, string $key): mixed
    {
        foreach ($this->normalizePersonInputs($data) as $personInput) {
            if (! empty($personInput[$key])) {
                return $personInput[$key];
            }
        }

        return null;
    }

    /**
     * Normalize optional nullable fields before insert/update.
     */
    protected function normalizeNullableFields(array &$data): void
    {
        foreach ([
            'lead_source_id',
            'lead_type_id',
            'priority',
            'user_id',
            'organization_id',
            'person_id',
        ] as $field) {
            if (array_key_exists($field, $data) && $data[$field] === '') {
                $data[$field] = null;
            }
        }
    }
}
