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

    public function tna()
    {
        return $this->hasMany(TNA::class);
    }

    public function sewingplan()
    {
        return $this->hasMany(SewingPlan::class);
    }

    public function sewingbalance()
    {
        return $this->hasMany(SewingBalance::class);
    }

    public function shipment()
    {
        return $this->hasMany(Shipment::class);
    }

    public function job()
    {
        return $this->hasMany(Job::class);
    }

    public function buyer()
    {
        return $this->hasMany(Buyer::class);
    }

    public function company()
    {
        return $this->hasMany(Company::class);
    }

    public function division()
    {
        return $this->hasMany(Division::class);
    }

    public function user()
    {
        return $this->hasMany(User::class);
    }
}
