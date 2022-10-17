@extends('layouts.main')
@section('title')
Employee Import
@endsection
@section('pagetitle')
Employee / Upload Information
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
            <h4>Upload Employee Information</h4>
        </div>
        <div class="panel panel-container">
            <div class="panel-body">
                <form enctype="multipart/form-data" action="/upload/info/process" method="POST">
                   
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
@endsection