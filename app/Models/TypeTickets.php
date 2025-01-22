<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeTickets extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
    ];

    protected $table = 'type_tickets';
    
    public function tickets() : HasMany
    {
        return $this->hasMany(Tickets::class, 'type_ticket_id');
    }
}
