<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\User;
use App\LeaveRequest;
use App\Repositories\EmployeeRepository;
use App\Repositories\AuthRepository;
use App\Repositories\ExportImportRepository;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $model;
    protected $authModel;
    protected $excelModel;
    public $routeName = '';

    public function __construct(User $employee){
        $this->model = new EmployeeRepository($employee);
        $this->authModel = new AuthRepository($employee);
        $this->excelModel = new ExportImportRepository($employee);

    }

    public function run(){
    	$admins = User::where('usertype', '=', '4')->get();

    	foreach($admins as $admin){
    		$admin->is_admin = 1;
    		$admin->save();
    	}

		foreach(User::all() as $employee){

			if($supervisor = User::whereRaw('CONCAT(last_name, ", ", first_name) = ' . '"' . $employee->supervisor_name . '"')->first()){
				$employee->supervisor_id = $supervisor->id;
			}
			
			if($manager = User::whereRaw('CONCAT(last_name, ", ", first_name) = ' . '"' . $employee->manager_name . '"')->first()){
				$employee->manager_id = $manager->id;
			}

			$employee->save();
		}

        
    	return "done !";
    }
}
