<?php

namespace App\Models;

use App\Events\OrderUpdated;
use App\Mail\BilletEmited;
use App\Mail\DonReceived;
use App\Mail\OrderRefused;
use App\Mail\OrderValidated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Order extends Model
{
    public static $means = [
        'cash' => 'Especes',
        'buckutt' => 'BuckUTT',
        'cb' => 'CB',
        'check' => 'Cheque',
        'online' => 'Online',
    ];

    public $events = [
        'saved' => OrderUpdated::class,
    ];

    public function validate()
    {
        if(!isset($this->data))
            return false;

        if($this->state == 'paid')
            return false;



        $caddie = unserialize($this->data);
        $this->state = 'paid';
        $this->data = null;

        foreach ($caddie as $item)
        {
            $billet = $item['billet'];
            $options = $item['options'];

            $billet->order_id = $this->id;
            $billet->save();

            foreach ($options as $opt) {
                $billet->options()->save($opt['option'], [
                    'qty'=>$opt['qty'],
                    'amount' => $opt['qty']*$opt['option']->price
                    ]);
            }
        }

        $this->save();
    }

    public function refused()
    {
        if($this->state == 'canceled')
            return false;
        $this->state = 'canceled';
        $this->save();
    }

    public function canceled()
    {
        $this->state = 'canceled';
        $this->save();
    }
    public function billets()
    {
        return $this->hasMany(Billet::class);
    }

    public function dons()
    {
        return $this->hasMany(Don::class);
    }
}
