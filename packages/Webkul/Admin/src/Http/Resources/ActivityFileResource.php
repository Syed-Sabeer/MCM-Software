<?php

namespace Webkul\Admin\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $name = trim((string) $this->name);
        $name = $name !== ''
            ? $name
            : (trim((string) optional($this->activity)->title) ?: basename((string) $this->path));

        $extension = pathinfo((string) $this->path, PATHINFO_EXTENSION);
        if ($extension && ! str_contains(basename($name), '.')) {
            $name .= '.'.$extension;
        }

        return [
            'id'         => $this->id,
            'name'       => $name,
            'path'       => $this->path,
            'url'        => $this->url,
            'preview_url' => route('admin.activities.file_preview', $this->id),
            'download_url' => route('admin.activities.file_download', $this->id),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
