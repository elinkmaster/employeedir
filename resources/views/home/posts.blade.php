@foreach($engagements as $engagement)
<div class="panel panel-default ">
    <div class="panel-body timeline-container" >
    <b class="engagement_title" data-id="{{$engagement->id}}">{{ $engagement->title}}</b>
    <br>
    <small class="engagement_title" data-id="{{$engagement->id}}">{{ $engagement->subtitle}}</small>
    <br>
    <br>
     @if(isset($engagement->image_url) || $engagement->image_url != "")
        @if(pathinfo($engagement->image_url, PATHINFO_EXTENSION) == "mp4")
            <video controls style="width: 100%;">
              <source src="{{ $engagement->image_url}}" type="video/mp4">
              <source src="{{ $engagement->image_url}}" type="video/ogg">
            Your browser does not support the video tag.
            </video>
        @else
        <img class="engagement_title" data-id="{{$engagement->id}}" src="{{ $engagement->image_url}}" style="width: 100%;">
        @endif
    <br>
    <br>
    @endif
    <p>{{ $engagement->message }}</p>
    <small style="margin-right: 20px;">{{ monthDay($engagement->activity_date) }}</small>
    </div>
        @include('home.comments')
    </div>
@endforeach
@if(count($engagements) == 0)
<div class="panel panel-default ">
    <div class="panel-body timeline-container" >
        <center>No events yet</center>
    </div>
</div>
@endif