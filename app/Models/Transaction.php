<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Transaction extends Model
{
    use HasFactory;
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'crm_subscription_id', 'crm_subscription_id');
    }
}
