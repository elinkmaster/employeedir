@extends('layouts.main')
@section('content')
<style type="text/css">
	small.leave-success{
		color: green;
	}
</style>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				Leave Request Information
			</div>
			<div class="panel panel-body">
				<div class="row" id="printable">
					<center>
						<img src="http://www.elink.com.ph/wp-content/uploads/2016/01/elink-logo-site.png" style="width: 80px; height: 80px;"> 
						<b style="font-size: 18px;">&nbsp;eLink Systems & Concepts Corp.</b>
						<br>
						<br>
						<b style="font-size: 20px;">LEAVE APPLICATION REQUEST</b>
					</center>

					<div class="col-md-12" style="border-top: 1px solid rgba(0,0,0,.125); padding-top: 15px; margin-top: 15px; padding-left: 0px;">
                    </div>
					<br>
					<br>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label style="margin-left: 10px;">Date filed:</label>
                                                <p style="margin-left: 10px;">{{ slashedDate($leave_request->date_filed) }}</p>
                                            </div>

                                            <div class="col-md-8">
                                                <table class="table table-bordered table-striped table-primary">
                                                    <thead>
                                                        <tr>
                                                            <th>Leave Date</th>
                                                            <th>Length</th>
                                                            <th>Pay Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    foreach($details as $info):
                                                    ?>
                                                        <tr>
                                                            <td>{{ date('F d, Y',strtotime($info->date)) }}</td>
                                                            <td>{{ $info->length == 1 ? "Whole Day" : "Half Day" }}</td>
                                                            <td>{{ $info->pay_type == 1 ? "With Pay" : "Without Pay" }}</td>
                                                        </tr>
                                                    <?php
                                                    endforeach;
                                                    ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
					<div class="col-md-12">
						&nbsp;
					</div>
					<div class="col-md-4">
						<label>Name: </label>
						<p>{{ isset($leave_request->employee) ? $leave_request->employee->fullName2() : "" }}</p>
					</div>
					<div class="col-md-4">
						<label>Position: </label>
						<p>{{ isset($leave_request->employee) ? $leave_request->employee->position_name : "" }}</p>
					</div>

					<div class="col-md-4">
						<label>Dept/Section: </label>
						<p>{{ isset($leave_request->employee) ? $leave_request->employee->team_name : "" }}</p>
					</div>
					<div class="col-md-12">
						&nbsp;
					</div>
					<div class="col-md-4">
						<label>No. of Days: </label>
						<p>{{ $leave_request->number_of_days }}</p>
					</div>
					<div class="col-md-4">
						<label>Type of Leave:</label>
						<p>{{ $leave_request->leave_type->leave_type_name }}</p>
					</div>
					<div class="col-md-4">
						<label>Contact Number:</label>
						<p>{{ $leave_request->contact_number }}</p>
					</div>
					<div class="col-md-12">
						&nbsp;
					</div>
					<div class="col-md-4">
						<label>Report Date:</label>
						<p>{{ prettyDate($leave_request->report_date) }}</p>
					</div>
					<div class="col-md-8">
						<label>Reason:</label>
						<p>{{ $leave_request->reason }}</p>
					</div>
					<div class="col-md-12">
						<br>
						<br>
					</div>
					<div class="col-md-4">
						
						<label>Recommending Approval:</label>
						<p>{{ isset($leave_request->employee->supervisor) ? $leave_request->employee->supervisor->fullName2() : '' }}{{ isset($leave_request->employee)  && $leave_request->employee->supervisor_id == Auth::user()->id ? '(You)' : ''}}</p>
						<small {{ $leave_request->recommending_approval_by_signed_date === NULL ? '' : 'class=leave-success' }}>{{ $leave_request->recommending_approval_by_signed_date === NULL ? 'Not yet recommended' : 'Recommended last ' .  prettyDate($leave_request->recommending_approval_by_signed_date) }}</small>
						<br>
						<br>
					</div>
					<div class="col-md-4">
						<label>Approved by:</label>
						<p>{{ isset($leave_request->employee->manager) ? $leave_request->employee->manager->fullName2() : '' }} {{ isset($leave_request->employee)  && $leave_request->employee->manager_id == Auth::user()->id ? '(You)' : ''}}</p>
						<small {{ $leave_request->approved_by_signed_date === NULL ? '' : 'class=leave-success' }}>{{ $leave_request->approved_by_signed_date === NULL ? 'Not yet approved' : 'Approved last ' .  prettyDate($leave_request->approved_by_signed_date) }}</small>
						<br>
						<br>
					</div>
					<div class="col-md-4">
						<label>Approve Status:</label>
						<p><?php echo $leave_request->getApprovalStatus() ?></p>
						<br>
						<br>
					</div>
					<div class="col-md-12" style="border-top: 1px solid rgba(0,0,0,.125); padding-top: 15px; margin-top: 0px; padding-left: 0px;">
                    </div>
                    <div class="col-md-6">
                        <label>{{ isset($leave_request->employee) ? $leave_request->employee->first_name : '' }}'s Remaining Leave Credits:</label>
                        <?php
                        $pto_forwarded = $credits->past_credit - $credits->conversion_credit;
                        $pto_accrue = $credits->current_credit;
                        $loa = abs($credits->loa);
                        $use_jan_jun = $credits->used_jan_to_jun;
                        $pto_expired = $credits->expired_credit;
                        $balance = $pto_forwarded + $pto_accrue - $loa - $use_jan_jun - $pto_expired;
                        ?>
                        <p>PTO Balance: <b><?php echo $credits->is_regular == 1 ? number_format($balance - $credits->used_jul_to_dec,1) : "0.0" ?></b></p>
                    </div>
					<div class="col-md-6" style="direction: rtl">
                                            <?php
                                            if(isset($leave_request->employee)):
                                            ?>
                                                @if($leave_request->isForRecommend())
                                                    <form action="{{ url('leave/recommend') }}" method="POST" style="display: inline-flex;">
                                                        {{ csrf_field() }}
                                                        <input type="hidden" name="leave_id" value="{{ $leave_request->id }}">
                                                        <button class="btn btn-primary">Recommend</button>
                                                    </form>
						@elseif($leave_request->isForApproval())
                                                    <form action="{{ url('leave/approve') }}" method="POST" style="display: inline-flex;">
                                                        {{ csrf_field() }}
                                                        <input type="hidden" name="leave_id" value="{{ $leave_request->id }}">
                                                        <button class="btn btn-primary">Approve</button>
                                                    </form>
						@endif
						@if($leave_request->isForNoted())
                                                    <form action="{{ url('leave/noted') }}" method="POST" style="display: inline-flex;">
                                                        {{ csrf_field() }}
                                                        <input type="hidden" name="leave_id" value="{{ $leave_request->id }}">
                                                        <button class="btn btn-primary">Noted</button>
                                                    </form>
						@endif
                        <!--usertype 3 is manager and 1 is administrator tweak date 10/14/22 -->    
						@if(Auth::user()->isAdmin() || Auth::user()->usertype == 3 )
                         <button class="btn btn-danger" data-target="#declinemodal" data-toggle="modal">Decline/Cancel</button>
                         <a href="/leave/{{ $leave_request->id }}/edit" class="btn btn-info">Update</a>
						@elseif(Auth::user()->isAdmin()  || Auth::user()->usertype == 3)
						<a href="/leave/{{ $leave_request->id }}/edit" class="btn btn-info">Update</a>
                                                      <form action="{{ url('leave/approve') }}" method="POST" style="display: inline-flex;">
                                                        {{ csrf_field() }}
                                                        <input type="hidden" name="leave_id" value="{{ $leave_request->id }}">
                                                        <button class="btn btn-primary">Approve</button>
                         <!--usertype 3 is manager -->                    </form>
						@elseif(Auth::user()->isAdmin() || Auth::user()->usertype == 3)
                        <a href="/leave/{{ $leave_request->id }}/edit" class="btn btn-info">Update</a>
                        @endif
                                            <?php
                                            endif;
                                            ?>
                                            

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
    <script type="text/javascript">
        $('.table').DataTable( {
            "paging"    :   false,
            "ordering"  :   false,
            "info"      :   false,
            searching   :   false
        } );
    </script>
    <div id="declinemodal" class="modal fade">
        <div class="modal-dialog">
                <div class="modal-content">
                        <div class="modal-header" style="background-color: #0086CD;">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: white !important;opacity: 1;">×</button>
                                <h4 class="modal-title"><b style="color: white">Reason for declining</b></h4>
                        </div>
                        <div class="modal-body">
                                <div clas="row">
                                        <form action="{{ url('leave/decline') }}" method="POST">
                                                <div class="form-group">
                                                        {{ csrf_field() }}
                                                        <p>You are about to decline a leave request. You may write a reason why.</p>
                                                </div>
                                                <div class="form-group">
                                                        <textarea class="form-control" name="reason_for_disapproval" style="resize: vertical;"></textarea>
                                                        <input type="hidden" name="leave_id" value="{{ $leave_request->id }}">
                                                </div>
                                                <div class="col-md-12">
                                                        <br>
                                                        <button type="submit" class="btn btn-primary pull-right" style="margin-top: 5px;">Submit</button>
                                                        <button type="button" class="btn btn-default pull-right" style="margin-top: 5px; margin-right: 5px;" data-dismiss="modal">Cancel</button>
                                                </div>
                                        </form>
                                </div>
                        </div>
                        <div class="modal-footer">

                        </div>
                </div>
        </div>
    </div>
@endsection