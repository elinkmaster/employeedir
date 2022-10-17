@extends('layouts.main')
@section('content')
    <style>
    @include('leave.leave-style');
    </style>
   
    <div class="panel panel-default ">
        <div class="panel-heading">
            LEAVE CREDITS UPLOAD
        </div>
        <div class="panel-body timeline-container ">
            <div class="flex-center position-ref full-height">
                <form action="/export/save" method="post" id="leave_form" enctype="multipart/form-data">
                {{ csrf_field() }}
                    <div class="form-group">
                        <label for="exampleFormControlFile1">Select Excel File</label>
                        <input type="file" class="form-control-file" name="prev_leave_credits">
                    </div>

                    <div class="form-group">
                        <input type="submit" id="register-button" class="btn btn-primary" value="Submit">
                        <input type="reset" class="btn btn-default" value="Reset">
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection