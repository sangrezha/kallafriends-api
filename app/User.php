<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    protected $table = 'kfr_member';
    public $incrementing = false;

    protected $fillable = [
        'name', 'email', 'passwordhash',
    ];

    protected $hidden = [
        'passwordhash'
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
