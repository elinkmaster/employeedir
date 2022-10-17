@extends('layouts.main')
@section('title')
Home
@endsection
@section('pagetitle')
Home
@endsection
@section('content')

@if(count($posts) > 0)
<div class="col-md-12">
    <h3><b>Announcement</b></h3>
</div>
<div class="col-md-12">
    <div class="panel panel-container" style="padding-top: 0px; background: none">
        <div class="row">
            <div id="myCarousel" class="carousel slide" data-ride="carousel">
                <!-- Indicators -->
                <?php $first=true;?>
                <ol class="carousel-indicators">

                    @foreach($posts as $post)
                        <li data-target="#myCarousel" data-slide-to="{{ $post->id }}" class="{{ $first ? 'active' : '' }}"></li>
                        <?php $first=false;?>
                    @endforeach
                </ol>

                <!-- Wrapper for slides -->
                <div class="carousel-inner">
                    <?php $first=true;?>
                    @foreach($posts as $post)
                        <div class="item {{ $first ? 'active' : '' }}">
                            <?php $first=false;?>
                            <img class="img-thumbnail" src="{{ $post->image }}">
                        </div>
                    @endforeach
                </div>

                <!-- Left and right controls -->
                <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#myCarousel" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div><!--/.row-->
    </div>
</div>
@endif
<div class="col-lg-4 col-md-4">
    <div class="panel panel-default ">
        <div class="panel-heading">
            New Hires
            <span class="pull-right clickable panel-toggle panel-button-tab-left"><em class="fa fa-toggle-up"></em></span></div>
        <div class="panel-body timeline-container">
            <ul class="timeline">
                @foreach($new_hires as $employee)
                    <li class="new_hires_div">
                        <div class="timeline-badge">
                            <div style="background-image: url('{{ $employee->profile_img }}'); width: 50px; height: 50px; margin-top: -10px; background-size: cover; background-repeat: no-repeat; background-position: 50% 50%; box-shadow: 1px 1px 10px 7px #fff; border-radius: 50%;">
                            </div>
                        </div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h4 class="timeline-title name-format"><a href="employee_info/{{$employee->id}}"> {{ $employee->fullname() }}</a></h4>
                            </div>
                            <div class="timeline-body">
                                <p>{{ joinGrammar($employee->prod_date) }} the {{ $employee->team_name }} as {{ $employee->position_name }}</p>
                            </div>
                            <div class="timeline-body">
                                <small>{{ $employee->prettyprodDate() }}</small>
                            </div>
                        </div>
                    </li>
                @endforeach

                @if(count($new_hires) == 0)
                    <style type="text/css">
                        .timeline:before{
                            display: none;
                        }
                    </style>
                    <center>
                        No employees so far
                    </center>
                @else
                    <center>
                        <br>
                        <br>
                        <span class="fa fa-spinner" id="new_hire_loader"></span>
                        <br>
                        <br>
                        <button class="btn" type="button" id="more_new_hires"><span class="fa fa-arrow-down"></span>&nbsp;&nbsp;View More</button>
                    </center>
                @endif
            </ul>
        </div>
    </div>
</div>
<div class="col-lg-4 col-md-4">
    <div class="panel panel-default ">
        <div class="panel-heading">
            Birthday Celebrants for {{ date('F') }}
            <span class="pull-right clickable panel-toggle panel-button-tab-left"><em class="fa fa-toggle-up"></em></span></div>
        <div class="panel-body timeline-container" >
            @if(count($birthdays) > 0)
                <h4 style=" font-weight: 600;font-size: 16px;text-align: center;padding: 11px;padding-left: 30px;font-family: cursive;">{{ date('F') }}</h4>
                <br>
                <div class="birthday-celebrants-div">
                    @foreach($birthdays as $celebrant)
                        <div class="birthday-holder">

                            <div style="background-image: url('{{ $celebrant->profile_img }}'); width: 50px; height: 50px; margin-right: 15px; background-size: cover; background-repeat: no-repeat; background-position: 50% 50%; float: left;">
                            </div>
                            <p class="name-format"><a href="employee_info/{{$celebrant->id}}" >{{ $celebrant->fullname() }}</a><br><span ><span class="fa fa-gift"></span> {{ monthDay($celebrant->birth_date) }}</span></p>
                        </div>
                    @endforeach
                </div>
            @else
                <center>
                    <br>
                    <span class="fa fa-birthday-cake fa-xl"></span>
                    <br>
                    <br>
                    No birthday celebrant for {{ date('F') }}
                </center>
            @endif
        </div>
    </div>
