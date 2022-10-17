<div>    
    @if($mail_object)
    Good day {{ $mail_object['lnk_linkee_name'] }},
    <br>
    <br>
    You have a pending Cementing Expectation for Acknowledgement. To view our Cementing Expectation Session, please login to the <a href="http://dir.elink.corp/">HR Portal</a> and <a href="http://dir.elink.corp/ce-expectation/<?php echo $mail_object['se_com_id']  ?>">click this Link after</a>. Thank you.
    <br>
    <br>
    Sincerely,
    <br>
    <br>
    {{ $mail_object['linker_name'] }}
    @endif
</div>