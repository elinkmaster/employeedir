@extends('layouts.main')
@section('title')
View Profile
@endsection
@section('pagetitle')
Employee Information
@endsection
@section('content')
<style type="text/css">
    .card-title {
        font-size: 16px;
        line-height: 21px;
        margin-top: 15px;
        font-weight: 400;
        color: black;
    }
    .card-subtitle{
        font-size: 12px;
        color: #878;
    }
    .label-profile{
        padding-left: 15px; padding-right: 15px;
    }
    .employee-details-value{
        font-size: 16px;
        line-height: 21px;
        padding-bottom: 10px;
        color: black;
    }
    .form-group label{
        font-weight: 600;
        color: #878;
    }
    .col-md-9 hr{
        margin: 0px;
    }
</style>
<br>    
<div>
    <?php $class = "col-md-12";?>
    @auth
    @if(Auth::user()->id != $employee->id)
        <?php $class="col-md-9" ?>
        <div class="col-md-3" style="padding-left: 0px !important; padding-right: 0px;">
            <div class="section-header">
                <h4>Profile Picture</h4>
            </div>
            <div class="panel panel-container">
                <div class="row no-padding">
                    <center>
                    <img alt="image" class="img-circle" style="width: 150px; height: 150px; margin-top: 30px;" src="{{ $employee->profile_img }}">
                    <br>
                    <h4 class="card-title m-t-10" style="font-size: 16px;line-height: 21px;margin-top: 15px;font-weight: 400;color: black;">
                        {{ $employee->fullname() }}
                    </h4>
                    <h6 class="card-subtitle">{{ $employee->position_name }}</h6>
                    <h6 class="card-subtitle">{{ $employee->team_name }}</h6>
                    <hr>
                    </center>
                    <span class="pull-left label-profile">Production Date: <i>{{ $employee->prettyproddate() }}</i></span>
                    <br>
                    <br>
                </div>
            </div>
        </div>
    @endif
    @endauth
    <div class="{{ $class }}">
        <div class="section-header">
            <h4>Employee Information</h4>
        </div>

        <div class="panel panel-container">
            <div class="panel-body">
                <label>Personal Information</label>
                <hr style="border-top: 1px dashed #dadada; margin-top: 1px; margin-bottom: 10px;">
                <br>
                @if(Auth::guest())
                <div class="col-md-2">
                    <img alt="image" class="img" style="width: 150px; margin-top: -10px;" src="{{ $employee->profile_img }}">
                </div>
                @endif

                @if(Auth::guest())
                <div class="col-md-10">
                @else
                <div class="col-md-12">
                @endif
                    <div class="row">
                        <div class="col-md-2 min-widt-200">
                            <div class="form-group">
                                <label>First Name</label>
                                <p class="employee-details-value name-format">{{ $employee->first_name}}</p>
                            </div>
                        </div>
                        <div class="col-md-2 min-widt-200">
                            <div class="form-group">
                                <label>Middle Name</label>
                                 <p class="employee-details-value name-format">{{ $employee->middle_name}}</p>
                            </div>
                        </div>
                        <div class="col-md-2 min-widt-200">
                            <div class="form-group">
                                <label>Last Name</label>
                                <p class="employee-details-value name-format">{{ $employee->last_name}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 min-widt-200">
                            <div class="form-group">
                                <label>Employee ID</label>
                                <p class="employee-details-value">{{ $employee->eid}}</p>
                            </div>
                        </div>
                        <div class="col-md-2 min-widt-200">
                            <div class="form-group">
                                <label>Phone Name</label>
                                <p class="employee-details-value name-format">{{ $employee->alias}}</p>
                            </div>
                        </div>
                       <!--  <div class="col-md-3">
                            <div class="form-group">
                                <label>Gender</label>
                                <p class="employee-details-value">{{ $employee->gender()}}</p>
                            </div>
                        </div> -->
                        <div class="col-md-2 min-widt-200">
                            <div class="form-group">
                                <label>Birthdate</label>
                                <p class="employee-details-value">{{ $employee->prettybirthdate()}}</p>
                            </div>
                        </div>
                    </div>
                    <br>
                </div>
                    <label>Job Information</label>
                    <hr style="border-top: 1px dashed #dadada; margin-top: 1px; margin-bottom: 10px;">
                    <br>
                    <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3 min-widt-200">
                            <div class="form-group">
                                <label>Position</label>
                                <p class="employee-details-value">{{ $employee->position_name}}</p>
                            </div>
                        </div>
                        <div class="col-md-3 min-widt-200">
                            <div class="form-group">
                                <label>Supervisor</label>
                                <p class="employee-details-value name-format">{{ $employee->supervisor_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-3 min-widt-200">
                            <div class="form-group">
                                <label>Manager</label>
                               <p class="employee-details-value name-format">{{  $employee->manager_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-3 min-widt-200">
                            <div class="form-group">
                                <label>Team/Department</label>
                                <p class="employee-details-value">{{ $employee->team_name}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 min-widt-200">
                            <div class="form-group">
                                <label>Hire Date</label>
                               <p class="employee-details-value">{{ $employee->prettydatehired()}}</p>
                            </div>
                        </div>
                        <div class="col-md-3 min-widt-200">
                            <div class="form-group">
                                <label>Status</label>
                               <p class="employee-details-value">{{ $employee->status()}}</p>
                            </div>
                        </div>
                        <div class="col-md-3 min-widt-200">
                            <div class="form-group">
                                <label>Account</label>
                               <p class="employee-details-value">{{ @$employee->account->account_name}}</p>
                            </div>
                        </div>
                          @if(isset($employee->ext))
                        <div class="col-md-3 min-widt-200">
                            <div class="form-group">
                                <label>Phone Extension</label>
                               <p class="employee-details-value">{{ @$employee->ext}}</p>
                            </div>
                        </div>
                        @endif
                        
                    </div>
                    <div class="row">
                        @if(isset($employee->prod_date))
                        <div class="col-md-3 min-widt-200">
                            <div class="form-group">
                                <label>Production date</label>
                               <p class="employee-details-value">{{ @$employee->prettyproddate()}}</p>
                            </div>
                        </div>
                        @endif
                        @if(isset($employee->wave))
                        <div class="col-md-3 min-widt-200">
                            <div class="form-group">
                                <label>Wave Number</label>
                               <p class="employee-details-value">{{ $employee->wave == "" ? "--" : $employee->wave }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    <br>
                </div>
                <br>
                <label>Login Credentials</label>
                <hr style="border-top: 1px dashed #dadada; margin-top: 1px; margin-bottom: 10px;">
                <br>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Email</label>
                                <br>
                                <a href="mailto:{{ $employee->email}}"><span class="employee-details-value">{{ $employee->email}}</span></a>
                            </div>
                        </div>
                        @if(isset($employee->email2) && $employee->email2 != "")
                        <div class="col-md-12 min-widt-200">
                            <div class="form-group">
                                <label>Email 2</label>
                                <br>
                                <a href="mailto:{{ $employee->email2}}">
                                    <span class="employee-details-value">{{ $employee->email2}}</span>
                                </a>
                            </div>
                        </div>
                        @endif
                        @if(isset($employee->email3) && $employee->email3 != "")
                        <div class="col-md-12 min-widt-200">
                            <div class="form-group">
                                <label>Email 3</label>
                                <br>
                                <a href="mailto:{{ $employee->email3}}">
                                    <span class="employee-details-value">{{ $employee->email3}}</span>
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                    <br>
                    @auth
                    @if(Auth::user()->id == $employee->id)
                        <br>
                        <div class="row">
                            <div class="col-md-3" style="display: flex;">
                                <a type="button" class="btn btn-default" href="{{url('employee/'. $employee->id .'/changepassword')}}">Change Password</a>
                                &nbsp;
                                <a class="btn btn-primary" href="/update-profile">Update Selected Information</a>
                            </div>
                        </div>
                    @endif
                    @endauth
                </div>
            </div>
        </div>
    <!-- MY REQUESTS -->
@if(isset($my_requests))
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">Leave History</div>
            <div class="pane-body panel">
                <br>
                <br>
                <table class="table">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Employee</td>
                            <td>Leave Dates</td>
                            <td>No. Of Days</td>
                            <td>Status</td>
                            <td>Date Requested</td>
                            <td width="100px">Options</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($my_requests as $leave_request)
                        <tr>
                            <td>{{ $leave_request->id }}</td>
                            <td>{{ $leave_request->employee->fullName2() }}</td>
                            <td>{{ prettyDate($leave_request->leave_date_from) }} - {{ prettyDate($leave_request->leave_date_to) }} </td>
                            <td>{{ (int)$leave_request->number_of_days }}</td>
                            <td>{{ $leave_request->status() }}</td>
                            <td>{{ prettyDate($leave_request->date_filed) }}</td>
                            <td width="100px" align="center">
                                <a href="{{url('leave') . '/' . $leave_request->id}}" title="View" data-id="{{ $leave_request->id }}" class="btn_view"><span class="fa fa-eye"></span></a>
                                &nbsp;&nbsp;
                                <!-- <a href="" title="Edit"><span class="fa fa-pencil"></span></a>
                                &nbsp;&nbsp; -->
                                <!-- <a href="" title="Approve"><span class="fa fa-thumbs-up"></span></a>
                                &nbsp;&nbsp;
                                <a href="" title="Disapprove"><span class="fa fa-thumbs-down"></span></a> -->
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif
    </div>
</div>
@endsection