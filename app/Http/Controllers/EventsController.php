<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('events.index')->with('events', Events::all());
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $events = new Events();
        $events->event_name = $request->event_name;
        $events->event_description = $request->event_description;
        $events->event_color = $request->event_color;
        if($request->has('start_date')){
            $events->start_date = date("Y-m-d H:i:s", strtotime($request->start_date));
        }
        if($request->has('end_date')){
            $events->end_date = date("Y-m-d H:i:s", strtotime($request->end_date));
        }
        if($events->save()){
            return redirect('events/' . $events->id)->with('success', 'Event successfully added!');
        } else {
            return back()->with('error', 'Something went wrong.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('events.view')->with('event', Events::find($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('events.edit')->with('event', Events::find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $event = Events::find($id);
        $event->event_name = $request->event_name;
        $event->event_description = $request->event_description;
        $event->event_color = $request->event_color;
        if($request->has('start_date')){
            $event->start_date = date("Y-m-d H:i:s", strtotime($request->start_date));
        }
        if($request->has('end_date')){
            $event->end_date = date("Y-m-d H:i:s", strtotime($request->end_date));
        }

        if($event->save()){
            return redirect('events/' . $event->id)->with('success', 'Succesfully updated the event details');
        } else {
            return back()->with('error', 'Something went wrong.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event = Events::find($id);
        if($event->delete()){
            return redirect('events')->with('success', 'Succesfully deleted the event');
        } else {
            return back()->with('error', 'Something went wrong.');
        }
    }

    public function calendar(){
        return view('events.calendar');
    }
    public function lists(){
        return Events::all();
    }
}
