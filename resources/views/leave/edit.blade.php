@extends('layouts.main')
@section('content')
    <style>
    @include('leave.leave-style');
    </style>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default ">
                <div class="panel-heading">
                    LEAVE APPLICATION FORM
                </div>
                <div class="panel-body timeline-container ">
                    <div class="flex-center position-ref full-height">
                        <form action="/leave/update" method="post" id="leave_form">
                        {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group float-left">
                                        <strong><p>Remaining Leave Credits: </p></strong>
                                        <?php
                                        $pto_forwarded = $credits->past_credit - $credits->conversion_credit;
                                        $pto_accrue = $credits->current_credit;
                                        $loa = abs($credits->loa);
                                        $use_jan_jun = $credits->used_jan_to_jun;
                                        $pto_expired = $credits->expired_credit;
                                        $balance = $pto_forwarded + $pto_accrue - $loa - $use_jan_jun - $pto_expired;
                                        ?>
                                        <p id="p_leave_credits">PTO Balance: <b><?php echo $credits->is_regular == 1 ? number_format($balance - $credits->used_jul_to_dec,1) : "0.0" ?></b></p>
                                    </div> 
                                </div>
                                <div class="col-md-4">
                                    <strong><p>Date Filed: </p></strong>
                                    <div class="form-group float-right">
                                        <input type="text" value="{{ date('m/d/Y',strtotime($leave_request->date_filed)) }}" name="date_filed" class="form-control" placeholder="Date Filed" readonly>
                                    </div> 
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong><p>Name: </p></strong>
                                    <div class="form-group">
                                        <p>{{ $leave_request->employee->fullName2() }}</p>
                                        <input type="hidden" name="employee_id" value="{{ $leave_request->employee->id }}">
                                        <input type="hidden" name="id" value="{{ $leave_request->id }}">
                                    </div> 
                                </div>
                                <div class="col-md-4">
                                    <strong><p>Position:</p></strong>
                                    <div class="form-group">
                                        <input type="text" id="txtPhone" name="position" class="form-control" placeholder="Position" value="{{ $leave_request->employee->position_name }}" readonly>
                                    </div> 
                                </div>
                                <div class="col-md-4">
                                    <strong><p>Department:</p></strong>
                                    <div class="form-group">
                                        <input type="text" name="department" class="form-control" placeholder="Dept/Section" value="{{ $leave_request->employee->team_name }}" readonly>
                                    </div> 
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong><p>Date Filed:</p></strong>
                                    <div class="form-group">
                                        <input type="text" name="leave_date[]" class="form-control _leaveDate" placeholder="Date Filed" autocomplete="off" value="{{ date('m/d/Y', strtotime($leave->date)) }}">
                                    </div> 
                                </div>
                                <div class="col-md-2">
                                    <strong><p>Length</p></strong>
                                    <div class="form-group">
                                        <select name="length[]" class="form-control _lengthDaySel">
                                            <option value="1"{{ $leave->length == 1 ? " selected" : "" }}>Whole Day</option>
                                            <option value="0.5"{{ $leave->length == 0.5 ? " selected" : "" }}>Half Day</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <strong><p>With/Without Pay</p></strong>
                                    <div class="form-group">
                                        <select name="pay_type[]" class="form-control">
                                            <option value="0"{{ $leave->pay_type == 0 ? " selected" : "" }}>Without Pay</option>
                                            <option value="1"{{ $leave->pay_type == 1 ? " selected" : "" }}>With Pay</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <strong><p>Add Date to File</p></strong>
                                    <button type="button" class="btn btn-primary" id="_addLeaveItem">
                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                    </button>
                                </div>
                                <div class="col-md-2"><input type="hidden" name="field_id[]" value="{{ $leave->id }}"></div>
                            </div>
                            <div id="_date_form">
                            @for($i = 1; $i < count($filed_days); $i++)
                                <div id="main_row_{{ $filed_days[$i]->id }}" class="row">
                                    <div class="col-md-4">
                                        <strong><p>Date Filed:</p></strong>
                                        <div class="form-group">
                                            <input type="text" name="leave_date[]" class="form-control _leaveDate" placeholder="Date Filed" autocomplete="off" value="{{ date('m/d/Y', strtotime($filed_days[$i]->date)) }}">
                                        </div> 
                                    </div>
                                    <div class="col-md-2">
                                        <strong><p>Length</p></strong>
                                        <div class="form-group">
                                            <select name="length[]" class="form-control _lengthDaySel">
                                                <option value="1"{{ $filed_days[$i]->length == 1 ? " selected" : "" }}>Whole Day</option>
                                                <option value="0.5"{{ $filed_days[$i]->length == 0.5 ? " selected" : "" }}>Half Day</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <strong><p>With/Without Pay</p></strong>
                                        <div class="form-group">
                                            <select name="pay_type[]" class="form-control">
                                                <option value="0"{{ $filed_days[$i]->pay_type == 0 ? " selected" : "" }}>Without Pay</option>
                                                <option value="1"{{ $filed_days[$i]->pay_type == 1 ? " selected" : "" }}>With Pay</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <strong><p>Remove this Leave</p></strong>
                                        <button type="button" data-type="main" data-id="{{ $filed_days[$i]->id }}" class="btn btn-danger" onclick="removeThisLeave(this)">
                                            <span class="glyphicon glyphicon-minus" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="hidden" name="field_id[]" value="{{ $filed_days[$i]->id }}">
                                    </div>
                                </div>
                            @endfor
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong><p>Leave Category</p></strong>
                                    <div class="form-group">
                                        <select name="pay_type_id" class="form-control _leaveCategory">
                                            <option value="1"{{ $leave_request->pay_type_id == 1 ? " selected" : "" }}>Planned</option>
                                            <option value="2"{{ $leave_request->pay_type_id == 2 ? " selected" : "" }}>Unplanned</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4"></div>
                                <div class="col-md-4">
                                    <strong><p>Number of Days:</p></strong>
                                    <div class="form-group">
                                        <input type="text" name="number_of_days" class="form-control _numOfDaysField" list="leave_days" placeholder="No. of Days" autocomplete="off" value="{{ $leave_request->number_of_days }}" readonly>
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
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6" style="border-right: 1px solid rgba(0,0,0,.125);">
                                                <div class="form-group">
                                                    <ul class="list-group list-group-flush">
                                                    @foreach($leave_types as $lv)
                                                    <?php
                                                    if($lv->status < 3):
                                                    ?>
                                                    <li class="list-group-item{{$lv->status == 1 ? " _planned" : " _unplanned"}}">
                                                        <label class="switch float-left">
                                                            <input type="checkbox" name="leave_type_id" value="{{ $lv->id }}" id="progress{{ $lv->id  }}" tabIndex="1" class="primary" onClick="ckChange(this)" {{ $leave_request->leave_type_id == $lv->id ? 'checked' : ''}}>
                                                            <span class="slider"></span>
                                                        </label>&nbsp;
                                                        <span>{{ $lv->leave_type_name }}</span>
                                                    </li>
                                                    <?php
                                                    endif;
                                                    ?>
                                                    @endforeach
                                                    </ul>
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="report-date-box">
                                            I will report for work on 
                                            <input type="text" name="report_date" class="datepicker" placeholder="date" value="{{ date('m/d/Y',strtotime($leave_request->report_date)) }}" autocomplete="off">
                                            If i fail to do so on the said date without any justifiable cause.
                                            I can considered to have abandoned my employment. I understand that any misrepresentation I make on this request is a serious offense and shall be a valid ground for disciplinary action against me.
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
                                        <textarea name="reason" class="form-control" rows="4">{{ $leave_request->reason }}</textarea>
                                    </div> 
                                </div>

                                <div class="col-md-6">
                                    <strong><p>Contact Number: </p></strong>
                                    <div class="form-group">
                                        <input type="text" name="contact_number" class="form-control" value="{{ $leave_request->contact_number }}">
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
                <button type="button" data-type="sub" data-id="~~id~~" class="btn btn-danger" onclick="removeThisLeave(this)">
                    <span class="glyphicon glyphicon-minus" aria-hidden="true"></span>
                </button>
            </div>
            <div class="col-md-2">
                <input type="hidden" name="field_id[]" value="0">
            </div>
        </div>
    </script>
@endsection
@section('scripts')
<!-- <script src="https://cloud.tinymce.com/stable/tinymce.min.js"></script>
<script>tinymce.init({ selector:'textarea', forced_root_block : 'p' });</script> -->
<script type="text/javascript">
    var ctr = 1;
    var locked_days = [
    <?php
    foreach($blocked_dates as $b):
        echo '"'.$b.'"'.",";
    endforeach;
    ?>
    ];
    $(document).ready(function(){
        console.log(locked_days);
        $('#opportunity-table').DataTable();
        checkCategory();
        $('._leaveDate').datepicker({
            beforeShowDay   : function(d){
                var tar = jQuery.datepicker.formatDate('mm/dd/yy',d);
                tar = tar.toString();
                console.log(tar);
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
    
    $('.datepicker').datepicker({
        uiLibrary: 'bootstrap4'
    });
    
    $("._leaveCategory").change(function(){
        checkCategory();
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
                console.log(tar);
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
    
    $("#leave_form").validate({
        rules : {
            employee_id: "required",
            position : "required",
            department: "required",
            date_filed: "required",
            //leave_date_from : "required",
            //leave_date_to : "required",
            number_of_days : "required",
            //leave_type_id : "required",
            report_date : "required",
            reason : "required",
            contact_number: "required"
        },
        submitHandler : function(form, event){
            var validator = this;

            if($('input[name=leave_type_id]:checked').length == 0) {
                
            //} else if($('input[name=pay_type_id]:checked').length == 0){
                
            } else {
                form.submit();
            }
        }
    });
    
    $("._lengthDaySel").on("change",function(){
        console.log("change has come");
        console.log(computeTotalField());
    });
    
    function checkCategory(){
        var val = $("._leaveCategory").val();
        
        if(val == 1){
            $("._unplanned").hide();
            $("._planned").show();
        }else{
            $("._unplanned").show();
            $("._planned").hide();
        }
    }
     
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
        var type = $(obj).data('type');
        if(type == "sub")
            $("#row_" + id).remove();
        else{
            $("#main_row_" + id).remove();
        }
        computeTotalField();
        if(type == "main")
            $.post('/leave/rack',{id : id, total : $("._numOfDaysField").val(), leave : {{ $filed_days[0]->leave_id }}},function(e){
                console.log(e);
            },'json');
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

</script>
@endsection