<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $dates = [
        'start_at',
        'end_at',
        'updated_at',
        'created_at',
    ];

    public function billets()
    {
        return $this->belongsToMany(Billet::class);
    }

    public function prices()
    {
        return $this->belongsToMany(Price::class);
    }

    public function scopeActive($query)
    {
        return $query->where('start_at','<=', Carbon::now()->toDateTimeString())->where('end_at', '>', Carbon::now()->toDateTimeString());
    }

    public function available()
    {
        if($this->max_order == 0) {
            return $this->max_choice;
        }

        return ($this->max_order - $this->billets->count());
    }
}
