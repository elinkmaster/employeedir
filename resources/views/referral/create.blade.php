@extends('layouts.main')
@section('title')
    Referral Form
@endsection
@section('pagetitle')
    Referral&nbsp;&nbsp;&nbsp;<span class="text-muted">/</span>&nbsp;&nbsp;Create
@endsection
@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="panel panel-default">
            <div class="panel-heading">
                Job Referral
            </div>
            <div class="panel-body panel no-padding">
                <div class="row">
                    <div class="col-md-12">
                        <form method="POST" action="{{ url('referral') }}">
                            {{ csrf_field() }}

                            <div class="col-md-12">
                                <br>
                                <b>Referrer <span class="text-muted" style="font-weight: 400;">(You)</span></b>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input class="form-control" name="referrer_first_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Middle Name</label>
                                    <input class="form-control" name="referrer_middle_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input class="form-control" name="referrer_last_name">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Department</label>
                                    <input class="form-control" name="referrer_department">
                                </div>
                            </div>
                            <div class="col-md-12 no-padding">
                                <hr>
                            </div>
                            <div class="col-md-12">
                                <b>Referral</b>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input class="form-control" name="referral_first_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Middle Name</label>
                                    <input class="form-control" name="referral_middle_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input class="form-control" name="referral_last_name">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Contact Number</label>
                                    <input class="form-control" name="referral_contact_number">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input class="form-control" name="referral_email">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Position Applied</label>
                                    <input class="form-control" name="position_applied">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-primary">Submit</button>
                                <br>
                            </div>
                            <div class="col-md-12">
                                <br>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@endsection