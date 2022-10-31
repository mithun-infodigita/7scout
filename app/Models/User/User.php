<?php

namespace App\Models\User;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable,
        UserAttribute,
        UserMethod,
        UserRelationship,
        UserScope,
        HasApiTokens;

    public $incrementing = false;

    protected $guard_name = 'sanctum';

    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'password'
    ];


    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = [
        'full_name',
    ];

    protected $with = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [

    ];



}
