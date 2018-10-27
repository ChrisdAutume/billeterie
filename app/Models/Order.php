<?php

namespace App\Models;

use App\Events\OrderUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Config;

class Order extends Model
{
    public static $means = [
        'cash' => 'Especes',
        'buckutt' => 'BuckUTT',
        'cb' => 'CB',
        'check' => 'Cheque',
        'online' => 'Online',
    ];

    public static $states = [
        'ordering' => 'Commande en cours',
        'paid' => 'Payé',
        'canceled' => 'Annulé',
        'refunded' => 'Remboursé',
    ];

    public $events = [
        'saved' => OrderUpdated::class,
    ];

    public $fillable = [
        'name',
        'surname',
        'mail',
        'state',
        'mean_of_paiment',
        'price',
        ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->attributes['state'] = 'ordering';
    }

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
            if($item instanceof Don)
            {
                $item->order_id = $this->id;
                $item->save();
            } elseif(isset($item['billet'])) {
                $billet = $item['billet'];
                if(is_array($billet))
                {
                    $b = new Billet();
                    $b->forceFill($item['billet']);
                    $billet = $b;
                }
                $options = $item['options'];
                $billet->order_id = $this->id;
                $billet->save();

                foreach ($options as $opt) {
                    if(is_array($opt['option']))
                    {
                        $instance = new Option();
                        $instance->forceFill($opt['option']);
                        unset($instance->pivot); //TODO: A faire moins moche
                        $instance->exists = true;
                        $opt['option'] = $instance;
                    }

                    $billet->options()->save($opt['option'], [
                        'qty' => $opt['qty'],
                        'amount' => $opt['qty'] * $opt['option']->price
                    ]);
                }
                $billet->sendToMail();
            } elseif (isset($item['don']))
            {
                $don = new Don();
                $don->amount = $item['don'];
                $don->order_id = $this->id;
                $don->save();

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

    public function getEtuPayUrl()
    {
        if($this->state != "ordering")
            throw new \Exception("Order already paid or canceled");

        $articles = [];
        $order = unserialize($this->data);
        foreach ($order as $item)
        {
            if(isset($item['billet']))
            {
                if (!is_array($item['billet']))
                    $item['billet'] = ($item['billet'])->toArray();

                $articles[] = [
                    'name' => $item['billet']['name'],
                    'price' => 0,
                    'quantity'   => 1
                ];
            } elseif (isset($item['don']) || $item instanceof Don)
            {
                $articles[] = [
                    'name' => 'Don de '.round(intval($item['don']/100),2).'€',
                    'price' => 0,
                    'quantity'   => 1
                ];
            }
        }

        array_unshift($articles, [
            'name' => 'Commande #'.$this->id,
            'price' => $this->price,
            'quantity'   => 1
        ]);

        $crypt = new Encrypter(base64_decode(Config::get('services.etupay.api_key')), 'AES-256-CBC');
        $payload =  $crypt->encrypt(json_encode([
            'type' => 'checkout',
            'amount'=> $this->price,
            'client_mail' => $this->mail,
            'firstname' => $this->name,
            'lastname' => $this->surname,
            'description' => 'Commande sur la billetterie - '.config('billeterie.event.name'),
            'articles' => $articles,
            'service_data' => $this->id,
        ]));

        return Config::get('services.etupay.endpoint').'&payload='.$payload;

    }
}
