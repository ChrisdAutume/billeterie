<?php

namespace App\Http\Controllers;

use App\Mail\BilletEmited;
use App\Mail\OrderValidated;
use App\Models\Billet;
use App\Models\Order;
use App\Models\Price;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function adminExpressOrder(Request $request, $place_id = null)
    {
        Auth::user()->requireLevel(2);

        if(!is_null($place_id))
        {
            $order = new Order();
            $order->mean_of_paiment = 'cash';
            $order->name = 'expressOrder';
            $order->surname = 'expressOrder';
            $order->mail = config('billeterie.contact');
            $order->state = 'paid';
            $order->save();

            $billet = new Billet();
            $billet->order_id = $order->id;
            $billet->name = 'expressOrder';
            $billet->surname = 'expressOrder';
            $billet->mail = config('billeterie.contact');
            $billet->price_id = $place_id;
            $billet->validated_at = Carbon::now();
            $billet->save();
            $request->session()->flash('success', 'Place ajouté !');
            return redirect()->back();
        }
        return view('admin.express_order', [
           'prices' => Price::VisibleNow()->get()
        ]);
    }
    public function getNewItem(Request $request)
    {
        if(Price::VisibleNow()->count() == 0)
        {
            $request->session()->flash('warning', "Aucune place ne peut être vendu actuellement.");
            return redirect()->route('home');
        }
        return view('dashboard.createTicket');
    }

    public function resetCaddie(Request $request)
    {
        $request->session()->forget('billets');
        return redirect()->route('getCaddie');
    }

    public function postNewItem(Request $request)
    {
        // Vérification des champs
        if(!($request->input('name') && $request->input('surname') && filter_var($request->input('mail'), FILTER_VALIDATE_EMAIL)))
        {
            $request->session()->flash('error', 'Erreur dans le remplissage du formulaire !');
            return redirect()->route('addNewItem')->withInput();
        }

        $billet = new Billet();
        $billet->name = $request->input('name');
        $billet->surname = $request->input('surname');
        $billet->mail = $request->input('mail');

        $request->session()->put('billet', $billet);

        return view('dashboard.choosePrice', [
            'billet'=>$billet,
            'prices'=> Price::VisibleNow()->get()
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processNewItem(Request $request)
    {
        $billet = $request->session()->get('billet');

        if(!$billet)
            abort('404');

        if(!isset($billet->price_id) && !$request->input('price_type'))
            abort('404');

        if($request->input('price_type')) {
            $price = Price::findOrFail(intval($request->input('price_type')));
            $billet->price_id = $price->id;
            $request->session()->put('billet', $billet);

            //Récupération des options
            $opt_session= [];
            foreach ($price->optionsSellable as $option)
            {
                $key_value = (int) $request->input('option_'.$price->id.'_'.$option->id);
                if($request->has('option_'.$price->id.'_'.$option->id) && $key_value > 0 && !$option->isMandatory)
                {
                    if($key_value <= $option->available() && $key_value >= $option->min_choice && $key_value<= $option->max_choice) {
                        $opt_session[] = [
                            'option' => $option,
                            'qty' => $key_value
                        ];
                    }

                } else if($option->isMandatory && ($option->min_choice <= $option->available()))
                {
                    // Ajout automatique, si option obligatoire
                    $opt_session[] = [
                        'option' => $option,
                        'qty' => $option->min_choice
                    ];
                }
            }
            $request->session()->put('options', $opt_session);
        }else $price = $billet->price;


        if(count($price->fields) > 0)
        {
            /**
             * 1- Si input, afficher la page avec questionnaire
             *  -> traitement // vérification si ok
             * 2- Si pas input, ajout du billet
             */

            if(!$request->input('price_type'))
            {
                $price_data = [];
                $error = false;
                foreach($price->fields as $field)
                {
                    if($request->has('field_'.$field->id))
                        $price_data[$field->id] = $request->input('field_'.$field->id);
                    else if($field->mandatory)
                    {
                        $request->session()->flash('error', "L'ensemble des champs n'ont pas été remplit.");
                        $error=true;
                        continue;
                    }
                }
                if($error)
                    return view('dashboard.fieldPrice', [
                        'fields' => $price->fields
                    ]);
                else $billet->fields = json_encode($price_data);
            } else
                return view('dashboard.fieldPrice', [
                    'fields' => $price->fields
                ]);
        }

        if ($price->canBeBuy($billet->mail)) {
            $billet->price_id = $price->id;
            $request->session()->push('billets', [
                'billet' => $billet,
                'options' => $request->session()->get('options'),
            ]);
        } else
            $request->session()->flash('error', "Une erreur est survenu lors de l'ajout de votre billet au panier !");

        $request->session()->forget('billet');
        $request->session()->forget('options');

        return redirect()->route('getCaddie');

    }

    public function getCaddie(Request $request)
    {
        if(Price::VisibleNow()->count() == 0)
        {
            $request->session()->flash('warning', "Aucune place ne peut être vendu actuellement.");
            return redirect()->route('home');
        }

        return view('dashboard.caddie');
    }

    public function adminOrderList(Request $request)
    {
        Auth::user()->requireAdmin();
        $orders = Order::with('billets', 'dons')->get();
        return view('admin.order_list', ['orders'=> $orders]);
    }

    public function adminGetCreateNewOrder(Request $request)
    {
        Auth::user()->requireLevel(2);
        return view('admin.order_create', ['prices'=>Price::all(), 'means'=>Order::$means]);
    }

    public function adminPostCreateNewOrder(Request $request)
    {
        Auth::user()->requireLevel(2);

        if(!$this->checkRequestInput($request)) {
            $request->session()->flash('error', "L'ensemble des champs n'est pas remplit.");
            return redirect()->back();
        }
        $amount = 0;

        $order = new Order();
        $order->name = $request->input('buyer_name');
        $order->surname = $request->input('buyer_surname');
        $order->mail = $request->input('buyer_mail');
        $order->save();

        $inputs = $request->input();
        foreach ($inputs['price'] as $id=>$value)
        {
            $billet = new Billet();
            $billet->order_id = $order->id;
            $billet->name = $inputs['name'][$id];
            $billet->surname = $inputs['surname'][$id];
            $billet->mail = $inputs['mail'][$id];
            $billet->price_id = $value;
            $billet->save();
            $amount += Price::find($value)->price;
        }

        $order->price = $amount;
        $order->mean_of_paiment = $request->input('paiment');
        $order->state = 'paid';
        $order->seller_id = Auth::user()->id;
        $order->save();

        $request->session()->flash('success', 'La commande a été traité.');
        return redirect()->back();
    }

    public function checkRequestInput(Request $request)
    {
        foreach ($request->input() as $input)
        {
            if($input == null)
                return false;

            if(is_array($input))
                foreach ($input as $in)
                    if($in == null)
                        return false;
        }
        return true;
    }

}
