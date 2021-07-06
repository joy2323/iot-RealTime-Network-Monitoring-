<aside id="aside">
    <nav id="sideNav">
        <!-- MAIN MENU -->
        <ul class="nav nav-list">
            <br>
            <br>

            <li>
                <!-- dashboard -->
                <a class="dashboard" href="#">
                    <!-- warning - url used by default by ajax (if eneabled) -->
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-home"></i> <span>Dashboard</span>
                </a>
                <ul>
                    <!-- submenus -->
                    <li class="active"><a href="{{url('/client')}}">Distribution Stations (DTs)</a></li>
                    <li><a href="{{url('/injection-stations')}}">Injection Stations (HVs)</a></li>
                </ul>
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
                <a href="{{url('/viewall-bu')}}">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-users"></i> <span>{{Auth::user()->dash_label1}}s</span>
                </a>
            </li>
            <li>
                <a href="{{url('/client-sites')}}">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-bar-chart"></i> <span> Sites </span>
                </a>

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