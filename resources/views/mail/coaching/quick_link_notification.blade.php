<div>    
    @if($mail_object)
    Good day {{ $mail_object['lnk_linkee_name'] }},
    <br>
    <br>
    You have a pending Quick Linking for Acknowledgement. To view our Quick Linking Session, please login to the <a href="http://dir.elink.corp/">HR Portal</a> and <a href="http://dir.elink.corp/coaching-session">click this Link after</a>. Thank you.
    <br>
    <br>
    Sincerely,
    <br>
    <br>
    {{ $mail_object['linker_name'] }}
    @endif
</div>