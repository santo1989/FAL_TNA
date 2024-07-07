<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TnaExplanation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function tna()
    {
        return $this->belongsTo(Tna::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'input_by');
    }
}
