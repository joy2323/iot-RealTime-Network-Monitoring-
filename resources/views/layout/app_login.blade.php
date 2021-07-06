<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=0" />
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SUSEJ IoT- RealTime Network Monitoring</title>

        <!-- <script src="{{ asset('js/app.js') }}" defer></script> -->

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <!-- <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" /> -->
    </head>

    <body class="container-login100" style="background-image: url('images/scada.jpg'); width: 100%;
    z-index: 0; background-position: center; ">
        <div id="app">
            <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm" style="height:50px;">
                <div class="container">
                    <div class="navbar-header">
                        <a class="navbar-brand float-left" href="{{url('/')}}">
                            <h4 style="color:white; margin:auto"><img src="{{asset('img/logo.jpg')}}" height="40px"
                                    width="40px" class="admin_img" alt="logo"> SUSEJ IoT</h4>
                        </a>
                    </div>
                </div>
            </nav>

            <main class="py-4">
                @yield('content')

            </main>
        </div>
        @include('theme.footer')
        <script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
        <script>
        var notify = false;
        $(document).ready(function() {
            var notify = localStorage.getItem("notified");
            if (notify) {
                document.getElementById("alerinfo").style.display = "none";
            } else {

                localStorage.setItem("notified", true);

            }
        });
        $('body').on('click', '.closeinfo', function() {
            document.getElementById("alerinfo").style.display = "none";
            localStorage.setItem("notified", true);
        });
        </script>

    </body>


</html>
