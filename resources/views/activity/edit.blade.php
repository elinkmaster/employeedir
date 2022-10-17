@extends('layouts.main')
@section('title')
Activity / Edit 
@endsection
@section('pagetitle')
Activity / Edit 
@endsection
@section('content')
<style type="text/css">
    .row.margin-container{
        margin: 10px;
    }
</style>
{{ Form::open(array('url' => 'activities/' . $activity->id,'id' => 'edit_activity_form', 'files' => true)) }}
    {{ Form::hidden('_method', 'PUT') }}
    {{ csrf_field() }}
    <div class="col-md-4" style="">
        <div class="section-header">
            <h4>Edit Activity</h4>
        </div>
        <div class="panel panel-container">
            <div class="row margin-container">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="title" class="form-control" value="{{ $activity->title}}" required>
                </div>
                <div class="form-group">
                    <label>Subtitle</label>
                    <input type="text" name="subtitle" value="{{ $activity->subtitle}}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" class="form-control" style="min-height: 150px;height: 250px; resize: vertical;">{{ $activity->message}}</textarea>
                </div>
                <br>

                    <img src="{{$activity->image_url}}" style="width: 200px" id="img_holder" class="{{ (isset($activity->image_url) || $activity->image_url != '') ? '' : 'hidden'}}">
                <br>
                <br>
                
                <div class="form-group">
                    <label>Image Attachment</label>
                    <input type="file" name="image_url" >
                </div>
                <br>
                <br>
                <div class="form-group">
                    <label>Activity Date</label>
                    <input type="text" name="activity_date" class="form-control datepicker" required value="{{ slashedDate($activity->activity_date) }}">
                </div>
                <br>
                <br>
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
     $('#edit_activity_form').submit(function(){
        changed = false;
     });
     
     window.onbeforeunload = function(){
        if(changed){
            return '';
        }
     }

    function readURL(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          $('#img_holder').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
      }
    }
    $("input[name=image_url]").change(function() {
      readURL(this);
    });
</script>
@endsection