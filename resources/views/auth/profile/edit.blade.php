@extends('layouts.main')
@section('title')
View Profile
@endsection
@section('pagetitle')
Employee Information
@endsection
@section('content')
<style type="text/css">
    .card-title {
        font-size: 16px;
        line-height: 21px;
        margin-top: 15px;
        font-weight: 400;
        color: black;
    }
    .card-subtitle{
        font-size: 12px;
        color: #878;
    }
    .label-profile{
        padding-left: 15px; padding-right: 15px;
    }
    .employee-details-value{
        font-size: 16px;
        line-height: 21px;
        padding-bottom: 10px;
        color: black;
    }
    .form-group label{
        font-weight: 600;
        color: #878;
    }
    .col-md-9 hr{
        margin: 0px;
    }
</style>
<br>    
<div>
    @auth
        @if(Auth::user()->id == $employee->id)
        <div class="container-fluid">
            <div class="panel panel-primary">
                <div class="panel-heading">Update Selected Personal Information</div>
                <div class="panel-body">
                    <form class="form-horizontal" action="/save-profile" method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="email">Current Address</label>
                            <input type="hidden" name="o_current_address" value="{{ $employee->address }}">
                            <div class="col-sm-10">
                                <input type="text" name="n_current_address" class="form-control" value="{{ $employee->address }}" placeholder="Current Address">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="email">Contact Number</label>
                            <input type="hidden" name="o_contact_num" value="{{ $employee->contact_number }}">
                            <div class="col-sm-10">
                                <input type="text" name="n_contact_num" class="form-control" placeholder="Contact Number" value="{{ $employee->contact_number }}">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="email">In Case of Emergency, Please Contact</label>
                            <input type="hidden" name="o_emergency" value="{{ $details->em_con_name }}">
                            <div class="col-sm-10">
                                <input type="text" name="n_emergency" class="form-control" value="{{ $details->em_con_name }}" placeholder="Emergency Contact Person">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="email">Contact Person Number</label>
                            <input type="hidden" name="o_emergency_num" value="{{ $details->em_con_num }}">
                            <div class="col-sm-10">
                                <input type="text" name="n_emergency_num" class="form-control" value="{{ $details->em_con_num }}" placeholder="Emergency Contact Number">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="email">Relationship</label>
                            <input type="hidden" name="o_rel" value="{{ $details->em_con_rel }}">
                            <div class="col-sm-10">
                                <input type="text" name="n_rel" class="form-control" value="{{ $details->em_con_rel }}" placeholder="Relationship">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="email">Marital Status</label>
                            <input type="hidden" name="o_marital_stat" value="{{ $employee->gender }}">
                            <div class="col-sm-10">
                                <select name="n_marital_stat" class="select2">
                                    <option value="1"<?php echo $employee->gender == 1 ? " selected" : "" ?>>Single</option>
                                    <option value="2"<?php echo $employee->gender == 2 ? " selected" : "" ?>>Married</option>
                                    <option value="3"<?php echo $employee->gender == 3 ? " selected" : "" ?>>Separated</option>
                                    <option value="4"<?php echo $employee->gender == 4 ? " selected" : "" ?>>Annulled</option>
                                    <option value="5"<?php echo $employee->gender == 5 ? " selected" : "" ?>>Divorced</option>
                                </select>
                            </div>
                        </div>
              
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form> 
                </div><!-- End Body  -->
            </div>
        </div>
        @endif
    @endauth
   
@endsection