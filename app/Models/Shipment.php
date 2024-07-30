<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $guarded = [];
    use HasFactory;
    public function user()
    {
        return $this->belongsTo(User::class);
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
    
    public function job()
    {
        return $this->belongsTo(Job::class);
    }
    public function sewingblance()
    {
        return $this->hasMany(SewingBlance::class);
    } 
    

}
