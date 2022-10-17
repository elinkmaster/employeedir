<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EmployeeDepartment;
use App\ElinkDivision;
use App\ElinkAccount;
use App\User;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = EmployeeDepartment::all();
        return view('admin.department')->with('departments', $departments);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $divisions = ElinkDivision::all();
        $accounts = ElinkAccount::all();
        $managers = User::all();

        return view('admin.department.create')->with('divisions', $divisions)->with('accounts', $accounts)->with('managers', $managers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $employeeDepartment = new EmployeeDepartment();
        $employeeDepartment->department_name = $request->department_name;
        $employeeDepartment->department_code = $request->department_code;
        $employeeDepartment->division_id = $request->division_id;
        $employeeDepartment->account_id = $request->account_id;
        $employeeDepartment->save();

        return redirect('department')->with('success', "Successfully created department");
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
        $department = EmployeeDepartment::find($id);
        $divisions = ElinkDivision::all();
        $accounts = ElinkAccount::all();
        $managers = User::all();

        return view('admin.department.edit')->with('divisions', $divisions)->with('accounts', $accounts)->with('managers', $managers)->with('department', $department);
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
        $employeeDepartment = EmployeeDepartment::find($id);
        $employeeDepartment ->department_name = $request->department_name;
        $employeeDepartment->department_code = $request->department_code;
        $employeeDepartment->division_id = $request->division_id;
        $employeeDepartment->account_id = $request->account_id;
        $employeeDepartment->update();

        return redirect('department')->with('success', "Successfully edited department");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $employeeDepartment = EmployeeDepartment::find($id);
        $employeeDepartment->delete();
        
        return redirect('department')->with('success', "Successfully deleted department");
    }
}
