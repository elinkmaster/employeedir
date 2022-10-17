@extends('layouts.main')
@section('title')
Department / Add New
@endsection
@section('pagetitle')
Department / Add New
@endsection
@section('content')
	<style type="text/css">
        .row.margin-container{
            margin: 10px;
        }
        #division_id-error{
            margin-top: 65px;
            margin-left: -61px;
            position: absolute;
        }
        label.error + span{
            padding-bottom: 30px;
        }
        #account_id-error{
            margin-left: -60px;
        }
	</style>
    <form id="create_department_form" role="form" method="POST" action="{{ route('department.store')}}" >
        {{ csrf_field() }}
        <div class="col-md-3" style="">
            <div class="section-header">
                <h4>New Department</h4>
            </div>
            <div class="panel panel-container">
                <div class="row margin-container">
                    <div class="form-group">
                        <label>Department Name</label>
                        <input type="text" name="department_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Department Code</label>
                        <input type="text" name="department_code" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Division </label>
                        <select class="select2 form-control"  name="division_id">
                            <option selected="" disabled="">Select</option>
                            @foreach($divisions as $division)
                            <option value="{{ $division->id }}"> {{$division->division_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Account </label>
                        <select class="select2 form-control"  name="account_id" required>
                            <option selected="" disabled="">Select</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}"> {{$account->account_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <br>
                        <button class="btn btn-primary">Save</button>               
                    </div>      
                </div>
            </div>
        </div>
    </form>
@endsection
@section('scripts')
    <script type="text/javascript">
        $('#create_department_form').validate({
            ignore: []
        });
    </script>
@endsection