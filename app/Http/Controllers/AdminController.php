<?php

namespace App\Http\Controllers;

use App\Models\Billet;
use App\Models\Don;
use App\Models\FieldPrice;
use App\Models\Guichet;
use App\Models\Item_list;
use App\Models\Liste;
use App\Models\Option;
use App\Models\Order;
use App\Models\Price;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{

    public function resetBilletterie(Request $request)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Billet::truncate();
        Don::truncate();
        FieldPrice::truncate();
        Order::truncate();
        Item_list::truncate();
        Liste::truncate();
        Guichet::truncate();
        Option::truncate();
        Price::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $request->session()->flash('success', "Les données ont été reset !");

        return redirect()->back();
    }

}
