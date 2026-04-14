<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'tickets';

    // Primary key
    protected $primaryKey = 'id_ticket';

    // Kolom yang dapat diisi
    protected $fillable = [
        'id_event',
        'type',
        'price',
        'qr_code',
        'quantity',
    ];

    // Relasi dengan tabel events
    public function event()
    {
        return $this->belongsTo(Event::class, 'id_event', 'id_event');
    }
    
    public function orders()
    {
        return $this->hasMany(Order::class, 'id_ticket', 'id_ticket');
    }

    public function ticketValidation()
    {
        return $this->hasMany(TicketValidation::class, 'id_ticket', 'id_ticket');
    }
}
