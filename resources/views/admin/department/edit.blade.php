@extends('layouts.main')
@section('title')
Department / Edit 
@endsection
@section('pagetitle')
Department / Edit 
@endsection
@section('content')
<style type="text/css">
    .row.margin-container{
        margin: 10px;
    }
</style>
{{ Form::open(array('url' => 'department/' . $department->id,'id' => 'edit_department_form')) }}
    {{ Form::hidden('_method', 'PUT') }}
    {{ csrf_field() }}
    <div class="col-md-3" style="">
        <div class="section-header">
            <h4>Edit Department</h4>
        </div>
        <div class="panel panel-container">
            <div class="row margin-container">
                <div class="form-group">
                    <label>Department Name</label>
                    <input type="text" name="department_name" class="form-control" value="{{ $department->department_name}}" required>
                </div>
                <div class="form-group">
                    <label>Department Code</label>
                    <input type="text" name="department_code" value="{{ $department->department_code}}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Division </label>
                    <select class="select2 form-control"  name="division_id">
                        <option selected="" disabled="">Select</option>
                        @foreach($divisions as $division)
                            <option {{ $department->division_id == $division->id ? 'selected' : '' }} value="{{ $division->id }}"> {{$division->division_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Account </label>
                    <select class="select2 form-control"  name="account_id" required>
                        <option selected="" disabled="">Select</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ $department->account_id == $account->id ? 'selected' : '' }}> {{$account->account_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <button class="btn btn-primary">Save</button>               
                </div>      
            </div>
        </div>
    </div>
</form>
@endsection
@section('scripts')
 <script type="text/javascript">
    var changed = false;
    $('#edit_department_form').validate({
        ignore: []
     });
     $('#image_uploader').change(function(){
        changed = true;
     });
     $('input').change(function(){
        changed = true;
     });
     $('select').change(function(){
        changed = true;
     });
     $('#edit_department_form').submit(function(){
        changed = false;
     });
     window.onbeforeunload = function(){
        if(changed){
            return '';
        }
     }
</script>
@endsection