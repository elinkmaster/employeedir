<?php

namespace App\Http\Controllers;

use App\User;
use App\Mail\ResetPasswordMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
   public function index()
    {
        return view('auth.passwords.reset');
    }

	public function confirmReset($token)
	{
        
        if(!$token){
            abort(404);
        }

        $id = Crypt::decrypt($token);

        $employee = User::where('id', $id)->first();

        $birthDate = Carbon::parse($employee->birth_date);
        $birthYear = str_split($birthDate->year);
        $newPassword = $birthDate->format('F') .''.$birthDate->format('d').''.$birthYear[2].''.$birthYear[3];

        $employee->password = Hash::make($newPassword);
        $employee->save();

         return view('auth.passwords.email_token');
    }

       public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $employee = User::select("id", "email")->where('email', $request->email)->get();

        if(count($employee) > 1 || count($employee) == 0){
            $employee = User::select("id", "email2")->where('email2', $request->email)->get();
        }

        if(count($employee) > 1 || count($employee) == 0){
            return back()->withErrors(['email'=> "Email doesn't exist in our record"]);
        }

	$data = [
            'token' => Crypt::encrypt($employee[0]->id),
            'email' => $employee[0]->email ?? $employee[0]->email2
        ];

        Mail::to($data['email'])->send(new ResetPasswordMail($data));

        return back()->with('Success', 'Password reset message has been sent, kindly check your email. Thank you');

    }
}
