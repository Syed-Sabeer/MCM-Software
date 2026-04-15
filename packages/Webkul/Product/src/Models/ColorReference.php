<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Model;

class ColorReference extends Model
{
    protected $fillable = [
        'name',
        'code',
    ];
}
