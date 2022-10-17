
@if(true)
<div class="panel-footer">
    <a><span class="fa fa-caret-down"></span>&nbsp; Expand Comments</a>
    @if(Auth::check())
    <div class="row">
    	<div class="col-md-12">
    	</div>
	    <div class="col-md-12">
	    	<form class="comment_form">
	    		<div class="form-group">
	    			<input type="hidden" name="post_id">
		    		<textarea rows="2" class="form-control comment-editor" name="comment"></textarea>
	    		</div>
		    	<button class="btn btn-primary pull-right">Comment</button>
	    	</form>
	    </div>
    </div>
    @else
    	<p><br></p>
    	<p><a href="{{ url('login') }}">Login to Comment</a></p>
    @endif
</div>
@endif