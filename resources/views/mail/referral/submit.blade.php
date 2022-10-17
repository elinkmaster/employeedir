<div>
    <center>
        <small style="padding: 20px 0 20px 0;font-size: 14px;">eLink Systems &amp; Concepts Corp.</small>
    </center>

    @if($referral)
        Good day,
        <br>
        <br>
        {{ $referral->getReferrerFullName() }} sent a referral.</b>).
        <br>
        <table style="width: 500px;">
            <tr>
                <td style="border-bottom: 1px solid grey;">Referral Name</td>
                <td style="border-bottom: 1px solid grey;">{{ $referral->getReferralFullName() }}</td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid grey;">Position Applied</td>
                <td style="border-bottom: 1px solid grey;">{{ $referral->position_applied }}</td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid grey;">Contact Number</td>
                <td style="border-bottom: 1px solid grey;">{{ $referral->referral_contact_number }}</td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid grey;">Email Address</td>
                <td style="border-bottom: 1px solid grey;">{{ $referral->referral_email }}</td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid grey;">Submitted Date</td>
                <td style="border-bottom: 1px solid grey;">{{ prettyDate($referral->created_at) }}</td>
            </tr>
        </table>
        <br>
        <br>
        Click this link to view more details <a href="{{ url('referral') . '/' . $referral->id }}">{{ url('referral') . '/' . $referral->id }}</a>
        <br>
    @endif
    <br>
    <br>
    <br>
    Sincerely,
    <br>
    Employee Directory Admin
</div>