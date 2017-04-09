<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailTemplate extends Model
{
    protected $fillable = [
        'title', 'content'
    ];

    protected $primaryKey = 'name';
    protected $keyType = 'String';

    public function getRouteKeyName()
    {
        return 'name';
    }
}
