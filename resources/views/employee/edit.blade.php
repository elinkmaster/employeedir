@extends('layouts.main')
@section('title')
Edit Profile
@endsection
@section('pagetitle')
Employee Information / Edit
@endsection
@section('content')
<link rel="stylesheet" href="{{asset('public/css/custom-bootstrap.css')}}">
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
                    <small class="asterisk-required" style="margin-left: 15px;font-size: 11px;">
                        required fields
                    </small></label>
                    <hr>    
                    <br> 
                    <div class="col-md-12">
                        @include('employee.fields.personal')
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
                        @include('employee.fields.user_access')
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
                        @include('employee.fields.job_related')
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
                        @include('employee.fields.government')
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
                        @include('employee.fields.login')
                        <br>
                    </div>
                    <div class="col-md-12">
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary" value="Save" />
                                                 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Modal -->
<div class="modal fade" id="modalMovements" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Staff Position Movements</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <div class="modal-body" style="max-height:500px; height: 450px; overflow: auto;">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th scope="col">Date of Transfer</th>
              <th scope="col">Department</th>
              <th scope="col">Position</th>
            </tr>
      
          </thead>
          <tr style="background-color: #fd9a47;">
              <td><input class="form-control datepicker" id="mv_transfer_date"></td>
              <td>
                  <select class="select2 form-control" id="department_name" style="width: 100%">
                    <option selected="" disabled="">Select</option>
                    @foreach($departments as $department)
                        <option <?php echo $department->department_name == @$employee->team_name ? "selected" : "";?> value="{{ $department->id }}"> {{$department->department_name}}</option>
                    @endforeach
                </select>
              </td>
              <td>
                <input class="form-control" id="mv_position" value="" list="positions" required>
                <datalist id="positions">
                    @foreach($positions as $position)
                        <option value="{{ $position->position_name }}">
                    @endforeach
                </datalist>
              </td>
          </tr>
          <tbody id="mdl_bodyMvmt">
          </tbody>
        </table>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button id="savingOption" type="button" class="btn btn-primary" data-dismiss="modal">Save changes</button>
        <input type="hidden" id="active-employee-id" value="{{ $employee->id }}">
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')

<script id="tmpl_rowMvmt" type="text/template">
    <tr>
        <td scope="row">~mv_transfer_date~</td>
        <td>~department_name~</td>
        <td>~mv_position~</td>
    </tr>
</script>

<script id="tmpl_addDependents" type="text/template">
    <div id="dep_~id~" class="row">
        <div class="col-md-3 form-group">
            <label>Dependent's Name</label>
            <br>
            <input id="dep_name_~id~" class="form-control" name="dependent_name[]" value="">
        </div>
        <div class="col-md-3 form-group">
            <label>Birthday</label>
            <br>
            <input id="dep_bday_~id~" class="form-control datepicker" name="dependent_bday[]" value="" autocomplete="off">
        </div>
        <div class="col-md-3 form-group">
            <label>Generali Number</label>
            <br>
            <input class="form-control" name="generali_num[]" value="<?php echo count($dependents) > 0 ? $dependents[0]->generali_num : "" ?>" autocomplete="off">
        </div>
        <div class="col-md-3 form-group" style="vertical-align: middle;">
            <br>
            <a href="#dependentsDiv" class="btn btn-danger" data-id="~id~" onclick="removeThisDependent(this)">Remove Dependent</a>
        </div>
    </div>
</script>

<script id="tmpl_addLinkee" type="text/template">
    <div id="linkee_row_~id~" class="row">
        <div class="col-md-5">
            <div class="form-group">
                <select id="sl_linkee_~id~" data-val="~id~" name="adtl_linkees[]" class="select2 process_linkee form-control">
                    <option value="0">Select a Linkee</option>
                    <?php
                    foreach($supervisors as $s):
                    ?>
                    <option value="{{ $s->id }}">{{$s->fullname()}}</option>
                    <?php
                    endforeach;
                    ?>
                </select>
                <input type="hidden" id="hidden_id_~id~" value="">
            </div>
        </div>
        <div class="col-md-1">
            <a href="#u_access-div" class="btn btn-danger" onclick="removeThisLinkee(~id~)">Remove Linkee</a>
        </div>
    </div>
