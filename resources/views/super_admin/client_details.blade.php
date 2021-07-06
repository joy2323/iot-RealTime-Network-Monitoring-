@extends('layout.app_super')

@section('title', 'Dashboard | SUSEJ IoT- RealTime Network Monitoring')

@section('content')


<style type="text/css">
.green {
    color: green;
}

.red {
    color: red;
}
</style>
<section id="middle">
    <header id="page-header">
        <ol class="breadcrumb">
            <li><a href="/clients">Client List</a></li>
            <li class="active">Sites</li>
        </ol>
    </header>
    <!-- page title -->
    <header id="page-header">
        <div class="row">
            <div class="col-md-11">
                <h1 style="margin:0px"><strong>{{ $client->name }}</strong></h1>
                <input type="hidden" name="id" id="clientid" value="{{ $client->id }}">

                <h4>DT Sites Overview </h4>
            </div>

        </div>
        <div class="row">
            <div class="col-lg-8">
                <table>
                    <tr>
                        <th style="padding-right: 50px" id="usersum"> <a href="#"> <b
                                    class="text-info">{{ $client->dash_label1}}s :
                                </b>{{ $userinfo['sum'] }}</a>
                        </th>
                        <th style="padding-right: 50px" id="sitesum"><a href="#"> <b class="text-info"> Installed Sites
                                    :
                                </b>{{ $userinfo['site_sum'] }}</a>
                        </th>

                        <th style="padding-right: 50px" id="dsite"> <a href="#"> <b class=" text-info">Down Sites :
                                </b>{{ $userinfo['downsite'] }}</a></th>
                        <th id="lsite"> <a href="#"><b class=" text-info">Live Sites : </b>{{ $userinfo['livesite'] }}
                            </a></th>
                    </tr>
                </table>
            </div>
            <div class="col-lg-4" style="float: left;">

            </div>
        </div>
    </header>

    <div id="content" class="padding-20">

        <div style="margin-bottom:10px; text-align:center;">
            <div class="row">
                <div class="col-sm-3">
                    <i class="fa fa-circle text-success"></i> : Healthy
                </div>
                <div class="col-sm-3">
                    <i class="fa fa-bell text-warning"></i> : Alarm
                </div>
                <div class="col-sm-3">
                    <i class="fa fa-bell text-danger"></i> : Critical Alarm
                </div>
                <div class="col-sm-3" style="text-align:center">
                    <i class="fa fa-close text-info"></i> : Not Configured
                </div>
            </div>
        </div>
        <div class="col panel panel-default" style="margin-bottom:10px">
            <div class="panel-heading">
                <span class="title elipsis" style="margin-bottom:8px">
                    <strong>Select BU to filter details</strong>
                </span>
            </div>
            <div class="panel-body" style="font-weight:bold;">
                <select id="selectbu" name="selectbu" class="form-control select2">
                    <option value="All">All Business Units</option>
                    @foreach($users as $data)
                    <option value='{{ $data->id }}'>{{ $data->name }}</option>
                    @endforeach
                </select>

            </div>

        </div>


        <div id="panel-1" class="panel panel-default" style="margin-top:10px">
            <div class="panel-heading">
                <span class="title elipsis" style="margin-bottom:8px">
                    <strong> Sites Summary
                    </strong>
                </span>

                <!-- right options -->
                <ul class="options pull-right list-inline">
                    <li><a href="#" class="opt panel_colapse" data-toggle="tooltip" title="Colapse"
                            data-placement="bottom"></a></li>
                </ul>
                <!-- /right options -->
            </div>
            <!-- panel content -->
            <!-- /.row -->
            <!-- Main row -->
            <div class="panel-body" id="panelbody">
                <div class="table-responsive">

                    <table class="table stripe" id="sitepreview" width="80%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 500px; font-size: large">{{$client->dash_label1}}</th>
                                <th style="width: 250px; font-size: large">Sites Installed</th>
                                <th style="width: 250px; font-size: large">Live Sites</th>
                                <th style="width: 250px; font-size: large">Down Sites</th>

                            </tr>

                        </thead>
                    </table>
                </div>
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
                    <li><a href="#" class="opt panel_fullscreen hidden-xs" data-toggle="tooltip" title="Fullscreen"
                            data-placement="bottom"><i class="fa fa-expand"></i></a></li>
                </ul>
                <!-- /right options -->
            </div>
            <!-- panel content -->
            <div class="panel-body" id="panelbody">
                <div class="table-responsive">
                    <table class="table table-bordered" id="clientsite" width="100%" cellspacing="2">
                        <thead>
                            <tr>
                                <th>Site ID</th>
                                <th style="width: auto;">Site Name</th>
                                <th style="width: 120px;">Env. Temp (&#8451;)</th>
                                <th style="width: 120px;">Today's Up Time </th>
                                <th style="width: 100px;">DT Status</th>
                                <th style="width: 80px;">Upriser A</th>
                                <th style="width: 80px;">Upriser B</th>
                                <th style="width: 80px;">Upriser C</th>
                                <th style="width: 80px;">Upriser D</th>
                                <th style="width: 50px;">Action
                                </th>
                            </tr>

                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>
</section>
<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/pages/adminsites.js')}}"></script>
@endsection