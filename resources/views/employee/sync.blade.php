@extends('layouts.main')
@section('title')
Employee Import
@endsection
@section('pagetitle')
Employee / Import
@endsection
@section('content')
<style type="text/css">
    label#bb {
        padding: 10px;
        display: table;
        background-color: #30a5ff;
        color: white;
    }
    #bb input[type=file]{
        display: none;
    }
    #result_messaging_div{
        display: none;
    }
    p.inserted {
        color: #03A9F4;
    }
    p.attrition {
        color: #DD2C00;
    }
    p.attrition, p.inserted {
        font-size: 13px;
        line-height: 18px;
        margin-left: 5px;
    }
    #deleted_employees_div, #inserted_employees_div{
        margin-top: 5pxl
    }
</style>
    <div class="col-md-4">
        <div class="section-header">
            <h4>Run Cron Job</h4>
        </div>
        <div class="panel panel-container">
            <div class="panel-body">
                <p>Click the button below to run masterlist and attrition cron jobs.</p>
                    <button id="sync_now" class="btn btn-success pull-right" style="background-color: #388E3C">
                        Sync Now
                    </button>
            </div>
        </div>
    </div>
    <div class="col-md-8" id="result_messaging_div">
        <div class="section-header">
            <h4>Sync Result</h4>
        </div>
        <div class="panel panel-container">
            <div class="panel-body">
                <div class="col-md-6" style="padding: 0px !important;">
                    <p>Deleted Employees</p>
                    <div class="collapse in" id="collapseDeleted">
                      <div class="card card-body" id="deleted_employees_div">
                      </div>
                    </div>
                </div>
                <div class="col-md-6" style="padding: 0px !important;">
                    <p>Inserted Employees</p>
                    <div class="collapse in" id="collapseInserted">
                      <div class="card card-body" id="inserted_employees_div">

                      </div>
                    </div>
                </div>
                <!-- <div class="col-md-6" style="padding: 0px !important;">
                    <p>Updated Employees</p>
                    
                    <div class="collapse in" id="collapseUpdated">
                      <div class="card card-body" id="updated_employees_div">

                      </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
    <br>
    <br>
    <br>
@endsection
@section('scripts')
<script type="text/javascript">
    var cronimport = false;
    var cronattrition = false;
    var import_result;
    var attrition_result;
   
    function checkSuccess(){
        if(cronimport && cronattrition) {
            for (var i = 0; i < attrition_result.deleted.length ; i++) {
                $('#deleted_employees_div').append('<p class="attrition">' + attrition_result.deleted[i] + '</p>');
            }
            for (var i = 0; i < import_result.Inserted.length ; i++) {
                $('#inserted_employees_div').append('<p class="inserted">' + import_result.Inserted[i] + '</p>');
            }
            if (import_result.Inserted.length == 0) {
                $('#inserted_employees_div').append("<p class='inserted'> 0 inserted</p>");
            }
            if (attrition_result.deleted.length == 0) {
                $('#deleted_employees_div').append('<p class="attrition"> 0 deleted </p>');
            }
            $('#result_messaging_div').show();
        }
    }
    $('#sync_now').click(function(){
        cronimport = false;
        cronattrition = false;
        $('#deleted_employees_div').html('');
        $('#inserted_employees_div').html('');

        $.ajax({url: "{{ url('/cron/importlatest') }}", success: function(result){
            import_result = result;
            cronimport = true;
            checkSuccess();
            },
        dataType: "json"
        });

        $.ajax({url: "{{ url('/cron/attrition') }}", success: function(result){
            attrition_result = result;
              cronattrition = true;
              checkSuccess();
            },
            dataType: "json"
        });
    });
</script>
@endsection