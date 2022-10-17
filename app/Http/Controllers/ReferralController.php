<?php

namespace App\Http\Controllers;

use App\Mail\ReferralSubmitted;
use App\User;
use App\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Spatie\Valuestore\Valuestore;

class ReferralController extends Controller
{
    public $settings;

    public function __construct()
    {
//        $this->middleware('auth', ['only' => ['edit']]);
        $this->settings = Valuestore::make(storage_path('app/settings.json'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // list of referral
        // Admin only can see this

         return view('referral.index')->with('referrals', Referral::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $allEmployees = User::allExceptSuperAdmin()->get();
        return view('referral.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $referral = new Referral();
        $referral->referrer_first_name = $request->referrer_first_name;
        $referral->referrer_middle_name = $request->referrer_middle_name;
        $referral->referrer_last_name = $request->referrer_last_name;
        $referral->referrer_department = $request->referrer_department;
        $referral->referral_first_name = $request->referral_first_name;
        $referral->referral_middle_name = $request->referral_middle_name;
        $referral->referral_last_name = $request->referral_last_name;
        $referral->referral_contact_number = $request->referral_contact_number;
        $referral->referral_email = $request->referral_email;
        $referral->position_applied = $request->position_applied;

        if($referral->save()){
            if($this->settings->get('email_notification')){
                $erp = User::where('is_erp', '=', 1)->orWhere('is_admin', '=', 1)->select('email')->get()->toArray();
                if(count($erp) > 0){
                    Mail::to($erp)->queue(new ReferralSubmitted($referral));
                }
            }

            return back()->with('success', 'Referral successfully sent to the ERP Team. Thank you.');
        }
        return back()->with('error', 'Something went wrong');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $referral = Referral::find($id);

        return view('referral.show')->with('referral', $referral);
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
