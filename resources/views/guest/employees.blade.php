@extends('layouts.main')
@section('title')
Employees
@endsection
@section('pagetitle')
Employees
@endsection
@section('content')
<style type="text/css">
    .emp-profile{
        background-color: white;
    }
    .col-md-12{
        margin-bottom: 1px !important;
    }
    .emp-profile{
        margin: auto;
    }
    .header-container{
        margin-top: 20px;
    }
    #search_employee{
        padding-left: 5px;
    }
    .alphabet-search{
        display: inline-flex;
        list-style: none;
    }
    .alphabet-search li{
        margin-left: 10px;
    }
    .header-list{

    }
    .employee-description{
        color: #777;
        font-size: 12px;
    }
    h1, h2, h3, h4, h5, h6 {
        color: #777;
    }
    li a.selected{
        font-weight: 900!important;
    }
    .pagination>li:first-child>a, .pagination>li:first-child>span{
        border-top-left-radius: 0px !important;
        border-bottom-left-radius: 0px !important;
    }
    .pagination>li:last-child>a, .pagination>li:last-child>span {
        border-top-right-radius: 0px !important;
        border-bottom-right-radius: 0px !important;
    }
    .emp-profile .fa{
        color: #555 !important;
    }
    select{
        cursor: pointer !important;
    }
</style>

<div class="col-md-12">
<div class="header-container" style="margin-bottom: 5px;">
    <ul class="alphabet-search" style="padding-left: 0px">
        <li style="margin-left: 0px">
            <form style="display: unset;">
                <input type="hidden" name="alphabet" value="{{ $request->alphabet }}">
                <input type="hidden" name="department" value="{{ $request->department }}">
                <input type="text" placeholder="Search by name" id="search_employee" name="keyword" value="{{ $request->keyword }}">
                <button class="btn btn-primary" style="height:  35px; margin-top: 1px;">
                    <span class="fa fa-search"></span>
                </button>
            </form>
        </li>
    </ul>
    <ul class="alphabet-search">
        <li>
            <a href="?alphabet=">All</a>
        </li>   
        @foreach (range('A', 'Z') as $letter)
            <li>
                <a <?php echo $request->alphabet == $letter ? "class='selected'" : '' ?> style="font-weight: 500;" href="?alphabet={{ $letter . "\n" . "&keyword=" . $request->keyword . "&department=" . $request->department }}" >{{ $letter . "\n" }}</a>
            </li>
        @endforeach
        
    </ul>
    <ul class="alphabet-search pull-right">
        <li>

            <span class="fa fa-filter" title="Filter By" style="color: #777777; font-size: 18px; padding: 5px"></span>
            <select id="sort_option_list" style="padding: 7px; border-radius: 0px !important; font-size: 11px !important;">
                <option value="1" {{ isset($request->department) ? "selected" : "" }}>Department</option>
                <option value="2" {{ isset($request->position) ? "selected" : "" }}>Position</option>
                <option value="3" {{ isset($request->birthmonth) ? "selected" : "" }}>Birth Month</option>
            </select>
        </li>
        <li>
            <select style="padding: 7px; border-radius: 0px !important; font-size: 11px !important;" id="departments_list">
                <option selected>Search by department:</option>
                @foreach( $departments as $department)
               <option <?php echo $request->department == $department->department_name ? "selected" : "";?> >{{ $department->department_name}}</option>
               @endforeach
           </select>
           <select style="padding: 7px; border-radius: 0px !important; font-size: 11px !important; display: none;" id="position_list">
                <option selected>Search by Position:</option>
                @foreach( $positions as $position)
               <option <?php echo $request->position == $position->position_name ? "selected" : "";?> >{{ $position->position_name}}</option>
               @endforeach
           </select>
            <select style="width: 200px; border-color: #ddd; padding: 7px; border-radius: 0px !important; font-size: 11px !important; display: none;" id="month_list">
                <option selected>Search by Birth Month:</option>
                @for( $m = 1; $m <= 12 ; $m++)
                <option value="{{ $m }}" <?php echo $request->birthmonth == $m ? "selected" : "";?> >{{ date('F', mktime(0,0,0,$m, 1, date('Y'))) }}</option>
                @endfor
            </select>
       </li>
    </ul>
</div>
@if(count($employees) == 0)
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <center>
        <h3>No results found.</h3>
    </center>
