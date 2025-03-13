<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SewingBalance extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function sewingplan()
    {
        return $this->belongsTo(SewingPlan::class);
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }
}
