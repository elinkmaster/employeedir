<div>
    <div>
        <small style="padding: 20px 0 20px 0;font-size: 14px;">eLink&nbsp;&nbsp;&nbsp;F&nbsp;A&nbsp;L&nbsp;C&nbsp;O&nbsp;N&nbsp;&nbsp;&nbsp;∞&nbsp;&nbsp;&nbsp;HR Portal</small>
    </div>
    <br>
    Update Information Request for: {{ $change_details['name'] }}
    <br>
    <br>
    <?php
        $obj = [
            1 => "Single",
            2 => "Married",
            3 => "Separated",
            4 => "Annulled",
            5 => "Divorced"
        ];
    ?>
    <table border="1">
        <thead>
            <tr>
                <th>Data</th>
                <th>Original Info</th>
                <th>Updated Info</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th style="padding: 7px;">Current Address</th>
                <th style="padding: 7px;">{{ $change_details['o_current_address'] }}</th>
                <th style="padding: 7px;">{{ $change_details['n_current_address'] }}</th>
            </tr>
            <tr>
                <th style="padding: 7px;">Contact Number</th>
                <th style="padding: 7px;">{{ $change_details['o_contact_num'] }}</th>
                <th style="padding: 7px;">{{ $change_details['n_contact_num'] }}</th>
            </tr>
            <tr>
                <th style="padding: 7px;">Emergency Contact Person</th>
                <th style="padding: 7px;">{{ $change_details['o_emergency'] }}</th>
                <th style="padding: 7px;">{{ $change_details['n_emergency'] }}</th>
            </tr>
            <tr>
                <th style="padding: 7px;">Contact Person's Number</th>
                <th style="padding: 7px;">{{ $change_details['o_emergency_num'] }}</th>
                <th style="padding: 7px;">{{ $change_details['n_emergency_num'] }}</th>
            </tr>
            <tr>
                <th style="padding: 7px;">Relationship</th>
                <th style="padding: 7px;">{{ $change_details['o_rel'] }}</th>
                <th style="padding: 7px;">{{ $change_details['n_rel'] }}</th>
            </tr>
            <tr>
                <th style="padding: 7px;">Marital Status</th>
                <th style="padding: 7px;">{{ $obj[$change_details['o_marital_stat']] }}</th>
                <th style="padding: 7px;">{{ $obj[$change_details['n_marital_stat']] }}</th>
            </tr>
            <tr>
                <td colspan="3" style="text-align: center; padding: 7px;"><a type="button" href="http://dir.elink.corp/recommend-request-info/{{ $change_details['id'] }}">Recommend Change Information Request for Approval</a></td>
            </tr>
        </tbody>
    </table>
    <br>
    <br>
    Please <a href="http://dir.elink.corp/login">login</a> to your account before recommending for approval the update information request.
    <br>
    <br>
    Sincerely,
    <br>
    <br>
    Falcon Admin
</div>