@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <a style="color: white;" href="/leave">Pending Leaves</a> | <a style="color: white;" href="/approved-leaves">Approved Leaves</a> | <a style="color: red;" href="/cancelled-leaves">Cancelled Leaves</a> | <a style="color: white; font-weight: bold;" href="/leave/create">FILE A LEAVE</a>
                @if(Auth::check())
                    @if(Auth::user()->isAdmin())
                        | <a style="color: white;" href="{{ url('leave-report') }}">Leave Report</a> | <a style="color: white;" href="{{ url('expanded-credits') }}">Leave Credits</a>
                    @endif
                @endif
            </div>
            <div class="pane-body panel">
                <br>
                <br>

                <table class="_table">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Employee</td>
                            <td style="width: 450px;">Leave Type - Reason</td>
                            <td>Leave Dates</td>
                            <td>Pay Status</td>
                            <td>No. Of Days</td>
                            <td>Status</td>
                            <td>Date Requested</td>
                            <td width="100px">Options</td>
                        </tr>
                    </thead>
                    <tbody>
                    <pre>
                        <?php
                        $dte_array = [];
                        $ctr = 0;
                        ?>
                    </pre>
                        @foreach($leave_requests as $leave_request)
                        <?php
                            $reason = "";
                            $num_days = 0;
                            foreach($leave_request->leave_details as $_details):
                                $num_days += $_details->status == 1 ? $_details->length : 0;
                            endforeach;
                            
                            $leave_status = $leave_request->approve_status_id == 1 ? "Approved" :
                                    ($leave_request->approve_status_id == 2 ? "Not Approved" : "Pending");
                            
                            $reason = $leave_request->pay_type_id == 1 ? "Planned - " : "Unplanned - ";
                            $reason .= $leave_request->reason;
                            array_push($dte_array,$leave_request->id);
                            $ctr++;
                            
                            $emp_name = $req_obj->getEmployeeName($leave_request->employee_id);
                        ?>
                        <tr>
                            <td>{{ $leave_request->id }}</td>
                            <td>{{ $emp_name[0]->first_name. " " .$emp_name[0]->last_name }}</td>
                            <td>{{ $reason }}</td>
                            <td id="dte_{{ $leave_request->id }}"></td>
                            <td id="pte_{{ $leave_request->id }}"></td>
                            <td>{{ number_format($num_days,2,".",",") }}</td>
                            <td>{{ $leave_status }}</td>
                            <td>{{ date("M d, Y",strtotime($leave_request->date_filed)) }}</td>
                            <td width="100px" align="center">
                                <a href="{{url('leave') . '/' . $leave_request->id}}" title="View" data-id="{{ $leave_request->id }}" class="btn_view"><span class="fa fa-eye"></span></a>
                                &nbsp;&nbsp;
                                <!-- <a href="" title="Edit"><span class="fa fa-pencil"></span></a>
                                &nbsp;&nbsp; -->
                                <!-- <a href="" title="Approve"><span class="fa fa-thumbs-up"></span></a>
                                &nbsp;&nbsp;
                                <a href="" title="Disapprove"><span class="fa fa-thumbs-down"></span></a> -->
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var leaves = {{json_encode($dte_array)}};
    var ctr = 0;
    var str = "";
    var pay_type = "";
    
    $(function(){
        getProc(ctr);
    });
    
    function getProc(){
        $.get("/get-dates/" + leaves[ctr],function(data){
            str = "";
            pay_type = "";
            data.forEach(function(val){
                if(str.length == 0){
                    str = val.date;
                    pay_type = val.pay_type == 1 ? "With Pay" : "Without Pay";
                    //pay_type = val.pay_type;
                }
                else{
                    str += "<br>"+ val.date;
                    var this_pay =  val.pay_type == 1 ? "With Pay" : "Without Pay";
                    pay_type += "<br>" + this_pay;
                    
                }
            },'json');
        },'json').done(function(){
            $("#dte_" + leaves[ctr]).html(str);
            $("#pte_" + leaves[ctr]).html(pay_type);
            ctr += 1;
            if(ctr < leaves.length)
                getProc();
            else
                $("._table").DataTable({
                    lengthMenu  : [[15, 25, 50, -1], [15, 25, 50, "All"]]
                });
        });
    }
</script>
@endsection