<?php

namespace Webkul\Lead\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Lead\Contracts\Priority as PriorityContract;

class Priority extends Model implements PriorityContract
{
    protected $table = 'lead_priorities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the leads.
     */
    public function leads()
    {
        return $this->hasMany(LeadProxy::modelClass());
    }
}
