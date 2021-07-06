@extends('layout.app_super')

@section('title', 'Dashboard | SUSEJ IoT- RealTime Network Monitoring')

@section('content')

<section id="middle">

    <!-- page title -->
    <header id="page-header">
        <div class="row" style="text-align: center">
            <div class="col">
                <h1 style="margin:0px"><strong>{{ Auth::user()->name }}</strong></h1>
                <h4>DT Remote CB Test Dashboard</h4>
            </div>

            <div class="col">
                <input type="checkbox" checked data-toggle="toggle" data-on="Set ON<br>Command"
                    data-off="Set OFF<br>Command" id="ctrl" data-width="100" data-size="small" data-onstyle="success"
                    data-offstyle="danger">

            </div>
            <div class="col" style="margin-top: 20px">
                <button type="button" id="sender" class="btn btn-success">Send
                    Command</button>
            </div>


        </div>

        <div class="row">
            <div class="padding-20">
                <div class="row" style="text-align: left; ">
                    <div class="col-sm-4">
                        <select id="select" name="select" class="form-control select2"
                            style="width:100%; font-weight:bold; ">
                            <option value="All" selected="selected">All Client</option>
                            @foreach( $clients as $data)
                            <option value='{{ $data->id }}'>{{ $data->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <select id="selectbu" name="selectbu" class="form-control select2"
                            style="width:100%; font-weight:bold; ">
                            <option value="All" selected="selected">All Units</option>

                        </select>
                    </div>
                    <div class="col-sm-4">
                        <select id="selectut" name="selectut" class="form-control select2" style="width:100%">
                            <option value="All" selected="selected">All Sub-units</option>
                        </select>
                    </div>

                </div>
            </div>
    </header>

    <div id="content" class="padding-20">

        <div id="panel-1" class="panel panel-default" style="margin-top:10px">
            <div class="panel-heading">
                <span class="title elipsis">
                    <strong> Test Report
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
                                <th>Client</th>
                                <th>Units</th>
                                <th>Sub-units</th>
                                <th>Site Name</th>
                                <th>DT Status</th>
                                <th>Feedback</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>
</section>
<!--Modal: Login with Avatar Form-->
<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/pages/ctrltest.js')}}"></script>
@endsection