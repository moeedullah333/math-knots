<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarInspectionModel extends Model
{
    use HasFactory;

    protected $table = "car_inspection";

    public function car_detail()
    {
        return $this->hasOne(listCar::class, 'id', 'car_id');
    }

    public function user_detail()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->select(['id', 'first_name', 'last_name', 'phone', 'email', 'image', 'user_role', 'created_at', 'updated_at']);
    }

    public function mechanic_detail()
    {
        return $this->hasOne(User::class, 'id', 'mechanic_id')->select(['id', 'first_name', 'last_name', 'email', 'city', 'zip_code', 'state', 'ssn', 'years_of_exp', 'services', 'image', 'user_role', 'created_at', 'updated_at']);
    }
}
