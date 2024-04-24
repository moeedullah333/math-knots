<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class carAds extends Model
{
    use HasFactory;

    public function cars()
    {
        return $this->belongsTo(listCar::class);
    }
    
     public function car_detail()
    {
        return $this->hasOne(listCar::class,'id','car_id')->with('userDetail');
    }
}