@endif
@foreach($employees as $employee)
    <div class="col-md-12" style="padding-left: 0px; padding-right: 0px; ">
        <div class="emp-profile" style="padding: 10px; margin-bottom: 0px;">
            <div class="row">
                <div class="col-md-1" style="float: left; width: 100px;">
                    <div style="background-image: url({{$employee->profile_img}}); width: 60px; height: 60px;margin: 15px; background-size: cover; background-repeat: no-repeat; background-position: 50% 50%; border-radius: 50%;">
                    </div>
                </div>
                <div class="col-md-4">
                        <h4 class="timeline-title name-format" style="color: #444;font-weight: 500; font-size: 17px; margin-top: 10px;">
                            {{$employee->fullname()}}
                            <!-- <a href="{{url('profile/'. $employee->id)}}">{{$employee->fullname()}} </a> -->
                        </h4>
                   
                    <h5 style="color: #455;">{{ $employee->position_name}}</h5>
                    <h6>{{$employee->team_name}} <?php echo isset($employee->account) ? "- ". $employee->account->account_name : "" ; ?></h6>
                </div>
                <div class="col-md-3">
                    <h5>
                        <span class="fa fa-id-card" title="Employee ID"></span>
                        <span class="employee-description">&nbsp;&nbsp;{{$employee->eid}}</span>
                    </h5>
                    <h5>
                        <span class="fa fa-envelope" title="Email Address"></span>
                        <span class="employee-description" style="color: #0c59a2;;">&nbsp;&nbsp;{{$employee->email}}</span>
                    </h5>
                    @if(isset($employee->ext) && $employee->ext != '--' && $employee->ext != '')
                    <h5>
                         <span class="fa fa-phone" title="Extension Number"></span>
                        <span class="employee-description" >&nbsp;&nbsp;{{$employee->ext}}</span>
                    </h5>
                    @endif
                    @if(isset($employee->alias) && $employee->alias != '--' && $employee->alias != '')
                    <h5>
                         <span class="fa fa-mobile" title="Phone Name"></span>
                        <span class="employee-description" >&nbsp;&nbsp;{{$employee->alias}}</span>
                    </h5>
                    @endif
                </div>
                <div class="col-md-3">
                    @if(isset($employee->supervisor_name))
                    <h5 style="font-size: 12px;">
                        <span class="fa fa-user" title="Supervisor"></span>
                        <span class="name-format" style="color: gray;">Supervisor:</span>
                        {{$employee->supervisor_name}}
                    </h5>
                    @endif
                    @if(isset($employee->manager_name))
                        <h5 style="font-size: 12px;">
                            <span class="fa fa-user" title="Manager"></span>
                            <span style="color: gray;">Manager: </span>
                            <span class="name-format">{{ $employee->manager_name }}</span>
                        </h5>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
    <div class="col-md-12 header-container" style="margin-top: 0px;">
        <div class="pull-right">
            {{ $employees->appends(Illuminate\Support\Facades\Input::except('page'))->links() }}
        </div>
    </div>
@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $('#sort_option_list').trigger('change');
    });
    $('#departments_list').change(function(){
        var url = location.protocol + '//' + location.host + location.pathname;
        var keyword = "keyword=" + $("#search_employee").val();
        var alphabet = "alphabet=" + $('input[name=alphabet]').val();
        var department = "department=" + $(this).val();
        url += "?" + keyword + "&" + alphabet + "&" + department;
        window.location.replace(url);
    });

    $('#sort_option_list').change(function(){
        switch($(this).val()){
            case '1':
                $('#departments_list').show();
                $('#position_list').hide();
            break;
            case '2':
                $('#departments_list').hide();
                $('#position_list').show();
            break;
            case '3':
                $('#departments_list').hide();
                $('#position_list').hide();
                $('#month_list').show();
            break;
        }
    });

    $('#month_list').change(function(){
            var url = location.protocol + '//' + location.host + location.pathname;
            var position = "birthmonth=" + $(this).val();
            url += "?" + position;
            window.location.replace(url);
        });
    
    $('#position_list').change(function(){
        var url = location.protocol + '//' + location.host + location.pathname;
        var keyword = "keyword=" + $("#search_employee").val();
        var alphabet = "alphabet=" + $('input[name=alphabet]').val();
        var position = "position=" + $(this).val();
        url += "?" + keyword + "&" + alphabet + "&" + position;
        window.location.replace(url);
    });
</script>
@endsection 