<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SPIn extends Model
{
    use HasFactory;
    protected $table = 'spins';
    protected $fillable = [
            'environment',
            'auth_key',
            'tpn',
            'location_id',
            'status',
    ];
}
