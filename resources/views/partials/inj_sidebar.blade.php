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

            <li>
                <!-- dashboard -->
                @if(Auth::user()->role =='INJ admin')
                <a class="dashboard active" href="{{url('/injection-station')}}">
                    <!-- warning - url used by default by ajax (if eneabled) -->
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-home"></i> <span>Dashboard</span>
                </a>
                @else
                <a class="dashboard" href="#">
                    <!-- warning - url used by default by ajax (if eneabled) -->
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-home"></i> <span>Dashboard</span>
                </a>
                <ul>
                    <!-- submenus -->
                    <li><a href="{{url('/client')}}">Distribution Stations (DTs)</a></li>
                    <li class="active"><a href="{{url('/injection-stations')}}">Injection Stations (HVs)</a></li>
                </ul>
                @endif

            </li>
            <li>
                @if(Auth::user()->role =='Client admin')
                <a href="{{url('/view-stations')}}">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-users"></i> <span>Substations</span>
                </a>

                @else
                <a href="#">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-users"></i> <span>Feeders</span>
                </a>
                <ul>
                    <!-- submenus -->
                    <!-- <li><a href="{{url('/view-feeders')}}"> View Feeders</a></li> -->
                </ul>
                @endif
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

                <a href="#">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-commenting-o"></i> <span> Communication </span>
                </a>
                <ul>
                    <!-- submenus -->
                    <li><a href="{{url('/substation-communication')}}">View Emails/Phone number </a></li>
                    <li><a href="{{url('/add-substation-communication')}}">Add Emails/Phone number</a></li>

                </ul>

            </li>
            <li>
                <!-- dashboard -->
                <a href="#">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-power-off"></i> <span> Feeder Supply Control </span>

                </a>
            </li>

            <li>
                <a href="#">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-history"></i> <span> Reports </span>
                </a>
                <ul>
                    <!-- submenus -->
                    <li><a href="{{url('/Injection-report')}}"> Alarm Reports</a></li>
                    <li><a href="{{url('/login-Activities')}}">Login Reports </a></li>

                </ul>
            </li>

        </ul>


    </nav>

    <span id="asidebg">
        <!-- aside fixed background --></span>
</aside>