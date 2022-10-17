@extends('layouts.auth')

@section('content')
<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Elink Employee Directory | Login</title>
        <link href="{{ asset('./css/css.css')}}" rel="stylesheet">
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: "Source Sans Pro", "Segoe UI", Frutiger, "Frutiger Linotype", "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
                background-size: cover;
                background-image: url({{asset('./img/blue.jpg')}});
                background-repeat: no-repeat;

            }
            .full-height {
                height: 95vh;
            }
            .flex-center {
                align-items: center;
                display: block;
                justify-content: center;
            }
            .position-ref {
                position: relative;
            }
            .content {
                text-align: center;
                width: 400px;
                padding: 30px 50px 50px 30px;
                background-color: #00000042;
                box-shadow: 1px 1px 2px 0px #a9a9a970;
                margin: 0 auto !important;
            }
            .title {
                font-size: 50px;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .form-input{
                background-color: white;
                font-family: inherit;
                border: 1px solid #dfdfdf;
                color: inherit;
                display: block;
                font-size: 14px;
                margin: 0 0 13px 0;
                padding: 7px 7px 7px 15px;
                height: 34px;
                width: 100%;
            }
            .form-group label{
                font-weight: bold !important;
                font-size: 15px;
                margin-bottom: 2px;
                display: table-caption;
            }
            .form-group{
                margin-top: 10px;
            }
            .btn-holder{
                margin-top: 20px;
            }
            button.flat{
                width: auto;
                background: #36bae2;
                box-shadow: none;
                color: #fff;
                font-weight: 500;
                cursor: pointer;
                position: relative;
                display: inline-block;
                font-size: 14px !important;
                margin: 0;
                padding: 12px 32px;
                position: relative;
                text-align: center;
                border-radius: 2px;
                border: none;
                -webkit-transition: none;
                -moz-transition: none;
                transition: none;
            }
             button.flat:hover{
                background-color: #1da0c8;
             }
             .invalid-feedback{
                color: #ff8e8b;;
                font-size: 14px !important;
                line-height: 19px;
                font-weight: 500;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div style="margin: 0 auto; width: 100%;text-align: center; padding-top: 180px">
                <img src="{{ asset('./img/elink-logo-site.png')}}" style="width: 80px; margin: 0 auto !important;">
                <h1 style="color: #0C59A2; margin-top: -8px;">eLink  F A L C O N&nbsp;&nbsp;<span style="color: white">∞&nbsp;&nbsp;HR Portal</span></h1>
            </div>
            <div class="content">
                <div style="color: white;">
                    <span style="font-size: 18px; font-weight: 500;">&nbsp;&nbsp;&nbsp;&nbsp;FORGOT PASSWORD</span><span style="font-size: 18px; font-weight: 400"> </span>
                    <br>
                    <br>
                </div>
                <div class="links">
                    <form method="POST" action="">
                        @csrf
                        
                        <div class="form-group">
                            <input id="email" type="email" placeholder="Email Address" class="form-input" name="email" required autocomplete="email" autofocus>
                        </div>
                        @if ($errors->has('email'))
                            <span class="invalid-feedback">
                                {{ $errors->first('email') }}
                            </span>
                        @endif
                        @if (Session::has('Success'))
                            <span style="color: white">
                                {{ Session::get('Success') }}
                            </span>
                        @endif
                        <div class="form-group btn-holder">
                            <button class="button flat" name="submit" style="width: 106%;padding: 12px 12px 12px 12px;">
                                Reset Password
                            </button>
                        </div>
                    </form>
                </div>
                <div class="text-center">
                    <a class="small" style="color: rgb(211, 209, 209); font-size: 14px; font-weight: 50;" href="{{route('login')}}" >Login</a>
                </div>

            </div>
            <br>
            <center>
                <small style="color: #ddd;font-weight: 500;">Copyright {{ date('Y')}} eLink Systems & Concepts Corp.</small>
            </center>
        </div>
        <footer>
            <center>
                <!-- <small style="color: #ddd;font-weight: 500;">Copyright {{ date('Y')}} eLink Systems & Concepts Corp.</small> -->
            </center>
        </footer>
    </body>
</html>

@endsection

