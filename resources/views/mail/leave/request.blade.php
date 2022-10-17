<div>
	<center>
		<small style="padding: 20px 0 20px 0;font-size: 14px;">eLink Systems &amp; Concepts Corp.</small>
	</center>

	@if($leave_request)
		Good day,
		<br>
		<br>
		{{ $leave_request['leave']->employee->first_name }} requested to file for {{ $leave_request['leave']->pay_type_id == 1 ? " a planned" : " an unplanned " }} leave.
		<br>
                <table border="1" cellpadding="7">
                    <thead>
                        <tr>
                            <th>Leave Date</th>
                            <th>Length</th>
                            <th>Pay Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach($leave_request['details'] as $info):
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
                <br>
		Please click on the button below.
		<br>
		<br>
		<a href="{{ url('leave').'/'.$leave_request['leave']->id }}">View Leave Request</a>
	@endif
	<br>
	<br>
	<br>
	Sincerely,
	<br>
	Employee Directory Admin
</div>