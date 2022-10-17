@extends('layouts.main')
@section('title')
Activity / Add New
@endsection
@section('pagetitle')
Activity / Add New
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
    <form id="create_activity_form" role="form" method="POST" action="{{ route('activities.store')}}" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="col-md-4" style="">
            <div class="section-header">
                <h4>New Activity</h4>
            </div>
            <div class="panel panel-container">
                <div class="row margin-container">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Subtitle</label>
                        <input type="text" name="subtitle" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" class="form-control" style="min-height: 150px;height: 250px; resize: vertical;"></textarea>
                    </div>
                    <br>
                    <img src="" style="width: 200px" id="img_holder">
                    <br>
                    <br>
                    <div class="form-group">
                        <label>Image Attachment</label>
                        <input type="file" name="image_url" required>
                    </div>
                    <br>
                    <br>
                    <div class="form-group">
                        <label>Activity Date</label>
                        <input type="text" name="activity_date" class="form-control datepicker" required>
                    </div>
                    <br>
                    <br>
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
        $('#create_activity_form').validate({
            ignore: []
        });
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