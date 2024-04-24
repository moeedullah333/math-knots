<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'user_role',
        'image',
        'status',
        'city', 
        'zip_code', 
        'state', 
        'ssn', 
        'years_of_exp',
        'services',
        'auth_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin(){
      
        $is_admin =$this->roles()->where('name','admin')->first();
        if($is_admin != null){
            $is_admin = true;
        }else{
            $is_admin = false;
        }
        return $is_admin;
    }
    
    public function user_role()
    {
      return $this->hasOne(Role::class,'id','user_role')->select(['id','name']);  
    }
     public function getCreatedAtAttribute($value)
    {
        return date('y-M-d',strtotime($value));
    }

    // public function userProfile(){
    //     return $this->hasOne(userDetail::class,'user_id','id');
    // }
    
}
