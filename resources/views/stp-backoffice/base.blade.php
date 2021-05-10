<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit">
    <meta name="csrf-token" content="roK7duMIE8eC6DgFbaqdnUvJF0fiqiaNVPZHtxLm">
    <title>Admin  | StpUser - @yield('title')</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">


    <link rel="stylesheet" href="/vendor/laravel-admin/AdminLTE/plugins/iCheck/all.css">
    <link rel="stylesheet" href="/vendor/laravel-admin/AdminLTE/plugins/colorpicker/bootstrap-colorpicker.min.css">
    <link rel="stylesheet" href="/vendor/laravel-admin/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shortcut-buttons-flatpickr@0.3.0/dist/themes/light.min.css">
    <link rel="stylesheet" href="/vendor/laravel-admin/bootstrap-fileinput/css/fileinput.min.css?v=4.5.2">
    <link rel="stylesheet" href="/vendor/laravel-admin/AdminLTE/plugins/select2/select2.min.css">
    <link rel="stylesheet" href="/vendor/laravel-admin/AdminLTE/plugins/ionslider/ion.rangeSlider.css">
    <link rel="stylesheet" href="/vendor/laravel-admin/AdminLTE/plugins/ionslider/ion.rangeSlider.skinNice.css">
    <link rel="stylesheet" href="/vendor/laravel-admin/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css">
    <link rel="stylesheet" href="/vendor/laravel-admin/fontawesome-iconpicker/dist/css/fontawesome-iconpicker.min.css">
    <link rel="stylesheet" href="/vendor/laravel-admin/bootstrap-duallistbox/dist/bootstrap-duallistbox.min.css">
    <link rel="stylesheet" href="/vendor/laravel-admin/AdminLTE/dist/css/skins/skin-purple-light.min.css">
    <link rel="stylesheet" href="/vendor/laravel-admin/AdminLTE/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/vendor/laravel-admin/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/vendor/laravel-admin/laravel-admin/laravel-admin.css">
    <link rel="stylesheet" href="/vendor/laravel-admin/nprogress/nprogress.css">
    <link rel="stylesheet" href="/vendor/laravel-admin/sweetalert2/dist/sweetalert2.css">
    <link rel="stylesheet" href="/vendor/laravel-admin/nestable/nestable.css">
    <link rel="stylesheet" href="/vendor/laravel-admin/toastr/build/toastr.min.css">
    <link rel="stylesheet" href="/vendor/laravel-admin/bootstrap3-editable/css/bootstrap-editable.css">
    <link rel="stylesheet" href="/vendor/laravel-admin/google-fonts/fonts.css">
    <link rel="stylesheet" href="/vendor/laravel-admin/AdminLTE/dist/css/AdminLTE.min.css">



    <script src="/vendor/laravel-admin/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js"></script>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->


    <style>
        .nav-ops li {
            height: 100px;
        }
        .nav-ops li a.ops-wrapper {
            padding: 0!important;
            margin: 0!important;
            line-height: 100px!important;
            color: #dfdfe7!important;
        }
        .nav-ops li a:hover{
            background: none;
            cursor: pointer;
            color: #dfdfe7;
        }
        .messages-bar {
            height: 60px;
            background: #d9cff1;
            color: #040a45;
            padding-left: 44px;
            padding-right: 60px;
            overflow: hidden;
        }
        .messages-bar .now {
            float: left;
            line-height: 60px;
            display: inline-block;
            margin-right: 50px;
        }
        .messages-bar ul,.messages-bar li {
            margin: 0;
            border: 0;
            padding: 0;
        }
        .messages-bar li {
            line-height: 60px;
        }
        .messages-bar li a {
            color: #040a45;
        }

        /*顶部导航*/
        .nav-links-ul {
            width: 70%;
            display: flex;
            justify-content: space-around;
        }
        .nav-links-ul .nav-link {

        }
        .nav-links-ul .nav-link a {
            padding: 0;
            margin: 0;
            height: 100px;
            line-height: 100px;
            color: #6f7592;
            font-size: 20px;
        }
        .nav-links-ul .nav-link.active a {
            color: #dfdfe7;
        }
        .nav-links-ul .nav-link.active a span {
            position: relative;
        }
        .nav-links-ul .nav-link.active a span:after {
            content: "";
            background-color: #ad8cfc;
            width: 50%;
            height: 2px;
            position: absolute;
            bottom: -4px;
            left: 25%;
            border-radius: 10px;
        }
        .nav-links-ul .nav-link a:hover {
            background: none;
            cursor: pointer;
            color: #dfdfe7;
        }

        /*表格修改datatable的默认样式*/
        table.dataTable.no-footer {
            border-bottom: none!important;
        }
        table.dataTable thead th, table.dataTable thead td {
            border-bottom: 1px solid #f9f9f9!important;
        }
        .table-hover>tbody>tr:hover {
            background-color: #cae8ec;
        }

    </style>
    @section('style')

    @show

</head>

<body class="sidebar-mini">


