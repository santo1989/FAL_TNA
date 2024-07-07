<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TNA extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function explanations()
    {
        return $this->hasMany(TnaExplanation::class);
    }
}
