<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The name of primary key column
     *
     * @var string
     */
    protected $primaryKey = 'pk_user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function getUserByEmailID($emailID)
    {
        return self::where('email', $emailID)->get();
    }

    public static function getUserByEmpCode($empCode)
    {
        return self::where('employee_code', $empCode)->get();
    }
}
