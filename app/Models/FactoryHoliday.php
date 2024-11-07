<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactoryHoliday extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'holiday_date',
        'description',
        'is_weekend',
        'is_default',
        'is_additional',
        'is_active',
    ];
}
