<!doctype html>
<html lang="en-US">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <title> @yield('title') | {{ Auth::user()->name }}</title>
    <meta name="description" content="" />
    <meta name="Susej" content="Susej IoT" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- mobile settings -->
    <meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=0" />

    <!-- WEB FONTS -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
        integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700,800&amp;subset=latin,latin-ext,cyrillic,cyrillic-ext"
        rel="stylesheet" type="text/css" />
    <link type="text/css" href="{{ asset('assets/plugins/datatables/css/jquery.dataTables.min.css') }}" />

    <!-- CORE CSS -->
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- THEME CSS -->
    <link href="{{ asset('assets/css/essentials.css') }}" rel="stylesheet" type="text/css" />


    <link href="{{ asset('assets/css/layout.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/color_scheme/green.css') }}" rel="stylesheet" type="text/css" id="color_scheme" />

    <!-- PAGE LEVEL STYLES -->
    <link href="{{ asset('assets/css/layout-datatables.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/toastr/toastr.css') }}" />


</head>

<body>
    <!-- WRAPPER -->
    <div id="wrapper" class="clearfix">

        @include('partials.navbar')
        @if(Auth::user()->role =='Super admin')
        @include('partials.sidebar')
        @elseif(Auth::user()->role =='Client admin')
        @include('partials.client_sidebar')
        @else
        @include('partials.bu_sidebar')
        @endif

        @yield('content')
    </div>
    @include('modals.profile')
    <!-- JAVASCRIPT FILES -->
    <script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/apptable.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/datatables/js/jquery.dataTables.min.js') }}">
    </script>
    <script type="text/javascript" src="{{ asset('assets/plugins/datatables/js/dataTables.tableTools.min.js') }}">
    </script>
    <script type="text/javascript" src="{{ asset('assets/plugins/datatables/js/dataTables.colReorder.min.js') }}">
    </script>
    <script type="text/javascript" src="{{ asset('assets/plugins/datatables/js/dataTables.scroller.min.js') }}">
    </script>
    <script type="text/javascript" src="{{ asset('assets/plugins/datatables/dataTables.bootstrap.js') }}"></script>


    <script type="text/javascript" src="{{ asset('assets/plugins/toastr/toastr.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap3-typeahead.min.js') }}"></script>

    <script type="text/javascript" src="{{ asset('js/pages/Ctrldashboard.js?v=0')}}"></script>


</body>

</html>