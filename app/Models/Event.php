<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_event'; // Menggunakan id_event sebagai primary key
    protected $fillable = [
        'name',
        'date',
        'start_time',
        'end_time',
        'image',
        'location',
        'description',
        'capacity',
        'status',
        'category',
        'whatsapp_group_link', // Tambahkan ini
    ];
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'id_event');
    }
}
