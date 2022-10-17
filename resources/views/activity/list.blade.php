@extends('layouts.main')
@section('title')
Activities
@endsection
@section('pagetitle')
Activities
@endsection
@section('content')
<style type="text/css">
    table >tbody> tr > td {
        vertical-align: middle !important;
        margin: auto;
    }
    a.btn.btn-primary {
        float: right;
        margin: 10px;
    }
</style>
<a href="{{url('activities/create')}}" class="btn btn-primary" ><i class="fa fa-plus"></i>&nbsp;&nbsp;Add Activity</a>
<div class="section-header">
    <h4>List of Activities</h4>
</div>
<table class="table">
    <thead>
        <tr>
            <td align="left">#</td>
            <td>Activity Title</td>
            <td>Subtitle</td>
            <td>Message</td>
            <td>Image</td>
            <td></td>
        </tr>        
    </thead> 
    <tbody>
        <?php $counter = 0; ?>
        @foreach($activities as $activity)
            <tr> 
                <td>  {{ ++$counter }}</td>
                <td>  {{ $activity->title }}</td>
                <td>  {{ $activity->subtitle }}</td>
                <td>  {{ truncate($activity->message, 50, false) }}</td>
                <td align="center">  <a target="_blank" href="{{ $activity->image_url }}" ><img src="{{ $activity->image_url }}" style=" height: 40px;" /></a></td>
                <td align="center">
                    <a href="{{ url('/activities/'. $activity->id . '/edit')}}" title="Edit">
                        <i class="fa fa-pencil"></i>
                    </a>&nbsp;&nbsp;
                    <a href="#" class="delete_btn" data-toggle="modal" data-target="#messageModal" title="Delete" data-id="{{$activity->id}}">
                        <i class="fa fa-trash" style="color: red;" ></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<script type="text/javascript">
    $('.delete_btn').click(function(){
        $('#messageModal .modal-title').html('Delete Activity');
        $('#messageModal #message').html('Are you sure you want to delete the activity ?');
        $('#messageModal .delete_form').attr('action', "{{ url('activities') }}/" + $(this).attr("data-id"));
    });
    $('#messageModal #yes').click(function(){
        $('#messageModal .delete_form').submit();
    });
</script>
@endsection 