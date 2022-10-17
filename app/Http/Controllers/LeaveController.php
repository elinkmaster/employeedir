<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events;
use App\LeaveRequest;
use App\LeaveRequestDetails;
use App\EmployeeInfoDetails;
use App\Helpers\DateHelper;
use App\LeaveCredits;
use App\LeaveType;
use App\PayType;
use App\User;
use DateTime;
use App\Mail\LeaveNotification;
use App\Mail\LeaveApproved;
use App\Mail\LeaveDeclined;
use App\Mail\LeaveSelfNotification;
use App\Mail\LeaveRecommended;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Spatie\Valuestore\Valuestore;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
        //$leave_requests = LeaveRequest::unapproved()->MyLeaves(Auth::user())->get();
        $req_obj = new LeaveRequest();
        $todayDate = now();

        if(Auth::user()->isAdmin() || Auth::user()->usertype == 3){
            return view('leave.index')->with('leave_requests', LeaveRequest::where('status',1)->whereYear('created_at', '=', $todayDate->year)->where('approve_status_id',NULL)->orWhere('approve_status_id',3)->orWhere('approve_status_id',0)->get())->with(['req_obj' => $req_obj]);
        } else {
            if(Auth::user()){
                //$leave_requests = LeaveRequest::where('employee_id',Auth::user()->id)->where('approve_status_id',NULL)->get();
                $id = Auth::user()->id;
                $leave_requests = LeaveRequest::whereRaw("employee_id = $id and (approve_status_id is NULL or approve_status_id = 0 or approve_status_id = 3)")->get();
            }
            else
                $leave_requests = [];
            return view('leave.index')
            ->with('leave_requests', $leave_requests)
            ->with(['req_obj' => $req_obj]);
        }
        return redirect('leave/create');
    }
    
    public function approveLeaves(){
        $req_obj = new LeaveRequest();
	$todayDate = now();
        if(Auth::user()->isAdmin() || Auth::user()->usertype == 3){
            return view('leave.index')->with('leave_requests', LeaveRequest::where('approve_status_id',1)->whereYear('created_at', '=', $todayDate->year)->get())->with(['req_obj' => $req_obj]);
        } else {
            if(Auth::user())
                $leave_requests = LeaveRequest::where('employee_id',Auth::user()->id)->where('approve_status_id',1)->whereYear('created_at', '=', $todayDate->year)->get();
            else
                $leave_requests = [];
            return view('leave.index')
            ->with('leave_requests', $leave_requests)
            ->with(['req_obj' => $req_obj]);
        }
        return redirect('leave/create');
    }
    /*
    public function forApproval(){
        $employee_category = Auth::user()->employee_category;
        $employee_id = Auth::user()->id;

        $leave_requests = LeaveRequest::all();
        return view('leave.approve')
            ->with('leave_requests', $leave_requests)
            ->with('category',$employee_category)
            ->with('employee_id',$employee_id);
    }
     * 
     */
    
    public function forApproval(){
        $req_obj = new LeaveRequest();
	$todayDate = now();
        if(Auth::user()){
            $leave_requests = LeaveRequest::where('approve_status_id',NULL)->orWhere('approve_status_id',3)->orWhere('approve_status_id',0)->whereYear('created_at', '=', $todayDate->year)->get();
            $employee_id = Auth::user()->id;
        }
        else
            $leave_requests = [];
        return view('leave.approve')
        ->with('leave_requests', $leave_requests)
        ->with('employee_id',$employee_id)
        ->with(['req_obj' => $req_obj]);
    }
    
    public function teamApproves(){
        $req_obj = new LeaveRequest();

        if(Auth::user()){
            $leave_requests = LeaveRequest::where('approve_status_id',1)->get();
            $employee_id = Auth::user()->id;
        }
        else
            $leave_requests = [];
        return view('leave.approve')
        ->with('leave_requests', $leave_requests)
        ->with('employee_id',$employee_id)
        ->with(['req_obj' => $req_obj]);
        return redirect('leave/create');
    }
    
    public function cancelledLeaves(){
        $req_obj = new LeaveRequest();
	$todayDate = now();
        if(Auth::user()->isAdmin() || Auth::user()->usertype == 3){
            return view('leave.index')->with('leave_requests', LeaveRequest::where('approve_status_id',2)
	            ->whereYear('created_at', '=', $todayDate->year)->get())->with(['req_obj' => $req_obj]);
        } else {
            if(Auth::user())
                $leave_requests = LeaveRequest::where('employee_id',Auth::user()->id)->where('approve_status_id',2)
	               ->whereYear('created_at', '=', $todayDate->year)->get();
            else
                $leave_requests = [];
            return view('leave.index')
            ->with('leave_requests', $leave_requests)
            ->with(['req_obj' => $req_obj]);
        }
        return redirect('leave/create');
    }
    
    public function teamCancelled(){
        $req_obj = new LeaveRequest();

        if(Auth::user()){
            $leave_requests = LeaveRequest::where('approve_status_id',2)->get();
            $employee_id = Auth::user()->id;
        }
        else
            $leave_requests = [];
        return view('leave.approve')
        ->with('leave_requests', $leave_requests)
        ->with('employee_id',$employee_id)
        ->with(['req_obj' => $req_obj]);
        return redirect('leave/create');
    }
    
    /*
     * 
     */
    
    public function apiLeaveList(){
        return json_encode(['obj' => DB::table('leave_request')->leftJoin('employee_info','leave_request.employee_id','=','employee_info.id')->get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
	/*if(Auth::user()->is_regular == 0){
            abort(403);
        }*/
        //return view('leave.create')->with('employees', User::AllExceptSuperAdmin()->get());
        $id_obj = Auth::user()->id;
        $obj = DB::select($this->newQuery($id_obj));
        $credits = (object) [
            'past_credit'       => 0,
            'current_credit'    => 0,
            'used_credit'       => 0,
            'total_credits'     => 0
        ];
        if(count($obj) > 0)
            $credits = $obj[0];
        return view('leave.create2',[
                'employees'     => User::AllExceptSuperAdmin()->get(),
                'leave_types'   => LeaveType::all(),
                'blocked_dates' => $this->getBlockedDates(),
                'credits'       => $credits
            ]
        );
    }
    
    /*
    Get the Leave Dates from leave_request_details.
    The param $id is a primary key from leave_request table. There can be
    more than 1 day in one leave request.
    */
    public function getLeaveDates($id){
        //return LeaveRequestDetails::where('leave_id',$id)->get();
        return DB::table('leave_request_details')
            ->select(DB::raw('id, leave_id, date_format(date,"%b %e, %Y") as date, length, pay_type'))
            ->where("leave_id",$id)
            ->where("status",1)
            ->get();
    }
    
    /*
     * 
     */
    public function reports(){
        return view('leave.reports');
    }
        
    public function filterTest(){
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="myfile.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writesheet = new Spreadsheet();
        $writer = IOFactory::createWriter($writesheet, "Xlsx");
        $sheet = $writesheet->getActiveSheet();
        $header = array("Customer Number", "Customer Name", "Address", "City", "State", "Zip");
        $sheet->fromArray([$header], NULL, 'A1');  
        $writer->save('php://output');
    }
    
    public function processSave(Request $req){
        $file = [];
        $file['Original Filename'] = $req->file('prev_leave_credits')->getClientOriginalName();
        $path = str_replace("public","",$_SERVER['DOCUMENT_ROOT']);
        $file['path'] = $req->file('prev_leave_credits')->storeAs('/media/uploads/xls', $file['Original Filename']);

        $spreadsheet =IOFactory::load($path.'/storage/app/'.$file['path']);
        $worksheet = $spreadsheet->getSheet(0);
        
        $list = [];
        foreach($worksheet->getRowIterator() as $obj_row):
            $cellIterator = $obj_row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE);
            $ctr = 1;
            $row = ['num' => 0, 'id' => 0, 'name' => '', 'past_credits' => 0, 'current_credits' => 0, 'used_credits' => 0, 'obj' => [], 'status' => 0];
            foreach($cellIterator as $col):
                if($ctr == 1):
                    $row['num'] = $col->getValue();
                endif;
                
                if($ctr == 2):
                    $row['id'] = $col->getValue();
                    $obj = User::where('eid',$row['id'])->get();
                    if(count($obj) > 0){
                        $row['obj'] = $obj[0];
                    }
                endif;
                
                if($ctr == 5):
                    $row['name'] = $col->getValue();
                endif;
                
                if($ctr == 6):
                    $row['past_credits'] = $col->getValue();
                    if(isset($row['obj']->id)){
                        $year = date('Y') - 1;
                        $row['status'] = LeaveCredits::where('employee_id',"=",$row['obj']->id)->where('type',"=",2)->where('year',"=",$year)->update(['credit' => $row['past_credits']]);
                        if($row['status'] == 0){
                            $details = new LeaveCredits();
                            $details->employee_id = $row['obj']->id;
                            $details->credit = $row['past_credits'];
                            $details->year = $year;
                            $details->type = 2;
                            $details->save();
                        }
                    }
                    
                endif;
                
                if($ctr == 7):
                    $row['current_credits'] = $col->getCalculatedValue();
                    if(isset($row['obj']->id)){
                        $year = date('Y');
                        $details = new LeaveCredits();
                        $details->employee_id = $row['obj']->id;
                        $details->credit = $row['current_credits'];
                        $details->year = $year;
                        $details->month = 2;
                        $details->type = 1;
                        $details->save();
                    }
                endif;
                
                if($ctr == 8):
                    $row['used_credits'] = $col->getValue();
                    if(isset($row['obj']->id)){
                        $year = date('Y');
                        $details = new LeaveCredits();
                        $details->employee_id = $row['obj']->id;
                        $details->credit = $row['used_credits'];
                        $details->year = $year;
                        $details->month = 2;
                        $details->type = 5;
                        $details->save();
                    }
                    array_push($list,$row);
                endif;
                
                $ctr++;
                
            endforeach;
        endforeach;
        
        return json_encode($list);
    }
    
    public function uploadCredits(){
        return view('leave/upload');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        ini_set('memory_limit', '-1');
        $obj = [
            'leave_date' => $request->leave_date,
            'length'     => $request->length,
            'pay_type'   => $request->pay_type
        ];
        $leave = new LeaveRequest();

        $datetime = new DateTime();
        //$leave_date_from = $datetime->createFromFormat('m/d/Y', $request->leave_date_from)->format("Y-m-d H:i:s");
        //$leave_date_to = $datetime->createFromFormat('m/d/Y', $request->leave_date_to)->format("Y-m-d H:i:s");
        $report_date = $datetime->createFromFormat('m/d/Y', $request->report_date)->format("Y-m-d H:i:s");
        $date_filed = $datetime->createFromFormat('m/d/Y', $request->date_filed)->format("Y-m-d H:i:s");

        $leave->employee_id = $request->employee_id;
        $leave->filed_by_id = Auth::user()->id;
        $leave->recommending_approval_by_id = Auth::user()->supervisor_id;
        $leave->approved_by_id = Auth::user()->manager_id;

        // $leave->leave_date_from = $leave_date_from;
        // $leave->leave_date_to =$leave_date_to;
        $leave->number_of_days = $request->number_of_days;
        $leave->report_date = $report_date;
        $leave->reason = $request->reason;
        $leave->contact_number = $request->contact_number;
        $leave->leave_type_id = $request->leave_type_id ;//fixed (10/6/22)
        $leave->pay_type_id = $request->pay_type_id;
        $leave->date_filed = $date_filed;
        $leave->save();
        $leave_id = $leave->id;
        for($i = 0; $i < count($obj['leave_date']); $i++):
            $details = [
                'leave_id'      => $leave_id,
                'date'          => date("Y-m-d",strtotime($obj['leave_date'][$i])) == '1970-01-01'? now()->format('Y-m-d') : date("Y-m-d",strtotime($obj['leave_date'][$i])) ,
                'length'        => $obj['length'][$i],
                'pay_type'      => $obj['pay_type'][$i]
            ];
            LeaveRequestDetails::create($details);
        endfor;
        $leave_obj = ['leave' => $leave, 'details' => LeaveRequestDetails::where("leave_id",$leave_id)->get()];
        //SEND EMAIL NOTIFICATION
       // Mail::to(Auth::user()->email)->queue(new LeaveSelfNotification($leave));
        if($this->settings->get('email_notification')){
           // Mail::to($leave->recipients())->queue(new LeaveNotification($leave_obj));
        }

        return redirect("leave" . '/' . $leave_id)->with('success', 'Leave Request Successfully Submitted!!');
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
        $emp_id = $leave_request->employee_id;
        $obj = DB::select($this->newQuery($emp_id));
        $credits = (object) [
            'past_credit'       => 0,
            'current_credit'    => 0,
            'used_credit'       => 0,
            'total_credits'     => 0
        ];
        if(count($obj) > 0)
            $credits = $obj[0];
        $leaveDetails = LeaveRequestDetails::where("leave_id",$id)->where('status',1)->get();
        return view('leave.show')->with('leave_request', $leave_request)->with('details',$leaveDetails)->with('leave_types', LeaveType::all())->with('pay_types', PayType::all())->with('credits',$credits);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $row = [
            'leave_id'  => $id,
            'date'      => '',
            'length'    => 1,
            'pay_type'  => 0
        ];
        $leave_request = LeaveRequest::with('employee')->find($id);
        
        $emp_id = $leave_request->employee_id;
        $obj_credits = DB::select($this->newQuery($emp_id));
        $credits = (object) [
            'past_credit'       => 0,
            'current_credit'    => 0,
            'used_credit'       => 0,
            'total_credits'     => 0
        ];
        if(count($obj_credits) > 0)
            $credits = $obj_credits[0];
        
        $obj = LeaveRequestDetails::where('leave_id',$id)->where('status',1)->get();
        if(count($obj) > 0):
            $row = $obj[0];
        endif;
        return view('leave.edit')
            ->with('leave_request', $leave_request)
            ->with('leave_types', LeaveType::all())
            ->with('pay_types', PayType::all())
            ->with('filed_days', $obj)
            ->with('leave',$row)
            ->with('credits',$credits)
            ->with('blocked_dates', $this->getBlockedDates());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * raingrego@readersmagnet.com content endorsement & Rm show registration
     * 
     */
    public function update(Request $request, $id)
    {
        
    }
    
    public function displayReport(Request $r){
        $tar = [
            'from'      => date("Y-m-d", strtotime($r->get('from'))),
            'to'        => date("Y-m-d", strtotime($r->get('to'))),
            'type'      => $r->get('type')
        ];
        $from = $tar['from'];
        $to = $tar['to'];
        $type = $tar['type'] == 'weekly' ? " = 4" : "<> 4";
        
        $obj = DB::select("
            SELECT 
                lr.employee_id,
                lr.leave_type_id,
                lr.pay_type_id,
                lr.reason,
                lr.date_filed,
                lrd.id,
                lrd.leave_id,
                lrd.date,
                lrd.length,
                lrd.pay_type,
                ei.eid,
                concat(ei.first_name,' ',ei.last_name) as emp_name,
                ei.employee_category,
                lr.approve_status_id as status
            FROM
                elink_employee_directory.leave_request_details AS lrd
                    LEFT JOIN
                leave_request AS lr ON lr.id = lrd.leave_id
                    LEFT JOIN
                employee_info AS ei ON ei.id = lr.employee_id
            WHERE
                lrd.date >= '$from'
                    AND lrd.date <= '$to'
                    AND ei.employee_category $type
                    and lrd.status = 1
                    and lr.approve_status_id = 1;
            ");
        
        return view("leave.reports",["obj" => $obj, "target" => $tar]);
    }
    
    public function downloadReport(Request $r){
        $tar = [
            'from'      => date("Y-m-d", strtotime($r->get('from'))),
            'to'        => date("Y-m-d", strtotime($r->get('to'))),
            'type'      => $r->get('type')
        ];
        $from = $tar['from'];
        $to = $tar['to'];
        $type = $tar['type'] == 'weekly' ? " = 4" : "<> 4";
        
        $obj = DB::select("
            SELECT 
             lr.employee_id,
                lr.leave_type_id,
                lr.pay_type_id,
                lr.reason,
                lr.date_filed,
                lrd.id,
                lrd.leave_id,
                lrd.date,
                lrd.length,
                lrd.pay_type,
                ei.eid,
                concat(ei.first_name,' ',ei.last_name) as emp_name,
                ei.employee_category,
                lr.approve_status_id as status
            FROM
                elink_employee_directory.leave_request_details AS lrd
                    LEFT JOIN
                leave_request AS lr ON lr.id = lrd.leave_id
                    LEFT JOIN
                employee_info AS ei ON ei.id = lr.employee_id
            WHERE
                lrd.date >= '$from'
                    AND lrd.date <= '$to'
                    AND ei.employee_category $type
                    and lrd.status = 1
                    and lr.approve_status_id = 1;
            ");
        $writesheet = new Spreadsheet();
        $writer = IOFactory::createWriter($writesheet, "Xlsx");
        $sheet = $writesheet->getActiveSheet();
        $i = 1;
        $past = date('Y') - 1;
        $header = array("Leave ID", "EE Number", "EE Name", "Start", "End", "VL", "SL", "EL", "VLWOP", "SLWOP", "ELWOP");
        $sheet->fromArray([$header], NULL, 'A'.$i); 
        $i++;
        $leave_id = isset($obj[0]) ? $obj[0]->leave_id : 0;
        $ename = isset($obj[0]) ? $obj[0]->emp_name : '';
        $eid = isset($obj[0]) ? $obj[0]->eid : 0;
        $l_id = isset($obj[0]) ? $obj[0]->leave_id : '';
        $date_filed = isset($obj[0]) ? $obj[0]->date_filed : '';
        $start = isset($obj[0]) ? $obj[0]->date : '';
        $stop = isset($obj[0]) ? $obj[0]->date : '';
        $track = 0;
        $vl = 0;
        $sl = 0;
        $el = 0;
        $vlwop = 0;
        $slwop = 0;
        $elwop = 0;
        foreach($obj as $o){
            if($leave_id == $o->leave_id):

            else:
                $body = [
                    str_pad($l_id,5,"0",STR_PAD_LEFT),
                    $eid,
                    $ename,
                    date("F d, Y", strtotime($start)),
                    date("F d, Y", strtotime($stop)),
                    $vl,
                    $sl,
                    $el,
                    $vlwop,
                    $slwop,
                    $elwop
              
                ];
                $sheet->fromArray([$body], NULL, 'A'.$i); 
                $i++;
                $vl = 0;
                $sl = 0;
                $el = 0;
                $vlwop = 0;
                $slwop = 0;
                $elwop = 0;
                $leave_id = $o->leave_id;
                $ename = $o->emp_name;
                $eid = $o->eid;
                $l_id = $o->leave_id;
                $date_filed = $o->date_filed;
                $start = $o->date;
            endif;
            switch($o->leave_type_id):
                case 4: $o->pay_type == 1 ? $sl+=$o->length : $slwop+=$o->length; break;
                case 5: $o->pay_type == 1 ? $vl+=$o->length : $vlwop+=$o->length; break;
                case 6: $o->pay_type == 1 ? $el+=$o->length : $elwop+=$o->length; break;
            endswitch;
            $stop = $o->date;
        }
        if($l_id){
            $body = [
                str_pad($l_id,5,"0",STR_PAD_LEFT),
                $eid,
                $ename,
                date("F d, Y", strtotime($start)),
                date("F d, Y", strtotime($stop)),
                $vl,
                $sl,
                $el,
                $vlwop,
                $slwop,
                $elwop
            ];
            $sheet->fromArray([$body], NULL, 'A'.$i); 
        }
        ob_end_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="leave-report-'.date('mdY-His').'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->setPreCalculateFormulas(false);
        $writer->setOffice2003Compatibility(true);
        $writer->save('php://output');
    }
    
    public function updateLeaveEntry(Request $request){
        $leave = LeaveRequest::find($request->id);
        
        $obj = [
            'leave_date'    => $request->leave_date,
            'length'        => $request->length,
            'pay_type'      => $request->pay_type,
            'field_id'      => $request->field_id
        ];
        
        for($i = 0; $i < count($obj['field_id']); $i++):
            if($obj['field_id'][$i] > 0):
                LeaveRequestDetails::where('id',"=",$obj['field_id'][$i])
                    ->update([
                        'date'      => date("Y-m-d",strtotime($obj['leave_date'][$i])),
                        'length'    => $obj['length'][$i],
                        'pay_type'  => $obj['pay_type'][$i],
                    ]);
            else:
                $details = [
                    'leave_id'      => $request->id,
                    'date'          => date("Y-m-d",strtotime($obj['leave_date'][$i])),
                    'length'        => $obj['length'][$i],
                    'pay_type'      => $obj['pay_type'][$i]
                ];
                LeaveRequestDetails::create($details);
            endif;
        endfor;
        
        $datetime = new DateTime();
        //$leave_date_from = $datetime->createFromFormat('m/d/Y', $request->leave_date_from)->format("Y-m-d H:i:s");
        //$leave_date_to = $datetime->createFromFormat('m/d/Y', $request->leave_date_to)->format("Y-m-d H:i:s");
        $report_date = $datetime->createFromFormat('m/d/Y', $request->report_date)->format("Y-m-d H:i:s");
        $date_filed = $datetime->createFromFormat('m/d/Y', $request->date_filed)->format("Y-m-d H:i:s");

        $leave->employee_id = $request->employee_id;
        $leave->filed_by_id = Auth::user()->id;
        $leave->recommending_approval_by_id = Auth::user()->supervisor_id;
        $leave->approved_by_id = Auth::user()->manager_id;

        // $leave->leave_date_from = $leave_date_from;
        // $leave->leave_date_to =$leave_date_to;
        $leave->number_of_days = $request->number_of_days;
        $leave->report_date = $report_date;
        $leave->reason = $request->reason;
        $leave->contact_number = $request->contact_number;
        $leave->leave_type_id = $request->leave_type_id;
        $leave->pay_type_id = $request->pay_type_id;
        $leave->date_filed = $date_filed;
        $leave->save();
        header("Location: /leave/".$request->id);
        die();
    }
    
    public function deleteLeaveDate(Request $request){
        LeaveRequest::where('id',$request->leave)
            ->update(['number_of_days' => $request->total]);
        return LeaveRequestDetails::where('id',"=",$request->id)
            ->update(['status' => 0]);
    }
    
    public function uploadLastCredit(Request $request){
        
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
        $leave_request->approve_status_id = 3;

        if($leave_request->save()){
            // SEND EMAIL NOTIFICATION
            if($this->settings->get('email_notification')){
               // Mail::to($leave_request->employee->email)->queue(new LeaveRecommended($leave_request));
              //  $leave_id = $request->leave_id;
               // $leave_obj = ['leave' => $leave_request, 'details' => LeaveRequestDetails::where("leave_id",$leave_id)->get()];
               // Mail::to($leave_request->recipients())->queue(new LeaveNotification($leave_obj));
            }
            return back()->with('success', 'Leave request successfully recommended for approval.');
        } else {
            return back()->with('error', 'Something went wrong.');
        }
    }

    public function approve(Request $request){
        $leave_request = LeaveRequest::find($request->leave_id);
        $leave_request->approved_by_id = Auth::user()->id;
        $leave_request->approved_by_signed_date = date('Y-m-d H:i:s');
        $leave_request->approve_status_id = 1;
        
        $id = $request->leave_id;
        $req_det_obj = DB::select("select * from leave_request_details where leave_id = $id;");
        $with_pay = 0;
        if(count($req_det_obj) > 0):
            foreach($req_det_obj as $det):
                $with_pay += $det->pay_type == 1 ? 1 : 0;
            endforeach;
        endif;
        
        if($with_pay > 0):
            $lc_obj = DB::select("select * from leave_credits where leave_id = $id;");
            if(count($lc_obj) == 0 ):
                $lc = new LeaveCredits();
                $lc->employee_id = $leave_request->employee_id;
                $lc->credit = $with_pay;
                $lc->type = 5;
                $lc->month = date("m");
                $lc->year = date("Y");
                $lc->leave_id = $id;
                $lc->status = 1;
                $lc->save();
            endif;
        endif;
                    
        // SEND EMAIL NOTIFICATION
        
        if($leave_request->save()){
            if($this->settings->get('email_notification')){
             //   Mail::to($leave_request->employee->email)->queue(new LeaveApproved($leave_request));
            }
            return back()->with('success', 'Leave request successfully approved. . .');
        } else {
            return back()->with('error', 'Something went wrong. . .');
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
                //Mail::to($leave_request->employee->email)->queue(new LeaveDeclined($leave_request));
            }
            return back()->with('success', 'Leave request successfully declined.');
        } else {
            return back()->with('error', 'Something went wrong.');
        }
    }
    
    public function creditIncrementVer2($month){
        $obj = DB::select("
            SELECT 
                *
            FROM
                employee_info
            WHERE
                status = 1 AND deleted_at IS NULL
                    AND eid LIKE 'ESCC-%';
        ");
        
        foreach($obj as $e):
            $id = $e->id;
            $det = DB::select("
                SELECT 
                    id
                FROM
                    leave_credits
                WHERE
                    employee_id = $id AND type = 1
                        AND month = $month
                        AND year = YEAR(NOW());
            ");
            if(count($det) == 0){
                $div = 0;
                switch($e->employee_category):
                    case 1: $div = 20; break;
                    case 2: $div = 14; break;
                    case 3: $div = 10; break;
                    case 4: $div = 10; break;
                endswitch;
                $credit = new LeaveCredits();
                $credit->employee_id = $id;
                $credit->credit = $div / 12;
                $credit->type = 1;
                $credit->month = $month;
                $credit->year = date("Y");
                $credit->save();
            }
        endforeach;
        
        return "done";
    }

    public function creditIncrement(){
        $obj = DB::select("
            SELECT 
                *
            FROM
                employee_info
            WHERE
                status = 1 AND deleted_at IS NULL
                    AND eid LIKE 'ESCC-%';
        ");
        
        foreach($obj as $e):
            $id = $e->id;
            $det = DB::select("
                SELECT 
                    id
                FROM
                    leave_credits
                WHERE
                    employee_id = $id AND type = 1
                        AND month = MONTH(NOW())
                        AND year = YEAR(NOW());
            ");
            if(count($det) == 0){
                $div = 0;
                switch($e->employee_category):
                    case 1: $div = 20; break;
                    case 2: $div = 14; break;
                    case 3: $div = 10; break;
                    case 4: $div = 10; break;
                endswitch;
                $credit = new LeaveCredits();
                $credit->employee_id = $id;
                $credit->credit = $div / 12;
                $credit->type = 1;
                $credit->month = date("n");
                $credit->year = date("Y");
                $credit->save();
            }
        endforeach;
        
        return "done";
    }
    
    public function credits(){
        $obj = DB::select($this->newQuery());
        return view('leave.credits')->with('employees', $obj);
    }
    
    public function leaveCredits(){
        $obj = DB::select($this->newQuery());
        return view('leave.expanded-credits')->with('employees', $obj);
    }
    
    public function leaveTracker(){
        $obj = DB::select($this->newQuery());
        return view('leave.expanded-tracker')->with('employees', $obj);
    }
    
    public function pastCredits(){
        $obj = DB::select($this->pastQuery());
        return view('leave.past-credits')->with('employees', $obj);
    }

    public function editcredits(Request $request, $employee_id){
         $employee = User::find($employee_id);
        $credit = DB::select($this->newQuery($employee->id));

        if(count($credit) > 0){
            $credits = (object) $credit[0];
        }

	switch ($credits->employee_category):
            case 1:
                $div = 20;
                break;
            case 2:
                $div = 14;
                break;
            case 3:
                $div = 10;
                break;
            case 4:
                $div = 10;
                break;
        endswitch;
        $different_in_months = DateHelper::getDifferentMonths($employee->hired_date);
        $monthly_accrual = (($div / 12) * $different_in_months) + $credits->monthly_accrual;

        $credits->monthly_accrual = $monthly_accrual;

        return view('leave.editcredits', compact('employee', 'credits'));

    }

    public function updatecredits(Request $request){
	 if ($request->monthly_accrual == 0 && $request->pto_forwarded == 0) {
            return back()->withErrors(['fail' => 'Value zero on both monthly accrual and pto forwarded are not valid']);
        }
        $employee = User::find($request->employee_id);

	if ($request->monthly_accrual != 0) {
            $leave = LeaveCredits::create([
                'employee_id' => $employee->id,
                'credit' => $request->monthly_accrual,
                'type' => 7,
                'month' => now()->month,
                'year' => now()->year,
                'leave_id' => 0,
                'status' => 1
            ]);

            LeaveCredits::create([
                'employee_id' => $employee->id,
                'credit' => $request->monthly_accrual,
                'type' => 1,
                'month' => now()->month,
                'year' => now()->year,
                'leave_id' => 0,
                'status' => 1
            ]);
        }

        if ($request->pto_forwarded != 0) {
            LeaveCredits::create([
                'employee_id' => $employee->id,
                'credit' => $request->pto_forwarded ?? 0,
                'type' => 2,
                'month' => now()->month,
                'year' => now()->subYear()->format('Y'),
                'leave_id' => 0,
                'status' => 1
            ]);
        }

        return back()->with('success', 'Successfully updated leave credits!');
    }
    
    private function getBlockedDates(){
        $blocked_dates = [];
        $obj = LeaveRequest::getBlockedDates(Auth::user()->team_name);
        $events = LeaveRequest::getCWD();
        if(count($obj) > 0):
            foreach($obj as $e):
                $tar_date = date("m/d/Y", strtotime($e->cwd));
                if(!in_array($tar_date,$blocked_dates))
                        array_push($blocked_dates,$tar_date);
            endforeach;
        endif;
        if(count($events) > 0):
            foreach($events as $e):
                $tar_date = date("m/d/Y", strtotime($e->cwd));
                if(!in_array($tar_date,$blocked_dates))
                        array_push($blocked_dates,$tar_date);
            endforeach;
        endif;
        
        return $blocked_dates;
    }
    
    public function conversion(){
        $obj = DB::select($this->newQuery());
        return view('leave.credits-conversion')->with('employees', $obj);
    }
    
    public function saveConversion(Request $r){
        $conversion = $r->post('con');
        $id = $r->post('id');
        $lc = new LeaveCredits();
        $lc->employee_id = $id;
        $lc->credit = $conversion;
        $lc->type = 3;
        $lc->month = date('m');
        $lc->year = date('Y');
        $lc->leave_id = 0;
        $lc->status = 1;
        $lc->save();
    }
    
    public function xlsCredits(){
       
        $writesheet = new Spreadsheet();
        $writer = IOFactory::createWriter($writesheet, "Xlsx");
        $sheet = $writesheet->getActiveSheet();
        $i = 1;
        $past = date('Y') - 1;
        $header = array("Employee ID", "Employee Name", "Position", $past." PTO for Conversion", $past." PTO", date('Y')." PTO", "Used PTO", "PTO Balance");
        $sheet->fromArray([$header], NULL, 'A'.$i); 
        $i++;
        $obj = DB::select($this->newQuery());
        foreach($obj as $employee){
            $body = [
                $employee->eid,
                strtoupper($employee->employee_name),
                $employee->position_name,
                number_format($employee->conversion_credit,1),
                number_format($employee->past_credit - $employee->conversion_credit,1),
                number_format($employee->current_credit,1),
                number_format($employee->used_credit,1),
                number_format($employee->total_credits,1)
            ];
            $sheet->fromArray([$body], NULL, 'A'.$i); 
            $i++;
        }
        ob_end_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="leave-credits-'.date('mdY-His').'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->setPreCalculateFormulas(false);
        $writer->setOffice2003Compatibility(true);
        $writer->save('php://output');
    }
    
    public function xlsCreditsVer2(){
        $writesheet = new Spreadsheet();
        $writer = IOFactory::createWriter($writesheet, "Xlsx");
        $sheet = $writesheet->getActiveSheet();
        $i = 1;
        $past = date('Y') - 1;
        $header = array("ID", "Employee ID", "Employee Name", "Date Started", "Position", $past." PTO for Conversion", $past." PTO", date('Y')." PTO", "Used PTO", "PTO Balance");
        $sheet->fromArray([$header], NULL, 'A'.$i); 
        $i++;
        $obj = DB::select($this->newQuery());
        foreach($obj as $employee){
            $body = [
                $employee->id,
                $employee->eid,
                strtoupper($employee->employee_name),
                $employee->prod_date,
                $employee->position_name,
                number_format($employee->conversion_credit,1),
                number_format($employee->past_credit - $employee->conversion_credit,1),
                number_format($employee->current_credit,1),
                number_format($employee->used_credit,1),
                number_format($employee->total_credits,1)
            ];
            $sheet->fromArray([$body], NULL, 'A'.$i); 
            $i++;
        }
        ob_end_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="leave-credits-'.date('mdY-His').'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->setPreCalculateFormulas(false);
        $writer->setOffice2003Compatibility(true);
        $writer->save('php://output');
    }
    
    public function patchCredits(){
        $obj = DB::select("select id from employee_info");
        $list=[];
        foreach($obj as $employee):
            $id = $employee->id;
            $obj2 = DB::select("select id from leave_credits where employee_id = $id and month = 4 and year = 2020");
            if(count($obj2) > 1):
                $lc = LeaveCredits::find($obj2[1]->id);
                $lc->status = 0;
                $lc->save();
                array_push($list,$lc->id);
            endif;
        endforeach;
        
        return json_encode(['obj' => $list, 'status' => 'patch successful']);
    }
    
    /*
     *  on  year = YEAR(NOW()) for conversion credits
     *  type = 3
     */
    private function newQuery($id = NULL){
	$today = now();
            $today->month = 1;
            $today->day = 1;
        $firstHalfYearStart = $today->format('Y-m-d');
        
            $today->month = 6;
            $today->day = 30;
        $firstHalfYearEnd = $today->format('Y-m-d');

            $today->month = 7;
            $today->day = 1;
        $lastHalfYearStart = $today->format('Y-m-d');

            $today->month = 12;
            $today->day = 31;
        $lastHalfYearEnd = $today->format('Y-m-d');

        $sql = "  
  SELECT 
    eid,
    id,
    CONCAT(e.first_name, ' ', e.last_name) AS employee_name,
    employee_category,
    hired_date,
    e.prod_date,
    e.position_name,
    e.is_regular,
    0 as used_credit,
    0 as total_credits,
    IFNULL((SELECT 
                sum(credit)
                FROM
                    leave_credits
                WHERE
                    year = YEAR(NOW()) AND type = 6
                        AND employee_id = e.id
                        AND leave_credits.status = 1
                LIMIT 1),
            0) AS expired_credit,
    IFNULL((SELECT 
                    SUM(credit)
                FROM
                    leave_credits
                WHERE
                    year = YEAR(NOW()) - 1 AND type = 2
                        AND employee_id = e.id
                        AND leave_credits.status = 1
                ),
            0) AS past_credit,

    IFNULL((SELECT 
                    credit
                FROM
                    leave_credits
                WHERE
                    year = YEAR(NOW())  AND type = 3
                        AND employee_id = e.id
                        AND leave_credits.status = 1
                LIMIT 1),
            0) AS conversion_credit,
    IFNULL((SELECT 
                    credit
                FROM
                    leave_credits
                WHERE
                    year = YEAR(NOW()) AND type = 4
                        AND employee_id = e.id
                        AND leave_credits.status = 1
                LIMIT 1),
            0) AS loa,
	IFNULL((
            SELECT
                SUM(credit)
            FROM
                leave_credits
            WHERE
                year = YEAR(NOW())
                    AND
                type = 7
                    AND
                employee_id = e.id
                    AND
                leave_credits.status = 1
            ),
        0) AS monthly_accrual,
    IFNULL((SELECT 
                    SUM(credit)
                FROM
                    leave_credits
                WHERE
                    year = YEAR(NOW()) AND type = 1
                        AND leave_credits.status = 1
                        AND employee_id = e.id),
            0) AS current_credit,
    IFNULL((SELECT 
                    SUM(lrd.length)
                FROM
                    elink_employee_directory.leave_request AS lr
                        LEFT JOIN
                    leave_request_details AS lrd ON lrd.leave_id = lr.id
                WHERE
                    lr.employee_id = e.id
                        AND lrd.date >= '$firstHalfYearStart'
                        AND date <= '$firstHalfYearEnd'
                        AND lrd.pay_type = 1
                        AND lrd.status = 1
                        AND lr.approve_status_id = 1),
            0) AS used_jan_to_jun,
    IFNULL((SELECT 
                    SUM(lrd.length)
                FROM
                    elink_employee_directory.leave_request AS lr
                        LEFT JOIN
                    leave_request_details AS lrd ON lrd.leave_id = lr.id
                WHERE
                    lr.employee_id = e.id
                        AND lrd.date >= '$lastHalfYearStart'
                        AND date <= '$lastHalfYearEnd'
                        AND lrd.pay_type = 1
                        AND lrd.status = 1
                        AND lr.approve_status_id = 1),
            0) AS used_jul_to_dec
FROM
    elink_employee_directory.employee_info AS e
WHERE
    e.status = 1 AND e.deleted_at IS NULL
        AND eid LIKE 'ESCC-%'";
        
        if($id)
            $sql.=" and e.id = ".$id;
        
        return $sql;
    }
    
        private function pastQuery($id = NULL){
        $sql = "  
  SELECT 
    eid,
    id,
    CONCAT(e.first_name, ' ', e.last_name) AS employee_name,
    e.prod_date,
    e.position_name,
    0 as used_credit,
    0 as total_credits,
    IFNULL((SELECT 
                sum(credit)
                FROM
                    leave_credits
                WHERE
                    year = 2020 AND type = 6
                        AND employee_id = e.id
                        AND leave_credits.status = 1
                LIMIT 1),
            0) AS expired_credit,
    IFNULL((SELECT 
                    credit
                FROM
                    leave_credits
                WHERE
                    year = 2020 - 1 AND type = 2
                        AND employee_id = e.id
                        AND leave_credits.status = 1
                LIMIT 1),
            0) AS past_credit,
    IFNULL((SELECT 
                    credit
                FROM
                    leave_credits
                WHERE
                    year = 2020 AND type = 3
                        AND employee_id = e.id
                        AND leave_credits.status = 1
                LIMIT 1),
            0) AS conversion_credit,
    IFNULL((SELECT 
                    credit
                FROM
                    leave_credits
                WHERE
                    year = 2020 AND type = 4
                        AND employee_id = e.id
                        AND leave_credits.status = 1
                LIMIT 1),
            0) AS loa,
    IFNULL((SELECT 
                    SUM(credit)
                FROM
                    leave_credits
                WHERE
                    year = 2020 AND type = 1
                        AND leave_credits.status = 1
                        AND employee_id = e.id),
            0) AS current_credit,
    IFNULL((SELECT 
                    SUM(lrd.length)
                FROM
                    elink_employee_directory.leave_request AS lr
                        LEFT JOIN
                    leave_request_details AS lrd ON lrd.leave_id = lr.id
                WHERE
                    lr.employee_id = e.id
                        AND lrd.date >= '2020-01-01'
                        AND date <= '2020-06-30'
                        AND lrd.pay_type = 1
                        AND lrd.status = 1
                        AND lr.approve_status_id = 1),
            0) AS used_jan_to_jun,
    IFNULL((SELECT 
                    SUM(lrd.length)
                FROM
                    elink_employee_directory.leave_request AS lr
                        LEFT JOIN
                    leave_request_details AS lrd ON lrd.leave_id = lr.id
                WHERE
                    lr.employee_id = e.id
                        AND lrd.date >= '2020-07-01'
                        AND date <= '2020-12-31'
                        AND lrd.pay_type = 1
                        AND lrd.status = 1
                        AND lr.approve_status_id = 1),
            0) AS used_jul_to_dec
FROM
    elink_employee_directory.employee_info AS e
WHERE
    e.status = 1 AND e.deleted_at IS NULL
        AND eid LIKE 'ESCC-%'";
        
        if($id)
            $sql.=" and e.id = ".$id;
        
        return $sql;
    }
    
    private function newQuery2($id = NULL){
        $sql = "         
            SELECT 
                eid,
                id,
                CONCAT(e.first_name, ' ', e.last_name) AS employee_name,
                e.prod_date,
                e.position_name,
                @past:=IFNULL((SELECT 
                                credit
                            FROM
                                leave_credits
                            WHERE
                                year = YEAR(NOW()) - 1 AND type = 2
                                    AND employee_id = e.id AND leave_credits.status = 1
                            LIMIT 1),
                        0) AS past_credit,
                @conversion:=IFNULL((SELECT 
                                credit
                            FROM
                                leave_credits
                            WHERE
                                year = YEAR(NOW()) AND type = 3
                                    AND employee_id = e.id AND leave_credits.status = 1
                            LIMIT 1),
                        0) AS conversion_credit,
                        IFNULL((SELECT 
                                credit
                            FROM
                                leave_credits
                            WHERE
                                year = YEAR(NOW()) AND type = 4
                                    AND employee_id = e.id AND leave_credits.status = 1
                            LIMIT 1),
                        0) AS loa,
                @current:=IFNULL((SELECT 
                                SUM(credit)
                            FROM
                                leave_credits
                            WHERE
                                year = YEAR(NOW()) AND type = 1 AND leave_credits.status = 1
                                    AND employee_id = e.id),
                        0) AS current_credit,
                @used:=IFNULL((SELECT 
                                SUM(credit)
                            FROM
                                leave_credits
                            WHERE
                                year = YEAR(NOW()) AND type = 5 AND leave_credits.status = 1
                                    AND employee_id = e.id),
                        0) AS used_credit,
                @totalpto:=@past + @current - @used - @conversion AS total_credits
            FROM
                elink_employee_directory.employee_info AS e
            WHERE
                e.status = 1 AND e.deleted_at IS NULL
                    AND eid LIKE 'ESCC-%'";
        if($id)
            $sql.=" and e.id = ".$id;
        
        return $sql;
    }
}
