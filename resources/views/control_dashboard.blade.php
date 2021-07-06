@extends('layout.app_Ctrldashboard')

@section('title', 'Dashboard | SUSEJ IoT- RealTime Network Monitoring')

@section('content')

<section id="middle">

    <!-- page title -->
    <header id="page-header">
        <div class="row" style="text-align: center">
            <div class="col">
                <h1 style="margin:0px"><strong>{{ Auth::user()->name }}</strong></h1>
                <h4>DT Remote Control dashboard</h4>
            </div>
        </div>
        <div class="row">
            <div class="padding-40">
                <div class="row" style="text-align: left; ">

                    <h4>Search and Select Distribution Station</h4>
                    <input class="typeahead form-control" id="sitesearch" type="text" placeholder="Enter Station Name">

                </div>
            </div>
        </div>
        <div style="text-align: center; ">
            <h2>OR</h2>
        </div>
        <div class="row">
            <div class="padding-20">
                <div class="row" style="text-align: left; ">
                    <div class="col-sm-6">
                        <select id="selectbu" name="selectbu" class="form-control select2"
                            style="width:100%; font-weight:bold; ">
                            @if($type=='client')
                            <option value="All" selected>Select {{Auth::user()->dash_label1}}</option>
                            @foreach($ctrldata as $data)
                            <option value='{{ $data->id }}'>{{ $data->name }}</option>
                            @endforeach
                            @else
                            <option value="All">Select {{Auth::user()->dash_label1}}</option>
                            @foreach($ctrldata as $data)
                            <option value='{{ $data->id }}' selected>{{ $data->name }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <select id="selectut" name="selectut" class="form-control select2" style="width:100%">
                            @if($type=='ut')
                            <option value="All">Select {{Auth::user()->dash_label2}}</option>
                            <option value='{{ Auth::user()->id }}' selected>{{ Auth::user()->name }}</option>
                            @else
                            <option value="All" selected>Select {{Auth::user()->dash_label2}}</option>
                            @foreach($ctrldata as $data)
                            @foreach($data->ut_info as $ut)
                            <option value='{{ $ut->id }}'>{{ $ut->name }}</option>
                            @endforeach
                            @endforeach
                            @endif



                        </select>
                    </div>

                </div>
            </div>
    </header>

    <div id="content" class="padding-20">
        <h4 style="margin: auto;">Icon Description</h4>
        <div class="row">
            <div class="col-sm-3">
                <i class="fa fa-bell text-success"></i> : CB Turned ON
            </div>
            <div class="col-sm-3">
                <i class="fa fa-bell text-warning"></i> : CB Tripped
            </div>
            <div class="col-sm-3">
                <i class="fa fa-bell text-danger"></i> : CB Turned OFF
            </div>

            <div class="col-sm-3">
                <i class="fa fa-close text-info"></i> : CB Not Configured
            </div>
        </div>

        <div id="panel-1" class="panel panel-default" style="margin-top:10px">
            <div class="panel-heading">
                <span class="title elipsis">
                    <strong> Site Chart View
                    </strong>
                </span>

                <!-- right options -->
                <ul class="options pull-right list-inline">
                    <li><a href="#" class="opt panel_colapse" data-toggle="tooltip" title="Colapse"
                            data-placement="bottom"></a></li>
                    <!-- <li><a href="#" class="opt panel_fullscreen hidden-xs" data-toggle="tooltip" title="Fullscreen"
                            data-placement="bottom"><i class="fa fa-expand"></i></a></li> -->
                </ul>
                <!-- /right options -->
            </div>
            <!-- panel content -->
            <div class="panel-body" id="panelbody">
                <div class="table-responsive">
                    <table class="table table-bordered" id="sitecb" width="100%" cellspacing="2">
                        <thead>
                            <tr>
                                <th>Site ID</th>
                                <th>Site Name</th>
                                <th>DT Status</th>
                                <th>CB Main</th>
                                <th>CB Upriser A</th>
                                <th>CB Upriser B</th>
                                <th>CB Upriser C</th>
                                <th>CB Upriser D</th>
                                <th>Action
                                </th>
                            </tr>

                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>
</section>
<!--Modal: Login with Avatar Form-->
@include('modals.password')
@endsection