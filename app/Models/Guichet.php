<?php

namespace App\Models;

use App\Events\GuichetCreated;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid as RamseyUuid;

class Guichet extends Model
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

    public function billets()
    {
        return $this->hasManyThrough(Billet::class, Order::class);
    }

    public function getPrices()
    {
        if(is_array($this->acl))
            return Price::whereIn('id', $this->acl)->get();
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