</div>
<div class="col-lg-4 col-md-4">
    <div class="panel panel-default ">
        <div class="panel-heading">
            eLinkgagement Activities
            <span class="pull-right clickable panel-toggle panel-button-tab-left"><em class="fa fa-toggle-up"></em></span></div>
        <div class="panel-body timeline-container" >
            @foreach($engagements as $engagement)
                <hr>
                <b class="engagement_title" data-id="{{$engagement->id}}">{{ $engagement->title}}</b>
                <br>
                <small class="engagement_title" data-id="{{$engagement->id}}">{{ $engagement->subtitle}}</small>
                <br>
                <br>
                @if(isset($engagement->image_url) || $engagement->image_url != "")
                    @if(pathinfo($engagement->image_url, PATHINFO_EXTENSION) == "mp4")
                        <video width="320" height="240" controls>
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

            @endforeach
            @if(count($engagements) == 0)
                <center>No events yet</center>
            @endif
        </div>
    </div>
</div>
<div class="col-md-12">
    <button class="pull-right" id="backtotop"><span class="fa fa-lg fa-arrow-up"></span>&nbsp;Back to top</button>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    var current_page = 2;
    $('.engagement_title').click(function(){
        $('#engagementmodal').modal('show');

        $.ajax(
        {
            url: "{{url('showactivities')}}" + "/" + $(this).attr('data-id'), 
            method: 'GET',
            success: function(result) 
            {
                console.log(result);
                $('#engagement_title').html(result.title);
                $('#engagement_subtitle').html(result.subtitle);
                $('#engagement_image').attr('src', result.image_url);
                $('#engagement_message').html(result.message);
                $('#engagement_date_posted').html(timeConverter2(result.created_at));
                console.log(result.title);
            }
        });
    });
    $(document).ready(function(){
        $('#new_hire_loader').hide(); 
    });
    $('#more_new_hires').click(function(){
        $('#new_hire_loader').show();
        $('#more_new_hires').hide();
        $.ajax({
            url: "{{url('newhires')}}" + "?page=" + current_page, 
            success: function(result){
                setTimeout(function(){ 
                    $('#new_hire_loader').hide(); 
                    $('#more_new_hires').show();
                    result.data.forEach(function(employee) {
                        var new_hires_div = '<li class="new_hires_div">';
                        new_hires_div += '<div class="timeline-badge">';
                        new_hires_div += '<div style="background-image: url(' + employee.profile_img +'); width: 50px; height: 50px; margin-top: -10px; background-size: cover; background-repeat: no-repeat; background-position: 50% 50%; box-shadow: 1px 1px 10px 7px #fff; border-radius: 50%;"></div></div>';
                        new_hires_div += '<div class="timeline-panel">';
                        new_hires_div += '<div class="timeline-heading">';
                        new_hires_div += ' <h4 class="timeline-title name-format"><a href="profile/' + employee.id + '">'+ employee.last_name + ', ' + employee.first_name +'</a></h4>';
                        new_hires_div += '</div>';
                        new_hires_div += '<div class="timeline-body">';
                        new_hires_div += '<p>' + joinGrammar(employee.prod_date) + ' the ' + employee.team_name + ' as ' + employee.position_name + '</p>';
                        new_hires_div += '</div>';
                        new_hires_div += '<div class="timeline-body">';
                        new_hires_div += '<small>' + timeConverter(employee.prod_date ) + '</small>';
                        new_hires_div += "</div>";
                        new_hires_div += "</div>";
                        new_hires_div += "</li>";
                        $('.new_hires_div:last-of-type').after(new_hires_div);
                    });
                }, 1500);
                current_page++;
            }, error: function(){
                $('#new_hire_loader').hide(); 
                $('#more_new_hires').show();
            }
        });
    });
    $('#backtotop').click(function(){
        $("html, body").animate({
            scrollTop: 0
        }, 300); 
    });
    $(window).scroll(function() {
       if($(window).scrollTop() + $(window).height() == $(document).height()) {
           
       }
    });

    $(function() {
        // $('.comment-editor').froalaEditor({toolbarInline: false, toolbarButtons: ['undo', 'redo' , 'bold', '|', 'emoticons'], emoticonsStep: 12, })
    });

    $('.comment_form').submit(function(){
        console.log($(this).serialize());
        return false;
    });
</script>
@endsection