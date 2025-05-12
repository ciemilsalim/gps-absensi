<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'user_id', 'date',
        'check_in_time', 'check_in_lat', 'check_in_long',
        'check_out_time', 'check_out_lat', 'check_out_long',
    ];

    /** Relasi banyak ke satu: Attendance milik satu User */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
