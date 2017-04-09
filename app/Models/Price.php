<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
        'start_at',
        'end_at'
    ];

    public function lists()
    {
        return $this->belongsToMany(Liste::class)
            ->withPivot(['max_order'])
            ->withTimestamps();
    }

    public function billets()
    {
        return $this->hasMany(Billet::class);
    }

    public function options()
    {
        return $this->belongsToMany(Option::class, 'price_option');
    }

    public function optionsSellable()
    {
        return $this->belongsToMany(Option::class, 'price_option')->where('start_at','<=', Carbon::now()->toDateTimeString())->where('end_at', '>', Carbon::now()->toDateTimeString());
    }
    public function fields()
    {
        return $this->hasMany(FieldPrice::class);
    }

    public function billetSold(bool $agregation = null)
    {
        if($agregation && $this->price_aggregation)
        {
            $aggregation = explode(',', $this->price_aggregation);
            $already_sold = Billet::whereIn('price_id', $aggregation)->count();
            return $already_sold+$this->billets->count();
        } else return $this->billets->count();
    }
    public function canBeBuy($mail)
    {
        /*
         * Stack de vérification:
         *  0- Vérifier si horaire compatibles
         *  1- Vérifier si places disponibles (utiliser aggregat)
         *  2- Vérifier si condition listes
         *  3- Vérifier si un place mail/réduction n'existe pas déja
         */
        if(Carbon::now() < $this->start_at || Carbon::now() > $this->end_at )
            return false;

        if($this->max != 0 && $this->billetSold() >= $this->max)
            return false;

        if(count($this->lists) > 0) {
            $ok = false;
            foreach ($this->lists as $liste)
            {
                // 1- Check mail appartient a la liste
                if($liste->checkMail($mail))
                {
                    $count_already_buy = Billet::where('mail', $mail)->where('price_id', $this->id)->count();
                    $count_in_basket = 0;
                    //Count item in basket
                    if($billets = session('billets'))
                        foreach ($billets as $billet)
                            if(($billet->price_id == $this->id) && ($billet->mail == $mail))
                                $count_in_basket++;
                    if(($count_already_buy + $count_in_basket) < $liste->pivot->max_order)
                        return true;
                }
            }
            return false;
        }

        return true;
    }

    public function scopeVisibleNow($query)
    {
        return $query->where('start_at','<=', Carbon::now()->toDateTimeString())->where('end_at', '>', Carbon::now()->toDateTimeString());
    }

    public function scopeNextToBeVisible($query)
    {
        return $query->where('end_at', '>', Carbon::now()->toDateTimeString())->orderBy('start_at', 'desc');
    }



}
