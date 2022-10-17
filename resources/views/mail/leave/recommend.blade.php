<div>
	<center>
		<small style="padding: 20px 0 20px 0;font-size: 14px;">eLink Systems &amp; Concepts Corp.</small>
	</center>

	@if($leave_request)
		Good day {{ $leave_request->employee->first_name }},
		<br>
		<br>
                Your leave request is successfully recommended for approval.
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