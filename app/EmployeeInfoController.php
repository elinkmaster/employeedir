<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use DateTime;
use App\EmployeeDepartment;
use App\ElinkAccount;
use App\ElinkDivision;
use App\User;
use App\EmployeeInfoDetails;
use App\Employee;
use App\EmployeeAttrition;
use App\LeaveRequest;
use Response;
use File;
use DB;

class EmployeeInfoController extends Controller
{

    public function login(Request $request)
    {   
        return $this->authModel->login($request);
    }

    public function loginAPIv2(Request $request)
    {
        
        return $this->authModel->loginAPIv2($request);
    }

    public function loginAPI(Request $request)
    {
        return $this->authModel->loginAPI($request);
    }

   public function session(Request $request)
   {
        return $this->authModel->session($request);
   }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return 'index';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('employee.create')
        ->with('managers', User::allExceptSuperAdmin()->orderBy('last_name')->get())
        ->with('supervisors', User::allExceptSuperAdmin()->orderBy('last_name')->get())
        ->with('departments', EmployeeDepartment::all())->with('accounts', ElinkAccount::all())
        ->with('positions', User::select('position_name')->groupBy('position_name')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->model->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $employee = User::withTrashed()->find($id);
        
        if (isset($employee)) {
            $obj = EmployeeInfoDetails::where('employee_id',$id)->get();
            if(count($obj) > 0)
                $details = $obj[0];
            else
                $details = (object)[
                    'town_address'      => '',
                    'em_con_name'       => '',
                    'em_con_rel'        => '',
                    'em_con_num'        => ''
                ];
            return view('employee.view')->with('employee', $employee)->with('employee_details',$details);
        } else {
            return abort(404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {   
        $employee = User::find($id);
        $obj = EmployeeInfoDetails::where('employee_id',"=",$id)->get();
        if(count($obj) > 0):
            $obj = $obj[0];
        else:
            $obj = [
                'town_address' => '',
                'em_con_name' => '',
                'em_con_address' => '',
                'em_con_num' => '',
                'em_con_rel' => ''
            ];
        endif;

        if (isset($employee)) {
            return view('employee.edit')
                ->with('employee', $employee)
                ->with('supervisors', User::all())
                ->with('departments', EmployeeDepartment::all())
                ->with('accounts', ElinkAccount::all())
                ->with('details',$obj);
        } else {
            return abort(404);
        }
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
        return $this->model->updateEmployee($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $employee = User::find($id);
        $employee->delete();

        return redirect()->back()->with('success', "Successfully deleted employee record");
    }

    // 
    // change password : only admin can view this
    //
    public function changepassword(Request $request, $id)
    {
        return $this->authModel->changepassword($request, $id);
    }

    // 
    // change password : only admin can change
    //
    public function savepassword(Request $request, $id)
    {
        return $this->authModel->savepassword($request, $id);
        
    }
    // 
    // employees dashboard : only admin can view
    //
    public function employees(Request $request)
    {
        return $this->model->employees($request);
    }

    public function profile (Request $request, $id)
    {
        return view('auth.profile.view')->with('employee', User::withTrashed()->find($id));
    }

    public function myprofile(Request $request)
    {
        if (Auth::user()->isAdmin()) {
            return view('employee.view')->with('employee', Auth::user());
        }
        return view('auth.profile.view')->with('employee', Auth::user())
            ->with('my_requests', LeaveRequest::where('filed_by_id', Auth::user()->id)->get());
    }
    
    public function updateProfile(Request $request){
        $emp_details = [
            'em_con_name'   => '',
            'em_con_rel'    => ''
        ];
        $details = EmployeeInfoDetails::where('employee_id',Auth::user()->id)->get();
        if(count($details) > 0)
            $emp_details = $details[0];
        return view('auth.profile.edit')->with('employee', Auth::user())->with('details',$emp_details);
    }
    
    public function saveProfile(Request $request){
        $obj = [
            'employee_id'           => $request->post('employee_id'),
            'changedate'            => date('Y-m-d H:i:s'),
            'o_current_address'     => $request->post('o_current_address'),
            'n_current_address'     => $request->post('n_current_address'),
            'o_contact_num'         => $request->post('o_contact_num'),
            'n_contact_num'         => $request->post('n_contact_num'),
            'o_emergency'           => $request->post('o_emergency'),
            'n_emergency'           => $request->post('n_emergency'),
            'o_rel'                 => $request->post('o_rel'),
            'n_rel'                 => $request->post('n_rel'),
            'status'                => 1
        ];
        
        Mail::to("rene.abellana@gmail.com")->queue(new UpdateInfo($obj));
    }

    public function import(Request $request)
    {
        return view('employee.import');
    }

    public function importsave(Request $request)
    {
        return $this->excelModel->importsave($request);
    }
    /* EXPORT */
    public function exportdownload() 
    {
       return $this->excelModel->exportdownload();
    }


    public function importbday(Request $request) {
        $num_inserts = 0;
        $num_updates = 0;
        $updates = array();
        $inserts = array();
        $employees = array();
        $invalid_emails = array();

        $COUNT = 0;
        $EID = 1;
        $BDAY = 7;
        
        
        if ($request->hasFile("dump_file")) {
            $path = $request->dump_file->storeAs('/public/temp/'.Auth::user()->id, 'dump_file.'. \File::extension($request->dump_file->getClientOriginalName()));
        }

        $address = './storage/app/'. $path;
        
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $address );

        $worksheet = $spreadsheet->getActiveSheet();
        $rows = [];
        foreach ($worksheet->getRowIterator() AS $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE); 
            $cells = [];

            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            $rows[] = $cells;
            if (count($rows) == 1) {
               

            } else {
                // return json_encode($cells);
                $employee = User::where("eid", "LIKE", "%".$cells[$EID]."%");
                if ($employee->count() == 1) {
                    if ($cells[$BDAY]) {
                        if (is_numeric($cells[$BDAY])) {
                            $UNIX_DATE = ($cells[$BDAY] - 25569) * 86400;
                            $employee->update([
                                'birth_date' => gmdate("Y-m-d H:i:s", (int) $UNIX_DATE)
                            ]);
                            $num_updates ++;
                        } else {
                            $employee->update([
                                'birth_date' => gmdate("Y-m-d H:i:s", strtotime(str_replace('-','/',$cells[$BDAY])))
                            ]);
                            $num_updates ++;
                        }
                    }
                }
            }
        }
        return "num_updates: " . $num_updates;
    }
    
    public function checklatest() {
        $path = "/var/www/uploads/masterlist"; 

        $latest_ctime = 0;
        $latest_filename = '';    

        $d  = array_diff(scandir($path), array('.', '..'));
        foreach ($d as $entry) {
            $filepath = "{$path}/{$entry}";
            // could do also other checks than just checking whether the entry is a file
            if (is_file($filepath) && filectime($filepath) > $latest_ctime) {
                $latest_ctime = filectime($filepath);
                $latest_filename = $entry;
            }
        }

        $num_inserts = 0;
        $num_updates = 0;
        $updates = array();
        $inserts = array();
        $employees = array();
        $invalid_emails = array();

        $COUNT = 0;
        $EID = 1;
        $EXT = 2;
        $ALIAS = 3;
        $LAST_NAME = 4;
        $FIRST_NAME = 5;
        $FULLNAME = 6;
        $SUPERVISOR = 7;
        $MANAGER = 8;
        $DEPT = 9;
        $DEPT_CODE = 10;
        $DIVISION = 11;
        $ROLE = 12;
        $ACCOUNT = 13;
        $PROD_DATE = 14;
        $STATUS = 15;
        $HIRED_DATE = 16;
        $WAVE = 17;
        $EMAIL = 18;
        $GENDER = 19;
        $BDAY = 20;

        $address = $filepath;
        
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $address );

        $worksheet = $spreadsheet->getActiveSheet();
        $rows = [];

        foreach ($worksheet->getRowIterator() AS $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE); 
            $cells = [];

            foreach ($cellIterator as $cell) {
                $cellValue = $cell->getValue();
                if($cell == "--"){
                    $cellValue = "";
                }
                $cells[] = $cellValue;
            }
            $rows[] = $cells;
            // Check if document is valid 
            if (count($rows) == 1) {
            
                // 
                // TODO Trapping for excel column placing
                //

            } else {
                
                $cells[$EMAIL] = trim($cells[$EMAIL]);
                $cells[$EID] = trim($cells[$EID]);
                $emp = User::withTrashed()->where('eid', 'LIKE', '%'.$cells[$EID].'%');

                if (!$cells[$EMAIL] || !filter_var($cells[$EMAIL], FILTER_VALIDATE_EMAIL)) {
                    // list invalid email
                    if($cells[$FULLNAME] != null){
                        array_push($invalid_emails, $cells[$FIRST_NAME] . " " . $cells[$LAST_NAME]);
                    }
                    
                    continue;
                }

                if ($cells[$ACCOUNT]) {
                    $account = ElinkAccount::where('account_name', 'LIKE', $cells[$ACCOUNT]);
                    if ($account->count() == 0) {
                        ElinkAccount::insert([
                            'account_name' => $cells[$ACCOUNT]
                        ]);
                    }
                }

                if ($cells[$DIVISION]) {
                    $division = ElinkDivision::where('division_name','LIKE', $cells[$DIVISION]);
                    if ($division->count() == 0) {
                        ElinkDivision::insert([
                            'division_name' => $cells[$DIVISION]
                        ]);
                    }
                }

                if ($cells[$DEPT]) {
                    $department = EmployeeDepartment::where('department_name', 'LIKE', $cells[$DEPT]);
                    if ($department->count() == 0) {  
                        if($cells[$ACCOUNT]) {
                            $dept_account = ElinkAccount::where('account_name', 'LIKE', $cells[$ACCOUNT]);
                            if ($dept_account->count() > 0) {
                                if ($cells[$DIVISION]) {
                                    $dept_division = ElinkDivision::where('division_name','LIKE', $cells[$DIVISION]);
                                    if ($dept_division->count() > 0) {
                                        EmployeeDepartment::insert([
                                            'department_name' => $cells[$DEPT],
                                            'department_code' => $cells[$DEPT_CODE],
                                            'division_id' => $dept_division->first()->id,
                                            'account_id' => $dept_account->first()->id
                                        ]);
                                    }
                                }  
                            }
                        }
                    }
                }

                $account = ElinkAccount::where('account_name','LIKE' , '%'.trim($cells[$ACCOUNT]).'%')->get();
                
                if ($emp->count() >= 1) {
                    // Update 
                    $employee = array(
                        'eid' => trim($cells[$EID]),
                        'alias' => trim($cells[$ALIAS]),
                        'last_name' => trim($cells[$LAST_NAME]),
                        'first_name' => trim($cells[$FIRST_NAME]),
                        'supervisor_name' =>  trim($cells[$SUPERVISOR]),
                        'manager_name' => trim($cells[$MANAGER]),
                        'team_name' => trim($cells[$DEPT]),
                        'dept_code' => trim($cells[$DEPT_CODE]),
                        'position_name' => trim($cells[$ROLE]),
                        'gender' => genderValue(trim($cells[$GENDER])),
                        'division_name' => trim($cells[$DIVISION]),
                        'ext' => trim($cells[$EXT]),
                        'wave' => trim($cells[$WAVE]),
                    );

                    if (count($account) > 0) {
                        $employee['account_id'] = $account->first()->id;
                    } else {
                        $employee['account_id'] = 0;
                    }
                    if (strtolower($cells[$STATUS]) == strtolower('Active')) {
                        $employee['status'] = 1;
                    } else {
                        $employee['status'] = 2;
                    }
                    if ($cells[$HIRED_DATE]) {
                        if (is_numeric($cells[$HIRED_DATE])) {
                            $UNIX_DATE = ($cells[$HIRED_DATE] - 25569) * 86400;
                            $employee['hired_date'] = gmdate("Y-m-d H:i:s", (int) $UNIX_DATE);
                        }
                    }
                    if ($cells[$BDAY]) {
                        if (is_numeric($cells[$BDAY])) {
                            $UNIX_DATE = ($cells[$BDAY] - 25569) * 86400;
                            $employee['birth_date'] = gmdate("Y-m-d H:i:s", (int) $UNIX_DATE);
                        }
                    }
                    if ($cells[$PROD_DATE]) {
                        if (is_numeric($cells[$PROD_DATE])) {
                            $UNIX_DATE = ($cells[$PROD_DATE] - 25569) * 86400;
                            $employee['prod_date'] = gmdate("Y-m-d H:i:s", (int) $UNIX_DATE);
                        }
                    }

                    if ($emp->update($employee)) {
                        array_push($updates, $cells[$FIRST_NAME] . ' ' . $cells[$LAST_NAME]);
                        $num_updates ++;
                    }
                } else {
                    // SQL saving of data
                    $employee = new User; // USER : EMPLOYEE
                    $employee->eid = trim($cells[$EID]);
                    $employee->first_name = trim($cells[$FIRST_NAME]);
                    $employee->middle_name = '';
                    $employee->last_name = trim($cells[$LAST_NAME]);
                    $employee->email = trim($cells[$EMAIL]);
                    $employee->alias = trim($cells[$ALIAS]);
                    $employee->team_name = trim($cells[$DEPT]);
                    $employee->dept_code = trim($cells[$DEPT_CODE]);
                    $employee->position_name = trim($cells[$ROLE]);
                    $employee->supervisor_name = trim($cells[$SUPERVISOR]);
                    $employee->gender = genderValue(trim($cells[$GENDER]));
                    $employee->usertype = 1;
                    $employee->manager_name = trim($cells[$MANAGER]);
                    $employee->division_name = trim($cells[$DIVISION]);
                    $employee->all_access = 1;
                    $employee->ext = trim($cells[$EXT]);
                    $employee->wave = trim($cells[$WAVE]);
                    $employee->password = Hash::make(env('USER_DEFAULT_PASSWORD', 'qwe123!@#$'));
                    
                    $account = ElinkAccount::where('account_name','LIKE' , '%'.$cells[$ACCOUNT].'%')->get();
                    
                    if (count($account) > 0) {
                        $employee->account_id = $account->first()->id;
                    } else {
                        $employee->account_id = 0;
                    }
                    if (strtolower($cells[$STATUS]) == strtolower('Active')) {
                        $employee->status = 1;
                    } else {
                        $employee->status = 2;
                    }
                    if ($cells[$HIRED_DATE]) {
                        if (is_numeric($cells[$HIRED_DATE])) {
                            $UNIX_DATE = ($cells[$HIRED_DATE] - 25569) * 86400;
                            $employee->hired_date = gmdate("Y-m-d H:i:s", (int) $UNIX_DATE);
                        }
                    }
                    if ($cells[$BDAY]) {
                        if (is_numeric($cells[$BDAY])) {
                            $UNIX_DATE = ($cells[$BDAY] - 25569) * 86400;
                            $employee->birth_date = gmdate("Y-m-d H:i:s", (int) $UNIX_DATE);
                        }
                    }
                    if ($cells[$PROD_DATE]) {
                        if (is_numeric($cells[$PROD_DATE])) {
                            $UNIX_DATE = ($cells[$PROD_DATE] - 25569) * 86400;
                            $employee->prod_date = gmdate("Y-m-d H:i:s", (int)$UNIX_DATE);
                        }
                    }
                    if ($employee->gender == 1) {
                        $employee->profile_img = asset('public/img/nobody_m.original.jpg');
                    } else {
                        $employee->profile_img = asset('public/img/nobody_f.original.jpg');
                    }

                    $employee->save();
                    $num_inserts ++;

                    array_push($inserts, $cells[$FIRST_NAME] . " " . $cells[$LAST_NAME]);
                }
            }
        }

        // DELETE
        $result = json_encode(['Number of Inserts' => $num_inserts, 'Inserted' => $inserts, 'Number Of Updates' => $num_updates, 'Updated' => $updates, 'Invalid Entry' => $invalid_emails]);

        $bytes_written = File::put('./storage/logs/cron_masterlist.txt', $result);

        if ($bytes_written === false) {
            echo "Error writing to file";
        }
        return $result;
    }
    public function attrition(Request $request) {
        $path = "/var/www/uploads/attrition"; 

        $latest_ctime = 0;
        $latest_filename = '';    

        $d  = array_diff(scandir($path), array('.', '..'));
        foreach ($d as $entry) {
            $filepath = "{$path}/{$entry}";
            // could do also other checks than just checking whether the entry is a file
            if (is_file($filepath) && filectime($filepath) > $latest_ctime) {
                $latest_ctime = filectime($filepath);
                $latest_filename = $entry;
            }
        }
        $to_be_deleted = array();
        $num_inserts = 0;
        $num_updates = 0;
        $updates = array();
        $inserts = array();
        $employees = array();
        $invalid_emails = array();

        $COUNT = 0;
        $EID = 1;
        $FULLNAME = 2;
        $START_DATE = 3;
        $LAST_DATE = 4;
        $EMPLOYEE_TYPE = 5;
        $PARTICULARS = 6;
        $ALIAS = 7;
        $IT_STATUS = 8;
        $RA_STATUS = 9;

        $address = $filepath;
        
        //
        // Read the excel file
        //
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $address );

