<aside id="aside">
    <nav id="sideNav">
        <!-- MAIN MENU -->
        <ul class="nav nav-list">
            {{-- <div class="" style="margin-top:20px">
                <a href="{{ url('/profile') }}">
                    <img class="media-object img-thumbnail user-img rounded-circle admin_img3" alt="User Picture"
                        src="{{ url('/' . Auth::user()->image) }}">
                    <span style="color:white">Welcome {{ Auth::user()->name }}</span>
                </a>
            </div> --}}
            <br>
            <br>
            <li class="">
                <!-- dashboard -->
                <a class="dashboard" href="#">
                    <!-- warning - url used by default by ajax (if eneabled) -->
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-home"></i> <span>Dashboard</span>
                </a>
                <ul>
                    <!-- submenus -->
                    <li><a href="{{ url('/admin') }}">Distribution Stations (DTs)</a></li>
                    <li><a href="{{ url('/admin-injection-station') }}">Injection Stations (HVs)</a></li>
                </ul>
            </li>
            @if (Auth::user()->master_role == 1)
                <li>
                    <a href="{{ url('/admin-list') }}">
                        <i class="fa fa-menu-arrow pull-right"></i>
                        <i class="main-icon fa fa fa-user"></i> <span>Admins</span>
                    </a>
                </li>
            @endif
            <li>
                <a href="#">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-users"></i> <span> Clients </span>
                </a>
                <ul>
                    <!-- submenus -->
                    <li><a href="{{ url('/clients') }}"> View All Clients</a></li>
                    <li><a href="{{ url('/create-client') }}"> Create New Client</a></li>
                    <li><a href="{{ url('/create-bu') }}"> Create Client BU/Distric</a></li>
                    <li><a href="{{ url('/create-injection-station') }}"> Create Injection Station</a></li>

                    <!-- <li><a href="graphs-inline.html">Inline Charts</a></li>
                    <li><a href="graphs-chartjs.html">Chart.js</a></li> -->
                </ul>
            </li>
            <li>
                <a href="{{ url('/admin-sites') }}">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-bar-chart" aria-hidden="true"></i> <span>Installed Sites </span>
                </a>

            </li>

            <li>
                <a href="#">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-commenting-o"></i> <span> Communication </span>
                </a>
                <ul>
                    <!-- submenus -->
                    <li><a href="{{ url('/communication') }}">View Emails/Phone number </a></li>
                    <li><a href="{{ url('/add-communication') }}">Add Emails/Phone number</a></li>

                </ul>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class=" main-icon fas fa-laptop-code"></i> <span>APIs Management </span>
                </a>
                <ul>
                    <!-- submenus -->
                    <li><a href="{{ url('/getapikeylist') }}">View All APIs </a></li>
                </ul>
            </li>
            <li>
                <a href="">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-bell" aria-hidden="true"></i> <span>Notificatiion</span>
                </a>

            </li>
            <li>
                <a href="#">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-bug" aria-hidden="true"></i> <span>Sites Analysis </span>
                </a>
                <ul>
                    <!-- submenus -->
                    <li><a href="{{ url('/Control-Sites') }}">Control Test</a></li>
                    <li><a href="{{ url('/Fault-Sites') }}">Fault Scanning</a></li>
                    <li><a href="{{ url('/Not-Transmit') }}">Not Transmitting Scan</a></li>
                    <li><a href="{{ url('/Alarm-report') }}">Generate Alarm Report</a></li>
                    <li><a href="{{ url('/Login-report') }}">Generate Login Report</a></li>
                    <li><a href="{{ url('/power') }}">Generate Power Report</a></li>
                </ul>
            </li>

            <li>
                <a href="#">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-history"></i> <span> Logs </span>
                </a>
                <ul>
                    <!-- submenus -->
                    <li><a href="{{ url('/login-Activities') }}"> Login Activities</a></li>
                    <li><a href="{{ url('/control-Activities') }}">Control Logs </a></li>
                    <li><a href="{{ url('/cb-trip-logs') }}">CB Trip Logs</a></li>
                </ul>
            </li>

        </ul>


    </nav>

    <span id="asidebg">
        <!-- aside fixed background --></span>
</aside>
