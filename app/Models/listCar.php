<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class listCar extends Model
{
    use HasFactory;

    //for Images Of Cars
    public function carImages(){
        return $this->hasMany(carImages::class,'car_id','id');
    }
    //For Add Of Car
    public function carAdd(){
        return $this->hasOne(carAds::class,'car_id','id');
    }

    public function userDetail(){
        return $this->belongsTo(User::class,'user_id','id')->select(['id', 'first_name', 'last_name', 'phone', 'email', 'image', 'user_role', 'created_at', 'updated_at']);
    }
    
    public function car_inspection_detail()
    {
        return $this->hasOne(CarInspectionModel::class,'car_id','id');
    }

}