        $worksheet = $spreadsheet->getActiveSheet();
        $rows = [];

        // loop excel rows
        foreach ($worksheet->getRowIterator() AS $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE); 
            $cells = [];

            // storing the row value to $cells
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }

            // concat / add to array $rows : use $rows in the last part to get all rows
            $rows[] = $cells;

            if (count($rows) <= 2) {
               

            } else {

                if ($cells[$EID] && $cells[$EID] != "") {
                    $employee = User::where("eid", "LIKE", "%".trim($cells[$EID])."%");

                    if ($employee->count() == 1) {
                        $employee = $employee->first();
                        $num_updates ++;
                        
                        // display attrition employee name
                        array_push($to_be_deleted, ucwords(strtolower($cells[$FULLNAME])));

                        // store in attrition list
                        $attrition = EmployeeAttrition::where('employee_id', '=', '%' . $cells[$EID] . '%');
                            // check if employee exist in database
                            if ($attrition->count() == 0) {
                                
                                // create a record in employee attrition table
                                $newAttrition = new EmployeeAttrition;
                                $newAttrition->employee_id = $cells[$EID];
                                $newAttrition->employee_name = ucwords(strtolower($cells[$FULLNAME]));

                                $datetime = new DateTime();
                                // start date
                                if ($cells[$START_DATE] != "" && $cells[$START_DATE]) {
                                    if (is_numeric($cells[$START_DATE])) {
                                        $UNIX_DATE = ($cells[$START_DATE] - 25569) * 86400;
                                        $newAttrition->start_work_date = gmdate("Y-m-d H:i:s", (int) $UNIX_DATE);
                                    } else {
                                        $start_work_date = $datetime->createFromFormat('Y-m-d', $cells[$START_DATE])->format("Y-m-d H:i:s");
                                        $newAttrition->start_work_date = $start_work_date;
                                    }
                                }

                                // last date
                                if ($cells[$LAST_DATE] != "" && $cells[$LAST_DATE]) {
                                    if (is_numeric($cells[$LAST_DATE])) {
                                        $UNIX_DATE = ($cells[$LAST_DATE] - 25569) * 86400;
                                        $employee->last_work_date = gmdate("Y-m-d H:i:s", (int) $UNIX_DATE);
                                    }else{
                                         $last_work_date = $datetime->createFromFormat('Y-m-d', $cells[$LAST_DATE])->format("Y-m-d H:i:s");
                                         $newAttrition->last_work_date = $last_work_date;
                                    }
                                }

                                $newAttrition->employee_type = $cells[$EMPLOYEE_TYPE];
                                $newAttrition->particulars = $cells[$PARTICULARS];
                                $newAttrition->alias = $cells[$ALIAS];
                                $newAttrition->it_status = $cells[$IT_STATUS];
                                $newAttrition->ra_status = $cells[$RA_STATUS];
                                $newAttrition->save();

                                // change status to deleted ..  
                                $employee->status = 2;
                                $employee->save();
                            }

                        // delete employee from database
                        $employee->delete();
                    }
                }
            }
        }
        $result = json_encode(["deleted" => $to_be_deleted, "number_employees_deleted" =>  $num_updates]);


        $bytes_written = File::put('./storage/logs/cron_attrition.txt', $result);

        if ($bytes_written === false) {
            echo "Error writing to file";
        }
        return $result;
    }

    public function separatedEmployees(Request $request){

        $employees = User::separatedEmployees();
        if ($request->has('keyword') && $request->get('keyword') != "") {
                $employees = $employees->where(function($query) use($request)
                {
                    $query->where('first_name', 'LIKE', '%'.$request->get('keyword').'%')
                        ->orWhere('last_name', 'LIKE', '%'.$request->get('keyword').'%')
                        ->orWhere('middle_name', 'LIKE', '%'.$request->get('keyword').'%')
                        ->orWhere(DB::raw('CONCAT(first_name, " ", last_name)'), 'LIKE', '%'.$request->get('keyword').'%')
                        ->orWhere('email', 'LIKE', '%'.$request->get('keyword').'%')
                        ->orWhere('email2', 'LIKE', '%'.$request->get('keyword').'%')
                        ->orWhere('email3', 'LIKE', '%'.$request->get('keyword').'%')
                        ->orWhere('alias', 'LIKE', '%'.$request->get('keyword').'%')
                        ->orWhere('team_name', 'LIKE', '%'.$request->get('keyword').'%')
                        ->orWhere('dept_code', 'LIKE', '%'.$request->get('keyword').'%')
                        ->orWhere('position_name', 'LIKE', '%'.$request->get('keyword').'%')
                        ->orWhere('ext', 'LIKE', '%'.$request->get('keyword').'%');
                });
            }
        
        $employees = $employees->where('id', '<>', 1)->orderBy('last_name', 'ASC')->paginate(10);
        return view('employee.separated')->with('employees', $employees)->with('request', $request);
    }

    public function reactivate(Request $request, $id){
        return $this->model->reactivateEmployee($request, $id);
    }
    
    public function uploadInfo(){
        return view('employee.upload_info');
    }
    
    public function processUploadInfo(Request $req){
        $file = [];
        $file['Original Filename'] = $req->file('dump_file')->getClientOriginalName();
        $path = str_replace("public","",$_SERVER['DOCUMENT_ROOT']);
        $file['path'] = $req->file('dump_file')->storeAs('/media/uploads/xls', $file['Original Filename']);

        $spreadsheet =IOFactory::load($path.'/storage/app/'.$file['path']);
        $worksheet = $spreadsheet->getSheet(0);
        
        $list = [];
        $i = 1;
        foreach($worksheet->getRowIterator() as $row):
            if($i > 1):
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);
                $ctr = 1;
                $row = [
                    'flag'          => 0,
                    'id'            => 0, 
                    'status'        => '', 
                    'last_name'     => '', 
                    'first_name'    => '', 
                    'middle_name'   => '', 
                    'department'    => '', 
                    'dept_code'     => '',
                    'position'      => '',
                    'supervisor'    => '',
                    'sup_name'      => '',
                    'manager'       => '',
                    'mngr_name'     => '',
                    'email'         => '',
                    'start_date'    => '',
                    'prod_date'     => '',
                    'reg_date'      => '',
                    'employee_type' => 0,
                    'employee_cat'  => 0,
                    'account'       => 0,
                    'address'       => '',
                    'mobile'        => '',
                    'email2'        => '',
                    'sex'           => 0,
                    'civil_status'  => 0,
                    'birthday'      => '',
                    'home_address'  => '',
                    'emergency_con' => '',
                    'relationship'  => '',
                    'emergency_rel' => '',
                    'emer_contact'  => '',
                    'sss'           => '',
                    'phic'          => '',
                    'hdmf'          => '',
                    'tin'           => '',
                    'main'          => [],
                    'details'       => [],
                    'stat_main'     => 0,
                    'stat_details'  => 0,
                    'obj'           => [],
                ];
                $found = 0;
                foreach($cellIterator as $col):
                    if($ctr == 1):
                        $row['id'] = trim($col->getFormattedValue());
                        $eid = $row['id']; 
                        //$obj = User::where('eid',$row['id'])->get();
                        $obj = DB::select("select * from employee_info where eid = '$eid' and status = 1 and deleted_at is null");
                        if(count($obj) > 0){
                            $row['obj'] = $obj[0];
                            $found = 1;
                        }
                    endif;
                    
                    if($found > 0):
                        
                        $value = trim($col->getFormattedValue());
                    
                        if($ctr == 3):
                            $row['last_name']       = $value;
                        elseif($ctr ==4):
                            $row['first_name']      = $value;
                        elseif($ctr == 5):
                            $row['middle_name']     = $value;
                        elseif($ctr == 7):
                            $row['department']      = $value;
                        elseif($ctr == 8):
                            $row['dept_code']       = $value;
                        elseif($ctr == 9):
                            $row['position']        = $value;
                        elseif($ctr == 10):
                            $row['supervisor']      = $value;
                            $sup = DB::select("SELECT concat(first_name,' ',last_name) as head_name FROM `employee_info` where id = $value limit 1;");
                            if(count($sup) > 0):
                                $row['sup_name'] = $sup[0]->head_name;
                            endif;
                        elseif($ctr == 11):
                            $row['manager']         = $value;
                            $sup = DB::select("SELECT concat(first_name,' ',last_name) as head_name FROM `employee_info` where id = $value limit 1;");
                            if(count($sup) > 0):
                                $row['mngr_name'] = $sup[0]->head_name;
                            endif;
                        elseif($ctr == 12):
                            $row['email']           = $value;
                        elseif($ctr == 13):
                            $row['start_date']      = date("Y-m-d", strtotime($value));
                        elseif($ctr == 14):
                            $row['prod_date']       = date("Y-m-d", strtotime($value));
                        elseif($ctr == 15):
                            $row['reg_date']        = date("Y-m-d", strtotime($value));
                        elseif($ctr == 16):
                            $row['employee_type']   = $value;
                        elseif($ctr == 17):
                            $row['employee_cat']    = $value;
                        elseif($ctr == 18):
                            $row['account']         = $value;
                        elseif($ctr == 19):
                            $row['address']         = $value;
                        elseif($ctr == 20):
                            $row['mobile']          = $value;
                        elseif($ctr == 21):
                            $row['email2']          = $value;
                        elseif($ctr == 22):
                            $row['sex']             = $value;
                        elseif($ctr == 23):
                            $row['civil_status']    = $value;
                        elseif($ctr == 24):
                            $row['birthday']        = date("Y-m-d", strtotime($value));
                        elseif($ctr == 25):
                            $row['home_address']    = $value;
                        elseif($ctr == 26):
                            $row['emergency_con']   = $value;
                        elseif($ctr == 27):
                            $row['relationship']    = $value;
                        elseif($ctr == 28):
                            $row['emer_contact']    = $value;
                        elseif($ctr == 31):
                            $row['sss']             = $value;
                        elseif($ctr == 32):
                            $row['phic']            = $value;
                        elseif($ctr == 33):
                            $row['hdmf']            = $value;
                        elseif($ctr == 34):
                            $row['tin']             = $value;
                            $row['flag']            = 1;
                            $main = [
                                'first_name'            => $row['first_name'] ? $row['first_name'] : $row['obj']->first_name,
                                'middle_name'           => $row['middle_name'] ? $row['middle_name'] : $row['obj']->middle_name,
                                'last_name'             => $row['last_name'] ? $row['last_name'] : $row['obj']->last_name,
                                'email'                 => $row['email'] ? $row['email'] : $row['obj']->email,
                                'email2'                => $row['email2'] ? $row['email2'] : $row['obj']->email2,
                                'dept_code'             => $row['dept_code'] ? $row['dept_code'] : $row['obj']->dept_code,
                                'position_name'         => $row['position'] ? $row['position'] : $row['obj']->position_name,
                                'supervisor_id'         => $row['supervisor'] ? $row['supervisor'] : $row['obj']->supervisor_id,
                                'supervisor_name'       => $row['sup_name'] ? $row['sup_name'] : $row['obj']->supervisor_name,
                                'birth_date'            => $row['birthday'] ? $row['birthday'] : $row['obj']->birth_date,
                                'hired_date'            => $row['start_date'] ? $row['start_date'] : $row['obj']->hired_date,
                                'prod_date'             => $row['prod_date'] ? $row['prod_date'] : $row['obj']->prod_date,
                                'regularization_date'   => $row['reg_date'] ? $row['reg_date'] : $row['obj']->regularization_date,
                                'gender'                => $row['sex'] ? $row['sex'] : $row['obj']->gender,
                                'civil_status'          => $row['civil_status'] ? $row['civil_status'] : $row['obj']->civil_status,
                                'manager_id'            => $row['manager'] ? $row['manager'] : $row['obj']->manager_id,
                                'manager_name'          => $row['mngr_name'] ? $row['mngr_name'] : $row['obj']->manager_name, 
                                'account_id'            => $row['account'] ? $row['account'] : $row['obj']->account_id,
                                'sss'                   => $row['sss'] ? $row['sss'] : $row['obj']->sss,
                                'pagibig'               => $row['hdmf'] ? $row['hdmf'] : $row['obj']->pagibig,
                                'philhealth'            => $row['phic'] ? $row['phic'] : $row['obj']->philhealth,
                                'tin'                   => $row['tin'] ? $row['tin'] : $row['obj']->tin,
                                'address'               => $row['address'] ? $row['address'] : $row['obj']->address,
                                'contact_number'        => $row['mobile'] ? $row['mobile'] : $row['obj']->contact_number,
                                'is_regular'            => $row['employee_type'] ? $row['employee_type'] : $row['obj']->is_regular,
                                'employee_category'     => $row['employee_cat'] ? $row['employee_cat'] : $row['obj']->employee_category
                            ];
                            
                            $row['main']                = $main;
                            $row['stat_main']           = User::where('id',$row['obj']->id)->update($main);
                            $obj_det = EmployeeInfoDetails::where('employee_id',$row['obj']->id)->get();
                            
                            if(count($obj_det) > 0):
                                $det = $obj_det[0];
                                $details = [
                                    'town_address'      => $row['home_address'] ? $row['home_address'] : $det->town_address,
                                    'em_con_name'       => $row['emergency_con'] ? $row['emergency_con'] : $det->em_con_name,
                                    'em_con_num'        => $row['emer_contact'] ? $row['emer_contact'] : $det->em_con_num,
                                    'em_con_rel'        => $row['relationship'] ? $row['relationship'] : $det->em_con_rel
                                ];
                                $row['details']         = $details;
                                $row['stat_details']    = EmployeeInfoDetails::where('employee_id',$row['obj']->id)->update($details);
                            endif;
                            
                            array_push($list,$row);
                        endif;

                    endif;//end-if-found
                  
                    $ctr++;

                endforeach;
            endif;
            $i++;
        endforeach;
        
        return json_encode($list);
    }
}
