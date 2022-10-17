<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>eLink's Employee Directory | @yield('title')</title>
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

  <!-- overlayScrollbars -->
  <link href="{{ asset('public/css/OverlayScrollbars.min.css')}}" rel="stylesheet">
  <!-- Theme style -->
  <link href="{{ asset('public/css/adminlte.css')}}" rel="stylesheet">

  <script src="{{ asset('public/js/jquery-1.11.1.min.js')}}"></script>

  <script type="text/javascript" src="{{ asset('public/js/jquery.bootstrap-growl.min.js') }}"></script>

  <!-- FROALA EDITOR -->
  <link rel="stylesheet" href="{{ asset('public/css/froala_editor/froala_editor.css')}}">
  <link rel="stylesheet" href="{{ asset('public/css/froala_editor/froala_style.css')}}">
  <link rel="stylesheet" href="{{ asset('public/css/froala_editor/plugins/emoticons.css')}}">

  <link rel="stylesheet" href="{{ asset('public/css/bootstrap-datetimepicker.min.css')}}" />
  <link rel="stylesheet" href="{{ asset('public/css/pages/' . request()->path() . '.css')}}" />
  @yield('head')

  <script src="https://cdn.jsdelivr.net/npm/vue"></script>

</head>
<body class="hold-transition sidebar-mini layout-fixed">
<!-- Site wrapper -->
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Right navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a class="brand-link" href="{{url('/home')}}">
        <span>
            <img src="{{ asset('public/img/elink-logo-site.png')}}" style="width: 35px; margin-top: -10px;opacity: .8">
            &nbsp;Employee
        </span>
        Directory
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user (optional) -->
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                            @auth
                    <li>
                        <div class="profile-userpic">
                            <div style="background-image: url('public/img/liza-soberano_4.jpg');" class="pic-div">
                            </div>
                        </div>
                        <div class="profile-usertitle">
                            <br>
                            <h5 class="card-title m-t-10">{{ Auth::user()->fullname() }}</h5>
                            <small class="text-muted" title="Job Title">{{ Auth::user()->position_name }}</small>
                            <br>
                        <br>
                        </div>
                        <div class="clear"></div>
                    </li>
                    @endauth
                   @auth
                        @if(Auth::user()->isAdmin())
                            @include('layouts.menu.admin')
                        @elseif(Auth::user()->isHR())
                            @include('layouts.menu.hr')
                            test
                        @elseif(Auth::user()->isERP())
                            @include('layouts.menu.erp')
                        @else
                            @include('layouts.menu.normal')
                        @endif 
                    @endauth
                    @guest
                        @include('layouts.menu.normal')
                    @endguest
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content">

       <div class="col-sm-12">
            <div class="row">
                <ol class="breadcrumb">
                    <li><a href="#">
                        <em class="fa fa-home"></em>
                    </a></li>
                    <li class="active">{{ breadCrumbs() }}</li>
                </ol>
            </div>
            <div>
                @yield('content')

            </div>
        </div> 
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
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
        <script src="{{ asset('public/js/jquery.overlayScrollbars.min.js')}}"></script>
        <!-- AdminLTE App -->
        <script type="text/javascript" src="{{ asset('public/js/adminlte.js')}}"></script>

        <!-- Modal -->
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
        <center>
            <small style="color: #999;font-weight: 500;">Copyright {{ date('Y')}} eLink Systems & Concepts Corp.</small>
        </center>
        <br>  
    </div>  
    <div id="engagementmodal" class="modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title pull-left" id="engagement_title" style="width: 90%"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <small id="engagement_subtitle"></small>
            <center>
                <br>
                <img id="engagement_image" src="" style="width: 100%;" />
                <br>
                <br>
            </center>
            <p id="engagement_message"></p>
            <small id="engagement_date_posted"></small>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
</body>
<!-- Modal Success -->
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
</html>
