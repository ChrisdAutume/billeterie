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

        $articles = [];
        $amount = 0;

        if($request->input('don', 0) >= intval(config('billeterie.don.min', 0)/100))
        {
            $don = new Don();
            $don->amount = intval($request->input('don'))*100;
            $caddie[] = $don;

            $articles[] = [
                'name' => 'Don',
                'price' => intval($request->input('don'))*100,
                'quantity'   => 1
            ];

            $amount+=intval($request->input('don'))*100;
        } else if ($request->input('don', 0) != 0 && $request->input('don', 0) < intval(config('billeterie.don.min', 0)/100))
        {
            $request->session()->flash('warning', "Le montant minimum d'un don est de ".intval(config('billeterie.don.min', 0)/100).' €.');
            return redirect()->route('getCaddie');
        }

        $request->session()->forget('billets');

        if(count($caddie) <=0)
        {
            $request->session()->flash('error', 'Aucun articles de présent dans le panier.');
            return redirect()->route('getCaddie');
        }
        foreach ($caddie as $item)
        {
            if($item instanceof Don)
                continue;

            $billet = $item['billet'];
            $options = $item['options'];

            if($billet->price->canBeBuy($billet->mail))
            {
                $articles[] = [
                    'name' => $billet->price->name,
                    'price' => $billet->price->price,
                    'quantity'   => 1
                ];
                if (isset($billet->price->billets))
                    unset($billet->price->billets);
                if (isset($billet->price->lists))
                    unset($billet->price->lists);

                foreach ($options as $option)
                {
                    $articles[] = [
                        'name' => $option['option']->name,
                        'price' => $option['option']->price,
                        'quantity'   => $option['qty']
                    ];
                    if (isset($option['option']['pivot']))
                        unset($option['option']['pivot']);
                    $amount+= ($option['qty']*$option['option']->price);
                }
                $amount+=$billet->price->price;
            } else
                $request->session()->flash('warning', "Certains articles n'ont pu être ajouté a votre commande.");
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
