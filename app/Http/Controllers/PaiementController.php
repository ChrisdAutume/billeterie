<?php

namespace App\Http\Controllers;

use App\Models\Don;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Config;
use App\Http\Requests;

class PaiementController extends Controller
{
    public function postCaddie(Request $request)
    {
        if(!($request->input('name') && $request->input('surname') && filter_var($request->input('mail'), FILTER_VALIDATE_EMAIL)))
        {
            $request->session()->flash('error', 'Les coordonnées de l\'acheteur doivent être renseigné.');
            return redirect()->route('getCaddie');
        }

        $caddie = $request->session()->get('billets');
        $request->session()->forget('billets');

        $articles = [];
        $amount = 0;

        if(count($caddie) <=0)
        {
            $request->session()->flash('error', 'Aucun articles de présent dans le panier.');
            return redirect()->route('getCaddie');
        }
        foreach ($caddie as $item)
        {
            $billet = $item['billet'];
            $options = $item['options'];

            if($billet->price->canBeBuy($billet->mail))
            {
                $articles[] = [
                    'name' => $billet->price->name,
                    'price' => $billet->price->price,
                    'quantity'   => 1
                ];

                foreach ($options as $option)
                {
                    $articles[] = [
                        'name' => $option['option']->name,
                        'price' => $option['option']->price,
                        'quantity'   => $option['qty']
                    ];
                    $amount+= ($option['qty']*$option['option']->price);
                }
                $amount+=$billet->price->price;
            } else
                $request->session()->flash('warning', "Certains articles n'ont pu être ajouté a votre commande.");
        }

        if($request->input('don', 0) > 0)
        {
            $don = new Don();
            $don->amount = intval($request->input('don'))*100;
            $caddie[] = $don;

            $articles[] = [
                'name' => 'Don de promo',
                'price' => intval($request->input('don'))*100,
                'quantity'   => 1
            ];

            $amount+=intval($request->input('don'))*100;
        }

        $order = new Order();
        $order->price= $amount;
        $order->name = $request->input('name');
        $order->surname = $request->input('surname');
        $order->mail = $request->input('mail');
        $order->data = serialize($caddie);
        $order->save();


        //EtuPay implentation
        $crypt = new Encrypter(base64_decode(Config::get('services.etupay.api_key')), 'AES-256-CBC');
        $payload =  $crypt->encrypt(json_encode([
            'type' => 'checkout',
            'amount'=> $amount,
            'client_mail' => $request->input('mail'),
            'firstname' => $request->input('name'),
            'lastname' => $request->input('surname'),
            'description' => config('billeterie.don.enabled'),
            'articles' => $articles,
            'service_data' => $order->id,
        ]));

        return Redirect(Config::get('services.etupay.endpoint').'&payload='.$payload);
    }
}
