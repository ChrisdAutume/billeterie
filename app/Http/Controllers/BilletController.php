<?php

namespace App\Http\Controllers;

use App\Mail\BilletUpdated;
use App\Models\Billet;
use App\Models\Don;
use App\Models\Price;
use Carbon\Carbon;
use DebugBar\DebugBar;
use Illuminate\Http\Request;

use App\Http\Requests;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Auth;


class BilletController extends Controller
{
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
        return view('admin.ventes', ['prices' => Price::all(), 'dons'=> Don::all()->sum('amount')]);
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
        $request->session()->flash('success', "Place validé");
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
