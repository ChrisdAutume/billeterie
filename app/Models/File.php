<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid as RamseyUuid;

class File extends Model
{
    protected $fillable = [
        'name', 'type', 'data'
    ];


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if(!isset($this->attributes['uuid']))
            $this->generateUuid();
    }

    public function generateUuid()
    {
        $this->uuid = RamseyUuid::uuid4()->toString();
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

}
