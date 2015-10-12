<?php

namespace App\Models\User;

use App\Models\Model;

class User extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cellphone', 'password'
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $hidden = ['password', 'salt'];

    public $cellphone;
}