<div>    
    @if($mail_object)
    Good day {{ $mail_object['linkee'] }},
    <br>
    <br>
    You have a pending {{ $mail_object['link_type'] }} for acknowledgement last {{ date("F d, Y",strtotime($mail_object['lnk_date'])) }}. Please login to the <a href="http://dir.elink.corp/">HR Portal</a> and check our coaching session. Thank you.
    <br>
    <br>
    Sincerely,
    <br>
    <br>
    {{ $mail_object['linker'] }}
    @endif
</div>