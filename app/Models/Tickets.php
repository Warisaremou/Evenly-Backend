<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tickets extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'quantity',
        'price',
        'user_id',
        'event_id',
        'typeticket_id',   
    ];

    public function typeticket() : BelongsTo
    {
        return $this->belongsTo(Typetickets::class);
    }

    public function event() : BelongsTo
    {
        return $this->belongsTo(Events::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
