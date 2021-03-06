<?php

namespace App\Http\Controllers;

use App\Models\Billet;
use App\Models\Liste;
use App\Models\Option;
use App\Models\Price;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('prices.admin_index', [
            'prices' => Price::all(),
            'options' => Option::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->update($request, new Price());
        $request->session()->flash('success', "Tarif ajouté !");
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Price  $price
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Price $price)
    {
        if($request->isMethod('POST'))
        {
            $this->update($request, $price);
            $request->session()->flash('success', "Tarif mis à jour !");
            return redirect()->back();
        }
        return view('prices.admin_edit', [
            'prc' => $price,
            'prices' => Price::all(),
            'options' => Option::all()
        ]);
    }

    public function linkListe(Request $request, Price $price)
    {
        $price->lists()->attach($request->input('liste'), ['max_order'=> $request->input('max_order')]);
        return redirect()->back();
    }

    public function removeListe(Price $price, Liste $liste)
    {
        $price->lists()->detach($liste->id);
        return redirect()->back();
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Price  $price
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Price $price)
    {
        $price->fill($request->input());
        $price->sendBillet = $request->input('sendBillet', false);
        $aggregation_price = null;
        foreach ($request->input('agregat_price', []) as $price_id)
        {
            $aggregation_price .= intval($price_id).',';
        }
        $price->price_aggregation = $aggregation_price;
        $price->save();
        $price->options()->sync($request->input('options', []));
    }

}
