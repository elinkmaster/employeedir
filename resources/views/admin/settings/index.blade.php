@extends('layouts.main')
@section('title')
    Settings
@endsection
@section('pagetitle')
    Settings
@endsection
@section('content')
    <style>
        .settings-table{
            width: 100%;
        }
        .settings-table tr td{
            border-bottom: 1px dashed lightgrey;
        }
    </style>
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Global Settings
            </div>
            <div class="panel panel-body">
                <form action="{{ url('settings') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="col-md-12">
                        <h4>EMAIL</h4>
                        <div class="col-md-12 no-padding">
                            <table class="table settings-table">
                                <tr>
                                    <td style="width: 20px"><input type="checkbox" id="email_notification" name="email_notification" {{ $email_notification ? "checked" : '' }}></td>
                                    <td style="width: 200px"><label for="email_notification">Enable Email Notification</label></td>
                                    <td style="width: 170px;">Leave Email Main Recipients:</td>
                                    <td><input type="text" name="email_recipients" id="email_recipients"></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>Business Leaders:</td>
                                    <td><input type="text" name="business_leaders" id="business_leaders"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <br>
                        <button class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <style type="text/css">
        tags{
            margin-top: -8px !important;
        }
    </style>
@endsection
@section('scripts')
    <script type="text/javascript">
        emails_tagify_options = {
            templates : {
                tag : function(v, tagData){
                    console.log(tagData)
                    try{
                    return `<tag title='${v}' contenteditable='false' spellcheck="false" class='tagify__tag ${tagData.class ? tagData.class : ""}' ${this.getAttributes(tagData)}>
                                <x title='remove tag' class='tagify__tag__removeBtn'></x>
                                <div>
                                    <span class='tagify__tag-text'>${v}</span>
                                </div>
                            </tag>`
                    }
                    catch(err){}
                },

                dropdownItem : function(tagData){
                    try{
                    return `<div class='tagify__dropdown__item ${tagData.class ? tagData.class : ""}'>
                                    <span>${tagData.value}</span>
                                </div>`
                    }
                    catch(err){}
                }
            },
            enforceWhitelist : true,
            whitelist : [
              <?php
                if($employee_emails != null){
                    if(count($employee_emails) > 0){
                        foreach ($employee_emails as $employee) {
                            echo "{ value: '" . $employee->email . "'},"; 
                        }
                    }
                }

              ?>
            ],
            dropdown : {
                enabled: 1, // suggest tags after a single character input
                classname : 'extra-properties' // custom class for the suggestions dropdown
            } 
        }


        var email_recipients_input = document.querySelector('input[name=email_recipients]');
        var business_leaders_input = document.querySelector('input[name=business_leaders]');

        var email_recipients_tagify = new Tagify(email_recipients_input, emails_tagify_options)
        var business_leaders_tagify = new Tagify(business_leaders_input, emails_tagify_options)

        email_recipients_tagify.addTags(
             <?php
                echo "[";
                if ($current_email_recipients != null && $current_email_recipients != ""){
                     if(count(json_decode($current_email_recipients)) > 0){
                        foreach (json_decode($current_email_recipients) as $index => $email) {
                            echo "{ value: '" . $email->value . "'},"; 
                        }
                    }
                }
                echo "]";
              ?>
            );


    </script>
@endsection