<div>
    <center>
        <small style="padding: 20px 0 20px 0;font-size: 14px;">eLink Systems &amp; Concepts Corp.</small>
    </center>

    @if($leave_request)
        Good day {{ $leave_request->employee->first_name }},
        <br>
        <br>
        &nbsp;Unfortunately, your  leave request for {{$leave_request->leaveDays() }}  (<b>{{ prettyDate($leave_request->leave_date_from) }}</b> - <b>{{ prettyDate($leave_request->leave_date_to ) }}</b>) is <b>declined</b>.
        <br>
        <br>
        Reason for disapproval: {{ $leave_request->reason_for_disapproval }}
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