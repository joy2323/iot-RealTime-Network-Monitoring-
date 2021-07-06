@extends('layout.app_hv')

@section('title', 'Injection Station Dashboard | SUSEJ IoT- RealTime Network Monitoring')

@section('content')

<section id="middle">

    <!-- page title -->
    <header id="page-header">
        <div class="row">
            <div class="col-md-11">
                <h1 style="margin:0px"><strong>{{ Auth::user()->name }}</strong></h1>
                <h4>Injection Stations Overview </h4>
            </div>

        </div>

        <div class="row">
            <div class="col-md-11">
                <table>
                    <tr>

                        <th style="padding-right: 50px" id="usersum"> <a href="#"> <b class="text-info">Injection
                                    Station :
                                </b>{{ $userinfo['sum'] }}</a> </th>

                    </tr>
                </table>
            </div>

        </div>

    </header>

    <div id="content" class="padding-20">
        <h4 style="margin: auto;">Icon Description</h4>
        <div class="row">
            <div class="col-sm-4">
                <i class="fa fa-circle text-success"></i> : Feeder ON
            </div>
            <div class="col-sm-4">
                <i class="fa fa-bell text-danger"></i> : Feeder OFF
            </div>
            <div class="col-sm-4">
                <i class="fa fa-close text-info"></i> : Feeder Not Configured
            </div>
        </div>

        <div id="panel-1" class="panel panel-default" style="margin-top:10px">
            <div class="panel-heading">
                <span class="title elipsis">
                    <strong> Stations Status View
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
                    <table class="table table-bordered" id="injsite" width="100%">
                        <thead>
                            <tr>
                                <th>Station ID</th>
                                <th style="width: auto;">Station Name</th>
                                <th style="width: 50px;">Feeder 1</th>
                                <th style="width: 50px;">Feeder 2</th>
                                <th style="width: 50px;">Feeder 3</th>
                                <th style="width: 50px;">Feeder 4</th>
                                <th style="width: 50px;">Feeder 5</th>
                                <th style="width: 50px;">Feeder 6</th>
                                <th style="width: 50px;">Feeder 7</th>
                                <th style="width: 50px;">Feeder 8</th>
                                <th style="width: 50px;">Feeder 9</th>
                                <th style="width: 50px;">Feeder 10</th>
                                <th style="width: 50px;">Feeder 11</th>
                                <th style="width: 50px;">Feeder 12</th>
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
<script type="text/javascript" src="{{ asset('js/pages/inj.js')}}"></script>
@endsection