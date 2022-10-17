@extends('layouts.main')
@section('title')
Add Employee
@endsection
@section('pagetitle')
Employee / Add
@endsection
@section('content') 
<style type="text/css">
    .card-title{
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
    .employee-details-value{
        font-size: 16px;
        line-height: 21px;
        padding-bottom: 10px;
        color: black;
    }
    .label-profile{
        padding-left: 15px; 
        padding-right: 15px;
    }
    .col-md-9 hr{
        margin: 0px;
    }
</style>
<form id="create_employee_form" role="form" method="POST" action="{{ route('employee_info.store')}}" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div> 
        <div class="col-md-3" style="padding-left: 10px !important; padding-right: 10px;">
            <div class="section-header">
                <h4>Profile Picture </h4>
            </div>
            <div class="panel panel-container">
                <div class="row no-padding">
                    <center>
                        <img alt="image" id="profile_image" class="img-circle" style="width: 150px; height: 150px; margin-top: 30px;" src="{{ asset('public/img/nobody_m.original.jpg') }}">
                        <br> 
                        <br>
                         <label id="bb" class="btn btn-default"> Upload Photo
                            <input id="image_uploader" type="file" class="btn btn-small" value="" onchange="previewFile()"  name="profile_image"/>
                        </label> 
                        <h4 class="card-title m-t-10"></h4>
                        <h6 class="card-subtitle"></h6>
                        <h6 class="card-subtitle"></h6>
                    </center>
                    <br>
                    <br>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="section-header">
                <h4>Employee Information </h4>
            </div>

            <div class="panel panel-container">
                <div class="panel-body">
                    <small class="asterisk-required" style="margin-left: 0px;font-size: 13px;">required fields</small>
                    <br> 
                    <br> 
                    <label><b>Personal</b></label>
                    <hr>
                    <br> 
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="asterisk-required">First Name</label>
                                    <input  class="form-control" name="first_name" value="" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Middle Name</label>
                                    <input class="form-control" name="middle_name" value="">
                                </div> 
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="asterisk-required">Last Name</label>
                                    <input class="form-control" name="last_name" value="" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="asterisk-required">Employee ID</label>
                                    <input class="form-control" name="eid" value="" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label >Phone Name/Alias</label>
                                    <input class="form-control" name="alias" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="asterisk-required">Birthdate</label>
                                    <input class="form-control datepicker" name="birth_date" value="" required autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-2 form-group">
                                        <br>
                                        <label>Gender</label>
                                        <br>
                                        <input type="radio" id="male" name="gender_id" value="1" required>
                                        <label class="radio-label" for="male">Male</label>
                                        &nbsp;
                                        &nbsp;
                                        <input type="radio" id="female" name="gender_id" value="2" required>
                                        <label class="radio-label" for="female">Female</label>
                                    </div>  
                                    <div class="col-md-2 form-group">
                                        <br>
                                        <label>Civil Status</label>
                                        <br>
                                        <select name="civil_status" class="select2">
                                            <option value="1">Single</option>
                                            <option value="2">Married</option>
                                            <option value="3">Separated</option>
                                            <option value="4">Anulled</option>
                                            <option value="5">Divorced</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <br>
                                            <label>Avega Number</label>
                                        <br>
                                        <input type="text" class="form-control" name="avega_num">
                                    </div>
                                    <div class="col-md-6" form-group></div>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <br>
                                        <label>Father's Name</label>
                                        <br>
                                        <input class="form-control" name="fathers_name" value="">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <br>
                                        <label>Complete Mother's Maiden Name</label>
                                        <br>
                                        <input class="form-control" name="mothers_name" value="">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <br>
                                        <label>Spouse's Name</label>
                                        <br>
                                        <input class="form-control" name="spouse_name" value="">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label>Father's Birthday</label>
                                        <br>
                                        <input class="form-control datepicker" name="fathers_bday" value="" autocomplete="off">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>Mother's Birthday</label>
                                        <br>
                                        <input class="form-control datepicker" name="mothers_bday" value="" autocomplete="off">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>Spouse's Birthday</label>
                                        <br>
                                        <input class="form-control datepicker" name="spouse_bday" value="" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            
                            <div id="dependentsDiv" class="col-md-12" style="border-style: solid; border-width: thin; border-color: blue;">
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <br>
                                        <label>Dependent's Name</label>
                                        <br>
                                        <input class="form-control" name="dependent_name[]" value="">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <br>
                                        <label>Birthday</label>
                                        <br>
                                        <input class="form-control datepicker" name="dependent_bday[]" value="" autocomplete="off">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <br>
                                        <label>Generali Number</label>
                                        <br>
                                        <input class="form-control" name="generali_num[]" value="" autocomplete="off">
                                    </div>
                                    <div class="col-md-3 form-group" style="vertical-align: middle;">
                                        <br>
                                        <br>
                                        <button class="btn btn-primary add-dependent">Add Dependent</button>
                                    </div>
                                </div>                                
                            </div>
                            
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>City Address</label>
                                            <textarea name="address" class="form-control" rows="4"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Home Town Address</label>
                                            <textarea name="town_address" class="form-control" rows="4"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>In case of emergency please contact.</label>
                                            <br>
                                            <input type="text" name="em_con_name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Relationship</label>
                                            <br>
                                            <input type="text" name="em_con_rel" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Contact Number</label>
                                            <br>
                                            <input type="text" name="em_con_num" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Address</label>
                                            <textarea name="em_con_address" class="form-control" rows="4"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">&nbsp;</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                    <br>
                    </div>
                    <label><b>Job Related</b></label>
                    <hr>
                    <br>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Access Type</label>
                                    <br>
                                    <input type="radio" checked id="employee" name="employee_type" value="1" required>
                                    <label class="radio-label" for="employee">Employee</label>
                                    &nbsp;
                                    &nbsp;
                                    <input type="radio" id="supervisor" name="employee_type" value="2" required>
                                    <label class="radio-label" for="supervisor">Supervisor</label>
                                    &nbsp;
                                    &nbsp;
                                    <input type="radio" id="manager" name="employee_type" value="3" required>
                                    <label class="radio-label" for="manager">Manager</label>
                                    &nbsp;
                                    &nbsp;
                                    &nbsp;
                                    &nbsp;
                                    |
                                    &nbsp;
                                    &nbsp;
                                    &nbsp;
                                    &nbsp;
                                    <input type="checkbox" id="admin" name="is_admin" >
                                    <label class="radio-label" for="admin">SuperAdmin</label>
                                    &nbsp;
                                    &nbsp;
                                    &nbsp;
                                    &nbsp;
                                    <input type="checkbox"  id="hr" name="is_hr">

                                    <label class="radio-label" for="hr">HR</label>
                                    &nbsp;
                                    &nbsp;
                                    &nbsp;
                                    &nbsp;
                                    <input type="checkbox" id="erp" name="is_erp">

                                    <label class="radio-label" for="erp">ERP</label>

                                    &nbsp;
                                    &nbsp;
                                    <select name="is_regular" class="select2 is_reg_event">
                                        <option value="-1">Employee Type</option>
                                        <option value="0">Probationary</option>
                                        <option value="1">Regular</option>
                                        <option value="2">Project Based</option>
                                    </select>
                                    &nbsp;
                                    &nbsp;
                                    <select name="employee_category" class="select2">
                                        <option value="0">Employee Category</option>
                                        <option value="1">Manager</option>
                                        <option value="2">Supervisor</option>
                                        <option value="3">Support</option>
                                        <option value="4">Rank</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="asterisk-required">Position</label>
                                    <input class="form-control" name="position_name" value="" list="positions" required>
                                    <datalist id="positions">
                                        @foreach($positions as $position)
                                            <option value="{{ $position->position_name }}">
                                        @endforeach
                                    </datalist>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="asterisk-required">Account</label>
                                    <select class="select2 form-control"  name="account_id">
                                        <option selected="" disabled="">Select</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}"> {{$account->account_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Team/Department</label>
                                     <select class="select2 form-control" id="_team_name" name="team_name">
                                        <option selected="" disabled="">Select</option>
                                        @foreach($departments as $department)
                                            <option data-_dept_code="<?php echo $department->department_code ?>" value="{{ $department->department_name }}"> {{$department->department_name}}</option>
                                        @endforeach
                                    </select>
                                    <input id="_dept_code" type="hidden" name="dept_code" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group reg_div_">
                                    <label>Regularization Date</label>
                                    <input type="text" name="regularization_date" class="form-control datepicker" value="" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label >Supervisor</label>
                                    <select class="select2 form-control"  name="supervisor_id">
                                        <option selected="" disabled="">Select</option>
                                        @foreach($supervisors as $supervisor)
                                            <option value="{{ $supervisor->id }}"> {{$supervisor->fullname()}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Manager</label>
                                   <select class="select2 form-control" name="manager_id">
                                        <option selected="" disabled="">Select</option>
                                       @foreach($managers as $manager)
                                            <option value="{{ $manager->id }}"> {{$manager->fullname()}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Hire Date</label>
                                    <input class="form-control datepicker" name="hired_date" value="" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Production Date</label>
                                    <input class="form-control datepicker" name="prod_date" value="" autocomplete="off">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="asterisk-required">Employee Status</label>
                                         <select class="select2 form-control" name="status_id" required>
                                            <option selected="" disabled="">Select</option>
                                            <option value="1" selected>Active</option>
                                            <option value="2">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label >EXT</label>
                                        <input class="form-control" name="ext" value="" >
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label >Wave </label>
                                        <input class="form-control" name="wave" value="" >
                                    </div>
                                </div>    
                            </div>
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Rehirable</label>
                                        <select class="select2 form-control" name="rehirable" required>
                                            <option selected="" disabled="">Select</option>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div> 
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Reason</label>
                                        <input type="text" name="rehire_reason" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 hidden">
                                <div class="form-group">
                                    <input type="checkbox" name="all_access"> &nbsp;
                                    <span for="all_access">can view information from other account ?</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" id="">
                        <br>
                        <br>
                    </div>
                    <label><b>Government Numbers</b></label>
                    <hr>
                    <br>
                    <div class="col-md-12 no-padding" >
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >SSS Number</label>
                                <input class="form-control" name="sss" type="text" value="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Pag-ibig/HDMF</label>
                                <input class="form-control" name="pagibig" type="text" value="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Philhealth Number</label>
                                <input class="form-control" name="philhealth" type="text" value="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>TIN ID</label>
                                <input class="form-control" name="tin" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" id="">
                        <br>
                    </div>
                    <label><b>Login Credentials</b></label>
                    <hr>
                    <br>
                    <br> 
                    <div class="col-md-12">
                         <div class="row">
                                 <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="asterisk-required">Primary Email</label>
                                        <input class="form-control" name="email" type="email" value="" required>
                                    </div>
                                </div>
                                 <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Email 2</label>
                                        <input class="form-control"" name="email2" type="email" value="">
                                    </div>
                                </div>
                                 <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Email 3</label>
                                        <input class="form-control" name="email3" type="email" value="">
                                    </div>
                                </div>
                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <br>
                                        <p>
                                            <pre style="border: 0px solid transparent; border-radius: 0px !important; margin-top: -3px;"><i class="fa fa-info-circle">&nbsp;</i> Password will be generated automatically once saved.</pre>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4 hidden password">
                                    <div class="form-group">
                                        <label>Confirm Password</label>
                                        <input class="form-control">
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="col-md-12">
                        <br>
                        <br>
                        <div class="row">
                             <div class="col-md-4">
                                <div class="form-group">
                                    <button class="btn btn-primary">Save</button>                         
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
@section('scripts')
<script id="tmpl_addDependents" type="text/template">
    <div id="dep_~id~" class="row">
        <div class="col-md-3 form-group">
            <label>Dependent's Name</label>
            <br>
            <input class="form-control" name="dependent_name[]" value="">
        </div>
        <div class="col-md-3 form-group">
            <label>Birthday</label>
            <br>
            <input id="dep_bday_~id~" class="form-control datepicker" name="dependent_bday[]" value="" autocomplete="off">
        </div>
        <div class="col-md-3 form-group">
            <label>Generali Number</label>
            <br>
            <input class="form-control" name="generali_num[]" value="" autocomplete="off">
        </div>
        <div class="col-md-3 form-group" style="vertical-align: middle;">
            <br>
            <a href="#dependentsDiv" class="btn btn-danger" data-id="~id~" onclick="removeThisDependent(this)">Remove Dependent</a>
        </div>
    </div>
</script>
<script type="text/javascript">    
    
    var ctr = 1;
    
    $('#create_employee_form').validate({
        ignore: [], 
        rules : {
            first_name: {
                maxlength: 50
            },
            middle_name: {
                maxlength: 50
            },
            last_name: {
                maxlength: 50
            },
            alias:{
                maxlength: 100
            },
            position_name: {
                maxlength: 50
            }
        }
    });
    
    $("#_team_name").change(function(){
        var val = $(this).find(':selected').data('_dept_code');
        $("#_dept_code").val(val);
    });

    $(".add-dependent").click(function(e){
        e.preventDefault();
        console.log(ctr);
        var template = document.getElementById("tmpl_addDependents").innerHTML;
        var js_tmpl = "";
        js_tmpl = template.replace(/~id~/g,ctr);
        $("#dependentsDiv").append(js_tmpl);
        console.log('You Clicked Here');
        $("#dep_bday_" + ctr).datepicker({
            changeYear  : true,
            changeMonth : true,
            yearRange   : "1930:<?php echo date("Y") ?>"
        });
        
        ctr++;
    });
    
    $(".datepicker").datepicker({
        changeYear  : true,
        changeMonth : true,
        yearRange   : "1930:<?php echo date("Y") ?>"
    });
    // $('input[name=employee_type]').change(function(){
    //     switch($(this).val()){'
    //         case '2':
    //              $('select[name=supervisor_id]').parent().parent().show();
    //              $('select[name=manager_id]').parent().parent().show();
    //              $('input[name=all_access]').parent().parent().show();
    //         break;
    //         case '3':
    //             console.log('sulod');
    //             $('select[name=supervisor_id]').parent().parent().hide();
    //              $('input[name=all_access]').parent().parent().show();
    //         break;
    //         case '4':
    //              $('select[name=supervisor_id]').parent().parent().hide();
    //              $('select[name=manager_id]').parent().parent().hide();
    //              $('input[name=all_access]').parent().parent().show();
    //         break;
    //         case '1':
    //              $('select[name=supervisor_id]').parent().parent().show();
    //              $('select[name=manager_id]').parent().parent().show();
    //              $('input[name=all_access]').parent().parent().hide();
    //         break;
    //     }
    // });
    // $('input[name=employee_type]').trigger('change');

    $(".is_reg_event").change(function(){
        var val = $(this).val();
        console.log('type event triggered ' + val);
        if(parseInt(val) == 1)
            $(".reg_div_").show();
        else
            $(".reg_div_").hide();
    });
    $(".reg_div_").hide();
    
    function removeThisDependent(obj){
        
        var id = $(obj).data('id');
        $("#dep_" + id).remove();
    }
</script>
@endsection