<?php

namespace App\Http\Controllers;

use App\Mail\BilletEmited;
use App\Mail\OrderValidated;
use App\Models\Billet;
use App\Models\Option;
use App\Models\Order;
use App\Models\Price;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function apiCreate(Request $request)
    {
        try {
            if (!($request->has(['buyer.name', 'buyer.surname']) && filter_var($request->input('buyer.mail'), FILTER_VALIDATE_EMAIL))) {
                return response()->json(['error' => 'Buyer information are not correct'], 400);
            }

            $billets = [];
            $total = 0;
            $items = $request->input('items');

            if (count($items) == 0)
                throw new \Exception("No items found");

            foreach ($items as $item)
            {
                $billet = [
                    'billet' => null,
                    'options' => [
                        //['option'=> Object, 'qyt'=> 0 ]
                    ]
                ];
                $item = collect($item);
                if (!($item->has('name') && $item->has('surname') && filter_var($item->get('mail'), FILTER_VALIDATE_EMAIL) && $item->has('price_id')))
                    throw new \Exception("Item incomplete");

                $price = Price::find($item->get('price_id'));
                if(!$price || !$price->canBeBuy($item->get('mail')))
                    throw new \Exception("The price can't be bought.");

                $b = new Billet([
                    'name' => $item->get('name'),
                    'surname' => $item->get('surname'),
                    'mail' => $item->get('mail'),
                    'price_id' => $price->id
                ]);
                $total += $price->price;



                // Let's do options
                $opts = $price->optionsSellable;
                $order_opts = $item->get('options');
                foreach ($opts as $opt)
                {
                    if($opt->isMandatory && ($opt->min_choice <= $opt->available()))
                    {
                        $billet['options'][] = [
                            'option' => $opt->toArray(),
                            'qty'   => $opt->min_choice
                        ];
                        $total += $opt->price * $opt->min_choice;
                    } else {
                        $k = array_search($opt->id, array_column($order_opts, 'id'));
                        if(isset($k))
                        {
                            $qty = $order_opts[$k]['qty'];

                            if ($qty <= $opt->available() && $qty >= $opt->min_choice && $qty<= $opt->max_choice)
                            {
                                $billet['options'][] = [
                                    'option' => $opt->toArray(),
                                    'qty'   => $qty
                                ];
                                $total += $opt->price * $qty;
                            }
                        }
                    }
                }

                //Let's add fields
                $fields = $price->fields;
                $order_fields = $item->get('options');
                $billet_fields = [];
                foreach ($fields as $field)
                {
                    $k = array_search($field->id, array_column($order_fields, 'id'));
                    if(isset($k))
                    {
                        $data = $order_fields[$k]['value'];
                        $billet_fields[$field->name] = $data;
                    } else if($k->mandatory)
                        throw new \Exception($field->name." field is empty.");
                }
                $b->fields = $billet_fields;

                if($order_fields)


                $billet['billet'] = $b->toArray();
                $billets[] = $billet;
            }

            if($request->has('don') && config("billeterie.don.enabled") && $request->input('don') >= config("billeterie.don.min"))
            {
                $billets['don'] = intval($request->input('don'));
                $total += $billets['don'];
            }

            $order = new Order();
            $order->name = $request->input('buyer.name');
            $order->surname = $request->input('buyer.surname');
            $order->mail = $request->input('buyer.mail');
            $order->data = serialize($billets);
            $order->price = $total;
            $order->save();

            return response()->json([
                'order_id'  => $order->id,
                'amount'    => $order->price,
                'paiment_url'   => $order->getEtuPayUrl()
            ]);
        } catch (\Exception $e)
        {
            return response()->json([
                "error" => $e->getMessage()
            ],400);
        }
    }
    public function apiGetAvailablesPrices(Request $request)
    {
        if ($request->has('mail') && filter_var($request->input('mail'), FILTER_VALIDATE_EMAIL))
        {
            $mail = $request->input('mail');
            $prices = Price::VisibleNow()->with(['options', 'fields'])->get();
            $return = $prices->map(function(Price $price) use ($mail){
                $b = collect($price)->only(['id', 'name', 'description', 'price', 'options'])->toArray();
                $b['can_buy'] = $price->canBeBuy($mail);
                return $b;
            });


            return response()->json([
                'prices' => $return
            ]);
        } else
            return response()->json([]);
    }

    public function adminOrderEdit(Order $order, Request $request)
    {
        return view('orders.admin_edit', [
            'order' => $order,
            'prices' => Price::all()
        ]);
    }

    public function adminPostOrderEdit(Order $order, Request $request)
    {
        $order->fill($request->input());
        $order->save();
        $request->session()->flash('success', "Commande mis à jour !");

        return redirect()->back();
    }
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
                        if (isset($option['pivot']))
                            unset($option['pivot']);
                        $opt_session[] = [
                            'option' => $option,
                            'qty' => $key_value
                        ];
                    }

                } else if($option->isMandatory && ($option->min_choice <= $option->available()))
                {
                    // Ajout automatique, si option obligatoire
                    if (isset($option['pivot']))
                        unset($option['pivot']);
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

            if (isset($billet['price']['billets']))
                unset($billet['price']['billets']);
            if (isset($billet['price']['lists']))
                unset($billet['price']['lists']);
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
        $orders = Order::with('billets', 'dons', 'billets.price')->get(['id', 'name', 'surname', 'state']);
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
        $i=0;
        foreach ($inputs['price'] as $id=>$value)
        {
            $i++;
            $price=Price::find($value);
            $billet = new Billet();
            $billet->order_id = $order->id;
            $billet->name = $inputs['name'][$id];
            $billet->surname = $inputs['surname'][$id];
            $billet->mail = $inputs['mail'][$id];
            $billet->price_id = $value;
            $billet->save();
            $amount += $price->price;

            foreach ($price->optionsSellable as $option)
            {
                $opt = false;
                $key_value = (int) $request->input('option_'.$i.'_'.$price->id.'_'.$option->id);
                if($request->has('option_'.$i.'_'.$price->id.'_'.$option->id) && $key_value > 0 && !$option->isMandatory)
                {
                    if($key_value <= $option->available() && $key_value >= $option->min_choice && $key_value<= $option->max_choice) {
                        $amount += $key_value * $option->price;
                        $opt = [
                            'option' => $option,
                            'qty' => $key_value
                        ];
                    }

                } else if($option->isMandatory && ($option->min_choice <= $option->available()))
                {
                    // Ajout automatique, si option obligatoire
                    $amount += $key_value * $option->price;

                    $opt = [
                        'option' => $option,
                        'qty' => $option->min_choice
                    ];
                }

                if ($opt)
                {
                    $billet->options()->save($opt['option'], [
                        'qty' => $opt['qty'],
                        'amount' => $opt['qty'] * $opt['option']->price
                    ]);
                }
            }
            $billet->sendToMail();
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
