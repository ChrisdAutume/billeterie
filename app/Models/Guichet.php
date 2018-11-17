<?php

namespace App\Models;

use App\Events\GuichetCreated;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Guichet extends Authenticatable
{
    protected $dates = [
        'created_at',
        'updated_at',
        'start_at',
        'end_at'
    ];

    protected $events = [
        'created' => GuichetCreated::class,
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if(!isset($this->attributes['uuid']))
            $this->generateUuid();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function billets_validated()
    {
        return $this->belongsToMany(Billet::class);
    }

    public function billets()
    {
        return $this->hasManyThrough(Billet::class, Order::class);
    }

    public function getPrices()
    {
        return $this->getAcl();
    }

    public function getAcl()
    {
        if($this->type == 'sell')
        {
            if(is_array($this->acl))
                return Price::whereIn('id', $this->acl)->get();
        } else if($this->type == 'validation')
        {
            if(is_array($this->acl))
            {
                $rtn = [];
                foreach ($this->acl as $acl)
                {
                    $acl= explode(':', $acl);
                    switch ($acl[0])
                    {
                        case 'billet':
                            $rtn[] = Billet::find($acl[1]);
                            break;
                        case 'option':
                            $rtn[] = Option::find($acl[1]);
                            break;
                    }
                    return $acl;
                }
            }
        }
    }
    public function generateUuid()
    {
        $this->uuid = RamseyUuid::uuid4()->toString();
    }

    public function setAclAttribute($value)
    {
        if(is_array($value))
            $this->attributes['acl'] = json_encode($value);
    }

    public function getAclAttribute($value)
    {
        return json_decode($value);
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

}
