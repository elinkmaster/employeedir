@extends('layouts.main')
@section('content')
<style type="text/css">
	img.preview{
	    width: 100%;
    	height: 100%;
		float: left;
	}
	div.gallery{
		display: inline-block;
	}
</style>
<div class="panel panel-default">
	<div class="panel panel-heading">
		Create Post
	</div>
	<div class="panel-body">
		<div class="col-md-4">
			<form id="post_form" method="POST" action="{{ url('posts') }}" enctype="multipart/form-data">
				{{ csrf_field() }}
				<div class="form-group">
					<label>Images</label>
					<input type="file" name="images_videos" id="images_videos">
				</div>
				<button class="btn btn-primary pull-right">Submit</button>
			</form>
		</div>
		<div class="col-md-8">
			<div class="gallery">
				<div class="gallery-row col-md-3 no-padding" id="div1">
				</div>
				<div class="gallery-row col-md-3 no-padding" id="div2">
				</div>
				<div class="gallery-row col-md-3 no-padding" id="div3">
				</div>
				<div class="gallery-row col-md-3 no-padding" id="div4">
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
	$(function() {
    // Multiple images preview in browser
    var imagesPreview = function(input) {
    var counter = 0;
        if (input.files) {
            var filesAmount = input.files.length;

            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();

                reader.onload = function(event){
                    $($.parseHTML('<img class="preview">')).attr('src', event.target.result).appendTo('#div' + ((counter++ % 4) + 1));
                }
                reader.readAsDataURL(input.files[i]);
            }
        }

    };

    $('#images_videos').on('change', function() {
    	$('div.gallery-row').empty();
        imagesPreview(this, 'div.gallery');
    });
});
</script>
@endsection