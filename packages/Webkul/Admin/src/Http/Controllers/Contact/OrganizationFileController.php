<?php

namespace Webkul\Admin\Http\Controllers\Contact;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Contact\Models\OrganizationFile;
use Webkul\Contact\Repositories\OrganizationRepository;

class OrganizationFileController extends Controller
{
    public function __construct(protected OrganizationRepository $organizationRepository) {}

    /**
     * Store uploaded files for an organization.
     */
    public function store(Request $request, int $id): RedirectResponse
    {
        $organization = $this->organizationRepository->findOrFail($id);

        $request->validate([
            'title'       => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'files.*'     => ['required', 'file', 'max:10240'],
        ]);

        foreach ($request->file('files', []) as $file) {
            $path = $file->store('organization-files', 'public');

            OrganizationFile::create([
                'organization_id' => $organization->id,
                'user_id'         => Auth::id(),
                'title'           => $request->input('title'),
                'description'     => $request->input('description'),
                'path'            => $path,
                'original_name'   => $file->getClientOriginalName(),
                'mime_type'       => $file->getClientMimeType(),
                'size'            => $file->getSize(),
            ]);
        }

        return back()->with('success', trans('admin::app.contacts.organizations.view.files-uploaded'));
    }
}

