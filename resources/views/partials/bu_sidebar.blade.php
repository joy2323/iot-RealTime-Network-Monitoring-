<aside id="aside">
    <nav id="sideNav">
        <!-- MAIN MENU -->
        <ul class="nav nav-list">
            {{-- <div class="" style="margin-top:20px">
                        <a href="{{url('/profile')}}">
            <img class="media-object img-thumbnail user-img rounded-circle admin_img3" alt="User Picture"
                src="{{ url('/'.Auth::user()->image )}}">
            <span style="color:white">Welcome {{ Auth::user()->name }}</span>
            </a>
            </div> --}}
            <br>
            <br>
            <li class="active">
                <!-- dashboard -->
                <a class="dashboard" href="{{url('/bu')}}">
                    <!-- warning - url used by default by ajax (if eneabled) -->
                    <i class="main-icon fa fa-home"></i> <span>Dashboard</span>
                </a>
            </li>


            @if(Auth::user()->master_role == 1)
            <li>
                <a href="{{url('/admin-list')}}">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa fa-user"></i> <span>Admins</span>
                </a>
            </li>
            @endif
            <li>
                <a href="{{url('/view-ut')}}">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-users"></i> <span>{{Auth::user()->dash_label2}}s </span>
                </a>

            </li>
            <li>
                <a href="{{url('/sites')}}">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-bar-chart"></i> <span> Sites </span>
                </a>
                <!-- <ul>
                    <li><a href="{{url('/sites')}}"> View Sites</a></li>
                    <li><a href="{{url('/add-site-user')}}"> Add UT/Service center Site </a></li>

                </ul> -->
            </li>

            <li>
                <a href="#">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-commenting-o"></i> <span> Communication </span>
                </a>
                <ul>
                    <!-- submenus -->
                    <li><a href="{{url('/communication')}}">View Emails/Phone number </a></li>
                    <li><a href="{{url('/add-communication')}}">Add Emails/Phone number</a></li>

                </ul>
            </li>
            <li>
                <!-- dashboard -->
                <a href="/control">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-power-off"></i> <span> DT Supply Control </span>

                </a>
            </li>
            <li>
                <a href="{{url('/power')}}">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-bolt"></i> <span> Power </span>
                </a>

            </li>
            <li>
                <a href="#">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-history"></i> <span> Reports </span>
                </a>
                <ul>
                    <!-- submenus -->
                    <li><a href="{{url('/alarms')}}"> Alarm Reports</a></li>
                    <li><a href="{{url('/control-Activities')}}">Control Logs </a></li>
                    <li><a href="{{url('/login-Activities')}}">Login Reports </a></li>
                </ul>
            </li>

        </ul>


    </nav>

    <span id="asidebg">
        <!-- aside fixed background --></span>
</aside>