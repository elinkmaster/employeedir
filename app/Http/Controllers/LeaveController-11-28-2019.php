<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LeaveRequest;
use App\LeaveType;
use App\PayType;
use App\User;
use DateTime;
use App\Mail\LeaveNotification;
use App\Mail\LeaveApproved;
use App\Mail\LeaveDeclined;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Spatie\Valuestore\Valuestore;

class LeaveController extends Controller
{
    public $settings;

    public function __construct()
    {
        $this->settings = Valuestore::make(storage_path('app/settings.json'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


        $leave_requests = LeaveRequest::unapproved()->managedBy(Auth::user())->get();

        if(Auth::user()->isAdmin()){
            return view('leave.index')->with('leave_requests', LeaveRequest::all());
        } else {
            return view('leave.index')
            ->with('leave_requests', $leave_requests);
        }
        return redirect('leave/create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('leave.create')->with('employees', User::AllExceptSuperAdmin()->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $leave = new LeaveRequest();

        $datetime = new DateTime();
        $leave_date_from = $datetime->createFromFormat('m/d/Y', $request->leave_date_from)->format("Y-m-d H:i:s");
        $leave_date_to = $datetime->createFromFormat('m/d/Y', $request->leave_date_to)->format("Y-m-d H:i:s");
        $report_date = $datetime->createFromFormat('m/d/Y', $request->report_date)->format("Y-m-d H:i:s");
        $date_filed = $datetime->createFromFormat('m/d/Y', $request->date_filed)->format("Y-m-d H:i:s");

        $leave->employee_id = $request->employee_id;
        $leave->filed_by_id = Auth::user()->id;
        $leave->recommending_approval_by_id = Auth::user()->supervisor_id;
        $leave->approved_by_id = Auth::user()->manager_id;

        $leave->leave_date_from = $leave_date_from;
        $leave->leave_date_to =$leave_date_to;
        $leave->number_of_days = $request->number_of_days;
        $leave->report_date = $report_date;
        $leave->reason = $request->reason;
        $leave->contact_number = $request->contact_number;
        $leave->leave_type_id = $request->leave_type_id;
        $leave->pay_type_id = $request->pay_type_id;
        $leave->date_filed = $date_filed;
        $leave->save();

        // SEND EMAIL NOTIFICATION
        if($this->settings->get('email_notification')){
            Mail::to($leave->recipients())->queue(new LeaveNotification($leave));
        }

        return redirect("leave" . '/' . $leave->id)->with('success', 'Leave Request Successfully Submitted!!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $leave_request = LeaveRequest::with('employee')->find($id);

        return view('leave.show')->with('leave_request', $leave_request)->with('leave_types', LeaveType::all())->with('pay_types', PayType::all());
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

    public function recommend(Request $request){
        $leave_request = LeaveRequest::find($request->leave_id);
        $leave_request->recommending_approval_by_id = Auth::user()->id;
        $leave_request->recommending_approval_by_signed_date = date('Y-m-d H:i:s');
        $leave_request->approve_status_id = 1;

        if($leave_request->save()){
            // SEND EMAIL NOTIFICATION
            if($this->settings->get('email_notification')){
                Mail::to($leave_request->employee->email)->queue(new LeaveApproved($leave_request));
            }
            return back()->with('success', 'Leave request successfully approved.');
        } else {
            return back()->with('error', 'Something went wrong.');
        }
    }

    public function approve(Request $request){
        $leave_request = LeaveRequest::find($request->leave_id);
        $leave_request->approved_by_id = Auth::user()->id;
        $leave_request->approved_by_signed_date = date('Y-m-d H:i:s');
        $leave_request->approve_status_id = 1;

        // SEND EMAIL NOTIFICATION

        if($leave_request->save()){
            if($this->settings->get('email_notification')){
                Mail::to($leave_request->employee->email)->queue(new LeaveApproved($leave_request));
            }
            return back()->with('success', 'Leave request successfully approved.');
        } else {
            return back()->with('error', 'Something went wrong.');
        }
    }

    public function noted(Request $request){
        $leave_request = LeaveRequest::find($request->leave_id);
        $leave_request->noted_by_id = Auth::user()->id;
        $leave_request->noted_by_signed_date = date('Y-m-d H:i:s');
        $leave_request->approve_status_id = 1;

        if($leave_request->save()){
            return back()->with('success', 'Leave request successfully approved.');
        } else {
            return back()->with('error', 'Something went wrong.');
        }
    }
    public function decline(Request $request){
        $leave_request = LeaveRequest::find($request->leave_id);
        $leave_request->reason_for_disapproval = $request->reason_for_disapproval;
        $leave_request->approve_status_id = 2;

        if($leave_request->save()){
            if($this->settings->get('email_notification')){
                Mail::to($leave_request->employee->email)->queue(new LeaveDeclined($leave_request));
            }
            return back()->with('success', 'Leave request successfully declined.');
        } else {
            return back()->with('error', 'Something went wrong.');
        }
    }

    public function credits(Request $request){

        return view('leave.credits')->with('employees', User::allExceptSuperAdmin()->get());
    }

    public function editcredits(Request $request, $employee_id){
        return view('leave.editcredits')->with('employee', User::find($employee_id));
    }

    public function updatecredits(Request $request){
        $employee = User::find($request->employee_id);
        $employee->leave_credit = $request->leave_credits;
        if($employee->save()){
            return back()->with('success', 'Successfully updated leave credits!');
        }else {
            return back()->with('error', 'Something went wrong!');
        }
    }
}
