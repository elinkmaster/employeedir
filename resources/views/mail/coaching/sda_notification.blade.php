<div>    
    @if($mail_object)
    Good day {{ $mail_object['lnk_linkee_name'] }},
    <br>
    <br>
    You have a pending Skill Development Activity Session for Acknowledgement. To view our Skill Development Activity Session, please login to the <a href="http://dir.elink.corp/">HR Portal</a> and <a href="http://dir.elink.corp/skill-dev-act/<?php echo $mail_object['sda_com_id']  ?>">click this Link after</a>. Thank you.
    <br>
    <br>
    Sincerely,
    <br>
    <br>
    {{ $mail_object['linker_name'] }}
    @endif
</div>