<?php

namespace App\Http\Controllers;

use App\Mail\GuichetCreated;
use App\Models\Billet;
use App\Models\Guichet;
use Carbon\Carbon;
use Monolog\Logger;
use Torann\Hashids\Facade as Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Price;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Excel;


class GuichetController extends Controller
{
    public function getAdminGuichet()
    {
        Auth::user()->requireLevel(10);
        return view('admin.guichet_create', [
            'prices' => Price::all(),
            'guichets' => Guichet::all()
        ]);
    }

    public function postAdminGuichet(Request $request)
    {
        Auth::user()->requireLevel(10);

        if(empty($request->input('name')) || empty($request->input('mail')) || empty($request->input('start_at')) || empty($request->input('end_at')))
        {
            $request->session()->flash('error', "L'ensemble des champs n'est pas remplit.");
            return redirect()->back();
        }

        $guichet = new Guichet();
        $guichet->name = $request->input('name');
        $guichet->mail = $request->input('mail');
        $guichet->start_at = Carbon::createFromFormat('d/m/Y H:i', $request->input('start_at'));
        $guichet->end_at = Carbon::createFromFormat('d/m/Y H:i', $request->input('end_at'));
        $guichet->type = $request->input('type', 'sell');
        $guichet->acl = $request->input('acl');
        $guichet->save();

        if(!is_null($guichet->mail) && $guichet->type == 'sell')
        {
            Mail::to($guichet->mail)
                ->cc(config('billeterie.contact'))
                ->queue(new GuichetCreated($guichet));

            //$job = (new GuichetEnded($guichet))->delay($guichet->end_at);
            //dispatch($job);
        }
        $request->session()->flash('success', "Guichet crée !");
        return redirect()->route('admin_guichet_create');

    }

    public function getIndex()
    {
        Auth::user()->requireLevel(2);
        return view('guichet.home');
    }

    /**
     * API bulk validation
     */
    public function apiPostValidation(Request $request)
    {
        if(Auth::user()->type != 'validation')
        {
            return response()->json(['error' => 'This api can\'t be used.'], 401);
        }
        $datas = $request->input();

        foreach($datas as $data)
        {
            $billet = Billet::find($data['id']);
            if($billet)
            {
                if($billet->validated_at)
                {
                    Log::info("Billet ".$billet->id." déja validé !");
                } else {
                    $billet->validated_at = (new \DateTime())->setTimestamp($data['validated_at']);
                    $billet->save();
                }
            }
        }
        return response()->json([]);
    }
    /**
     * Js bulk validation for gichet
     * expect a json object with `id => datetime` inside
     */
    public function postOfflineValidation(Request $request)
    {
        Auth::user()->requireLevel(2);
        $input = $request->all();

        // Find all given billets
        $billets = Billet::find(array_keys($input));
        $count = 0;
        foreach ($billets as $billet) {
            $date = new \DateTime($input[$billet->id]);
            if ($billet->validated_at == null) {
                $billet->validated_at = $date;
                $billet->save();
                $count++;
            }
            else if($billet->validated_at != $date) {
                // Warning someone use a ticket twice
                Log::warning('Billet '. $billet->name . ' ' . $billet->surname . ' ('.$billet->id.') has been validated more than once via offline mode', [ 'billet' => $billet ]);
            }
        }

        return response()->json([
            'updated' => $count,
        ]);
    }

    /**
     * Get data used for autocompletion and offline validation
     */
    public function getOfflineData(Request $request)
    {
        Auth::user()->requireLevel(2);


        // Cache it for 1 minute
        $result = Cache::remember('guichet_offline_data', 1, function () {
            $billets = Billet::with('options')->with('price')->get();
            $result = [];
            foreach($billets as $billet) {

                // Give only partial security code to avoid a disclosure of all
                // tickets code in case of a gichet corruption
                // Even with -3 characters, we have no collisions on a real database of 1328 tickets
                // But if we remove to much characters, it will be too easy to found
                // a working partial code. So we remove 2 chars.
                $code = substr($billet->getQrCodeSecurity(), 0, -2);

                $options = '';
                foreach ($billet->options as $option)
                {
                    if($options)
                        $options .= ', ';

                    $options .= $option->pivot->qty .' '.$option->name;
                }

                $result[] = [
                    'id' => $billet->id,
                    'name' => $billet->name,
                    'surname' => $billet->surname,
                    'mail' => $billet->mail,
                    'validated_at' => $billet->validated_at ? $billet->validated_at->format(\DateTime::ATOM) : null,
                    'code' => $code,
                    'options' => $options,
                ];
            }

            return $result;
        });

        return response()->json([
            'serverTime' => date(\DateTime::ATOM),
            'reducedCodeLength' => 2,
            'billets' => $result,
        ]);
    }

