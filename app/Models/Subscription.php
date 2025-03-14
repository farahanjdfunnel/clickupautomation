<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    public function scopeDueForCharge($query)
    {
        return $query->where('status', 'active')
        ->where('next_billing_date', '=', now());
    }
}
