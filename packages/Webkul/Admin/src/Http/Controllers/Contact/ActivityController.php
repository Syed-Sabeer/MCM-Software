<?php

namespace Webkul\Admin\Http\Controllers\Contact;

use Illuminate\Support\Facades\DB;
use Webkul\Activity\Repositories\ActivityRepository;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Resources\ActivityResource;
use Webkul\Email\Repositories\AttachmentRepository;
use Webkul\Email\Repositories\EmailRepository;

class ActivityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected ActivityRepository $activityRepository,
        protected EmailRepository $emailRepository,
        protected AttachmentRepository $attachmentRepository
    ) {}

    /**
     * Display a listing of the resource for organizations.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $contactIds = DB::table('persons')
            ->where('organization_id', $id)
            ->pluck('id')
            ->all();

        $contactNames = DB::table('persons')
            ->whereIn('id', $contactIds)
            ->selectRaw("id, COALESCE(NULLIF(TRIM(name), ''), NULLIF(TRIM(CONCAT_WS(' ', first_name, last_name)), ''), CONCAT('Contact #', id)) as display_name")
            ->pluck('display_name', 'id');

        // Get organization activities
        $organizationActivities = $this->activityRepository
            ->select('activities.*')
            ->leftJoin('organization_activities', 'activities.id', '=', 'organization_activities.activity_id')
            ->where(function ($query) use ($id) {
                $query->where('organization_activities.organization_id', $id)
                    ->orWhere(function ($query) use ($id) {
                        $query->where('activities.entity_type', 'organizations')
                            ->where('activities.entity_id', $id);
                    });
            })
            ->get();

        // Get person activities for all persons in this organization
        $personActivities = $this->activityRepository
            ->select('activities.*')
            ->leftJoin('person_activities', 'activities.id', '=', 'person_activities.activity_id')
            ->leftJoin('persons', 'person_activities.person_id', '=', 'persons.id')
            ->where(function ($query) use ($id, $contactIds) {
                $query->where('persons.organization_id', $id);

                if (! empty($contactIds)) {
                    $query->orWhere(function ($query) use ($contactIds) {
                        $query->where('activities.entity_type', 'persons')
                            ->whereIn('activities.entity_id', $contactIds);
                    });
                }
            })
            ->get();

        // Combine both collections and remove duplicates
        $activities = $organizationActivities->merge($personActivities)
            ->unique('id')
            ->each(function ($activity) use ($contactIds, $contactNames) {
                $activity->loadMissing(['files', 'persons', 'participants', 'user']);

                if ($activity->entity_type === 'persons' && $activity->entity_id) {
                    $activity->setAttribute('contact_name', $contactNames->get((int) $activity->entity_id));

                    return;
                }

                $person = $activity->persons->first(fn ($person) => in_array((int) $person->id, array_map('intval', $contactIds), true));

                if ($person) {
                    $name = trim((string) $person->name);
                    $fallbackName = trim(implode(' ', array_filter([
                        $person->first_name ?? null,
                        $person->last_name ?? null,
                    ])));

                    $activity->setAttribute('contact_name', $name !== '' ? $name : ($fallbackName ?: 'Contact #'.$person->id));
                }
            });

        return ActivityResource::collection($this->concatEmailAsActivities($id, $activities));
    }

    /**
     * Concat email as activities.
     */
    public function concatEmailAsActivities($organizationId, $activities)
    {
        // For organizations, we'll just return the activities
        // Emails are linked to persons/leads, not directly to organizations
        return $activities->sortByDesc('id')->sortByDesc('created_at');
    }
}
