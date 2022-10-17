@extends('layouts.main')
@section('title')
Dashboard
@endsection
@section('pagetitle')
Employee Information / Change Password
@endsection
@section('content')
<style type="text/css">
    h1.page-header{
        margin-left: 20px;
    }
    div.circle{
        border-radius: 100px;
        border: 1px solid #eaeaea;
        width: 50px;
        padding: 12px;
        text-align: center;
        vertical-align: middle;
        background-color: green;
        color: white;
        margin-right: 10px;
    }
    table tr{
        background-color: white;
    }
    table h5{
        font-weight: 500;
        font-size: 14px;
        line-height: 19px;
        margin-bottom: 0px;
    }
    table small{
        color: #90a4ae;
        font-size: 13px;
        line-height: 17px;
    }
    table >tbody> tr > td {
        vertical-align: middle !important;
        margin: auto;
    } 
    .sorting_1{
        padding-left: 20px !important;
    }
</style>
<br>
<div >
    <div class="col-md-4" >
        <div class="panel panel-container">
            <div class="row no-padding">
            	<br>
            	<form method="POST" action="{{ url('employee/'. $id . '/savepassword') }}">
            		{{ csrf_field() }}
                    @if(Auth::user()->isAdmin() == false)
        	        <div class="col-md-12 password">
	                   <div class="form-group">
	                       <label>Old Password</label>
	                       <input type="password" class="form-control" placeholder="Password" name="old_password" >
	                   </div>
                    </div>
                    @endif
	                <div class="col-md-12 password">
	                    <div class="form-group">
    	                    <label>New Password</label>
    	                    <input type="password" class="form-control" placeholder="Password" name="new_password" >
	                    </div>
	                </div>
	                <div class="col-md-12  password">
	                   <div class="form-group">
	                       <label>Confirm Password</label>
	                       <input type="password"  class="form-control" placeholder="Confirm Password" name="confirm_password">
	                   </div>
	                </div>
	                <div class="col-md-12">
	            	    @if($errors->any())
						    <span class="message-{{session('errors')->first('status') }}">{{session('errors')->first('message') }}</span>
    					    <br>
    						<br>
    					@endif
                    </div>
    	            <div class="col-md-12  password">
    	                <div class="form-group">
    	                    <button type="submit" class="btn btn-primary">Change</button>
    	                </div>
    	            </div>
	        	</form>
            	<br>
            	<br>
            </div>
        </div>
    </div>
</div>
@endsection