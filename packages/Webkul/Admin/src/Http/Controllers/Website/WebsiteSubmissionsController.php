<?php

namespace Webkul\Admin\Http\Controllers\Website;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;

class WebsiteSubmissionsController extends Controller
{
    /**
     * Display Website Submissions index page.
     */
    public function index(): View
    {
        return view('admin::website-submissions.index');
    }

    /**
     * Display list of contacts (paginated via API).
     */
    public function contacts(): View
    {
        return view('admin::website-submissions.contacts');
    }

    /**
     * Display list of careers/resumes (paginated via API).
     */
    public function careers(): View
    {
        return view('admin::website-submissions.careers');
    }

    /**
     * Fetch contacts from external API (JSON response for Vue).
     */
    public function getContacts(): JsonResponse
    {
        $page = request()->input('page', 1);
        $perPage = request()->input('per_page', 15);

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get(
                'http://localhost/MCMWebsite/api/contacts?page=' . $page . '&per_page=' . $perPage
            );

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch contacts: ' . $response->status(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fetch careers from external API (JSON response for Vue).
     */
    public function getCareers(): JsonResponse
    {
        $page = request()->input('page', 1);
        $perPage = request()->input('per_page', 15);

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get(
                'http://localhost/MCMWebsite/api/careers?page=' . $page . '&per_page=' . $perPage
            );

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch careers: ' . $response->status(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fetch single contact detail.
     */
    public function showContact(int $id): JsonResponse
    {
        try {
            $response = \Illuminate\Support\Facades\Http::get(
                "http://localhost/MCMWebsite/api/contacts/{$id}"
            );

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'message' => 'Contact not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fetch single career detail.
     */
    public function showCareer(int $id): JsonResponse
    {
        try {
            $response = \Illuminate\Support\Facades\Http::get(
                "http://localhost/MCMWebsite/api/careers/{$id}"
            );

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'message' => 'Career not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
