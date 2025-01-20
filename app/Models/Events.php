<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    use HasUuids;

    protected $fillable = [
        'picture',
        'title',
        'date_time',
        'location',
        'description',
    ];
    public function categories()
    {
        return $this->belongsToMany(Categories::class, 'event_category');
    }
}
