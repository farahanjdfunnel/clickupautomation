<?php

namespace App\Models;

use App\Helper\CRM;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ClickupAuths extends Model
{
    protected $table = 'clickup_tokens';
    use HasFactory;
    protected $guarded = [];
}
