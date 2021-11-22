<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('public/favicon.ico') }}" type="image/png">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
        crossorigin="anonymous" />
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{asset('public/assets/css/ionicons.min.css')}}">
    <!-- fullCalendar 2.2.5-->
    <link rel="stylesheet" href="{{asset('public/assets/plugins/fullcalendar/fullcalendar.min.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/plugins/fullcalendar/fullcalendar.print.css')}}" media="print">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('public/assets/plugins/datatables/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/css/cdn/buttons.dataTables.min.css')}}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{asset('public/assets/plugins/select2/css/select2.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('public/assets/css/dist/adminlte.min.css')}}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{asset('public/assets/plugins/iCheck/flat/blue.css')}}">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{asset('public/assets/plugins/iCheck/all.css')}}">
    <!-- Morris chart -->
    <link rel="stylesheet" href="{{asset('public/assets/plugins/morris/morris.css')}}">
    <!-- jvectormap -->
    <link rel="stylesheet" href="{{asset('public/assets/plugins/jvectormap/jquery-jvectormap-1.2.2.css')}}">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="{{asset('public/assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')}}">
    <!-- Google Font: Source Sans Pro -->
    <link href="{{ asset('public/assets/css/fonts/fonts.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/css/pnotify.custom.min.css')}}" media="all" rel="stylesheet" type="text/css" />
    <!--sweetalert2  -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/css/sweetalert.css') }}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/daterangepicker/daterangepicker.css')}}">
    {{-- <link rel="stylesheet" href="{{ asset('public/plugins/chart/Chart.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('public/toggle.css')}}">
    <link rel="stylesheet" href="{{ asset('public/assets/css/custom.css')}}">
    <link href="{{asset('public/assets/izitoast/css/iziToast.min.css')}}" rel="stylesheet">
    @yield("extra_css")
    <style type="text/css">
        tfoot input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
            font-size: 0.6em;
            height: 35px !important;
        }

        .message {
            text-align: left;
            padding-left: 30px;
        }

        .center {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 100%;
        }

    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-light border-bottom" style="background-color: #fff;">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{session('user')}}
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{route('admin-logout')}}">Logout
                            </a>

                            {{-- <a class="dropdown-item" href="#">Change Password</a> --}}
                        </div>
                    </li>
                </ul>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar elevation-4">
            <a href="{{ route('index')}}" class="brand-link">
                <img src="{{ asset('public/phlippedlogo.png' ) }}" alt="phlipped logo" class="brand-image center"
                    >
                <p class="brand-text"></p>
            </a>
            <hr>
            <!-- Sidebar -->
            <div class="sidebar px-0 bg-light">
                <!-- Sidebar Menu -->
                <nav>
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">

                        @if(Request::is('index'))
                        @php($class="menu-open")
                        @php($active="active1")
                        @else
                        @php($class="")
                        @php($active="")
                        @endif
                        <li class="nav-item">
                            <a href="{{route('index')}}" class="nav-link active-hover {{$active}}">
                                <i class="fas fa-tachometer-alt nav-icon"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        @if(Request::is('manage_users/listUsersIndex'))
                        @php($class="menu-open")
                        @php($active="active1")
                        @else
                        @php($class="")
                        @php($active="")
                        @endif
                        <li class="nav-item">
                            <a href="{{route('listUsersIndex')}}" class="nav-link active-hover {{$active}}">
                                <i class="fa fa-users nav-icon"></i>
                                <p>Manage User Listing</p>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1 class="m-0 ml-2 text-dark">@yield('heading') </h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                {{-- @if(!(Request::is('index'))) --}}
                                <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                                {{-- @endif --}}
                                @yield('breadcrumb')
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>

            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>


        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->
    @yield('script2')
    <!-- jQuery -->
    <script src="{{asset('public/assets/plugins/jquery/jquery.min.js')}}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{{asset('public/assets/js/jquery-ui.min.js')}}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)

    </script>

    <!-- Bootstrap 4 -->
    <script src="{{asset('public/assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <!-- Select2 -->
    <script src="{{asset('public/assets/plugins/select2/js/select2.full.min.js')}}"></script>
    <!-- iCheck 1.0.1 -->
    <script src="{{asset('public/assets/plugins/iCheck/icheck.min.js')}}"></script>
    <!-- FastClick -->
    <script src="{{asset('public/assets/plugins/fastclick/fastclick.js')}}"></script>
    <!-- DataTables -->
    <script src="{{asset('public/assets/js/cdn/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('public/assets/plugins/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/assets/js/cdn/dataTables.buttons.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/assets/js/cdn/buttons.print.min.js')}}"></script>
    <!-- AdminLTE App -->
    <script src="{{asset('public/assets/js/adminlte.js')}}"></script>
    <!-- date-range-picker -->
    <script src="{{ asset('public/assets/plugins/moment/moment.min.js')}}"></script>
    <script src="{{ asset('public/assets/plugins/daterangepicker/daterangepicker.js')}}"></script>
    <!-- ckeditor -->
    <script src="{{ asset('public/ckeditor/ckeditor.js') }}"></script>
    <!-- Graph -->
    {{-- <script src="{{ asset('public/plugins/chart/Chart.js') }}"></script> --}}
    <!--sweetalert2 -->
    <script src="{{ asset('public/assets/js/sweetalert.min.js') }}"></script>
    <script src="{{asset('public/assets/izitoast/js/iziToast.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/assets/js/pnotify.custom.min.js')}}"></script>
    <!-- AdminLTE for demo purposes -->
    @yield('script')
    {{-- <script src="{{asset('public/vendor/datatables/buttons.server-side.js')}}"></script> --}}
</body>

</html>
