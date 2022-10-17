<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Valuestore\Valuestore;
use App\User;

class SettingsController extends Controller
{
    public $settings;

    public function __construct()
    {
        $this->middleware('admin');
        $this->settings = Valuestore::make(storage_path('app/settings.json'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $email_notification = $this->settings->get('email_notification');
        $employee_emails = User::select('email')->get();

        $current_email_recipients = $this->settings->get('leave_email_main_recipients');
        return view('admin.settings.index')->with('email_notification', $email_notification)->with('employee_emails', $employee_emails)->with('current_email_recipients', $current_email_recipients);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        if($request->has('email_notification')){
            $this->settings->put('email_notification', true);
        }else {
            $this->settings->put('email_notification', false);
        }
        $this->settings->put('leave_email_main_recipients', $request->email_recipients);

        return back()->with('success', 'Settings successfully saved.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
