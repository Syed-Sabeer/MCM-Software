<?php

namespace Webkul\Contact\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationFile extends Model
{
    protected $table = 'organization_files';

    protected $fillable = [
        'organization_id',
        'user_id',
        'title',
        'description',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];
}

