<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarAdSafeUnSafe extends Model
{
    use HasFactory;

    protected $table = "car_ad_safe_unsafe";

    public function user_detail()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function car_ad_detail()
    {
        return $this->hasOne(carAds::class,'id','car_ad_id');
    }
}