<div class="wrapper">

    <!-- Main Header -->
    <header class="main-header">
        <a href="/admin" class="logo" style="background: #040a45;color: #dfdfe7;height: 100px;line-height: 100px;">
            <span class="logo-lg"><b>STP</b> Backoffice</span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation" style="background: #040a45;">
            <!-- Sidebar toggle button-->
            <ul class="nav navbar-nav nav-links-ul">
                <li class="nav-link {{(request()->path() == 'admin' || request()->path() == 'admin/dashboard') ? 'active' : ''}}">
                    <a href="/admin"><span>Dashboard</span></a>
                </li>
                <li class="nav-link {{(request()->path() == 'admin/user-records' || request()->path() == 'admin/user-information') ? 'active' : ''}}">
                    <a href="/admin/user-records"><span>Users Record</span></a>
                </li>
                <li class="nav-link">
                    <a><span>Analytics</span></a>
                </li>
                <li class="nav-link">
                    <a><span>Setting</span></a>
                </li>
            </ul>

            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav nav-ops">

                    <li>
                        <a class="ops-wrapper">
                            <i class="fa fa-bell" style="font-size: 20px;margin-right: 20px;"></i>
                        </a>
                    </li>

                    <!-- User Account Menu -->
                    <li class="dropdown user user-menu" style="height: 100px;">
                        <!-- Menu Toggle Button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="height: 100px;padding: 0;margin: 0;">
                            <!-- The user image in the navbar-->
                            <img src="/vendor/laravel-admin/AdminLTE/dist/img/user2-160x160.jpg" class="user-image" alt="User Image" style="clear: both;width: 70px;height: 70px;margin-top: 15px;margin-right: 60px;margin-left: 15px;">
                            <!-- hidden-xs hides the username on small devices so only the image appears. -->
                            {{--                            <span class="hidden-xs">Administrator</span>--}}
                        </a>
                        <ul class="dropdown-menu">
                            <!-- The user image in the menu -->
                            <li class="user-header">
                                <img src="/vendor/laravel-admin/AdminLTE/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">

                                <p style="color: #333;">
                                    {{ Admin::user()->name }}
                                    <small>Member since admin {{ Admin::user()->created_at }}</small>
                                </p>
                            </li>
                            <li class="user-footer">
{{--                                <div class="pull-left">--}}
{{--                                    <a href="/admin/auth/setting" class="btn btn-default btn-flat">Setting</a>--}}
{{--                                </div>--}}
                                <div class="center-block">
                                    <a href="/admin/auth/logout" class="btn btn-default btn-flat" style="width: 100%;">Logout</a>
                                </div>
                            </li>
                        </ul>
                    </li>



                </ul>
            </div>
        </nav>

    </header>
    <div class="messages-bar" style="height: 60px;background: #d9cff1;color: #040a45;">
        <span class="now moment-now"></span>
        <ul>
            <li><a>Notification messages</a></li>
        </ul>
    </div>
    <div class="content-wrapper" style="margin-left: 0 !important;padding: 15px;">
        @section('content')
        @show


    </div>
</div>
<!-- REQUIRED JS SCRIPTS -->
<script src="/vendor/laravel-admin/AdminLTE/bootstrap/js/bootstrap.min.js"></script>
<script src="/vendor/laravel-admin/AdminLTE/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script src="/vendor/laravel-admin/AdminLTE/dist/js/app.min.js"></script>
<script src="/vendor/laravel-admin/jquery-pjax/jquery.pjax.js"></script>
<script src="/vendor/laravel-admin/nprogress/nprogress.js"></script>
<script src="/vendor/laravel-admin/nestable/jquery.nestable.js"></script>
<script src="/vendor/laravel-admin/toastr/build/toastr.min.js"></script>
<script src="/vendor/laravel-admin/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script src="/vendor/laravel-admin/sweetalert2/dist/sweetalert2.min.js"></script>
{{--<script src="/vendor/laravel-admin/laravel-admin/laravel-admin.js"></script>--}}
<script src="/public/js/common.js"></script>
<script src="/vendor/laravel-admin/AdminLTE/plugins/iCheck/icheck.min.js"></script>
<script src="/vendor/laravel-admin/AdminLTE/plugins/colorpicker/bootstrap-colorpicker.min.js"></script>
<script src="/vendor/laravel-admin/AdminLTE/plugins/input-mask/jquery.inputmask.bundle.min.js"></script>
<script src="/vendor/laravel-admin/moment/min/moment-with-locales.min.js"></script>
<script src="/vendor/laravel-admin/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/shortcut-buttons-flatpickr@0.1.0/dist/shortcut-buttons-flatpickr.min.js"></script>
<script src="https://npmcdn.com/flatpickr@4.6.6/dist/l10n/zh.js"></script>
<script src="/vendor/laravel-admin/bootstrap-fileinput/js/plugins/canvas-to-blob.min.js"></script>
<script src="/vendor/laravel-admin/bootstrap-fileinput/js/fileinput.min.js?v=4.5.2"></script>
<script src="/vendor/laravel-admin/AdminLTE/plugins/select2/select2.full.min.js"></script>
<script src="/vendor/laravel-admin/number-input/bootstrap-number-input.js"></script>
<script src="/vendor/laravel-admin/AdminLTE/plugins/ionslider/ion.rangeSlider.min.js"></script>
<script src="/vendor/laravel-admin/bootstrap-switch/dist/js/bootstrap-switch.min.js"></script>
<script src="/vendor/laravel-admin/fontawesome-iconpicker/dist/js/fontawesome-iconpicker.min.js"></script>
<script src="/vendor/laravel-admin/bootstrap-fileinput/js/plugins/sortable.min.js?v=4.5.2"></script>
<script src="/vendor/laravel-admin/bootstrap-duallistbox/dist/jquery.bootstrap-duallistbox.min.js"></script>
<script src="/public/js/moment.js"></script>
</body>
</html>
<script>

    $(function () {
        //顶部导航高亮
        const currentHighlightIndex = $('.nav-links-ul .nav-link.active').index();
        $('.nav-links-ul .nav-link').hover(function() {
            $('.nav-links-ul .nav-link').removeClass('active');
            $(this).addClass('active');
        },function() {
            $(this).removeClass('active');
        });
        $('.nav-links-ul').hover(function() {}, function() {
            $('.nav-links-ul .nav-link').eq(currentHighlightIndex).addClass('active');
        });

        $('.moment-now').text(moment().format('MMM DD,YYYY hh:mm:ss A'));
    });




</script>
@section('script')
@show
