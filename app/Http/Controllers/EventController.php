<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Controllers\Controller;
use Request;
use Response;
use Validator;
use Redirect;
use Log;


class EventController extends Controller
{
    public function show()
    {
      return Response::json(Event::all(), 200);
    }

    public function index()
    {
        $events = [];
        $events = Event::orderBy('start_at')->get();
        return view('events.index', compact('events'));
    }

/**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('events.create');
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
        $validator = Validator::make(Request::all(), Event::storeRules());
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }

        $event = new Event();
        $event->name = Request::get('name');
        $event->description = Request::get('description');
        $event->place = Request::get('place');
        $event->image = Request::get('image');
        $event->categories = 'all';
        $event->start_at = $this->formatEventDate(Request::get('start_at_date'), Request::get('start_at_hour'));
        $event->end_at = $this->formatEventDate(Request::get('end_at_date'), Request::get('end_at_hour'));
        $event->save();

        return redirect('admin/event');
    }


    private function formatEventDate($date, $hour)
    {
        $date = implode('-', array_reverse(explode('-', $date)));
        return strtotime($date.' '.$hour);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $event = Event::find($id);
        return view('events.edit', compact('event'));
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
        $validator = Validator::make(Request::all(), Event::storeRules());
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }

        $event = Event::find($id);
        $event->fill(Request::all());
        $event->categories = 'all';
        $event->start_at = $this->formatEventDate(Request::get('start_at_date'), Request::get('start_at_hour'));
        $event->end_at = $this->formatEventDate(Request::get('end_at_date'), Request::get('end_at_hour'));
        $event->save();

        return redirect('admin/event');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Event::destroy($id);
        return redirect('admin/event');
    }

  }