</script>

 <script type="text/javascript">
    var changed = false;
    var ctr = 1;
    var emp_no = {{ @$employee->id }};
    var csrf_token = $('meta[name="csrf-token"]').attr('content');
    var ctr_linkee = 2;
    
    $.ajaxPrefilter(function(options, originalOptions, jqXHR){
        if (options.type.toLowerCase() === "post") {
            options.data = options.data || "";
            options.data += options.data?"&":"";
            options.data += "_token=" + encodeURIComponent(csrf_token);
        }
    });
    
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
        $("#_team_name").change(function(){
            var val = $(this).find(':selected').data('_dept_code');
            $("#_dept_code").val(val);
        });
        
        $("#btnViewMovments").click(function(x){
        x.preventDefault();
        $.get('/browse-transfer',{emp_no  : emp_no},function(data){
            var template = document.getElementById("tmpl_rowMvmt").innerHTML;
            var js_tmpl = "";
            $.each(data,function(key,val){
                js_tmpl += template
                            .replace(/~mv_transfer_date~/g,val.mv_transfer_date)
                            .replace(/~department_name~/g,val.department_name)
                            .replace(/~mv_position~/g,val.mv_position);
            });
            $("#mdl_bodyMvmt").html(js_tmpl);
                
            console.log(data);
        },'json');
    });
    
    $("#savingOption").click(function(x){
        x.preventDefault();
        var obj = {
            mv_employee_no      : emp_no,
            mv_transfer_date    : $("#mv_transfer_date").val(),
            mv_dept             : $("#department_name").val(),
            dept_name           : $("#department_name option:selected").text(),
            mv_position         : $("#mv_position").val()
        };
        console.log(obj);
        $.post("/save-transfer",obj,function(x){
            location.reload();
        },'json');
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

   const createNodeUsingTemplate = ({data}) => {
        let cloneTemplate = document.getElementById('linkee_template').content.cloneNode(true)
        let app = document.getElementById('linkees');

        let div = cloneTemplate.querySelector('div');
        let span = cloneTemplate.querySelector('span');
        let button = cloneTemplate.querySelector('button');
        let input = cloneTemplate.querySelector('input');

        div.id = "linkee-"+data.id
        input.name = "linkee-"+data.id
        input.value = data.id
        span.innerText = data.last_name +" "+data.first_name
        app.appendChild(cloneTemplate)
        button.setAttribute("onclick",`deleteNodeAndData(document.getElementById('${div.id}'))`)
        // button.addEventListener('click', deleteNodeAndData(document.getElementById(div.id)))
    }

const deleteNodeAndData = async(node) => {
        let input = node.querySelector('input')

        let confirmmed = confirm('Are you sure you would like to remove this linkee?')
        if(confirmmed){
            let response = await fetch('{{route('remove-linkees')}}',{
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                '_token': '{{csrf_token()}}',
                'adtl_linkee': input.value,
                'adtl_linker': '{{$employee->id}}'
            })
        });

            response = await response.json()
            console.log(response);
            if(response.data){
                node.remove();
            }
        }
        

        // node.remove()
    }

    document.getElementById('addLinkeeBtn').addEventListener('click', async(e) => {
        e.preventDefault();

        let linkee = document.getElementById('linkees_list').value;

        let response = await fetch('{{route('add-linkees')}}', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                '_token': '{{csrf_token()}}',
                'adtl_linker': '{{$employee->id}}',
                'adtl_linkee': linkee,
                'adtl_row': '1',
            })
        });

        response = await response.json()
        if(response.data){
            createNodeUsingTemplate(response)
            // console.log(true)
        }
    });

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
    
   // $(".add-linkee").click(function(e){
   //     var template = document.getElementById("tmpl_addLinkee").innerHTML;
   //     var js_tmpl = "";
   //     js_tmpl = template.replace(/~id~/g,ctr_linkee);
   //     $("#u_access-div").append(js_tmpl);
   //     $("#sl_linkee_" + ctr_linkee).select2();
   //     console.log('You Clicked Here');
   //     ctr_linkee++;
   //     e.preventDefault();
   // });
    
   // $(document).on('change', '.process_linkee', function() {
     //   var emp = $("#active-employee-id").val();
    //    var val = $(this).val();
    //    var row = $(this).data('val');
    //    var obj = {adtl_linker : emp, adtl_linkee : val, adtl_row: row};
        
    //    $.get("/process-linkee",obj,function(data){
    //        console.log(data);
    //    },"json");
    //    console.log(obj);
   // });
    
    function removeThisLinkee(id){
        console.log(id);
    }
    
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
