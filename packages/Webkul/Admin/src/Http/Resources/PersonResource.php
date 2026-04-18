<?php

namespace Webkul\Admin\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $fullName = trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));

        return [
            'id'              => $this->id,
            'name'            => $fullName !== '' ? $fullName : ($this->name ?? ''),
            'email'           => $this->email ?: collect($this->emails ?? [])->pluck('value')->filter()->first(),
            'organization_name' => $this->organization?->name,
            'emails'          => $this->emails,
            'contact_numbers' => $this->contact_numbers,
            'organization'    => new OrganizationResource($this->organization),
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
