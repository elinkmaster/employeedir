@extends('layouts.main')
@section('title')
    Referral Lists
@endsection
@section('pagetitle')
    Referral&nbsp;&nbsp;&nbsp;<span class="text-muted">/</span>&nbsp;&nbsp;Lists
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Job Referrals
                </div>
                <div class="panel-body panel no-padding">
                    <div class="row">
                        <div class="col-md-12">
                            <br>
                            <br>
                            <br>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <td>ID</td>
                                        <td>Position Applied</td>
                                        <td>Referral Name</td>
                                        <td>Contact Number</td>
                                        <td>Email</td>
                                        <td>Referred By</td>
                                        <td>Referred Date</td>
                                        <td>Option</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($referrals as $referral)
                                    <tr>
                                        <td>{{ $referral->id }}</td>
                                        <td>{{ $referral->position_applied }}</td>
                                        <td>{{ $referral->getReferralFullName() }}</td>
                                        <td>{{ $referral->referral_contact_number }}</td>
                                        <td>{{ $referral->referral_email }}</td>
                                        <td>{{ $referral->getReferrerFullName() }}</td>
                                        <td>{{ prettyDate($referral->created_at) }}</td>
                                        <td>
                                            <a href="{{ url('referral') . '/' . $referral->id }}"><span class="fa fa-eye"></span></a>&nbsp;&nbsp;
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
@endsection