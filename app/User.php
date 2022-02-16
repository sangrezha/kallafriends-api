<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    protected $table = 'kfr_member';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'customer_id', 'type', 'name', 'email', 'dob', 'id_type', 'id_number', 'id_province', 'id_city', 'address', 'mobile', 'phone', 'sex', 'occupation', 'religion', 'relation', 'hobby', 'socmed', 'facebook', 'twitter', 'instagram', 'google', 'taukalla', 'multi_car', 'car', 'other_brand', 'other_car', 'car_year', 'car_nopol', 'car_norangka', 'call_via', 'call_when', 'call_time', 'community', 'id_community', 'id_community_anggota', 'picture', 'status', 'validmember', 'communitymember', 'point_profile', 'reset_token', 'reset_expired', 'stnk', 'id_facebook', 'id_google', 'id_apple', 'auth_apple_code'
    ];

    protected $hidden = [
        'passwordhash', 'post_by', 'post_date', 'modify_date',
    ];

    public function getAuthPassword()
    {
        return $this->passwordhash;
    }

    public function validateForPassportPasswordGrant($password)
    {
        $hashedPassword = md5(serialize($password));

        return $hashedPassword == $this->passwordhash;
    }
}
