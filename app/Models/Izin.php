<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Izin extends Model
{
    use HasFactory;

    // Kolom yang dapat diisi (fillable)
    protected $fillable = [
        'user_id',
        'jenis',
        'start_date',
        'end_date',
        'reason',
        'document_path',
        'status',
        'reject_reason',
    ];

    // Menambahkan properti untuk otomatis meng-cast field tanggal
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // Relasi dengan model User
    public function user()
    {
        return $this->belongsTo(User::class); // Relasi belongsTo dengan model User
    }

    
}
