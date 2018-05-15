<?php

namespace App\Http\Controllers;

use App\Mail\BilletUpdated;
use App\Models\Billet;
use App\Models\Don;
use App\Models\Option;
use App\Models\Price;
use Carbon\Carbon;
use DebugBar\DebugBar;
use Illuminate\Http\Request;

use App\Http\Requests;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Auth;


class BilletController extends Controller
{
    public function adminPostBilletEdit(Billet $billet, Request $request)
    {
        $billet->fill($request->input());
        $billet->save();

        $billet->sendToMail(true);
        $request->session()->flash('success', "Billet mis à jour et envoyé par mail !");

        return redirect()->back();
    }

    public function adminBilletDelete(Billet $billet, Request $request)
    {
        $billet->delete();
        $request->session()->flash('success', "Billet supprimé !");

        return redirect()->back();
    }

    public function getBarcode(Request $request)
    {
        if ($request->input('id'))
        {
            $billet = view('billets.billet', ['billet'=>Billet::find($request->input('id'))])->render();
            return PDF::loadHTML($billet)->setPaper([0,0,375,960], 'landscape')->setWarnings(true)->stream('billet.pdf');
        }
    }


    public function postSendAgain(Request $request)
    {
        if($request->has('mail'))
        {
            $billets = Billet::where('mail', $request->input('mail'))->get();
            if($billets->count() > 0)
            {
                foreach ($billets as $billet) {
                    $billet->sendToMail();
                }

                $request->session()->flash('success', "La billet vient d'être renvoyé !");
                return redirect()->route('home');
            } else {
                $request->session()->flash('error', "Aucun billet trouvé à cette adresse mail. Si erreur, contactez ".config('billeterie.contact'));
                return redirect()->route('home');
            }
        } else {
            $request->session()->flash('error', "La requête est incorrect !");
            return redirect()->route('home');
        }
    }

    public function adminSell(Request $request)
    {
        Auth::user()->requireAdmin();
        //Dons

        //Calculs des stats
        $stats = Price::query()
        ->join('billets', 'prices.id', '=', 'billets.price_id')
        ->select([
                \DB::RAW('DATE(billets.created_at) as `day`'),
                \DB::raw('COUNT(billets.id) as `count`'),
                'prices.name'
            ])
            ->groupBy('prices.name')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $dates_interval = new \DatePeriod(
            (new \DateTime($stats->first()->day))->modify('-1 day'),
            new \DateInterval('P1D'),
            (new \DateTime($stats->last()->day))->modify('+1 day')
        );
        //dd($stats);
        return view('admin.ventes', [
            'prices' => Price::all(),
            'dons'=> Don::all()->sum('amount'),
            'options' => Option::all(),
            'stats' => $stats,
            'dates_interval' => $dates_interval
            ]);
    }

    public function adminSendMail(Request $request, $id)
    {
        Auth::user()->requireAdmin();
        $billet = Billet::findOrFail($id);
        $billet->sendToMail();

        $request->session()->flash('success', "Le mail vient d'être envoyé a ".$billet->mail);
        return redirect()->back();
    }

    public function adminValid(Request $request, $id)
    {
        Auth::user()->requireAdmin();
        $billet = Billet::findOrFail($id);
        $billet->validated_at = Carbon::now();
        $billet->save();
        $option_text = "";
        foreach ($billet->options as $option)
        {
            $option_text .= ' '.$option->pivot->qty .' '.$option->name;
        }
        $request->session()->flash('success', "Place validé, $option_text");
        return redirect()->back();
    }

    public function adminView(Billet $billet)
    {
        return $billet->outputBillet()->stream(str_slug($billet->name.' '.$billet->surname).'.pdf');
    }

    public function download(string $securite, Billet $billet)
    {
        if($securite != $billet->getDownloadSecurity())
        {
            session()->flash('error', "Clé de sécurité invalide.");
            return redirect()->route('home');
        }

        return $billet->outputBillet()->stream(str_slug($billet->name.' '.$billet->surname).'.pdf');
    }

}
