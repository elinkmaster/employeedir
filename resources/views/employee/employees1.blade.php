@extends('layouts.main')
@section('title')
Employees
@endsection
@section('pagetitle')
Employees
@endsection
@section('content')
    <style type="text/css">
        #employees_table_wrapper{
            background-color: #fff;
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
        table tr {
            background-color: #fff;
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
        a.btn.btn-primary, a.btn.btn-warning, a.btn.btn-success{
            float: right;
            margin: 10px;
        }
        .alphabet-search{
        display: inline-flex;
        list-style: none;
        }
        .alphabet-search li{
            margin-left: 10px;
        }
        .table-striped>tbody>tr:nth-of-type(odd) {
            background-color: #fff !important;
        }
        .table-striped>tbody>tr:nth-of-type(even) {
            background-color: #fbfbfb !important;
        }
        .table-striped > thead > tr {
            background-color: #f8f8f8 !important;
            padding-top: 10px;
        }
    </style>

    <a href="{{url('employee_info/create')}}" class="btn btn-primary" >
        <i class="fa fa-plus"></i>
        &nbsp;&nbsp;Add Employee
    </a>
    <div class="section-header">
        <h4>List of Employees</h4>
    </div>
    <ul class="alphabet-search pull-right" style="position: absolute; padding: 8px; z-index: 1 !important">
        <li>

            <span class="fa fa-filter" title="Filter By" style="color: #777777; font-size: 18px; padding: 5px"></span>
            <select id="sort_option_list" style="border-color: #ddd; padding: 7px; border-radius: 0px !important; font-size: 12px !important;">
                <option value="1" {{ isset($request->department) ? "selected" : "" }}>Department</option>
                <option value="2" {{ isset($request->position) ? "selected" : "" }}>Position</option>
                <option value="3" {{ isset($request->birthmonth) ? "selected" : "" }}>Birth Month</option>
            </select>
        </li>
        <li>
            <select style="width: 200px; border-color: #ddd;padding: 7px; border-radius: 0px !important; font-size: 13px !important;" id="departments_list">
                <option selected>Search by department:</option>
                @foreach( $departments as $department)
               <option <?php echo $request->department == $department->department_name ? "selected" : "";?> >{{ $department->department_name}}</option>
               @endforeach
            </select>
            <select style="width: 200px; border-color: #ddd; padding: 7px; border-radius: 0px !important; font-size: 13px !important; display: none;" id="position_list">
                <option selected>Search by Position:</option>
                @foreach( $positions as $position)
                <option <?php echo $request->position == $position->position_name ? "selected" : "";?> >{{ $position->position_name}}</option>
               @endforeach
            </select>
            <select style="width: 200px; border-color: #ddd; padding: 7px; border-radius: 0px !important; font-size: 13px !important; display: none;" id="month_list">
                <option selected>Search by Birth Month:</option>
                @for( $m = 1; $m <= 12 ; $m++)
                <option value="{{ $m }}" <?php echo $request->birthmonth == $m ? "selected" : "";?> >{{ date('F', mktime(0,0,0,$m, 1, date('Y'))) }}</option>
                @endfor
            </select>
        </li>
        <li>
           <a href="{{url('employees')}}" class="btn btn-default" style="margin: 0px; height: 30px;">Clear Filter</a>
        </li>
        <li style="margin-top: -5px">
            &nbsp;
            &nbsp;
            <label>Inactive Employees</label>
            <input type="radio" id="inactive_employees" {{ $request->inactive == 'true' ? 'checked' : '' }}>
            <br>
            &nbsp;
            &nbsp;
            <label>No Profile Images</label>
            <input type="radio" id="no_profile_images" {{ $request->no_profile_images == 'true' ? 'checked' : '' }}>
        </li>
        <li>

            &nbsp;
            &nbsp;
            <label>Invalid Birthday</label>
            <input type="radio" id="invalid_birth_date" {{ $request->invalid_birth_date == 'true' ? 'checked' : '' }}>
        </li>
    </ul>
	<table id="employees_table" class="table table-striped">
        <thead>
            <tr>
                <td data-priority="1">#</td>
                <td data-priority="2">Employee</td>
                <td data-priority="4">Email <br> <small>Phone name and ext</small></td>
                <td>Team/Department</td>
                <td>Supervisor</td>
                <!-- <td >Manager</td> -->
                <!-- <td >Division</td> -->
                <!-- <td >Account</td> -->
                <td>Production Date</td>
                <td data-priority="3">Action</td>
            </tr>        
        </thead> 
        <tbody>
            <?php $counter = 0; ?>
            @foreach($employees as $employee)
                <tr> 
                    <td>{{ ++$counter }}</td>
                    <td >
                        @if(isset($employee->profile_img))
                         <div style="background-image: url('{{ $employee->profile_img }}'); width: 40px; height: 40px; background-size: cover; background-repeat: no-repeat; background-position: 50% 50%; box-shadow: 1px 1px 10px 7px #fff; float: left; margin-right: 10px;">
                        </div>
                         @else
                        <div class="circle pull-left" style="float: left !important">J</div>
                        @endif
                        <h5 style="text-align: left !important;">
                            {{ $employee->first_name . ' ' .  $employee->last_name  }}
                        </h5>
                        <small style="text-align: left !important;">
                            {{ $employee->position_name }}
                        </small>
                    </td>
                    <td><a href="mailto:{{$employee->email}}"> {{ $employee->email }} 
                        </a>
                        <br>
                        {{ $employee->alias }}
                        @if($employee->ext != '' && isset($employee->ext))
                        <br>
                        <small>ext: {{$employee->ext}}</small>
                        @endif
                    </td>
                    <td >{{ $employee->team_name }}</td>
                    <td>{{ @$employee->supervisor_name }}</td>
                    <!-- <td>{{ @$employee->manager_name }}</td> -->
                    <!-- <td>{{ @$employee->division_name }}</td> -->
                    <!-- <td>{{ @$employee->account->account_name }}</td> -->
                    <td>{{ $employee->prodDate() }}</td>
                    <td>

                        <a href="{{ url('/employee_info/'. $employee->id)}}" title="View">
                            <i class="fa fa-eye"></i>
                        </a>&nbsp;&nbsp;
                        
                        <a href="{{ url('/employee_info/'. $employee->id . '/edit')}}" title="Edit">
                            <i class="fa fa-pencil"></i>
                        </a>&nbsp;&nbsp;
                        @if($employee->deleted_at == null)
                        <a href="#"  class="delete_btn" data-toggle="modal" data-target="#messageModal" title="Deactivate" data-id="{{$employee->id}}">
                            <i class="fa fa-user-times" style="color: red;" ></i>
                        </a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <script type="text/javascript">
        $('.delete_btn').click(function(){
            $('#messageModal .modal-title').html('Delete Employee');
            $('#messageModal #message').html('Are you sure you want to delete the employee ?');

            $('#messageModal .delete_form').attr('action', "{{ url('employee_info') }}/" + $(this).attr("data-id"));
        });
        $('#messageModal #yes').click(function(){
                $('#messageModal .delete_form').submit();
        });
        $(document).ready(function(){
            $('#sort_option_list').trigger('change');
        });
        $('#departments_list').change(function(){
            var url = location.protocol + '//' + location.host + location.pathname;
            var department = "department=" + $(this).val();
            url += "?" + department;
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

        $('#position_list').change(function(){
            var url = location.protocol + '//' + location.host + location.pathname;
            var position = "position=" + $(this).val();
            url += "?" + position;
            window.location.replace(url);
        });

        $('#month_list').change(function(){
            var url = location.protocol + '//' + location.host + location.pathname;
            var position = "birthmonth=" + $(this).val();
            url += "?" + position;
            window.location.replace(url);
        });


        $('#inactive_employees').change(function(){
            var url = location.protocol + '//' + location.host + location.pathname;
            if($(this).is(':checked')){
                var inactive = "inactive=" + true;
                url += "?" + inactive;
                window.location.replace(url);
            }else{
                window.location.replace(url);
            }
        });
        $('#no_profile_images').change(function(){
            var url = location.protocol + '//' + location.host + location.pathname;
            if($(this).is(':checked')){
                var no_profile_images = "no_profile_images=" + true;
                url += "?" + no_profile_images;
                window.location.replace(url);
            }else{
                window.location.replace(url);
            }
        });

        $('#invalid_birth_date').change(function(){
            var url = location.protocol + '//' + location.host + location.pathname;
            if($(this).is(':checked')){
                var no_profile_images = "invalid_birth_date=" + true;
                url += "?" + no_profile_images;
                window.location.replace(url);
            }else{
                window.location.replace(url);
            }
        });
    </script>
@endsection