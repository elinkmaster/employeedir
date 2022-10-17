<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ElinkActivities;
use DateTime;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('activity.list')->with('activities', ElinkActivities::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('activity.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $activity = new ElinkActivities;
        $activity->title = $request->title;
        $activity->subtitle = $request->subtitle;
        $activity->message = $request->message;

        $datetime = new DateTime();
        if ($request->has('activity_date') && $request->activity_date) {
            $activity_date = $datetime->createFromFormat('m/d/Y', $request->activity_date)->format("Y-m-d H:i:s");
            $activity->activity_date = $activity_date;
        }
        
        $activity->save();

        /* saving photo : TODO : optimize saving of image to save space */
        if ($request->hasFile("image_url")) {
            $path = $request->image_url->store('images/'.$activity->id);
            $activity->image_url =  asset('storage/app/'.$path);
            $activity->save();
        }

        return redirect()->back()->with('success', "Successfully added an activity");;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ElinkActivities::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('activity.edit')->with('activity', ElinkActivities::find($id));
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
        $activity = ElinkActivities::find($id);
        $activity->title = $request->title;
        $activity->subtitle = $request->subtitle;
        $activity->message = $request->message;

        $datetime = new DateTime();
        if ($request->has('activity_date') && $request->activity_date) {
            $activity_date = $datetime->createFromFormat('m/d/Y', $request->activity_date)->format("Y-m-d H:i:s");
            $activity->activity_date = $activity_date;
        }

        $activity->save();

        /* saving photo : TODO : optimize saving of image to save space */
        if ($request->hasFile("image_url")) {
            $path = $request->image_url->store('images/'.$activity->id);
            $activity->image_url =  asset('storage/app/'.$path);
            $activity->save();
        }

        return redirect()->back()->with('success', "Successfully edited an activity");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $activity = ElinkActivities::find($id);
        $activity->delete();

        return redirect()->back()->with('success', "Successfully deleted activity record");
    }

}
