<?php 
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\User;
use App\ElinkAccount;
use App\ElinkDivision;
use App\EmployeeDepartment;
use DB;

class ExportImportRepository implements RepositoryInterface
{
    // model property on class instances
    protected $model;

    // Constructor to bind model to repo
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    // Get all instances of model
    public function all()
    {
        return $this->model->all();
    }

    // create a new record in the database
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    // update record in the database
    public function update(array $data, $id)
    {
        $record = $this->find($id);
        return $record->update($data);
    }

    // remove record from the database
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    // show the record with the given id
    public function show($id)
    {
        return $this->model->findOrFail($id);
    }

    // Get the associated model
    public function getModel()
    {
        return $this->model;
    }

    // Set the associated model
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    // Eager load database relationships
    public function with($relations)
    {
        return $this->model->with($relations);
    }

    // route : employees/import
    // IMPORT SAVE 
    //
    public function importsave(Request $request)
    {
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
                $emp = User::withTrashed()->where('eid','LIKE','%'.$cells[$EID].'%');

                if (!$cells[$EMAIL] || !filter_var($cells[$EMAIL], FILTER_VALIDATE_EMAIL)) {
                    // list invalid email
                    if($cells[$EMAIL] != "" && $cells[$EMAIL] != null){
                        array_push($invalid_emails, $cells[$FIRST_NAME]);
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
                        array_push($updates, $cells[$EMAIL]);
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

                    array_push($inserts, $employee);
                }
            }
        }
        return view('employee.import')
                ->with('num_inserts', $num_inserts)
                ->with('inserts', $inserts)
                ->with('invalid_emails', $invalid_emails);
    }
    
    public function exportdownload() {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        //$employees = User::allExceptSuperAdmin()->get();
        $employees = DB::table('employee_info')
            ->leftJoin('employee_info_details','employee_info.id','=','employee_info_details.employee_id')
            ->where('employee_info.eid','!=','--')
            ->where('deleted_at',NULL)
            ->get();
        $COUNT = 0;
        $EID = 1;
        $LAST_NAME = 2;
        $FIRST_NAME = 3;
        $FULLNAME = 4;
        $ROLE = 5;
        $SUPERVISOR = 6;
        $MANAGER = 7;
        $DIVISION = 8;
        $DEPT = 9;
        $DEPT_CODE = 10;
        $ACCOUNT = 11;
        $EXT = 12;
        $ALIAS = 13;
        $PROD_DATE = 14;
        $STATUS = 15;
        $HIRED_DATE = 16;
        $WAVE = 17;
        $EMAIL = 18;
        $GENDER = 19;
        $BDAY = 20;
        $CITYADD = 21;
        $HOMEADD = 22;
        $CIVILSTAT = 23;
        $CONTACTNUM = 24;
        $INCASECON = 25;
        $INCASEREL = 26;
        $INCASERELCON = 27;
        $INCASERELADD = 28;
        $SSS = 29;
        $PHILHEALTH = 30;
        $HMDF = 31;

        $worksheet->getCell(getNameFromNumber($COUNT + 1) . 1 )->setValue('Count'); 
        $worksheet->getCell(getNameFromNumber($EID + 1) . 1 )->setValue('EID');
        $worksheet->getCell(getNameFromNumber($LAST_NAME + 1) . 1 )->setValue('Last Name');
        $worksheet->getCell(getNameFromNumber($FIRST_NAME + 1) . 1 )->setValue('First Name');
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
        $worksheet->getCell(getNameFromNumber($SSS + 1) . 1 )->setValue('SSS');
        $worksheet->getCell(getNameFromNumber($PHILHEALTH + 1) . 1 )->setValue('Philhealth');
        $worksheet->getCell(getNameFromNumber($HMDF + 1) . 1 )->setValue('HMDF');

        $worksheet->getColumnDimension(getNameFromNumber($COUNT + 1))->setWidth(7);
        $worksheet->getColumnDimension(getNameFromNumber($EID + 1))->setWidth(20);
        $worksheet->getColumnDimension(getNameFromNumber($EXT + 1))->setWidth(5);
        $worksheet->getColumnDimension(getNameFromNumber($ALIAS + 1))->setWidth(30);
        $worksheet->getColumnDimension(getNameFromNumber($LAST_NAME + 1))->setWidth(20);
        $worksheet->getColumnDimension(getNameFromNumber($FIRST_NAME + 1))->setWidth(20);
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

    public function exportdownload_2020_08_19() {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        //$employees = User::allExceptSuperAdmin()->get();
        $employees = DB::table('employee_info')
            ->leftJoin('employee_info_details','employee_info.id','=','employee_info_details.employee_id')
            ->where('employee_info.eid','!=','--')
            ->get();
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
        $CITYADD = 21;
        $HOMEADD = 22;
        $CIVILSTAT = 23;
        $CONTACTNUM = 24;
        $INCASECON = 25;
        $INCASEREL = 26;
        $INCASERELCON = 27;
        $INCASERELADD = 28;
        $SSS = 29;
        $PHILHEALTH = 30;
        $HMDF = 31;

        $worksheet->getCell(getNameFromNumber($COUNT + 1) . 1 )->setValue('Count'); 
        $worksheet->getCell(getNameFromNumber($EID + 1) . 1 )->setValue('EID');
        $worksheet->getCell(getNameFromNumber($EXT + 1) . 1 )->setValue('EXT');
        $worksheet->getCell(getNameFromNumber($ALIAS + 1) . 1 )->setValue('Phone/Pen Names');
        $worksheet->getCell(getNameFromNumber($LAST_NAME + 1) . 1 )->setValue('Last Name');
        $worksheet->getCell(getNameFromNumber($FIRST_NAME + 1) . 1 )->setValue('First Name');
        $worksheet->getCell(getNameFromNumber($FULLNAME + 1) . 1 )->setValue('Name');
        $worksheet->getCell(getNameFromNumber($SUPERVISOR + 1) . 1 )->setValue('Sup');
        $worksheet->getCell(getNameFromNumber($MANAGER + 1) . 1 )->setValue('Mng');
        $worksheet->getCell(getNameFromNumber($DEPT + 1) . 1 )->setValue('Dept');
        $worksheet->getCell(getNameFromNumber($DEPT_CODE + 1) . 1 )->setValue('Dept Code');
        $worksheet->getCell(getNameFromNumber($DIVISION + 1) . 1 )->setValue('Division');
        $worksheet->getCell(getNameFromNumber($ROLE + 1) . 1 )->setValue('Role');
        $worksheet->getCell(getNameFromNumber($ACCOUNT + 1) . 1 )->setValue('Account');
        $worksheet->getCell(getNameFromNumber($PROD_DATE + 1) . 1 )->setValue('Prod Date');
        $worksheet->getCell(getNameFromNumber($STATUS + 1) . 1 )->setValue('Status');
        $worksheet->getCell(getNameFromNumber($HIRED_DATE + 1) . 1 )->setValue('Hire Date');
        $worksheet->getCell(getNameFromNumber($WAVE + 1) . 1 )->setValue('Wave');
        $worksheet->getCell(getNameFromNumber($EMAIL + 1) . 1 )->setValue('Email');
        $worksheet->getCell(getNameFromNumber($GENDER + 1 ) . 1 )->setValue('Gender');
        $worksheet->getCell(getNameFromNumber($BDAY + 1) . 1 )->setValue('Bday');
        $worksheet->getCell(getNameFromNumber($CITYADD + 1) . 1 )->setValue('Address');
        $worksheet->getCell(getNameFromNumber($HOMEADD + 1) . 1 )->setValue('Home Adress');
        $worksheet->getCell(getNameFromNumber($CIVILSTAT + 1) . 1 )->setValue('Civil Stat');
        $worksheet->getCell(getNameFromNumber($CONTACTNUM + 1) . 1 )->setValue('Number');
        $worksheet->getCell(getNameFromNumber($INCASECON + 1) . 1 )->setValue('Contact Person');
        $worksheet->getCell(getNameFromNumber($INCASEREL + 1) . 1 )->setValue('Relationship');
        $worksheet->getCell(getNameFromNumber($INCASERELCON + 1) . 1 )->setValue('Number');
        $worksheet->getCell(getNameFromNumber($INCASERELADD + 1) . 1 )->setValue('Address');
        $worksheet->getCell(getNameFromNumber($SSS + 1) . 1 )->setValue('SSS');
        $worksheet->getCell(getNameFromNumber($PHILHEALTH + 1) . 1 )->setValue('Philhealth');
        $worksheet->getCell(getNameFromNumber($HMDF + 1) . 1 )->setValue('HMDF');

        $worksheet->getColumnDimension(getNameFromNumber($COUNT + 1))->setWidth(7);
        $worksheet->getColumnDimension(getNameFromNumber($EID + 1))->setWidth(20);
        $worksheet->getColumnDimension(getNameFromNumber($EXT + 1))->setWidth(5);
        $worksheet->getColumnDimension(getNameFromNumber($ALIAS + 1))->setWidth(30);
        $worksheet->getColumnDimension(getNameFromNumber($LAST_NAME + 1))->setWidth(20);
        $worksheet->getColumnDimension(getNameFromNumber($FIRST_NAME + 1))->setWidth(20);
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
            $worksheet->getCell(getNameFromNumber($EXT + 1) . $row )->setValue($value->ext);
            $worksheet->getCell(getNameFromNumber($ALIAS + 1) . $row )->setValue($value->alias);
            $worksheet->getCell(getNameFromNumber($LAST_NAME + 1) . $row )->setValue($value->last_name);
            $worksheet->getCell(getNameFromNumber($FIRST_NAME + 1) . $row )->setValue($value->first_name);
            $worksheet->getCell(getNameFromNumber($FULLNAME + 1) . $row )->setValue($value->first_name." ".$value->last_name);
            $worksheet->getCell(getNameFromNumber($SUPERVISOR + 1) . $row )->setValue($value->supervisor_name);
            $worksheet->getCell(getNameFromNumber($MANAGER + 1) . $row )->setValue($value->manager_name);
            $worksheet->getCell(getNameFromNumber($DEPT + 1) . $row )->setValue($value->team_name);
            $worksheet->getCell(getNameFromNumber($DEPT_CODE + 1) . $row )->setValue($value->dept_code);
            $worksheet->getCell(getNameFromNumber($DIVISION + 1) . $row )->setValue($value->division_name);
            $worksheet->getCell(getNameFromNumber($ROLE + 1) . $row )->setValue($value->position_name);

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
            $worksheet->getCell(getNameFromNumber($PROD_DATE + 1) . $row )->setValue(date("F d, Y", strtotime($value->prod_date)));
            $worksheet->getCell(getNameFromNumber($STATUS + 1) . $row )->setValue($value->status == 1 ? 'Active' : 'Inactive');
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
}