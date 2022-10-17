<?php 
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use DB;

class AuthRepository implements RepositoryInterface
{
    public $adServer = 'ldap://windc.elink.corp';
    public $port = 389;
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



    public function ldapLogin($username, $password){
        $ldap = ldap_connect($this->adServer, $this->port);

        if(!$ldap){
            return back()->withErrors(['email' => "LDAP Error!"]);
        }

        $ldaprdn = 'ELINK' . "\\" . $username;

        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

        try {
            $bind = @ldap_bind($ldap, $ldaprdn, $password);
        } catch(Exception $e){
            return back()->withErrors(['email' => "LDAP Error!"]);
        }
        
        if (ldap_get_option($ldap, LDAP_OPT_DIAGNOSTIC_MESSAGE, $extended_error)) {
            $error_code = $this->parseExentedLdapErrorCode($extended_error);
            if($error_code == 532){
                return back()->withErrors(['email' => "LDAP Password is expired!"]);
            } else if($error_code == 775){
                return back()->withErrors(['email' => "LDAP Username is locked!"]);
            } else if($error_code == 49 || $error_code == "52e"){
                return back()->withErrors(['email' => "LDAP Invalid credentials!"]);
            }
        }
        
        if ($bind) {
            $filter="(sAMAccountName=$username)";
            $attributes = array('mail');
            $result = ldap_search($ldap,"dc=ELINK,dc=CORP",$filter, $attributes);
            $info = ldap_get_entries($ldap, $result);
            $email = $info[0]['mail'][0];

            $ldap_user = null;

            if($email){
                $ldap_user = User::withTrashed()->findByEmail($email)->first();
            }else{
                return back()->withErrors(['email' => "Incorrect login credentials."]); 
            }

            $ldap_name = '';

            try {
               $ldap_name = explode('=',explode(",",$info[0]["dn"])[0])[1]; 
            } catch (Exception $e) {}

            if($ldap_user){
                Auth::login($ldap_user);
                return redirect('/');
            }else{
                return back()->withErrors(['email' => "$email was not set for $ldap_name. Please contact admin for assistance."]);
            }
        } else {
            return back()->withErrors(['email' => "Incorrect email and password combination!"]);
        }
    }


    public function parseExentedLdapErrorCode($message) {
        $code = null;
        if (preg_match("/(?<=data\s).*?(?=\,)/", $message, $code)) {
            return $code[0];
        }
        return null;
    }

    public function authFields($arrayIndex, $field, $password){
        foreach($arrayIndex as $index){
            if (Auth::attempt([ $index => $field, 'password' => $password])) {
                $user = User::where($index ,'=', $field);
                if ($user->count() > 0) {
                    Auth::login($user->first());
                    return redirect()->intended('/');
                }
            }
        }
        return back()->withErrors(['email' => "Incorrect email and password combination!"]);
    }

    public function login(Request $request){
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return $this->ldapLogin($request->email, $request->password);
        } else {
            return $this->authFields(['email', 'email2', 'email3'], $request->email, $request->password);
        }
    }

    public function ldapAPILogin($username, $password){
        $ldap = ldap_connect($this->adServer, $this->port);

        if(!$ldap){
            return false;
        }
        
        $ldaprdn = 'ELINK' . "\\" . $username;

        
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
        
        try {
            $bind = @ldap_bind($ldap, $ldaprdn, $password);
        } catch(Exception $e){
            return false;
        }
        
        if (ldap_get_option($ldap, LDAP_OPT_DIAGNOSTIC_MESSAGE, $extended_error)) {
            $error_code = $this->parseExentedLdapErrorCode($extended_error);
            if($error_code == 532){
                return false;
            } else if($error_code == 775){
                return false;
            } else if($error_code == 49 || $error_code == "52e"){
                return false;
            }
        }
        if ($bind) {
            $filter="(sAMAccountName=$username)";
            $attributes = array('mail');
            $result = ldap_search($ldap,"dc=ELINK,dc=CORP",$filter, $attributes);
            $info = ldap_get_entries($ldap, $result);
            
            $ldap_user = User::where('email', '=', $info[0]['mail'][0])->get();
            
            return "$ldap_user";
            
        } else {
            
            return false;
        }
    }
    public function loginAPIv2(Request $request){
        
        $ldap_user = $this->ldapAPILogin($request->email, $request->password);
        
        if($ldap_user){
            return response(['success' => true, 'user' => $ldap_user]);
            return "test";
        }

        $arrayIndex = array('email', 'email2', 'email3'); 

        foreach($arrayIndex as $index){
            if (Auth::attempt([ $index => $request->email, 'password' => $request->password ])){
                $user = User::where($index ,'=', $request->email);
                if ($user->count() > 0) {
                    return response(['success' => true, 'user' => $user->first()]);
                }
            }
        }
        return response(['success' => false, 'user' => null]);
    }
    public function loginAPI(Request $request){
        $param = "";
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::whereEmail($request->email);
            if ($user->count() > 0) {
                Auth::login($user->first());
                $param = '?id=' . Auth::user()->id . '&first_name=' . Auth::user()->first_name . '&last_name=' . Auth::user()->last_name;
            }
         }

         if (Auth::attempt(['email2' => $request->email, 'password' => $request->password])) {
           $user = User::where('email2' ,'=', $request->email);

            if ($user->count() > 0) {
                Auth::login($user->first());
                $param = '?id=' . Auth::user()->id . '&first_name=' . Auth::user()->first_name . '&last_name=' . Auth::user()->last_name;
            } 
         }

         if (Auth::attempt(['email3' => $request->email, 'password' => $request->password])) {
           $user = User::where('email3' ,'=', $request->email);

            if ($user->count() > 0) {
                Auth::login($user->first());
                $param = '?id=' . Auth::user()->id . '&first_name=' . Auth::user()->first_name . '&last_name=' . Auth::user()->last_name;
            }
         }
         return redirect($request->redirect_url . $param);
    }

    public function session(Request $request)
    {
        if (Auth::check() && isset($request->redirect_url)) {
            header('Location: ' . $request->redirect_url . '?user_id=' . Auth::user(  )->id);
        } else {
            header('Location: ' . $request->redirect_url . '?user_id=0');
        }
        die();
    }

    public function changepassword(Request $request, $id)
    {
        if (!Auth::user()->isAdmin()) {
            if (Auth::user()->id == $id){
                return view('employee.changepassword')->with('id', $id);
            } else {
                return abort(404);
            }
        }
        return view('employee.changepassword')->with('id', $id);
    }
    public function savepassword(Request $request, $id)
    {
        $user = User::find($id);

        if ($request->new_password == "" && $request->old_password == "" && $request->confirm_password == "") {
            return redirect()->back()->withErrors(array('message' => 'all field are required!', 'status' => 'error'));
        }

        if(!Auth::user()->isAdmin()){
            if (Hash::check($request->old_password, $user->password)) {
                // Do nothing
            } else {
                return redirect()->back()->withErrors(array('message' => 'incorrect old password', 'status' => 'error'));
            }  
        }

        if ($request->new_password == $request->confirm_password) {
            $user->password = Hash::make($request->new_password);
            if ($user->save()) {
                return redirect()->back()->withErrors(array('message' => 'Password successfully changed!', 'status' => 'success'));
            } else {
                return redirect()->back()->withErrors(array('message' => 'error saving !', 'status' => 'error'));
            }
        } else {
            return redirect()->back()->withErrors(array('message' => 'new password don\'t match', 'status' => 'error'));
        }
        
    }
}