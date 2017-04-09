<?php

namespace App\Http\Controllers;

use App\Models\Don;
use Illuminate\Http\Request;

class DonController extends Controller
{
    public function apiGetAmount(Request $request)
    {
        return response()->json([
            'amount' => Don::sum('amount')/100,
            'number' => Don::count(),
        ]);
    }
}
