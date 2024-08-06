<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cnote_histories extends Model
{
    use HasFactory;

    protected $table = 'cnote_histories';
    protected $fillable = [
        'id', 'cnote_number', 'code', 'date', 'description', 'photo1', 'photo2', 'photo3', 'photo4', 'photo5'
    ];

    public function cnote()
    {
        return $this->belongsTo(cnotes::class, 'cnote_number', 'cnote_number');
    }
}
