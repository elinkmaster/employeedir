<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>eLink Falcon - HR Portal | @yield('title')</title>
    <link rel="icon" type="image/png" href="http://www.elink.com.ph/wp-content/uploads/2016/01/elink-logo-site.png">
    <link href="{{ asset('public/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('public/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ asset('public/css/datepicker3.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ asset('public/css/styles.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ asset('public/css/custom.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ asset('public/css/jquery.dataTables.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ asset('public/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{ asset('public/css/tagify.css')}}" rel="stylesheet">
    <link href="{{ asset('public/css/css.css')}}" rel="stylesheet">
    <link href="{{ asset('public/css/theme-midnight.css')}}" rel="stylesheet">

    <script src="{{ asset('public/js/jquery-1.11.1.min.js')}}"></script>

    <script type="text/javascript" src="{{ asset('public/js/jquery.bootstrap-growl.min.js') }}"></script>

    <!-- FROALA EDITOR -->
    <link rel="stylesheet" href="{{ asset('public/css/froala_editor/froala_editor.css')}}">
    <link rel="stylesheet" href="{{ asset('public/css/froala_editor/froala_style.css')}}">
    <link rel="stylesheet" href="{{ asset('public/css/froala_editor/plugins/emoticons.css')}}">

    <link rel="stylesheet" href="{{ asset('public/css/bootstrap-datetimepicker.min.css')}}" />
    <link rel="stylesheet" href="{{ asset('public/css/jquery-ui.min.css')}}" />
    <link rel="stylesheet" href="{{ asset('public/css/pages/' . request()->path() . '.css')}}" />
    @yield('head')

    <script src="https://cdn.jsdelivr.net/npm/vue"></script>
</head>
<body>
    <nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header" style="float: left">
                <button type="button" id="toggle-sidebar">
                    <span class="fa fa-bars"></span>
                </button>
                <a class="navbar-brand" href="{{url('/home')}}">
                    <span>
                        <img src="{{ asset('public/img/elink-logo-site.png')}}" style="width: 40px; margin-top: -10px">
                        &nbsp;eLink&nbsp; F A L C O N&nbsp;&nbsp;
                    </span>
                    ∞&nbsp;&nbsp; HR Portal&nbsp;&nbsp; 
                    <img src="{{ asset('public/img/falcon-logo.png')}}" style="width: 30px; margin-top: -10px; float: right; padding-top: 7px;">
                </a>
                <ul class="nav navbar-top-links navbar-right">
                    @guest
                        <li class="login-btn"><a href="{{ url('/login') }}">Login</a></li>
                    @endguest
                    @auth
                        <li class="dropdown">
                            <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#" aria-expanded="true">
                                <em class="fa fa-user"></em>
                            </a>
                            <ul class="dropdown-menu dropdown-messages">
                                <li>
                                    <div class="profile-sidebar">
                                        <div class="profile-userpic">
                                           <div style="background-image: url('{{ Auth::user()->profile_img }}');" class="pic-div">
                                                        </div>
                                        </div>
                                        <div class="profile-usertitle">
                                            <div class="profile-usertitle-name">{{ Auth::user()->fullname() }}</div>
                                            <div class="profile-usertitle-status"></span><small class="text-muted" title="Job Title">{{ Auth::user()->position_name }}</small></div>
                                        </div>
                                    </div>
                                </li>
                                    <li class="divider" style="height: 0px"></li>

                                 @if(Auth::user()->isAdmin()) 
                                    <li>
                                        <a href="{{url('settings')}}">
                                            <em class="fa fa-gear">&nbsp;</em>
                                            Global Settings
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                @endif
                                <li >
                                    <a href="{{url('myprofile')}}">
                                        <em class="fa fa-user">&nbsp;</em>
                                        My Profile
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="{{ route('logout')}}">
                                        <em class="fa fa-power-off">&nbsp;</em>
                                        Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div><!-- /.container-fluid -->
    </nav>
    <div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">
        <ul class="nav menu">
           @include('layouts.menu')
        </ul>
    </div><!--/.sidebar-->
        
    <div class="col-sm-10 col-sm-offset-2" id="main-container">
        <div class="row" style="margin-top: 60px;">
            <ol class="breadcrumb">
                <li><a href="#">
                    <em class="fa fa-home"></em>
                </a></li>
                <li class="active">{{ breadCrumbs() }}</li>
            </ol>
        </div><!--/.row-->
        <div class="page-content">
            @yield('content')
            <div class="col-md-12">
                <center>
                    <small style="color: #999;font-weight: 400; padding: 40px 0px;">Copyright {{ date('Y')}} eLink Systems & Concepts   Corp.</small>
                </center>
            </div>
        </div>
    </div>  <!--/.main-->
    <script type="text/javascript" src="{{ asset('public/js/bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/js/chart.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/js/chart-data.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/js/easypiechart.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/js/easypiechart-data.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/js/bootstrap-datepicker.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/js/jquery.dataTables.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/js/jquery.validate.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/js/dataTables.responsive.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/js/select2.full.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/js/moment.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/bootstrap/js/transition.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/bootstrap/js/collapse.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/js/loadingoverlay.min.js')}}"></script>

    <script type="text/javascript" src="{{ asset('public/js/global.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/js/custom.js')}}"></script>

    <script type="text/javascript" src="{{ asset('public/js/froala_editor/froala_editor.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/js/froala_editor/plugins/emoticons.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/js/froala_editor/plugins/link.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/js/tagify.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/js/jQuery.tagify.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/js/jquery-ui.min.js')}}"></script>

<!-- Modal Success -->
   <div id="messageModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
           <button type="button" class="close" data-dismiss="modal">&times;</button>
           <h4 class="modal-title"></h4>
         </div>
          <div class="modal-body">
           <p id="message"></p>
          </div>
          <div class="modal-footer">
             {{ Form::open(array('url' => 'employee_info/', 'class' => ' delete_form' )) }}
                   {{ Form::hidden('_method', 'DELETE') }}
                    {{ Form::submit('Yes', array('class' => 'btn btn-danger')) }}
                {{ Form::close() }}
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
        </div>
      </div> 
    </div>
@if (session('success'))
    <div id="alertmodal" class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header" style="background-color: #2196F3;">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: white !important;opacity: 1;">×</button>
            <h4 class="modal-title"><b style="color: white">Success!</b></h4>
          </div>
          <div class="modal-body">
            <p>{{ session('success') }}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript">
        $('#alertmodal').modal('show');
    </script>
@endif
<!-- Modal Error -->
@if (session('error'))
<div id="alertmodal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #d32f2f;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: white !important;opacity: 1;">×</button>
        <h4 class="modal-title"><b style="color: white">Error!</b></h4>
      </div>
      <div class="modal-body">
        <p>{{ session('error') }}</p>
      </div>
      <div class="modal-footer"> 
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
    $('#alertmodal').modal('show');
</script>
@endif
<style type="text/css">
    .delete_form{
        display: inline-block;
    }
</style>
@yield('scripts')
</body>

</html>
