
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    
        <!-- CSRF Token -->
        <meta name="csrf-token" content="FwrGFPKfRpVBfUvacNw7Ke4CdfgDQTAkUhqqqD4d">
    
        <title>SUSEJ-IoT | High Voltage Inputing</title>
    
        <!-- Styles -->
        <meta charset="utf-8">
        <!--[if IE]><meta http-equiv="x-ua-compatible" content="IE=9" /><![endif]-->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SUSEJ-IoT | High Voltage Inputing</title>
        <meta name="description" content="SusejGROUP ">
        <meta name="keywords" content="">
        <meta name="author" content="Femonofsky">
    
        <!-- Favicons
        ================================================== -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="shortcut icon" href="/img/logo.jpg" type="image/x-icon">
        <link rel="apple-touch-icon" href="/img/apple-touch-icon.png">
        <link rel="apple-touch-icon" sizes="72x72" href="h/img/img/apple-touch-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/img/apple-touch-icon-114x114.png">
        <link type="text/css" rel="stylesheet" href="/vendors/bootstrapvalidator/css/bootstrapValidator.min.css"/>
        <!-- Bootstrap -->
        <link rel="stylesheet" type="text/css"  href="/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="/fonts/font-awesome/css/font-awesome.css">
    
        <!-- Slider
        ================================================== -->
        <link href="/static/sensor/css/owl.carousel.css" rel="stylesheet" media="screen">
        <link href="/static/sensor/css/owl.theme.css" rel="stylesheet" media="screen">
    
        <!-- Stylesheet
        ================================================== -->
    
        <link rel="stylesheet" type="text/css"  href="/css/style.css">
        <link rel="stylesheet" type="text/css" href="/css/responsive.css">
        <link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic' rel='stylesheet' type='text/css'>
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,700,300,600,800,400' rel='stylesheet' type='text/css'>
    
        <script type="text/javascript" src="/js/modernizr.custom.js"></script>
    
        <!-- Scripts -->
        <script>
            window.Laravel = {"csrfToken":"FwrGFPKfRpVBfUvacNw7Ke4CdfgDQTAkUhqqqD4d"};
        </script>
    </head>
    <body style="background-color:white;background:url('/img/night.jpg');background-repeat: no-repeat;background-size: cover;background-position: center center;">
        <div id="app">
            <nav class="navbar navbar-inverse navbar-static-top">
                <div class="container">
                    <div class="navbar-header">
    
                        <!-- Collapsed Hamburger -->
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                            <span class="sr-only">Toggle Navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
    
                        <!-- Branding Image -->
                        <img src="/img/logo.jpg" height="50px" width="50px" style="float:left">
                        <a class="navbar-brand" href="" style="color:white"></a>
                    </div>
    
                    <div class="collapse navbar-collapse" id="app-navbar-collapse">
                        <!-- Left Side Of Navbar -->
                        <ul class="nav navbar-nav">
                            &nbsp;
                        </ul>
    
                    </div>
                </div>
            </nav>
    
            <div class="preloader" style=" position: fixed;
                width: 100%;
                height: 100%;
                top: 0;
                left: 0;
                z-index: 100000;
                backface-visibility: hidden;
                background: #ffffff;">
            <div class="preloader_img" style="width: 200px;
                height: 200px;
                position: absolute;
                left: 48%;
                top: 48%;
                background-position: center; 
                z-index: 999999">
                <img src="/img/loader.gif" style=" width: 40px;" alt="loading...">
            </div>
        </div>
        <div class="container">
            <div class="row">
                @if(Session::has('fail'))
                    <div class="alert alert-dismissible alert-success">
                        <button type="button" class="close" data-dismiss="alert">×</button>{{Session::get('fail')}}
                    </div>
                @endif
                <div class="col-md-10 col-md-offset-1" style="margin-top:100px">
                    <div class="panel panel-info">
                        <div class="panel-heading"><b><h3>  LOGIN</h3></b></div>
                        <div class="panel-body">
                        <form class="form-horizontal login_validator" role="form" method="POST" id="login_validator" action="{{ route('login') }}">
                                {{ csrf_field() }}
        
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
        
                                <div class="form-group">
                                    <label for="email" class="col-md-4 control-label">E-Mail Address</label>
        
                                    <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-addon input_email"><i
                                                class="fa fa-envelope text-primary"></i></span>
                                        <input id="email" type="email" class="form-control" name="email" value="" placeholder="E-mail" required autofocus>
                                    </div>
        
        
                                                                    </div>
                                </div>
        
                                <div class="form-group">
                                    <label for="password" class="col-md-4 control-label">Password</label>
        
                                    <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-addon addon_password"><i
                                                class="fa fa-lock text-primary"></i></span>
                                        <input type="password" class="form-control form-control-md" id="password"   name="password" placeholder="Password">
                                    </div>
                                        <!--<input id="password" type="password" class="form-control" name="password" required> -->
        
                                                                    </div>
                                </div>
        
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="remember" > Remember Me
                                            </label>
                                        </div>
                                    </div>
                                </div>
        
                                <div class="form-group">
                                    <div class="col-md-8 col-md-offset-4">
                                        <button type="submit" class="btn btn-success">
                                            <span class="glyphicon glyphicon-off"></span> Login
                                        </button>
        
                                        <a class="btn btn-link" href="http://localhost:9000/password/reset">
                                            Forgot Your Password?
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
            </div>
        
        <!-- Scripts -->
        <script src="/js/app.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
       <script type="text/javascript" src="/js/jquery.1.11.1.js"></script>
       <!-- Include all compiled plugins (below), or include individual files as needed -->
       <script type="text/javascript" src="/js/bootstrap.js"></script>
       <script type="text/javascript" src="/js/SmoothScroll.js"></script>
       <script type="text/javascript" src="/js/jquery.isotope.js"></script>
           <script src="/js/owl.carousel.js"></script>
    
           <script type="text/javascript" src="/js/jquery.min.js"></script>
    <script type="text/javascript" src="/vendors/bootstrapvalidator/js/bootstrapValidator.min.js"></script>
    <script type="text/javascript" src="/vendors/wow/js/wow.min.js"></script>
    <script type="text/javascript" src="/js/pages/login1.js"></script>
    
       <!-- Javascripts
       ================================================== -->
       <!--<script type="text/javascript" src="http://localhost:9000/js/main.js"></script>
        <script type="text/javascript" src="http://localhost:9000/js/myScript.js"></script> -->
    </body>
    </html>
    