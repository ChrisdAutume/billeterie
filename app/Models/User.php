<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'firstname',
        'lastname',
        'mail',
        'level',
        'student_id'
    ];

    const ROLE_SELLER = 10;
    const ROLE_ADMIN  = 50;
    const ROLE_SUPERADMIN = 100;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
        'last_login',
    ];

    public function isAdmin()
    {
        return ($this->level >= 10);
    }

    public function requireAdmin()
    {
        if(!$this->isAdmin())
            abort(401);
    }

    public function requireLevel($level)
    {
        if($this->level < $level)
            abort(401);
    }

    public function isLevel($level)
    {
        if($this->level < $level)
            return false;
        return true;
    }

    public function hasRole($role): bool
    {
        $min = 0;
        switch ($role)
        {
            case 'seller': $min = static::ROLE_SELLER; break;
            case 'admin': $min = static::ROLE_ADMIN; break;
            case 'superadmin': $min = static::ROLE_SUPERADMIN; break;
        }

        if($this->level >= $min)
        {
            return true;
        } else {
            return false;
        }
    }

}
