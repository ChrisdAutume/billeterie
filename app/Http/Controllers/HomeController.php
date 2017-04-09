<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Price;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function homeAction(Request $request)
    {
        $time = null;
        $nextPrice = Price::NextToBeVisible()->first();
        if($nextPrice){
            $time = $nextPrice->start_at->diff(Carbon::now('Europe/Paris'))->format('%D jour(s) %H heure(s) %i minute(s)');
        }

        $content = Page::where('home', true)->first();

        return view('dashboard.home', [
            'priceAvailable'=> (Price::VisibleNow()->count() > 0),
            'time' => $time,
            'content' => $content
        ]);
    }
}
