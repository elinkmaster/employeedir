<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\User;
use App\EmployeeDepartment;

class TimeKeepingController extends Controller{
    
    public function saveBreakInfo(Request $request){
        $obj = [
            'employee_id'   => Auth::user()->id,
            'break_type'    => $request->post('id'),
            'break_status'  => $request->post('status'),
            'post'          => $_POST
        ];
        
        $break_type = $obj['break_type'];
        $employee_id = $obj['employee_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        if($break_type == 0){
            DB::update("update break_info set time_stop = now(), bi_status = 0 where time_stop is null and bi_status = 1 and bi_emp = $employee_id");
        }else
        if($obj['break_status'] == 1){
            DB::update("update break_info set time_stop = now(), bi_status = 0 where time_stop is null and bi_status = 1 and bi_emp = $employee_id");
            DB::insert("INSERT INTO `break_info` (`bi_id`, `bi_emp`, `bi_type`, `bi_ip` ,`time_start`, `time_stop`, `bi_status`) VALUES (NULL, '$employee_id', '$break_type', '$ip_address', now(), NULL, '1');");
        }else{
            DB::update("update break_info set time_stop = now(), bi_status = 0 where time_stop is null and bi_status = 1 and bi_type = $break_type and bi_emp = $employee_id limit 1");
        }
        
        return json_encode($obj);
    }
    
    public function personalTimeKeeping(){
        $id = Auth::user()->id;
        $marker = [];
        $obj = [];

        $team = Auth::user()->dept_code;
        $team_obj = EmployeeDepartment::where('department_code',$team)->first();
        $team_id = $team_obj ? $team_obj->id : 0;
        $br_obj = DB::select("select * from break_types where bt_dept = $team_id and bt_status = 1 order by bt_order asc");
        foreach($br_obj as $br):
            $type_id = $br->id;
            $obj = DB::select("select * from break_info where bi_emp = $id and bi_type = $type_id and (date(time_start) = curdate() or time_stop is null) and bi_status = 1 order by bi_type asc");
            
            if(count($obj) == 0)
                $insert = ["id" => $br->id, "desc" => $br->description,"status" => 1];
            else
                $insert = ["id" => $br->id, "desc" => $br->description,"status" => 0];
            array_push($marker,$insert);
        endforeach;
        
        $breaks = DB::select("SELECT bt.description as break_name, bi.* FROM `break_info` as bi left join break_types as bt on bt.id = bi.bi_type where (date(time_start) = curdate() or time_stop is null) and bi.bi_emp = $id order by time_start asc");

        return view('timekeeping.tk_main')
            ->with('employee', User::withTrashed()->find($id))
            ->with('time_obj',$obj)
            ->with('types',$marker)
            ->with('breaks',$breaks);
    }
    
    public function supView(Request $req){
        /*
         * select cast(concat(DATE_ADD(curdate(),INTERVAL -1 DAY), ' ', '20:00:00') as datetime) as dt
         */
        $main_id = Auth::user()->id;
        $main_names = DB::select("
            SELECT 
                id, first_name, last_name
            FROM
                elink_employee_directory.employee_info
            WHERE
                (supervisor_id = $main_id
                    OR manager_id = $main_id)
                    AND status = 1
                    AND deleted_at IS NULL
            ORDER BY last_name ASC;");
        
        //$main_names = User::where("supervisor_id",$main_id)->orWhere("manager_id",$main_id)->orderBy("last_name")->get(); 
        
        $staff_info = explode(",",$req->post('selected_staff'));
        
        if(count($staff_info) && $staff_info[0] > 0)
            $obj = User::whereIn('id',$staff_info)->get();
        else
            $obj = $main_names;
            /*
            $obj = DB::select("
                    SELECT 
                        *
                    FROM
                        elink_employee_directory.employee_info
                    WHERE
                        supervisor_id = $main_id OR manager_id = $main_id
                    ORDER BY last_name ASC;");
             * 
             */
        $array = [];
        foreach ($obj as $o):
                $id = $o->id;
                $d_obj = DB::select("
                    SELECT 
                        bi.*, bt.description AS break_type,timediff(bi.time_stop,bi.time_start) as com_minutes
                    FROM
                        break_info AS bi
                            LEFT JOIN
                        break_types AS bt ON bt.id = bi.bi_type
                    WHERE
                        bi.bi_emp = $id
                            AND time_start >= CAST(CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY),
                                    ' ',
                                    '20:00:00')
                            AS DATETIME)
                ");
                $time_in = DB::select("
                    SELECT 
                        *
                    FROM
                        break_info
                    WHERE
                        bi_emp = $id
                            AND time_start >= CAST(CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY),
                                    ' ',
                                    '20:00:00')
                            AS DATETIME)
                    ORDER BY time_start ASC
                    LIMIT 1;");
                $time_out = DB::select("
                    SELECT 
                        *
                    FROM
                        break_info
                    WHERE
                        bi_emp = $id AND bi_type = 8
                            AND bi_status = 1
                    ORDER BY time_start DESC
                    LIMIT 1;
                ");
                array_push($array,['obj' => $o, 'breaks' => $d_obj, 'time_in' => count($time_in) ? $time_in[0]->time_start : "", 'time_out' => count($time_out) ? $time_out[0]->time_start : ""]);
        endforeach;
        
        if($req->post('download'))
            $this->processDownload ($array);
        else
            return view('timekeeping.supervisor')
                ->with('names',$main_names)
                ->with('staff',$array)
                ->with('process',$req->post('display'))
                ->with('selected_staff',$staff_info);
    }
    
    private function processDownload($obj){
        $writesheet = new Spreadsheet();
        $writer = IOFactory::createWriter($writesheet, "Xlsx");
        $sheet = $writesheet->getActiveSheet();
        $i = 1;
        $header = array("Employee ID", "Last Name", "Given Name","Middle Name","Department","Position","Type", "Time In", "Time Out","Duration");
        $sheet->fromArray([$header], NULL, 'A'.$i); 
        $i++;
        foreach($obj as $staff){
            foreach($staff['breaks'] as $break):
                $body = [
                    $staff['obj']->eid,
                    $staff['obj']->last_name,
                    $staff['obj']->first_name,
                    $staff['obj']->middle_name,
                    $staff['obj']->team_name,
                    $staff['obj']->position_name,
                    $break->break_type,
                    $break->time_start,
                    $break->time_stop,
                    $break->com_minutes ? $break->com_minutes : "On-going"
                ];
                $sheet->fromArray([$body], NULL, 'A'.$i); 
                $i++;
            endforeach;
        }//end-foreach($obj)
        ob_end_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="time-keeping-'.date('mdY-His').'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->setPreCalculateFormulas(false);
        $writer->setOffice2003Compatibility(true);
        $writer->save('php://output');
    }
            
}//end-class
