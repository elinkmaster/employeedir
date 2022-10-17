@extends('layouts.main')
@section('title')
    Create Events
@endsection
@section('pagetitle')
    Events &nbsp;/&nbsp;Create
@endsection
@section('head')
    <link href="{{ asset('public/css/spectrum.css')}}" rel="stylesheet">
    <script src='{{ asset('public/js/spectrum.js')}}'></script>
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
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Events List
                </div>
                <div class="panel panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table>
                                <tr>
                                    <td>Event Name</td>
                                    <td>{{ $event->event_name }}</td>
                                </tr>
                                <tr>
                                    <td>Event Description</td>
                                    <td>{{ $event->description }}</td>
                                </tr>
                                <tr>
                                    <td>Start Date</td>
                                    <td>{{ prettyDate($event->start_date) }}</td>
                                </tr>
                                <tr>
                                    <td>End Date</td>
                                    <td>{{ prettyDate($event->end_date) }}</td>
                                </tr>
                                <tr>
                                    <td>Tip Color</td>
                                    <td><div style="background-color: {{ $event->event_color }}; width: 20px; height: 20px;"></div></td>
                                </tr>
                                <tr>
                                    <td>Create Date</td>
                                    <td>{{ prettyDate($event->created_at) }}</td>
                                </tr>
                            </table>
                            @if(Auth::check())
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ url('events') . '/' . $event->id . '/edit' }}" class="btn btn-primary pull-right">Edit</a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>

    </script>
@endsection