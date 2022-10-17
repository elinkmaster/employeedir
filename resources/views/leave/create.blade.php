@extends('layouts.main')
@section('content')
    <style>
    @include('leave.leave-style');
    </style>
   
    <div class="panel panel-default ">
        <div class="panel-heading">
            LEAVE APPLICATION FORM
        </div>
        <div class="panel-body timeline-container ">
            <div class="flex-center position-ref full-height">
                <form action="{{ url('leave') }}" method="post" id="leave_form">
                {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group float-left">
                                <strong><p>Remaining Leave Credits: </p></strong>
                                <p id="p_leave_credits">{{ leaveCredits(Auth::user()->leave_credit) }}</p>
                            </div> 
                        </div>
                        <div class="col-md-4">&nbsp;</div>
                        <div class="col-md-4">
                            <strong><p>Date Filed: </p></strong>
                            <div class="form-group float-right">
                                <input type="text" value="{{ date('m/d/Y') }}" name="date_filed" class="form-control" placeholder="Date Filed" readonly autocomplete="off">
                            </div> 
                        </div>
                    </div> 
                <div class="row">
                    <div class="col-md-4">
                        <strong><p>Name: </p></strong>
                        <div class="form-group">
                            <select name="employee_id" class="form-control" {{ Auth::user()->isAdmin() ? '' : 'readonly' }}>
                                <option></option>
                                @foreach($employees as $employee)
                                    <option value="{{$employee->id}}" {{ Auth::user()->id == $employee->id ? 'selected' : '' }} >{{ $employee->fullName2() }}</option>
                                @endforeach
                            </select>
                        </div> 
                        <!--
                        <strong><p>Dates Filed:</p></strong>
                        <div class="form-group">

                            <input type="text" name="leave_dates[]" class="form-control datepicker" placeholder="Date Filed" autocomplete="off">
                        </div> 
                        -->
                    </div>
                    <div class="col-md-4">
                        <strong><p>Position:</p></strong>
                        <div class="form-group">
                            <input type="text" id="txtPhone" name="position" class="form-control" placeholder="Position" value="{{ Auth::user()->position_name }}" {{ Auth::user()->isAdmin() ? '' : 'readonly' }}>
                        </div> 
                        <!--
                        <strong><p>To:</p></strong>
                        <div class="form-group">
                            <input type="text" name="leave_date_to"class="form-control datepicker" placeholder="To" autocomplete="off">
                        </div>
                        -->
                    </div>
                    <div class="col-md-4">
                        <strong><p>Department:</p></strong>
                        <div class="form-group">
                            <input type="text" name="department" class="form-control" placeholder="Dept/Section" value="{{ Auth::user()->team_name }}" {{ Auth::user()->isAdmin() ? '' : 'readonly' }}>
                        </div> 
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <strong><p>Date Filed:</p></strong>
                        <div class="form-group">
                            <input id="main_date_picker" type="text" name="leave_date[]" class="form-control" placeholder="Date Filed" autocomplete="off">
                        </div> 
                    </div>
                    <div class="col-md-2">
                        <strong><p>Length</p></strong>
                        <div class="form-group">
                            <select name="length[]" class="form-control _lengthDaySel">
                                <option value="1">Whole Day</option>
                                <option value="0.5">Half Day</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <strong><p>With/Without Pay</p></strong>
                        <div class="form-group">
                            <select name="pay_type[]" class="form-control">
                                <option value="0">Without Pay</option>
                                <option value="1">With Pay</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <strong><p>Add Date to File</p></strong>
                        <button type="button" class="btn btn-primary" id="_addLeaveItem">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="col-md-2"></div>
                </div>
                
                <div id="_date_form">
                </div>
                
                <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <strong><p>Total Number of Days:</p></strong>
                        <div class="form-group">
                            <input type="text" name="number_of_days" value="1" class="form-control _numOfDaysField" list="leave_days" placeholder="No. of Days" autocomplete="off" readonly>
                       
                        </div>
                    </div>
                </div>
                <!-- TYPE OF LEAVE -->
                <div class="row">
                    <div class="col-md-12" style="border-top: 1px solid rgba(0,0,0,.125); padding-top: 15px; margin-top: 25px">
                        <strong><p>Type of Leave:</p></strong>
                    </div>
                </div>
                <div class="row" style="padding-bottom: 25px; margin-bottom: 25px;">
                    <div class="col-md-3" style="border-right: 1px solid rgba(0,0,0,.125);">
                        <div class="form-group">
                            <ul class="list-group list-group-flush">
                            @foreach($leave_types as $lv)
                            <li class="list-group-item">
                                <label class="switch float-left">
                                    <input type="checkbox" name="leave_type_id" value="{{ $lv->id }}" id="progress{{ $lv->id  }}" tabIndex="1" class="primary" onClick="ckChange(this)">
                                    <span class="slider"></span>
                                </label>&nbsp;
                                <span>{{ $lv->leave_type_name }}</span>
                            </li>
                            @endforeach
                            <!--
                                <li class="list-group-item">
                                    <label class="switch float-left">
                                        <input type="checkbox" name="leave_type_id" value="1" id="progress1" tabIndex="1" class="primary" onClick="ckChange(this)">
                                        <span class="slider"></span>
                                    </label>&nbsp;
                                    <span>Paternity Leave</span>
                                </li>
                                <li class="list-group-item">
                                    <label class="switch float-left">
                                        <input type="checkbox" name="leave_type_id" value="2" id="progress2" tabIndex="1" class="primary" onClick="ckChange(this)">
                                        <span class="slider"></span>
                                    </label>&nbsp;
                                    <span>Maternity Leave</span>
                                </li>
                                <li class="list-group-item">
                                    <label class="switch float-left">
                                        <input type="checkbox"name="leave_type_id" value="3" id="progress3" tabIndex="1" class="primary" onClick="ckChange(this)">
                                        <span class="slider"></span>
                                    </label>&nbsp;
                                    <span>Vacation Leave</span>
                                </li>
                            -->
                            </ul>
                        </div> 
                    
                            <!--
                            <div class="col-md-6">
                                <div class="form-group">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <label class="switch float-left">
                                                <input type="checkbox" name="pay_type_id" value="1" id="pay1" class="primary" onClick="ckChange(this)">
                                                <span class="slider"></span>
                                            </label>&nbsp;
                                            <span>With Pay</span>
                                        </li>
                                        <li class="list-group-item">
                                            <label class="switch float-left">
                                                <input type="checkbox" name="pay_type_id" value="2" id="pay2" class="primary" onClick="ckChange(this)">
                                                <span class="slider"></span>
                                            </label>&nbsp;
                                            <span>Without Pay</span>
                                        </li>
                                    </ul>
                                </div> 
                            </div>
                            -->
                 
                    </div>
                    <div class="col-md-9">
                        <div class="report-date-box" style="padding-top: 25%">
                            I will report for work on 
                            <input type="text" name="report_date" class="datepicker" placeholder="date" autocomplete="off">
                            If i fail to do so on the said date without any justifiable cause.
                            I can considered to have abandoned my employment. I understand that any misrepresentation I make on this request is a serious offense and shall be a valid ground for disciplinary action against me.
                            <!-- <div class="col-xs-3">I will report for work on</div>
                            <div class="col-xs-3">
                                <input type="text" name="report_date" id="datepicker4" class="form-control report_Date datepicker" placeholder="date" autocomplete="off">
                            </div>
                            <div class="col-xs-6">If i fail to do so on the said date without any justifiable cause. </div>
                            <span>I can considered to have abandoned my employment. I understand that any misrepresentation I make on this request is a serious offense and shall be a valid ground for disciplinary action against me.</span> -->
                        </div>
                    </div>
                </div>
                        <!-- END TYPE OF LEAVE -->

                        <!-- REASON -->
                        <div class="col-md-12" style="border-top: 1px solid rgba(0,0,0,.125); padding-top: 15px; margin-top: 25px">

                        </div>
                        <div class="col-md-6">
                            <strong><p>Reason:</p></strong>
                            <div class="form-group">
                                <textarea name="reason" class="form-control" rows="4"></textarea>
                            </div> 
                        </div>

                        <div class="col-md-6">
                            <strong><p>Contact Number: </p></strong>
                            <div class="form-group">
                                <input type="text" name="contact_number" class="form-control">
                            </div> 
                        </div>

                    <div class="form-group">
                        <input type="submit" id="register-button" class="btn btn-primary" value="Submit">
                        <input type="reset" class="btn btn-default" value="Reset">
                    </div>
                </form>
            </div>
        </div>
    </div>
  <script id="tmpl_addLeaveDay" type="text/template">
    <div id="row_~~id~~"  class="row">
        <div class="col-md-4">
            <strong><p>Date Filed:</p></strong>
            <div class="form-group">
                <input id="date_picker_~~id~~"  type="text" name="leave_date[]" class="form-control" placeholder="Date Filed" autocomplete="off">
            </div> 
        </div>
        <div class="col-md-2">
            <strong><p>Length</p></strong>
            <div class="form-group">
                <select id="sel_ctr_~~id~~" name="length[]" class="form-control _lengthDaySel" onchange="computeTotalField()">
                    <option value="1">Whole Day</option>
                    <option value="0.5">Half Day</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <strong><p>With/Without Pay</p></strong>
            <div class="form-group">
                <select name="pay_type[]" class="form-control">
                    <option value="0">Without Pay</option>
                    <option value="1">With Pay</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <strong><p>Remove this leave</p></strong>
            <button type="button" data-id="~~id~~" class="btn btn-danger" onclick="removeThisLeave(this)">
                <span class="glyphicon glyphicon-minus" aria-hidden="true"></span>
            </button>
        </div>
        <div class="col-md-2"></div>
    </div>
</script>
@endsection
@section('scripts')
<!-- <script src="https://cloud.tinymce.com/stable/tinymce.min.js"></script>
<script>tinymce.init({ selector:'textarea', forced_root_block : 'p' });</script> -->
<script type="text/javascript">
    var locked_days = [
    <?php
    foreach($blocked_dates as $b):
        echo '"'.$b.'"'.",";
    endforeach;
    ?>
    ];
    var ctr = 1;
    
    $(document).ready(function(){
        $('#opportunity-table').DataTable();
        console.log(locked_days);
        $('#main_date_picker').datepicker({
            beforeShowDay   : function(d){
                var tar = jQuery.datepicker.formatDate('mm/dd/yy',d);
                tar = tar.toString();
                var info = [];
                if($.inArray(tar,locked_days) >= 0){
                    info = [false, "", "Locked Date"];
                }else
                    info = [true, "", "Available"];

                return info;
            },
            minDate         : +14
        });  
    });
    
    $("#_addLeaveItem").click(function(){
        console.log(ctr);
        var template = document.getElementById("tmpl_addLeaveDay").innerHTML;
        var js_tmpl = "";
        js_tmpl = template.replace(/~~id~~/g,ctr);
        $("#_date_form").append(js_tmpl);
        $('#date_picker_' + ctr).datepicker({
            beforeShowDay   : function(d){
                var tar = jQuery.datepicker.formatDate('mm/dd/yy',d);
                tar = tar.toString();
                var info = [];
                if($.inArray(tar,locked_days) >= 0){
                    info = [false, "", "Locked Date"];
                }else
                    info = [true, "", "Available"];

                return info;
            },
            minDate         : +14
        });
        
        ctr++;
        computeTotalField();
    });
    
    $("._lengthDaySel").on("change",function(){
        console.log("change has come");
        console.log(computeTotalField());
    });
      
    function computeTotalField(){
        var total = 0;
        var val = 0;
        $("._lengthDaySel").each(function(){
            val = $(this).val();
            total += parseFloat(val);
        });
        $("._numOfDaysField").val(total);
        return total;
    }
    
    function removeThisLeave(obj){
        var id = $(obj).data('id');
        $("#row_" + id).remove();
        computeTotalField();
    }
    
    //disable remaining checkbox Leave type
    function ckChange(ckType){
        var ckName = document.getElementsByName(ckType.name);
        var checked = document.getElementById(ckType.id);

        if (checked.checked) {
            for(var i=0; i < ckName.length; i++){

                if(ckName[i] != checked){
                    $(ckName[i]).prop('checked', false);
                }
            } 
        }    
    }

    $("#leave_form").validate({
        rules : {
            employee_id: "required",
            position : "required",
            department: "required",
            date_filed: "required",
            //leave_date_from : "required",
            //leave_date_to : "required",
            number_of_days : "required",
            leave_type_id : "required",
            report_date : "required",
            reason : "required",
            contact_number: "required"
        },
        submitHandler : function(form, event){
            var validator = this;

            if($('input[name=leave_type_id]:checked').length == 0) {
                console.log('error occured first');
            //} else if($('input[name=pay_type_id]:checked').length == 0){
            //    console.log('error occured 2nd');
            } else {
                $("#register-button").hide();
                form.submit();
            }
        }
    });
    
</script>
@endsection