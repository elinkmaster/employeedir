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
</style>
    <div class="col-md-3">
        <div class="section-header">
            <h4>Import Employees</h4>
        </div>
        <div class="panel panel-container">
            <div class="panel-body">
                <form enctype="multipart/form-data" action="{{ url('employees/import')}}" method="POST">
                   
                    <h2 id="filename"></h2>
                    <label id="bb" class="btn btn-primary" >Click here to attach Excel File
                        <input type="file" name="dump_file"  class="btn btn-small" id="fileexcel">
                    </label> 
                    {{ csrf_field() }}
                    <br>
                    <button type="submit" name="submit" class="btn btn-success" style="background-color: #388E3C">
                        Upload Now
                    </button>
                   
                </form>
            </div>
        </div>
    </div>
    @if(isset($num_inserts))
    <div class="col-md-9">
        <div class="section-header">
            <h4>Upload Employees</h4>
        </div>
        <div class="panel panel-container">
            <div class="panel-body">
                <div class="col-md-3">
                    <label>Number of Inserted Employees</label>
                    <p>{{$num_inserts}}</p>
                </div>
                <div class="col-md-3">
                    <label>Number of Updated Employees</label>
                    <p>{{$num_updates}}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
    <br>
    <br>
    <br>

@endsection
@section('scripts')
<script type="text/javascript">

    

    function fileUpload(input){
        var reader = new FileReader();
        reader.onload = function(e) {
          $('#filename').html(input.files[0].name);
        }

        reader.readAsDataURL(input.files[0]);
    }

    $('#fileexcel').change(function(){
        fileUpload(this);
    });

</script>
@endsection