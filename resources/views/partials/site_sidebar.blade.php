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
                <a class="dashboard" href="{{url('/dashboard')}}">
                    <!-- warning - url used by default by ajax (if eneabled) -->
                    <i class="main-icon fa fa-home"></i> <span>Dashboard</span>
                </a>
            </li>

            <li>
                <a href="{{url('/usersites')}}">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-bar-chart"></i> <span> Sites </span>
                </a>

            </li>

            <li>
                <a href="{{url('/alarms')}}">
                    <i class="fa fa-menu-arrow pull-right"></i>
                    <i class="main-icon fa fa-history"></i> <span> Alarm Reports </span>
                </a>

            </li>

        </ul>


    </nav>

    <span id="asidebg">
        <!-- aside fixed background --></span>
</aside>