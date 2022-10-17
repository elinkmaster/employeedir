<div>
	<center>
		<small style="padding: 20px 0 20px 0;font-size: 14px;">eLink Systems &amp; Concepts Corp.</small>
	</center>

	@if($leave_request)
		Good day {{ $leave_request->employee->first_name }},
		<br>
		<br>
		@if($leave_request->recommending_approval_by_signed_date != null)
		{{ $leave_request->employee->supervisor->fullName2() }}
		@elseif($leave_request->approved_by_signed_date != null)
		{{ $leave_request->employee->manager->fullName2() }}
		@endif
		&nbsp;approved your {{$leave_request->leaveDays() }} leave request.
		<br>
		<br>
		<br>
	@endif
	<br>
	<br>
	<br>
	Sincerely,
	<br>
	Employee Directory Admin
</div>