<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cnotes extends Model
{
    use HasFactory;

    protected $table = 'cnotes';
    protected $fillable = [
        'id', 'latitude', 'longitude', 'photo', 'cnote_number', 'pod_code', 'city_name', 'cust_type', 'signature',
        'cnote_date', 'keterangan', 'pod_status', 'price_per_kg', 'last_status', 'service_type', 'cnote_amount',
        'cnote_origin', 'cnote_weight', 'shipping_cost', 'cnote_cust_no', 'cnote_pod_date', 'freight_charge',
        'insurance_amount', 'reference_number', 'cnote_destination', 'cnote_goods_descr', 'estimate_delivery',
        'cnote_pod_receiver', 'cnote_receiver_name', 'cnote_services_code'
    ];

    public function details()
    {
        return $this->hasOne(cnote_details::class, 'cnote_number', 'cnote_number');
    }

    public function histories()
    {
        return $this->hasMany(cnote_histories::class, 'cnote_number', 'cnote_number');
    }

    public function photoHistories()
    {
        return $this->hasMany(photo_histories::class, 'cnote_number', 'cnote_number');
    }
}
