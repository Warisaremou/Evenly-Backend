<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Events extends Model
{
    use HasUuids;

    protected $fillable = [
        'cover',
        'title',
        'date_time',
        'location',
        'description',
    ];

    public function categories() : BelongsToMany 
    {
        return $this->belongsToMany(Categories::class, 'event_category');
    }

    public function tickets() : HasMany
    {
        return $this->hasMany(Tickets::class);
    }
}
