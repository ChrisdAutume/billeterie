<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Config;
use App\Http\Requests;

class EtuPayController extends Controller
{
    public function etupayReturn(Request $request)
    {
        if(!$request->input('payload'))
            abord(403, 'Something wrong happened.');

        if(!$callback = $this->readCallback($request->payload))
            abord(403, 'Something wrong happened.');

        switch ($callback->step) {
            case 'PAID':
            case 'AUTHORISATION':
                $request->session()->flash('success', "Le paiment a bien été accepté, vous recevrez prochainement une confirmation via mail.");
                $order = Order::find($callback->service_data);
                $request->session()->flash('order_validated', $order);
                break;
            case 'REFUSED':
                $request->session()->flash('error', "Votre commande a été annulé suite au refus de votre banque !");
                break;
            case 'CANCELED':
            $request->session()->flash('warning', "Votre commande a été annulé !");
                break;
        }

        return redirect()->route('home');

    }

    public function etupayCallback(Request $request)
    {
        if(!$request->input('payload'))
            abord(403, 'Something wrong happened.');

        if(!$callback = $this->readCallback($request->payload))
            abord(403, 'Something wrong happened.');

        $order = Order::findOrFail($callback->service_data);
        $order->mean_of_paiment = 'online';
        $order->transaction_id = $callback->transaction_id;

        switch ($callback->step) {
            case 'PAID':
            case 'AUTHORISATION':
                $order->validate();
                break;
            case 'REFUSED':
                $order->refused();
                break;
            case 'CANCELED':
                $order->canceled();
                break;
        }

        $order->save();
    }

    private function readCallback($payload)
    {
        $crypt = new Encrypter(base64_decode(Config::get('services.etupay.api_key')), 'AES-256-CBC');
        $payload = json_decode($crypt->decrypt($payload));
        if ($payload && is_numeric($payload->service_data))
            return $payload;

        return null;
    }
}
