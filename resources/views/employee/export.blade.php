@extends('layouts.main')
@section('title')
Employee Export
@endsection
@section('pagetitle')
Employee / Export
@endsection
@section('content')
<style type="text/css">
    label#bb {
        padding: 10px;
        display: table;
        background-color: #30a5ff;
        color: white;
    }
    #bb input[type=file]{
        display: none;
    }
</style>
    <div class="col-md-4">
        <div class="section-header">
            <h4>Export Employee</h4>
        </div>
        <div class="panel panel-container">
            <div class="panel-body">
                <a class="btn btn-primary" href="{{url('exportdownload')}}">Generate and Download latest report</a>
                <br><br>
                <p>Recent Reports <span>(click to download)</span></p>
                @foreach($files as $file)
                    <a href="{{ asset($file) }}"><p>{{ $file }}</p></a>
                @endforeach
            </div>
        </div>
    </div>
    <br>
    <br>
    <br>

@endsection
@section('scripts')
<script type="text/javascript">

    

    function fileUpload(input){
        var reader = new FileReader();
        reader.onload = function(e) {
          $('#filename').html(input.files[0].name);
        }
        
        reader.readAsDataURL(input.files[0]);
    }

    $('#fileexcel').change(function(){
        fileUpload(this);
    });

</script>
@endsection