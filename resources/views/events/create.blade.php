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
        .sp-picker-container{
            display: none;
        }
    </style>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Create Event
                </div>
                <div class="panel panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form method="POST" action="{{ url('events') }}">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label>Event Name</label>
                                    <input class="form-control" name="event_name">
                                </div>
                                <div class="form-group">
                                    <label>Event Description</label>
                                    <input class="form-control" name="event_description">
                                </div>
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input class="form-control" name="start_date" id="start_date">
                                </div>
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input class="form-control" name="end_date" id="end_date">
                                </div>
                                <div class="form-group">
                                    <label>Event Color</label>
                                    <input type='text' id="event_color" name="event_color" value="#0086CD"/>
                                </div>
                                <div class="form-group">
                                    <button>Save</button>
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
    <script>
        $('#start_date').datetimepicker();
        $('#end_date').datetimepicker({
            useCurrent: false //Important! See issue #1075
        });
        $("#start_date").on("dp.change", function (e) {
            $('#end_date').data("DateTimePicker").minDate(e.date);
        });
        $("#end_date").on("dp.change", function (e) {
            $('#start_date').data("DateTimePicker").maxDate(e.date);
        });

        var pallettes = [
            ["#ffffff", "#000000", "#efefe7", "#184a7b", "#4a84bd", "#c6524a", "#9cbd5a", "#8463a5", "#4aadc6", "#f79442"],
            ["#f7f7f7", "#7b7b7b", "#dedec6", "#c6def7", "#dee7f7", "#f7dede", "#eff7de", "#e7e7ef", "#deeff7", "#ffefde"],
            ["#dedede", "#5a5a5a", "#c6bd94", "#8cb5e7", "#bdcee7", "#e7bdb5", "#d6e7bd", "#cec6de", "#b5deef", "#ffd6b5"],
            ["#bdbdbd", "#393939", "#948c52", "#528cd6", "#94b5d6", "#de9494", "#c6d69c", "#b5a5c6", "#94cede", "#ffc68c"],
            ["#a5a5a5", "#212121", "#4a4229", "#10315a", "#316394", "#943131", "#739439", "#5a4a7b", "#31849c", "#e76b08"],
            ["#848484", "#080808", "#181810", "#082139", "#214263", "#632121", "#4a6329", "#393152", "#215a63", "#944a00"],
            ["#c60000", "#ff0000", "#ffc600", "#ffff00", "#94d652", "#00b552", "#00b5f7", "#0073c6", "#002163", "#7331a5"],

        ];

        $("#event_color").spectrum({
            color: '#0086CD',
            hideAfterPaletteSelect:true,
            showPalette: true,
            showSelectionPalette: true, // true by default
            palette: pallettes
        });
        $("#event_color").show();
        $("#event_color").attr('type', 'hidden');
    </script>
@endsection