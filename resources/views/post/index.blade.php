@extends('layouts.main')
@section('content')
<style>
	.switch {
		position: relative;
		display: inline-block;
		width: 60px;
		height: 34px;
		margin-bottom: 0px;
	}
	.switch input {
		display: none;
	}
	.slider {
		position: absolute;
		cursor: pointer;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background-color: #ccc;
		-webkit-transition: .4s;
		transition: .4s;
	}
	.slider:before {
		position: absolute;
		content: "";
		height: 26px;
		width: 26px;
		left: 4px;
		bottom: 4px;
		background-color: white;
		-webkit-transition: .4s;
		transition: .4s;
	}
	input.primary:checked + .slider {
		background-color: #2196F3;
	}
	input:checked + .slider:before {
		 -webkit-transform: translateX(26px);
		 -ms-transform: translateX(26px);
		 transform: translateX(26px);
	 }
	#post-table{
		width: 1000px;
	}
	.post-image{
		width: 400px;
		margin: 10px;
	}
	.image-id{
		padding: 10px;
	}
	.btn-delete{
		font-size: 40px;
		margin-left: 15px;
		margin-bottom: 5px;
	}
	.fa-trash{
		color: red;
	}
</style>
<div class="panel panel-default">
	<div class="panel panel-heading">
		Posts
		<a class="pull-right btn btn-primary" href="{{ url('posts/create') }}">Create New Post</a>
	</div>
	<div class="panel-body">
		<br>
		<table id="post-table" class="table-striped">
			<thead>
				<tr>
					<td>ID</td>
					<td >Image</td>
					<td align="center">Option</td>
				</tr>
			</thead>
			<tbody>
				@foreach($posts as $post)
				<tr>
					<td class="image-id">{{ $post->id }}</td>
					<td width="420px"><img src="{{ $post->image }}" class="img-thumbnail post-image"></td>
					<td align="center">
						<label class="switch" title="Enable/Disable">
							<input type="checkbox" {{ $post->enabled == 1 ? 'checked' : '' }} name="leave_type_id" value="1" id="progress1" tabIndex="1" class="primary" data-id="{{ $post->id }}" >
							<span class="slider"></span>
						</label>
						<a href="#" class="btn-delete delete_btn" data-toggle="modal" data-target="#messageModal" title="Delete" data-id="{{$post->id}}">
							<i class="fa fa-trash"></i>
						</a>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@endsection
@section('scripts')
	<script>
		$(document).on('change', 'input[type=checkbox]', function(){
			let enabled;
			let id = $(this).data('id');
			if($(this).is(":checked")){
				enabled = 1;
			}else{
				enabled = 0;
			}
            $.LoadingOverlay("show");
			setTimeout(function(){
                $.LoadingOverlay("hide");
				window.location.replace("{{ url('posts')}}" + "/" + id + "/enabled?enabled=" + enabled);
			}, 1000);
		});
		$('.delete_btn').click(function(){
			$('#messageModal .modal-title').html('Delete Post');
			$('#messageModal #message').html('Are you sure you want to delete the post ?');
			$('#messageModal .delete_form').attr('action', "{{ url('posts') }}/" + $(this).attr("data-id"));
		});
		$('#messageModal #yes').click(function(){
			$('#messageModal .delete_form').submit();
		});
	</script>
@endsection