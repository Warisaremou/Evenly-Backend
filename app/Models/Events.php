<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Events extends Model
{
    use HasUuids;

    protected $fillable = [
        'cover',
        'title',
        'date',
        'time',
        'location',
        'description',
        'user_id',
    ];

    public function categories() : BelongsToMany 
    {
        return $this->belongsToMany(Categories::class, 'event_category', 'event_id', 'category_id');
    }

    public function tickets() : HasMany
    {
        return $this->hasMany(Tickets::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
