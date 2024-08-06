<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cnote_details extends Model
{
    use HasFactory;

    protected $table = 'cnote_details';

    protected $fillable = [
        'id', 'cnote_number', 'cnote_date', 'cnote_origin', 'cnote_weight', 'cnote_shipper_city', 'cnote_shipper_name',
        'cnote_receiver_city', 'cnote_receiver_name', 'cnote_shipper_addr1', 'cnote_shipper_addr2', 'cnote_shipper_addr3',
        'cnote_receiver_addr1', 'cnote_receiver_addr2', 'cnote_receiver_addr3'
    ];

    public function cnote()
    {
        return $this->belongsTo(cnotes::class, 'cnote_number', 'cnote_number');
    }
}
