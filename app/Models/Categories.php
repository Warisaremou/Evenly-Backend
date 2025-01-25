<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Categories extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
    ];
    
    public function events() : BelongsToMany
    {
        return $this->belongsToMany(Events::class, 'event_category', 'category_id', 'event_id');
    }
}
