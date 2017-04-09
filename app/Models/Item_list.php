<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item_list extends Model
{
    public function liste()
    {
        return $this->belongsTo(Liste::class);
    }
}
