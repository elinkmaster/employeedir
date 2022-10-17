@extends('layouts.main')
@section('title')
    Referral Form
@endsection
@section('pagetitle')
    Referral&nbsp;&nbsp;&nbsp;<span class="text-muted">/</span>&nbsp;&nbsp;View
@endsection
@section('content')
    <style>
        table{
            width: 100%;
            margin: 10px;
        }
        table tr td {
            border-bottom: 1px dashed #dadada;
            font-size: 13px;
            padding-top: 15px;
            padding-bottom: 5px;
        }
        table tr td:nth-child(2){
            font-weight: 600;
        }
    </style>
    <div class="row">
        <div class="col-md-5">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Job Referral
                </div>
                <div class="panel-body panel no-padding">
                    <div class="row">
                        <div class="col-md-12">
                            <table>
                                <tr>
                                    <td>Referral Name</td>
                                    <td>{{ $referral->getReferralFullName() }}</td>
                                </tr>
                                <tr>
                                    <td>Position Applied</td>
                                    <td>{{ $referral->position_applied }}</td>
                                </tr>
                                <tr>
                                    <td>Contact Number</td>
                                    <td>{{ $referral->referral_contact_number }}</td>
                                </tr>
                                <tr>
                                    <td>Email Address</td>
                                    <td>{{ $referral->referral_email }}</td>
                                </tr>
                                <tr>
                                    <td>Referrer Name</td>
                                    <td>{{ $referral->getReferrerFullName() }}</td>
                                </tr>
                                <tr>
                                    <td>Referrer Department</td>
                                    <td>{{ $referral->referrer_department }}</td>
                                </tr>
                                <tr>
                                    <td>Submitted Date</td>
                                    <td>{{ prettyDate($referral->created_at) }}</td>
                                </tr>
                            </table>
                            <br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
@endsection