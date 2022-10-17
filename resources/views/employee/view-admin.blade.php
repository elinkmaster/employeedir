@extends('layouts.main')
@section('title')
Edit Profile
@endsection
@section('pagetitle')
Employee Information / Edit
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
<br>
{{ Form::open(array('url' => 'employee_info/' . $employee->id,'files' => true ,'id' => 'edit_employee_form')) }}
{{ Form::hidden('_method', 'PUT') }}
{{ csrf_field() }}
    <div col-md-12>
        <div class="col-md-3" style="padding-left: 10px !important; padding-right: 10px;">
            <div class="section-header">
                <h4>Profile Picture</h4>
            </div>
            <div class="panel panel-container">
                <div class="row no-padding">
                    <center>
                        <img alt="Profile Image" id="profile_image" style="width: 150px;margin-top: 30px;" src="{{ $employee->profile_img }}">
                        <br> 
                        <br>
                        <label id="bb" class="btn btn-default"> Upload Photo
                            <input id="image_uploader" type="file" class="btn btn-small" value="" onchange="previewFile()"  name="profile_image"/>
                        </label>    
                        <h4 class="card-title m-t-10">{{ $employee->fullname() }}</h4>
                        <h6 class="card-subtitle">{{ $employee->position_name }}</h6>
                        <h6 class="card-subtitle">{{ $employee->team_name }}</h6>
                        <hr>
                    </center>
                    <span class="pull-left label-profile">date hired: <i>{{ $employee->prettydatehired() }}</i></span>
                    <br>
                    <br>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="section-header">
                <h4>Employee Information</h4>
            </div>
            <div class="panel panel-container" style="padding-top: 0px">
                <div class="panel-body"> 
                    <label>Personal
                   </label>
                    <hr>    
                    <br> 
                    <div class="col-md-12">
                        @include('employee.fields.personalv')
                        <br>
                        <br>
                    </div>
                    <div class="col-md-12">
                        <br>
                        <br>
                    </div>
                    <br>
                    <br>
                    <label>User Access</label>
                    <hr>
                    <br>
                    <div class="col-md-12">
                        @include('employee.fields.user_accessv')
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    </div>
                    <label>Job Related</label>
                    <hr>
                    <br>
                    <div class="col-md-12">
                        @include('employee.fields.job_relatedv')
                        <br>
                        <br>
                        <br>
                    </div>
                    <div class="col-md-12">
                    </div>
                    <label>Government Numbers</label>
                    <hr>
                    <br>
                    <div class="col-md-12 no-padding" >
                        @include('employee.fields.governmentv')
                        <br>
                        <br>
                    </div>
                    <div class="col-md-12" id="">
                        <br>
                    </div>
                    <label>Login Credentials</label>
                    <hr>
                    <br>
                    <br>
                    <div class="col-md-12">
                        @include('employee.fields.loginv')
                        <br>
                    </div>
                    <div class="col-md-12">
                    @auth
                    @if(Auth::user()->id == $employee->id)
                        <br>
                        <div class="row">
                            <div class="col-md-3" style="display: flex;">
                                <a type="button" class="btn btn-default" href="{{url('employee/'. $employee->id .'/changepassword')}}">Change Password</a>
                                &nbsp;
                                <a class="btn btn-primary" href="/update-profile">Update Selected Information</a>
                            </div>
                        </div>
                    @endif
                        @if(Auth::user()->isAdmin())
                        <br>
                        <div class="row">
                            <div class="col-md-12" >
                                <a type="button" class="btn btn-default" href="{{url('employee/'. $employee->id .'/changepassword')}}">
                                    Change Password
                                </a>
                                @if($employee->isActive())
                                <a class="btn btn-primary" href="{{url('employee_info/' . $employee->id . '/edit')}}">
                                    Update Profile
                                </a>
                                <a href="#"  class="pull-right btn btn-primary delete_btn" data-toggle="modal" data-target="#messageModal"  data-id="{{$employee->id}}" style="background: red !important; border-color: red !important;">
                                Deactivate Employee
                                </a>
                                @else
                                <a class="btn btn-primary" href="{{url('employees/' . $employee->id . '/reactivate')}}">
                                    Reactivate Employee
                                </a>
                                @endif
                            </div>
                        </div>
                    @endif
                    @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
   <div id="messageModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
           <button type="button" class="close" data-dismiss="modal">&times;</button>
           <h4 class="modal-title">Deactivate Employee</h4>
         </div>
          <div class="modal-body">
           <p id="message">Are you sure to deactivate this employee?</p>
          </div>
          <div class="modal-footer">
             {{ Form::open(array('url' => 'employee_info/'. $employee->id, 'class' => ' delete_form' )) }}
                   {{ Form::hidden('_method', 'DELETE') }}
                    {{ Form::submit('Yes', array('class' => 'btn btn-danger')) }}
                {{ Form::close() }}
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
        </div>
      </div> 
    </div>
@endsection
@section('scripts')



<script id="tmpl_addDependents" type="text/template">
    <div id="dep_~id~" class="row">
        <div class="col-md-4 form-group">
            <label>Dependent's Name</label>
            <br>
            <input readonly="1" id="dep_name_~id~" class="form-control" name="dependent_name[]" value="">
        </div>
        <div class="col-md-4 form-group">
            <label>Birthday</label>
            <br>
            <input id="dep_bday_~id~" readonly="1" class="form-control" name="dependent_bday[]" value="" autocomplete="off">
        </div>
        <div class="col-md-3 form-group">
            <label>Generali Number</label>
            <br>
            <input class="form-control" name="generali_num[]" value="<?php echo count($dependents) > 0 ? $dependents[0]->generali_num : "" ?>" autocomplete="off" readonly="1">
        </div>
        <div class="col-md-4 form-group" style="vertical-align: middle;">
            <br>
        </div>
    </div>
</script>

 <script type="text/javascript">
    var changed = false;
    var ctr = 1;
    
    $(function(){
    <?php
        if(count($dependents) > 1):
            for($i = 1; $i < count($dependents); $i++):
            ?>
                addDep();
                $("#dep_name_" + <?php echo $i ?>).val("<?php echo $dependents[$i]->dependent ?>");
                $("#dep_bday_" + <?php echo $i ?>).val("<?php echo date("m/d/Y",strtotime($dependents[$i]->bday)) ?>")
            <?php
            endfor;
        endif;
    ?>
    });
    
     $('#edit_employee_form').validate({
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

     $('#image_uploader').change(function(){
        changed = true;
     });

     $('input').change(function(){
        changed = true;
     });

     $('select').change(function(){
        changed = true;
     });
     $('#edit_employee_form').submit(function(){
        changed = false;
     });
     window.onbeforeunload = function(){
        if(changed){
            return '';
        }
     }
    //  $('input[name=employee_type]').change(function(){
    //     switch($(this).val()){
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

    if($(".is_reg_event").val() == 1)    
        $(".reg_div_").show();
    else
        $(".reg_div_").hide();
    
    $(".add-dependent").click(function(e){
        e.preventDefault();
        console.log(ctr);
        addDep();
    });
    
    $(".datepicker").datepicker({
        changeYear  : true,
        changeMonth : true,
        yearRange   : "1930:<?php echo date("Y") ?>"
    });
    
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
    
    function addDep(){
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
    }
 </script>
@endsection