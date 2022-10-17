<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\User;
use App\ElinkActivities;
use App\EmployeeDepartment;
use App\Posts;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if (Auth::check()) {
            if(Auth::user()->isAdmin()) {
                return redirect('dashboard');
            }
        }   
        return view('home')
            ->with('posts', Posts::where('enabled', '=', '1')->get())
            ->with('new_hires', User::allExceptSuperAdmin()->orderBy('prod_date', 'DESC')->paginate(5))
            ->with('employees', User::allExceptSuperAdmin()->get())->with('birthdays', User::whereRaw('MONTH(birth_date) = '.date('n'))->whereRaw('deleted_at is null')->where("status","=",1)->orderByRaw('DAYOFMONTH(birth_date) ASC')->get())
            ->with('engagements', ElinkActivities::thisMonth()->orderBy('created_at', 'DESC')->get());
    }
    public function dashboard(Request $request)
    {
        return view('home')
            ->with('posts', Posts::where('enabled', '=', '1')->get())
            ->with('new_hires', User::allExceptSuperAdmin()->orderBy('prod_date', 'DESC')->paginate(5))
            ->with('employees', User::allExceptSuperAdmin()->get())
            ->with('birthdays', User::whereRaw('MONTH(birth_date) = '.date('n'))->whereRaw('deleted_at is null')->where("status","=",1)->orderByRaw('DAYOFMONTH(birth_date) ASC')->get())
            ->with('engagements', ElinkActivities::thisMonth()->orderBy('created_at', 'DESC')->get());
    }
    public function newhires(Request $request)
    {
        return User::allExceptSuperAdmin()->orderBy('prod_date', 'DESC')->paginate(5);
    }
} 
