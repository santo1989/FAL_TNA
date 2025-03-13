<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SewingPlan extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function sewingbalance()
    {
        return $this->hasMany(SewingBalance::class);
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function tna()
    {
        return $this->hasMany(TNA::class);
    }

    public function shipment()
    {
        return $this->hasMany(Shipment::class);
    }

   
}
