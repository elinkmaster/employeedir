@extends('layouts.main')
@section('title')
Department
@endsection
@section('pagetitle')
Departments
@endsection
@section('content')
<style type="text/css">
    #employees_table_wrapper{
        background-color: white;
        padding: 10px;
    }
    h1.page-header{
        margin-left: 20px;
        margin-bottom: -10px;
    }
    div.circle{
        border-radius: 100px;
        border: 1px solid #eaeaea;
        width: 46px;
        padding: 12px;
        text-align: center;
        vertical-align: middle;
        background-color: green;
        color: white;
        margin-right: 10px;
    }
    table tr{
        background-color: white;
    }
    table h5{
        font-weight: 500;
        font-size: 12px;
        line-height: 19px;
        margin-bottom: 0px;
    }
    table small{
        color: #90a4ae;
        font-size: 11px;
        line-height: 17px;
    }
    table >tbody> tr > td {
        vertical-align: middle !important;
        margin: auto;
    }
    .sorting_1{
        padding-left: 20px !important;
    }
    a.btn.btn-primary {
        float: right;
        margin: 10px;
    }

</style>
<a href="{{url('department/create')}}" class="btn btn-primary" ><i class="fa fa-plus"></i>&nbsp;&nbsp;Add Department</a>
<div class="section-header">
    <h4>List of Departments</h4>
</div>
<table id="employees_table" class="table">
    <thead>
        <tr>
            <td align="left">#</td>
            <td>Department Code</td>
            <td>Department Name</td>
            <td>Division</td>
            <td>Account</td>
            <td></td>
        </tr>        
    </thead> 
    <tbody>
        <?php $counter = 0; ?>
        @foreach($departments as $department)
            <tr> 
                <td>  {{ ++$counter }}</td>
                <td>  {{ $department->department_code }}</td>
                <td style="max-width: 250px;">
                    <h5 style="text-align: left !important;">{{$department->department_name}}</h5>
                </td>
                <td>{{ isset($department->division) == true ? $department->division->division_name : 'N/A'}}</td>
                <td>{{ isset($department->account) == true ? $department->account->account_name : 'N/A'}}</td>
                <td align="center">
                    <a href="{{ url('/department/'. $department->id . '/edit')}}" title="Edit">
                        <i class="fa fa-pencil"></i>
                    </a>&nbsp;&nbsp;
                    <a href="#" class="delete_btn" data-toggle="modal" data-target="#messageModal" title="Delete" data-id="{{$department->id}}">
                        <i class="fa fa-trash" style="color: red;" ></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<script type="text/javascript">
    $('.delete_btn').click(function(){
        $('#messageModal .modal-title').html('Delete Department');
        $('#messageModal #message').html('Are you sure you want to delete the department ?');
        $('#messageModal .delete_form').attr('action', "{{ url('department') }}/" + $(this).attr("data-id"));
    });
    $('#messageModal #yes').click(function(){
        $('#messageModal .delete_form').submit();
    });
</script>
@endsection 