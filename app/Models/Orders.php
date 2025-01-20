<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'ticket_id',
        'is_canceled',
        'is_expired',
    ];
}