    public function apiGetExport(Request $request)
    {
        $guichet = Auth::user();

        if(!$guichet || $guichet->type != 'validation')
        {
            return response()->json(['Token not authorized.'], 403);
        }
        if($request->has('updated_at'))
            $billets = Billet::with(['options','price'])->where('updated_at', '>=', $request->get('updated_at'))->get();
        else
            $billets = Billet::with(['options','price'])->get();

        $return = $billets->map(function(Billet $billet){
            $b = $billet->toArray();
            $b['qrcode'] = $billet->getQrCodeSecurity();
            return $b;
        });

        return response()->json($return);

    }
    /**
     * Export a CSV of billets to validate
     */
    public function getExport(Request $request)
    {
        Auth::user()->requireLevel(2);
        $orders = Order::orderBy('name', 'asc')->get();
        $billets = Billet::with('options')->with('price')->orderBy('name', 'asc')->get();

        $rorders = [];
        foreach($orders as $order)
        {
            $rorders[] = [
                'Numero de commande' => $order->id,
                'Prénom' => $order->surname,
                'Nom' => $order->name,
                'Email' => $order->mail,
                'Moyen de paiement' => $order->mean_of_paiment,
                'Montant' => round($order->amount/2, 2),
                'Guichet'=> ($order->guichet?$order->guichet->name:''),
                'Etat' =>   $order->state
            ];
        }
        $result = [];
        $roptions = [];
        foreach($billets as $billet) {

            $options = '';
            foreach ($billet->options as $option)
            {
                if($options)
                    $options .= ', ';

                $options .= $option->pivot->qty .' '.$option->name;
                $roptions[] = [
                    'Numero du billet' => $billet->id,
                    'Numero de commande' => $billet->order_id,
                    'Type' => $billet->price->name,
                    'Prénom' => $billet->surname,
                    'Nom' => $billet->name,
                    'Email' => $billet->mail,
                    'Option' => $option->name,
                    'Quantité' => $option->pivot->qty
                ];
            }

            $result[] = [
                'Numero du billet' => $billet->id,
                'Numero de commande' => $billet->order_id,
                'Type' => $billet->price->name,
                'Prénom' => $billet->surname,
                'Nom' => $billet->name,
                'Email' => $billet->mail,
                'Options' => $options,
                'Validé' => $billet->validated_at ? 'X' : '',
            ];
        }

        return Excel::create('bilan', function ($file) use ($result, $rorders, $roptions) {
            $file->sheet('Commande', function ($sheet) use ($rorders) {
                $sheet->fromArray($rorders);
            });
            $file->sheet('Détail billet', function ($sheet) use ($result) {
                $sheet->fromArray($result);
            });
            $file->sheet('Détail billet et options', function ($sheet) use ($roptions) {
                $sheet->fromArray($roptions);
            });
        })->export('xls');
    }

    public function validateTicket(Request $request)
    {
        Auth::user()->requireLevel(2);
        $billet= null;

        if($request->has('id'))
            $billet = Billet::where('id',$request->input('id'))->first();

        if($request->has('code'))
        {
            $code = urldecode($request->input('code'));
            /**
            $code = explode('|', $code);
            if(count($code) == 2)
                $billet = Billet::where('uuid', trim($code[0]))->first();
             **/
            $code = Hashids::decode($code);

            if(count($code) < 2)
            {
                return response()->json([
                    'result' => [
                        'code' => 'error',
                        'message' => 'Billet inconnu',
                    ]
                ]);
            }

            $billet = Billet::with(['price', 'options'])->find($code[0]);
        }

        if(!$billet) {
            return response()->json([
                'validated' => false,
                'text' => "Billet inconnu"
            ]);
        }
        elseif ($billet->validated_at) {
            $return = $billet->toArray();
            $return['validated'] = 'already';
            return response()->json($return);
        } elseif (isset($code) && $billet->getBilletHash() != $code[1])
        {
            return response()->json([
                'validated' => false,
                'text' => "Billet invalide, discordance d'informations."
            ]);
        }
        else
        {
            $billet->validated_at = Carbon::now();
            $billet->save();
            $return = $billet->toArray();

            $return['options'] = '';
            foreach ($billet->options as $option)
            {
                if($return['options'])
                    $return['options'] .= ', ';

                $return['options'] .= $option->pivot->qty .' '.$option->name;
            }

            $return['validated'] = true;
            return response()->json($return);
        }
    }

    public function getSellGuichet(string $uuid)
    {
        $guichet = Guichet::where('uuid', $uuid)
            ->where('type', 'sell')
            ->where('start_at','<=',Carbon::now('Europe/Paris'))
            ->where('end_at','>=',Carbon::now('Europe/Paris'))->first();

        if(!$guichet)
        {
            Session::flash('error', "Le guichet n'existe pas ou n'est pas actif");
            return redirect()->route('home');
        }

        return view('admin.order_create', ['prices'=>$guichet->getPrices(), 'means'=>Order::$means, 'guichet'=>$guichet]);
    }

    public function postSellGuichet(Request $request, string $uuid)
    {
        $guichet = Guichet::where('uuid', $uuid)
            ->where('start_at','<=',Carbon::now('Europe/Paris'))
            ->where('end_at','>=',Carbon::now('Europe/Paris'))->first();

        if(!$guichet)
        {
            Session::flash('error', "Le guichet n'existe pas ou n'est pas actif");
            return redirect()->route('home');
        }


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
            if(in_array($value,$guichet->acl)) {

                $price = Price::find($value);

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
        }


        $order->price = $amount;
        $order->mean_of_paiment = $request->input('paiment');
        $order->state = 'paid';
        $order->guichet_id = $guichet->id;
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
