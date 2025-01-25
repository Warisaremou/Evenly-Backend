<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Orders extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'is_canceled',
        'is_expired',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
