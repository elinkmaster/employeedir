<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use DateTime;
use App\EmployeeDepartment;
use App\ElinkAccount;
use App\ElinkDivision;
use App\User;
use App\EmployeeInfoDetails;
use App\EmployeeDependents;
use App\Employee;
use App\EmployeeAttrition;
use App\LeaveRequest;
use App\TempDetails;
use App\MovementsTransfer;
use App\Mail\UpdateInfo;
use App\Mail\ApproveInformation;
use App\AdtlLinkee;
use Response;
use File;
use DB;
use Illuminate\Support\Facades\Validator;

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
   
   public function processLinkees(Request $req){
	$validator = Validator::make($req->all(),[
            "adtl_linkee" => 'required'
        ]);

        if($validator->fails()){
            return ['data' => false];
        }

        $adtl_linker = $req->get('adtl_linker');
        $adtl_linkee = $req->get('adtl_linkee');
        $adtl_row = $req->get('adtl_row');
        $adtl_added_by = Auth::user()->id;
        $adtl_date_added = date("Y-m-d H:i:s");
       
        $exist = AdtlLinkee::where('adtl_linker', $adtl_linker)->where('adtl_linkee', $adtl_linkee)->first();

        if(!$exist){
            $linkee = AdtlLinkee::create([
                'adtl_linker' => $adtl_linker,
                'adtl_linkee' => $adtl_linkee,
                'adtl_row' => 1,
                'adtl_added_by' => $adtl_added_by,
                'adtl_date_added' => $adtl_date_added,
                'adtl_status' => 1
            ]);

            $linkerInformation = User::where('id', $linkee->adtl_linker)->first();
            $linkeeInformation = User::where('id', $linkee->adtl_linkee)->first();
	    $linkeeInformation->supervisor_id = $linkerInformation->id;
            $linkeeInformation->supervisor_name = $linkerInformation->fullname();
            $linkeeInformation->save();
        }
        return ['data' => $linkeeInformation ?? false];

       // $adtl_linker = $req->get('adtl_linker');
      //  $adtl_linkee = $req->get('adtl_linkee');
      //  $adtl_row = $req->get('adtl_row');
      //  $adtl_added_by = Auth::user()->id;
      //  $adtl_date_added = date("Y-m-d H:i:s");
       
       // $stat = DB::select("
         //   SELECT 
         //       adtl_id
         //   FROM
         //       adtl_linkees
         //   WHERE
          //      adtl_status = 1 AND adtl_linker = $adtl_linker
        //            AND adtl_row = $adtl_row;
      //  ");
        
       // if(count($stat) > 0){/* Update an existing linkee in a row */
         //   $id = $stat[0]->adtl_id;
         //   $status = DB::update("
          //      UPDATE 
          //          `adtl_linkees` 
           //     SET 
          //          `adtl_linker` = '$adtl_linker', `adtl_linkee` = '$adtl_linkee', `adtl_row` = '$adtl_row', `adtl_added_by` = '$adtl_added_by' 
          //      WHERE 
         //           `adtl_linkees`.`adtl_id` = $id;
        //    ");
       // }else{/* Create a Linkee in a row */
         //   $status = DB::insert("
         //       INSERT INTO  `adtl_linkees` 
         //           (`adtl_id`, `adtl_linker`, `adtl_linkee`, `adtl_row`, `adtl_added_by`, `adtl_date_added`, `adtl_status`) 
              //  VALUES 
            //        (NULL, '$adtl_linker', '$adtl_linkee', '$adtl_row', '$adtl_added_by', '$adtl_date_added', '1');
          //  ");
        //}
        
       // return ['status' => $status];
   }

  public function deleteLinkees(Request $request)
   {
        $validator = Validator::make($request->all(),[
            "adtl_linkee" => 'required'
        ]);

        if($validator->fails()){
            return ['data' => false];
        }

	$employee = User::where('id', $request->adtl_linkee)->first();
        $linkee = AdtlLinkee::where('adtl_linkee',$request->adtl_linkee)->where('adtl_linker', $request->adtl_linker)->first();

        if($linkee && $employee){
	    $employee->supervisor_id = 0;
            $employee->supervisor_name = '';
            $employee->save();
            DB::table('adtl_linkees')->where('adtl_id', $linkee->adtl_id)->delete();
            return ['data' => true];
        }

        return ['data' => false];
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {   
        $employee = User::find($id);
        $obj = EmployeeInfoDetails::where('employee_id',"=",$id)->get();
        $dep = EmployeeDependents::where('employee_num',$id)->where('status',1)->get();
        $linkees = $employee->getLinkees();
        if(count($obj) > 0):
            $obj = $obj[0];
        else:
            $obj = [
                'town_address' => '',
                'em_con_name' => '',
                'em_con_address' => '',
                'em_con_num' => '',
                'em_con_rel' => '',
                'resignation_date' => '',
                'avega_num' => ''
            ];
        endif;

        if (isset($employee)) {
            return view('employee.edit')
		->with('employee', $employee)
                ->with('supervisors', User::where('id', '<>', '1')->get())
                ->with('departments', EmployeeDepartment::all())
                ->with('accounts', ElinkAccount::all())
                ->with('details',$obj)
                ->with('dependents',$dep)
                ->with('positions', User::select('position_name')->groupBy('position_name')->get())
                ->with('linkees', $linkees)
                ->with('linkers',DB::select("select * from adtl_linkees where adtl_linker = $id and adtl_status = 1"));
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
    
    public function searchMovements(Request $request){
        return 
            MovementsTransfer::where('mv_employee_no',$request->get("emp_no"))
                ->leftJoin('employee_department','movements.mv_dept','=','employee_department.id')
                ->orderBy('mv_transfer_date', 'DESC')
                ->get();
    }
            
    public function saveMovements(Request $request){
        $obj = new MovementsTransfer();
        $obj->mv_employee_no    = $request->post('mv_employee_no');
        $obj->mv_dept           = $request->post('mv_dept');
        $obj->mv_position       = $request->post('mv_position');
        $obj->mv_transfer_date  = date('Y-m-d', strtotime($request->post('mv_transfer_date')));
        
        $sql = DB::select("
            SELECT 
                department_code
            FROM
                elink_employee_directory.employee_department
            WHERE
                id = $obj->mv_dept
            LIMIT 1;"
        );
        
        if(count($sql) > 0):
            $userarray = [
                'team_name'     => $request->post('dept_name'),
                'dept_code'     => $sql[0]->department_code,
                'position_name' => $obj->mv_position 
            ];
        endif;
        
        $affected = DB::table('employee_info')
              ->where('id', $request->post('mv_employee_no'))
              ->update($userarray);
        
        return ['status' => $obj->save(), 'User' => $affected];
    }
    
    public function downloadInactive(Request $request){
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        //$employees = User::allExceptSuperAdmin()->get();
        $employees = $this->model->download_inactive($request);
        $COUNT = 0;
        $EID = 1;
        $LAST_NAME = 2;
        $FIRST_NAME = 3;
        $MIDDLE_NAME = 4;
        $FULLNAME = 5;
        $ROLE = 6;
        $SUPERVISOR = 7;
        $MANAGER = 8;
        $DIVISION = 9;
        $DEPT = 10;
        $DEPT_CODE = 11;
        $ACCOUNT = 12;
        $EXT = 13;
        $ALIAS = 14;
        $PROD_DATE = 15;
        $STATUS = 16;
        $HIRED_DATE = 17;
        $WAVE = 18;
        $EMAIL = 19;
        $GENDER = 20;
        $BDAY = 21;
        $CITYADD = 22;
        $HOMEADD = 23;
        $CIVILSTAT = 24;
        $CONTACTNUM = 25;
        $INCASECON = 26;
        $INCASEREL = 27;
        $INCASERELCON = 28;
        $INCASERELADD = 29;
        $TIN = 30;
        $SSS = 31;
        $PHILHEALTH = 32;
        $HMDF = 33;
	$RESIGNATIONDATE = 34;

        $worksheet->getCell(getNameFromNumber($COUNT + 1) . 1 )->setValue('Count'); 
        $worksheet->getCell(getNameFromNumber($EID + 1) . 1 )->setValue('EID');
        $worksheet->getCell(getNameFromNumber($LAST_NAME + 1) . 1 )->setValue('Last Name');
        $worksheet->getCell(getNameFromNumber($FIRST_NAME + 1) . 1 )->setValue('First Name');
        $worksheet->getCell(getNameFromNumber($MIDDLE_NAME + 1) . 1 )->setValue('Middle Name');
        $worksheet->getCell(getNameFromNumber($FULLNAME + 1) . 1 )->setValue('Name');
        $worksheet->getCell(getNameFromNumber($ROLE + 1) . 1 )->setValue('Role');
        $worksheet->getCell(getNameFromNumber($SUPERVISOR + 1) . 1 )->setValue('Supervisor');
        $worksheet->getCell(getNameFromNumber($MANAGER + 1) . 1 )->setValue('Manager');
        $worksheet->getCell(getNameFromNumber($DIVISION + 1) . 1 )->setValue('Division');
        $worksheet->getCell(getNameFromNumber($DEPT + 1) . 1 )->setValue('Dept');
        $worksheet->getCell(getNameFromNumber($DEPT_CODE + 1) . 1 )->setValue('Dept Code');
        $worksheet->getCell(getNameFromNumber($ACCOUNT + 1) . 1 )->setValue('Account');
        $worksheet->getCell(getNameFromNumber($EXT + 1) . 1 )->setValue('EXT');
        $worksheet->getCell(getNameFromNumber($ALIAS + 1) . 1 )->setValue('Phone/Pen Names');
        $worksheet->getCell(getNameFromNumber($PROD_DATE + 1) . 1 )->setValue('Prod Date');
        $worksheet->getCell(getNameFromNumber($STATUS + 1) . 1 )->setValue('Status');
        $worksheet->getCell(getNameFromNumber($HIRED_DATE + 1) . 1 )->setValue('Hire Date');
        $worksheet->getCell(getNameFromNumber($WAVE + 1) . 1 )->setValue('Wave');
        $worksheet->getCell(getNameFromNumber($EMAIL + 1) . 1 )->setValue('Email');
        $worksheet->getCell(getNameFromNumber($GENDER + 1 ) . 1 )->setValue('Gender');
        $worksheet->getCell(getNameFromNumber($BDAY + 1) . 1 )->setValue('Bday');
        $worksheet->getCell(getNameFromNumber($CITYADD + 1) . 1 )->setValue('City Address');
        $worksheet->getCell(getNameFromNumber($HOMEADD + 1) . 1 )->setValue('Home Address');
        $worksheet->getCell(getNameFromNumber($CIVILSTAT + 1) . 1 )->setValue('Civil Stat');
        $worksheet->getCell(getNameFromNumber($CONTACTNUM + 1) . 1 )->setValue('Number');
        $worksheet->getCell(getNameFromNumber($INCASECON + 1) . 1 )->setValue('Contact Person');
        $worksheet->getCell(getNameFromNumber($INCASEREL + 1) . 1 )->setValue('Relationship');
        $worksheet->getCell(getNameFromNumber($INCASERELCON + 1) . 1 )->setValue('Number');
        $worksheet->getCell(getNameFromNumber($INCASERELADD + 1) . 1 )->setValue('Address');
        $worksheet->getCell(getNameFromNumber($TIN + 1) . 1 )->setValue('TIN');
        $worksheet->getCell(getNameFromNumber($SSS + 1) . 1 )->setValue('SSS');
        $worksheet->getCell(getNameFromNumber($PHILHEALTH + 1) . 1 )->setValue('Philhealth');
        $worksheet->getCell(getNameFromNumber($HMDF + 1) . 1 )->setValue('HMDF');
        $worksheet->getCell(getNameFromNumber($RESIGNATIONDATE + 1) . 1 )->setValue('Resignation Date');


        $worksheet->getColumnDimension(getNameFromNumber($COUNT + 1))->setWidth(7);
        $worksheet->getColumnDimension(getNameFromNumber($EID + 1))->setWidth(20);
        $worksheet->getColumnDimension(getNameFromNumber($EXT + 1))->setWidth(5);
        $worksheet->getColumnDimension(getNameFromNumber($ALIAS + 1))->setWidth(30);
        $worksheet->getColumnDimension(getNameFromNumber($LAST_NAME + 1))->setWidth(20);
        $worksheet->getColumnDimension(getNameFromNumber($FIRST_NAME + 1))->setWidth(20);
        $worksheet->getColumnDimension(getNameFromNumber($MIDDLE_NAME + 1))->setWidth(20);
        $worksheet->getColumnDimension(getNameFromNumber($FULLNAME + 1))->setWidth(40);
        $worksheet->getColumnDimension(getNameFromNumber($SUPERVISOR + 1))->setWidth(30);
        $worksheet->getColumnDimension(getNameFromNumber($MANAGER + 1))->setWidth(30);
        $worksheet->getColumnDimension(getNameFromNumber($DEPT + 1))->setWidth(25);
        $worksheet->getColumnDimension(getNameFromNumber($DEPT_CODE + 1))->setWidth(15);
        $worksheet->getColumnDimension(getNameFromNumber($DIVISION + 1))->setWidth(15);
        $worksheet->getColumnDimension(getNameFromNumber($ROLE + 1))->setWidth(30);
        $worksheet->getColumnDimension(getNameFromNumber($ACCOUNT + 1))->setWidth(15);
        $worksheet->getColumnDimension(getNameFromNumber($PROD_DATE + 1))->setWidth(15);
        $worksheet->getColumnDimension(getNameFromNumber($STATUS + 1))->setWidth(10);
        $worksheet->getColumnDimension(getNameFromNumber($HIRED_DATE + 1))->setWidth(10);
        $worksheet->getColumnDimension(getNameFromNumber($WAVE + 1))->setWidth(8);
        $worksheet->getColumnDimension(getNameFromNumber($EMAIL + 1))->setWidth(30);
        $worksheet->getColumnDimension(getNameFromNumber($GENDER + 1))->setWidth(10);
        $worksheet->getColumnDimension(getNameFromNumber($BDAY + 1))->setWidth(10);

        $row = 2;
        foreach ($employees as $index => $value) {

            $worksheet->getCell(getNameFromNumber($COUNT + 1) . $row )->setValue($row-1);
            $worksheet->getCell(getNameFromNumber($EID + 1) . $row )->setValue($value->eid);
            $worksheet->getCell(getNameFromNumber($LAST_NAME + 1) . $row )->setValue($value->last_name);
            $worksheet->getCell(getNameFromNumber($FIRST_NAME + 1) . $row )->setValue($value->first_name);
            $worksheet->getCell(getNameFromNumber($MIDDLE_NAME + 1) . $row )->setValue($value->middle_name);
            $worksheet->getCell(getNameFromNumber($FULLNAME + 1) . $row )->setValue($value->first_name." ".$value->last_name);
            $worksheet->getCell(getNameFromNumber($ROLE + 1) . $row )->setValue($value->position_name);
            $worksheet->getCell(getNameFromNumber($SUPERVISOR + 1) . $row )->setValue($value->supervisor_name);
            $worksheet->getCell(getNameFromNumber($MANAGER + 1) . $row )->setValue($value->manager_name);
            $worksheet->getCell(getNameFromNumber($DIVISION + 1) . $row )->setValue($value->division_name);
            $worksheet->getCell(getNameFromNumber($DEPT + 1) . $row )->setValue($value->team_name);
            $worksheet->getCell(getNameFromNumber($DEPT_CODE + 1) . $row )->setValue($value->dept_code);
            
            

            $account = ElinkAccount::find($value->account_id);
            if ($account) 
            {
                $worksheet->getCell(getNameFromNumber($ACCOUNT + 1) . $row )->setValue($account->account_name);
            }
            
            $civil_status = $value->civil_status == 1 ? "Single" : (
                $value->civil_status == 2 ? "Married" : (
                    $value->civil_status == 3 ? "Separated" : (
                        $value->civil_status == 4 ? "Anulled" : "Divorced"
                    )
                )
            );
            $worksheet->getCell(getNameFromNumber($EXT + 1) . $row )->setValue($value->ext);
            $worksheet->getCell(getNameFromNumber($ALIAS + 1) . $row )->setValue($value->alias);
            $worksheet->getCell(getNameFromNumber($PROD_DATE + 1) . $row )->setValue(date("F d, Y", strtotime($value->prod_date)));
            $worksheet->getCell(getNameFromNumber($STATUS + 1) . $row )->setValue($value->deleted_at == NULL && $value->status == 1 ? 'Active' : 'Inactive');
            $worksheet->getCell(getNameFromNumber($HIRED_DATE + 1) . $row )->setValue(date("F d, Y", strtotime($value->hired_date)));
            $worksheet->getCell(getNameFromNumber($WAVE + 1) . $row )->setValue($value->wave);
            $worksheet->getCell(getNameFromNumber($EMAIL + 1) . $row )->setValue($value->email);
            $worksheet->getCell(getNameFromNumber($GENDER + 1) . $row )->setValue(genderStringValue($value->gender));
            $worksheet->getCell(getNameFromNumber($BDAY + 1) . $row )->setValue(date("F d, Y", strtotime($value->birth_date)));
            $worksheet->getCell(getNameFromNumber($CITYADD + 1) . $row )->setValue($value->address);
            $worksheet->getCell(getNameFromNumber($HOMEADD + 1) . $row )->setValue($value->town_address);
            $worksheet->getCell(getNameFromNumber($CIVILSTAT + 1) . $row )->setValue($civil_status);
            $worksheet->getCell(getNameFromNumber($CONTACTNUM + 1) . $row )->setValue($value->contact_number);
            $worksheet->getCell(getNameFromNumber($INCASECON + 1) . $row )->setValue($value->em_con_name);
            $worksheet->getCell(getNameFromNumber($INCASEREL + 1) . $row )->setValue($value->em_con_rel);
            $worksheet->getCell(getNameFromNumber($INCASERELCON + 1) . $row )->setValue($value->em_con_num);
            $worksheet->getCell(getNameFromNumber($INCASERELADD + 1) . $row )->setValue($value->em_con_address);
            $worksheet->getCell(getNameFromNumber($TIN + 1) . $row )->setValue($value->tin);
            $worksheet->getCell(getNameFromNumber($SSS + 1) . $row )->setValue($value->sss);
            $worksheet->getCell(getNameFromNumber($PHILHEALTH + 1) . $row )->setValue($value->philhealth);
            $worksheet->getCell(getNameFromNumber($HMDF + 1) . $row )->setValue($value->pagibig);
            $worksheet->getCell(getNameFromNumber($RESIGNATIONDATE + 1) . $row )->setValue($value->deleted_at);


            $row++;
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        $timestamp = date('m_d_Y_G_i');
        $writer->save("./public/excel/report/inactives-". $timestamp . ".xlsx");

        $file_name = 'inactives-'.$timestamp.'.xlsx';

        return redirect('public/excel/report/' . $file_name);
        
    }
    
    public function downloadFilter(Request $request){
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        //$employees = User::allExceptSuperAdmin()->get();
        $employees = $this->model->download_filter($request);
        $COUNT = 0;
        $EID = 1;
        $LAST_NAME = 2;
        $FIRST_NAME = 3;
        $MIDDLE_NAME = 4;
        $FULLNAME = 5;
        $ROLE = 6;
        $SUPERVISOR = 7;
        $MANAGER = 8;
        $DIVISION = 9;
        $DEPT = 10;
        $DEPT_CODE = 11;
        $ACCOUNT = 12;
        $EXT = 13;
        $ALIAS = 14;
        $PROD_DATE = 15;
        $STATUS = 16;
        $HIRED_DATE = 17;
        $WAVE = 18;
        $EMAIL = 19;
        $GENDER = 20;
        $BDAY = 21;
        $CITYADD = 22;
        $HOMEADD = 23;
        $CIVILSTAT = 24;
        $CONTACTNUM = 25;
        $INCASECON = 26;
        $INCASEREL = 27;
        $INCASERELCON = 28;
        $INCASERELADD = 29;
        $TIN = 30;
        $SSS = 31;
        $PHILHEALTH = 32;
        $HMDF = 33;

        $worksheet->getCell(getNameFromNumber($COUNT + 1) . 1 )->setValue('Count'); 
        $worksheet->getCell(getNameFromNumber($EID + 1) . 1 )->setValue('EID');
        $worksheet->getCell(getNameFromNumber($LAST_NAME + 1) . 1 )->setValue('Last Name');
        $worksheet->getCell(getNameFromNumber($FIRST_NAME + 1) . 1 )->setValue('First Name');
        $worksheet->getCell(getNameFromNumber($MIDDLE_NAME + 1) . 1 )->setValue('Middle Name');
        $worksheet->getCell(getNameFromNumber($FULLNAME + 1) . 1 )->setValue('Name');
        $worksheet->getCell(getNameFromNumber($ROLE + 1) . 1 )->setValue('Role');
        $worksheet->getCell(getNameFromNumber($SUPERVISOR + 1) . 1 )->setValue('Supervisor');
        $worksheet->getCell(getNameFromNumber($MANAGER + 1) . 1 )->setValue('Manager');
        $worksheet->getCell(getNameFromNumber($DIVISION + 1) . 1 )->setValue('Division');
        $worksheet->getCell(getNameFromNumber($DEPT + 1) . 1 )->setValue('Dept');
        $worksheet->getCell(getNameFromNumber($DEPT_CODE + 1) . 1 )->setValue('Dept Code');
        $worksheet->getCell(getNameFromNumber($ACCOUNT + 1) . 1 )->setValue('Account');
        $worksheet->getCell(getNameFromNumber($EXT + 1) . 1 )->setValue('EXT');
        $worksheet->getCell(getNameFromNumber($ALIAS + 1) . 1 )->setValue('Phone/Pen Names');
        $worksheet->getCell(getNameFromNumber($PROD_DATE + 1) . 1 )->setValue('Prod Date');
        $worksheet->getCell(getNameFromNumber($STATUS + 1) . 1 )->setValue('Status');
        $worksheet->getCell(getNameFromNumber($HIRED_DATE + 1) . 1 )->setValue('Hire Date');
        $worksheet->getCell(getNameFromNumber($WAVE + 1) . 1 )->setValue('Wave');
        $worksheet->getCell(getNameFromNumber($EMAIL + 1) . 1 )->setValue('Email');
        $worksheet->getCell(getNameFromNumber($GENDER + 1 ) . 1 )->setValue('Gender');
        $worksheet->getCell(getNameFromNumber($BDAY + 1) . 1 )->setValue('Bday');
        $worksheet->getCell(getNameFromNumber($CITYADD + 1) . 1 )->setValue('City Address');
        $worksheet->getCell(getNameFromNumber($HOMEADD + 1) . 1 )->setValue('Home Address');
        $worksheet->getCell(getNameFromNumber($CIVILSTAT + 1) . 1 )->setValue('Civil Stat');
        $worksheet->getCell(getNameFromNumber($CONTACTNUM + 1) . 1 )->setValue('Number');
        $worksheet->getCell(getNameFromNumber($INCASECON + 1) . 1 )->setValue('Contact Person');
        $worksheet->getCell(getNameFromNumber($INCASEREL + 1) . 1 )->setValue('Relationship');
        $worksheet->getCell(getNameFromNumber($INCASERELCON + 1) . 1 )->setValue('Number');
        $worksheet->getCell(getNameFromNumber($INCASERELADD + 1) . 1 )->setValue('Address');
        $worksheet->getCell(getNameFromNumber($TIN + 1) . 1 )->setValue('TIN');
        $worksheet->getCell(getNameFromNumber($SSS + 1) . 1 )->setValue('SSS');
        $worksheet->getCell(getNameFromNumber($PHILHEALTH + 1) . 1 )->setValue('Philhealth');
        $worksheet->getCell(getNameFromNumber($HMDF + 1) . 1 )->setValue('HMDF');

        $worksheet->getColumnDimension(getNameFromNumber($COUNT + 1))->setWidth(7);
        $worksheet->getColumnDimension(getNameFromNumber($EID + 1))->setWidth(20);
        $worksheet->getColumnDimension(getNameFromNumber($EXT + 1))->setWidth(5);
        $worksheet->getColumnDimension(getNameFromNumber($ALIAS + 1))->setWidth(30);
        $worksheet->getColumnDimension(getNameFromNumber($LAST_NAME + 1))->setWidth(20);
        $worksheet->getColumnDimension(getNameFromNumber($FIRST_NAME + 1))->setWidth(20);
        $worksheet->getColumnDimension(getNameFromNumber($MIDDLE_NAME + 1))->setWidth(20);
        $worksheet->getColumnDimension(getNameFromNumber($FULLNAME + 1))->setWidth(40);
        $worksheet->getColumnDimension(getNameFromNumber($SUPERVISOR + 1))->setWidth(30);
        $worksheet->getColumnDimension(getNameFromNumber($MANAGER + 1))->setWidth(30);
        $worksheet->getColumnDimension(getNameFromNumber($DEPT + 1))->setWidth(25);
        $worksheet->getColumnDimension(getNameFromNumber($DEPT_CODE + 1))->setWidth(15);
        $worksheet->getColumnDimension(getNameFromNumber($DIVISION + 1))->setWidth(15);
        $worksheet->getColumnDimension(getNameFromNumber($ROLE + 1))->setWidth(30);
        $worksheet->getColumnDimension(getNameFromNumber($ACCOUNT + 1))->setWidth(15);
        $worksheet->getColumnDimension(getNameFromNumber($PROD_DATE + 1))->setWidth(15);
        $worksheet->getColumnDimension(getNameFromNumber($STATUS + 1))->setWidth(10);
        $worksheet->getColumnDimension(getNameFromNumber($HIRED_DATE + 1))->setWidth(10);
        $worksheet->getColumnDimension(getNameFromNumber($WAVE + 1))->setWidth(8);
        $worksheet->getColumnDimension(getNameFromNumber($EMAIL + 1))->setWidth(30);
        $worksheet->getColumnDimension(getNameFromNumber($GENDER + 1))->setWidth(10);
        $worksheet->getColumnDimension(getNameFromNumber($BDAY + 1))->setWidth(10);

        $row = 2;
        foreach ($employees as $index => $value) {

            $worksheet->getCell(getNameFromNumber($COUNT + 1) . $row )->setValue($row-1);
            $worksheet->getCell(getNameFromNumber($EID + 1) . $row )->setValue($value->eid);
            $worksheet->getCell(getNameFromNumber($LAST_NAME + 1) . $row )->setValue($value->last_name);
            $worksheet->getCell(getNameFromNumber($FIRST_NAME + 1) . $row )->setValue($value->first_name);
            $worksheet->getCell(getNameFromNumber($MIDDLE_NAME + 1) . $row )->setValue($value->middle_name);
            $worksheet->getCell(getNameFromNumber($FULLNAME + 1) . $row )->setValue($value->first_name." ".$value->last_name);
            $worksheet->getCell(getNameFromNumber($ROLE + 1) . $row )->setValue($value->position_name);
            $worksheet->getCell(getNameFromNumber($SUPERVISOR + 1) . $row )->setValue($value->supervisor_name);
            $worksheet->getCell(getNameFromNumber($MANAGER + 1) . $row )->setValue($value->manager_name);
            $worksheet->getCell(getNameFromNumber($DIVISION + 1) . $row )->setValue($value->division_name);
            $worksheet->getCell(getNameFromNumber($DEPT + 1) . $row )->setValue($value->team_name);
            $worksheet->getCell(getNameFromNumber($DEPT_CODE + 1) . $row )->setValue($value->dept_code);
            
            

            $account = ElinkAccount::find($value->account_id);
            if ($account) 
            {
                $worksheet->getCell(getNameFromNumber($ACCOUNT + 1) . $row )->setValue($account->account_name);
            }
            
            $civil_status = $value->civil_status == 1 ? "Single" : (
                $value->civil_status == 2 ? "Married" : (
                    $value->civil_status == 3 ? "Separated" : (
                        $value->civil_status == 4 ? "Anulled" : "Divorced"
                    )
                )
            );
            $worksheet->getCell(getNameFromNumber($EXT + 1) . $row )->setValue($value->ext);
            $worksheet->getCell(getNameFromNumber($ALIAS + 1) . $row )->setValue($value->alias);
            $worksheet->getCell(getNameFromNumber($PROD_DATE + 1) . $row )->setValue(date("F d, Y", strtotime($value->prod_date)));
            $worksheet->getCell(getNameFromNumber($STATUS + 1) . $row )->setValue($value->deleted_at == NULL && $value->status == 1 ? 'Active' : 'Inactive');
            $worksheet->getCell(getNameFromNumber($HIRED_DATE + 1) . $row )->setValue(date("F d, Y", strtotime($value->hired_date)));
            $worksheet->getCell(getNameFromNumber($WAVE + 1) . $row )->setValue($value->wave);
            $worksheet->getCell(getNameFromNumber($EMAIL + 1) . $row )->setValue($value->email);
            $worksheet->getCell(getNameFromNumber($GENDER + 1) . $row )->setValue(genderStringValue($value->gender));
            $worksheet->getCell(getNameFromNumber($BDAY + 1) . $row )->setValue(date("F d, Y", strtotime($value->birth_date)));
            $worksheet->getCell(getNameFromNumber($CITYADD + 1) . $row )->setValue($value->address);
            $worksheet->getCell(getNameFromNumber($HOMEADD + 1) . $row )->setValue($value->town_address);
            $worksheet->getCell(getNameFromNumber($CIVILSTAT + 1) . $row )->setValue($civil_status);
            $worksheet->getCell(getNameFromNumber($CONTACTNUM + 1) . $row )->setValue($value->contact_number);
            $worksheet->getCell(getNameFromNumber($INCASECON + 1) . $row )->setValue($value->em_con_name);
            $worksheet->getCell(getNameFromNumber($INCASEREL + 1) . $row )->setValue($value->em_con_rel);
            $worksheet->getCell(getNameFromNumber($INCASERELCON + 1) . $row )->setValue($value->em_con_num);
            $worksheet->getCell(getNameFromNumber($INCASERELADD + 1) . $row )->setValue($value->em_con_address);
            $worksheet->getCell(getNameFromNumber($TIN + 1) . $row )->setValue($value->tin);
            $worksheet->getCell(getNameFromNumber($SSS + 1) . $row )->setValue($value->sss);
            $worksheet->getCell(getNameFromNumber($PHILHEALTH + 1) . $row )->setValue($value->philhealth);
            $worksheet->getCell(getNameFromNumber($HMDF + 1) . $row )->setValue($value->pagibig);
            

            $row++;
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        $timestamp = date('m_d_Y_G_i');
        $writer->save("./public/excel/report/report". $timestamp . ".xlsx");

        $file_name = 'report'.$timestamp.'.xlsx';

        return redirect('public/excel/report/' . $file_name);
    }

    public function profile (Request $request, $id)
    {
        return view('auth.profile.view')->with('employee', User::withTrashed()->find($id));
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
             $dep = EmployeeDependents::where('employee_num',$id)->where('status',1)->get();
	    $linkees = $employee->getLinkees();
            if(count($obj) > 0)
                $details = $obj[0];
            else
                $details = (object)[
                    'town_address'      => '',
                    'em_con_name'       => '',
                    'em_con_rel'        => '',
                    'em_con_num'        => ''
                ];
            if(Auth::user()->isAdmin() || Auth::user()->id == $id)
                return view('employee.view-admin')           
                    ->with('employee', $employee)
		    ->with('linkees', $linkees)
                    ->with('supervisors', User::all())
                    ->with('departments', EmployeeDepartment::all())
                    ->with('accounts', ElinkAccount::all())
                    ->with('details',$details)
                    ->with('dependents',$dep);
            else
                return view('employee.view')->with('employee', $employee)->with('employee_details',$details);
        } else {
            return abort(404);
        }
    }

    public function myprofile(Request $request)
    {
        /*
        if (Auth::user()->isAdmin()) {
            return view('employee.view')->with('employee', Auth::user());
        }
        return view('auth.profile.view')->with('employee', Auth::user())
            ->with('my_requests', LeaveRequest::where('filed_by_id', Auth::user()->id)->get());
         * 
         */
            $employee = Auth::user();
            $id = Auth::user()->id;
            $obj = EmployeeInfoDetails::where('employee_id',$id)->get();
             $dep = EmployeeDependents::where('employee_num',$id)->where('status',1)->get();
            if(count($obj) > 0)
                $details = $obj[0];
            else
                $details = (object)[
                    'town_address'      => '',
                    'em_con_name'       => '',
                    'em_con_rel'        => '',
                    'em_con_num'        => ''
                ];
        return view('employee.view-admin')           
            ->with('employee', $employee)
            ->with('supervisors', User::all())
            ->with('departments', EmployeeDepartment::all())
            ->with('accounts', ElinkAccount::all())
            ->with('details',$details)
            ->with('linkees', $employee->getLinkees())
            ->with('dependents',$dep);
    }
    
    /*
        web-route: /update-profile
        Form to update selected personal information by the user
        Changes are subject by Supervisor or Manager recommendation.
        After recommendation, HR will approve the request for posting.
    */
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
    
    /*
    For updating the employee profile, selected fields of the employee.
    The supervisor or manager will recommend to HR to approve the recommended change.
    Email Notification will be sent to the HR once recommended.
    */
    public function saveProfile(Request $request){
    
        $obj = [
            'id'                    => 0,
            'name'                  => Auth::user()->first_name." ".Auth::user()->last_name,
            'employee_id'           => $request->post('employee_id'),
            'changedate'            => date('Y-m-d H:i:s'),
            'o_current_address'     => $request->post('o_current_address'),
            'n_current_address'     => $request->post('n_current_address'),
            'o_contact_num'         => $request->post('o_contact_num'),
            'n_contact_num'         => $request->post('n_contact_num'),
            'o_emergency'           => $request->post('o_emergency'),
            'n_emergency'           => $request->post('n_emergency'),
            'o_emergency_num'       => $request->post('o_emergency_num'),
            'n_emergency_num'       => $request->post('n_emergency_num'),
            'o_rel'                 => $request->post('o_rel'),
            'n_rel'                 => $request->post('n_rel'),
            'o_marital_stat'        => $request->post('o_marital_stat'),
            'n_marital_stat'        => $request->post('n_marital_stat'),
            'status'                => 2 
            /*
             * This status change is for HR to directly approve the change even without supervisor recommendation.
             * March 14, 2020
             */
        ];
        
        $temp = new TempDetails();
        $temp->employee_id              = $obj['employee_id'];
        $temp->changedate               = $obj['changedate'];
        $temp->o_current_address        = $obj['o_current_address'];
        $temp->n_current_address        = $obj['n_current_address'];
        $temp->o_contact_num            = $obj['o_contact_num'];
        $temp->n_contact_num            = $obj['n_contact_num'];
        $temp->o_emergency              = $obj['o_emergency'];
        $temp->n_emergency              = $obj['n_emergency'];
        $temp->o_emergency_num          = $obj['o_emergency_num'];
        $temp->n_emergency_num          = $obj['n_emergency_num'];
        $temp->o_rel                    = $obj['o_rel'];
        $temp->n_rel                    = $obj['n_rel'];
        $temp->o_marital_stat           = $obj['o_marital_stat'];
        $temp->n_marital_stat           = $obj['n_marital_stat'];
        $temp->status                   = $obj['status'];
        $temp->save();
        
        $obj['id'] = $temp->id;
        $sup_obj = User::find(Auth::user()->supervisor_id);
        $mngr_obj = User::find(Auth::user()->manager_id);
        $emails = [];
        if($sup_obj && isset($sup_obj->email))
            array_push($emails,$sup_obj->email);
        if($mngr_obj && isset($mngr_obj->email))
            array_push($emails,$mngr_obj->email);
        array_push($emails,"hrd@elink.com.ph");
        Mail::to($emails)->queue(new UpdateInfo($obj));
        
        return view('employee.confirm');
    }
    
    /*
    Used by the HR to approve the recommended change profile from the
    Supervisor or the Manager. Final process of the update profile by the staff.
    
    This function is part of the update profile, selected fields only by the 
    user or staff.
    
    1 = Update Profile Request
    2 = Successfully Recommended
    3 = Successfully Approved
    */
    public function approveChangeProfile(Request $request, $id){
        $obj = TempDetails::find($id);
        $user = User::find($obj->employee_id);
        $first_name = $user->first_name;
        $last_name = $user->last_name;
        $info = "You have not enough rights to approve the recommended request of ".$first_name." ".$last_name.".";
        
        if(Auth::user()->id == 2810):
            $info = "Recommended Update Profile Request Successfully Approved.";
            $obj = TempDetails::find($id);
            $obj->status = 3;
            $obj->save();
    
            $user_obj = User::find($obj->employee_id);
                $user_obj->civil_status     = $obj->n_marital_stat;
                $user_obj->contact_number   = $obj->n_contact_num;
                $user_obj->address          = $obj->n_current_address;
            $user_obj->save();
            
            $details_obj = EmployeeInfoDetails::where("employee_id",$obj->employee_id)->get();
            if(count($details_obj) > 0):
                $edit_obj = EmployeeInfoDetails::find($details_obj[0]->id);
                    $edit_obj->em_con_name      = $obj->n_emergency;
                    $edit_obj->em_con_rel       = $obj->n_rel;
                    $edit_obj->em_con_num       = $obj->n_emergency_num;
                $edit_obj->save();
            else:
                $details = new EmployeeInfoDetails();
                    $details->employee_id   = $obj->employee_id;
                    $details->em_con_name   = $obj->n_emergency;
                    $details->em_con_rel    = $obj->n_rel;
                    $details->em_con_num    = $obj->n_emergency_num;
                    $details->status        = 1;
                $details->save();
            endif;
            
            Mail::to("hrd@elink.com.ph")->queue(new ApproveInformation($obj_det));
        endif;
            
      
        return $info;
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
    
    /*
    Supervisor or Manager recommends the update profile request by the user
    to the HR.
    
    Email will be sent to the HR notifying that the request is being recommended
    for approval. 
    */
    public function recommendApproval($id){
        $obj = TempDetails::find($id);
        $user = User::find($obj->employee_id);
        $first_name = $user->first_name;
        $last_name = $user->last_name;
        $info = "You have not enough rights to validate this request.";
        
        if(Auth::user()->id == $user->supervisor_id || Auth::user()->id == $user->manager_id):
            $info = "Request Successfully Validated. Waiting for HR for the Final Approval";
            $obj = TempDetails::find($id);
            $obj->status = 2;
            $obj->save();
            $obj_det = [
                'id'                    => $id,
                'name'                  => $first_name." ".$last_name,
                'employee_id'           => $obj->employee_id,
                'o_current_address'     => $obj->o_current_address,
                'n_current_address'     => $obj->n_current_address,
                'o_contact_num'         => $obj->o_contact_num,
                'n_contact_num'         => $obj->n_contact_num,
                'o_emergency'           => $obj->o_emergency,
                'n_emergency'           => $obj->n_emergency,
                'o_emergency_num'       => $obj->o_emergency_num,
                'n_emergency_num'       => $obj->n_emergency_num,
                'o_rel'                 => $obj->o_rel,
                'n_rel'                 => $obj->n_rel,
                'o_marital_stat'        => $obj->o_marital_stat,
                'n_marital_stat'        => $obj->n_marital_stat,
            ];
            Mail::to("hrd@elink.com.ph")->queue(new ApproveInformation($obj_det));
        endif;
            
      
        return $info;
    }
    
    public function uploadInfo(){
        return view('employee.upload_info');
    }
    
    public function viewRecord($id){
        $obj =DB::table('employee_info')
            ->leftJoin('employee_info_details','employee_info.id','=','employee_info_details.employee_id')
            ->get();
        return json_encode(['obj' => $obj]);
    }
    
    public function formAvega(){
        return view('employee.upload_avega');
    }
    
    public function processAvega(Request $req){
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
             
                foreach($cellIterator as $col):
                    if($ctr == 1):
                        $eid =  trim($col->getFormattedValue());
                        $obj = DB::select("select id from employee_info where eid = '$eid' limit 1");
                        $id = count($obj) > 0 ? $obj[0]->id : 0;
                    endif;
                    if( $ctr == 2 ):
                        $account = trim($col->getFormattedValue());
                    endif;
                    if( $ctr == 3 ):
                        $last = trim($col->getFormattedValue());
                    endif;
                    if( $ctr == 4 ):
                        $first = trim($col->getFormattedValue());
                        $name = $first." ".$last;
                        $row = [
                            'id'            => $id,
                            'eid'           => $eid,
                            'account'       => $account,
                            'name'          => $name,
                            'update_status' => 0
                        ];
                        array_push($list,$row);
                        if($row['eid'] && $row['account'] && $id):
                            $row['update_status'] = DB::update("update employee_info_details set avega_num = '$account' where employee_id = $id limit 1");
                        endif;
                    endif;
                    $ctr++;
                endforeach;
                

            endif;
            $i++;
        endforeach;
        
        return ['FILE' => $file, 'LIST' => $list];
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
