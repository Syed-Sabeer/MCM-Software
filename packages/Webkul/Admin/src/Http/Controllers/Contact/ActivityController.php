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
        $activities = $this->activityRepository
            ->leftJoin('organization_activities', 'activities.id', '=', 'organization_activities.activity_id')
            ->where('organization_activities.organization_id', $id)
            ->get();

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
