<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tickets extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'quantity',
        'price',
        'user_id',
        'event_id',
        'type_ticket_id',   
    ];

    public function type_ticket() : BelongsTo
    {
        return $this->belongsTo(Typetickets::class, 'type_ticket_id');
    }

    public function event() : BelongsTo
    {
        return $this->belongsTo(Events::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders() : HasMany
    {
        return $this->hasMany(Orders::class);
    }
}
