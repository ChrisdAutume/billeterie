<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Http\Controllers\Controller;
use Request;
use Response;
use Validator;
use Redirect;
use Log;


class PartnerController extends Controller
{
    public function show()
    {
      return Response::json(Partner::all(), 200);
    }

    public function index()
    {
        $partners = [];
        $partners = Partner::all();
        return view('partners.index', compact('partners'));
    }

/**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('partners.create');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        // Request::flash();
        // validate the request inputs
        $validator = Validator::make(Request::all(), Partner::storeRules());
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }

        $partner = new Partner();
        $partner->name = Request::get('name');
        $partner->link = Request::get('link');
        $partner->image = Request::get('image');
        $partner->save();

        return redirect('admin/partner');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $partner = Partner::find($id);
        return view('partners.edit', compact('partner'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        // validate the request inputs
        $validator = Validator::make(Request::all(), Partner::storeRules());
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }

        $partner = Partner::find($id);
        $partner->fill(Request::all());
        $partner->save();

        return redirect('admin/partner');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Partner::destroy($id);
        return redirect('admin/partner');
    }

  }