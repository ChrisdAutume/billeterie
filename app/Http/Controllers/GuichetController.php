<?php

namespace App\Http\Controllers;

use App\Mail\GuichetCreated;
use App\Models\Billet;
use App\Models\Guichet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Price;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

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

        if(empty($request->input('name')) || empty($request->input('mail')) || empty($request->input('start_at')) || empty($request->input('end_at')) || empty($request->input('acl')))
        {
            $request->session()->flash('error', "L'ensemble des champs n'est pas remplit.");
            return redirect()->back();
        }

        $guichet = new Guichet();
        $guichet->name = $request->input('name');
        $guichet->mail = $request->input('mail');
        $guichet->start_at = Carbon::createFromFormat('d/m/Y H:i', $request->input('start_at'));
        $guichet->end_at = Carbon::createFromFormat('d/m/Y H:i', $request->input('end_at'));
        $guichet->acl = $request->input('acl');
        $guichet->save();

        if(!is_null($guichet->mail))
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

    public function getAutocomplete(Request $request)
    {
        Auth::user()->requireLevel(2);
        $result = Billet::select('uuid', 'name', 'surname', 'validated_at', 'id')->where("name","LIKE","%{$request->input('q')}%")->orWhere("surname","LIKE","%{$request->input('q')}%")->get();
        return response()->json($result);
    }

    public function validateTicket(Request $request)
    {
        Auth::user()->requireLevel(2);
        $billet= null;

        if($request->has('id'))
            $billet = Billet::where('id',$request->input('id'))->first();

        if($request->has('code'))
        {
            $code = $request->input('code');
            $code = explode('-', $code);
            if(count($code) == 2)
                $billet = Billet::where('id', $code[0])->where('uuid', 'LIKE', '%'.trim($code[1]).'%')->first();
        }

        if(!$billet)
            return response()->json(['validated' => false]);
        elseif ($billet->validated_at) {
            $return = $billet->toArray();
            $return['validated'] = 'already';
            return response()->json($return);
        }
        else
        {
            $billet->validated_at = Carbon::now();
            $billet->save();
            $return = $billet->toArray();

            $return['validated'] = true;
            return response()->json($return);
        }
    }

    public function getSellGuichet(string $uuid)
    {
        $guichet = Guichet::where('uuid', $uuid)
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
        foreach ($inputs['price'] as $id=>$value)
        {
            if(in_array($value,$guichet->acl)) {
                $billet = new Billet();
                $billet->order_id = $order->id;
                $billet->name = $inputs['name'][$id];
                $billet->surname = $inputs['surname'][$id];
                $billet->mail = $inputs['mail'][$id];
                $billet->price_id = $value;
                $billet->save();
                $amount += Price::find($value)->price;
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
