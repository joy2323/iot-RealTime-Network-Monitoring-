@extends('layout.app_super')

@section('title', 'Faulty Sites | Susej IoT')
@section('content')

<section id="middle">
    <header id="page-header">
        <ol class="breadcrumb">
            <li><a href="/admin">Dashboard</a></li>
            <li class="active">Faulty Sites</li>
        </ol>
    </header>
    <header id="page-header">
        <div class="col">

            <label>Fault Days Count</label>
            <select id="day" name="day" class="form-control select2">
                <option value="15">Past 15 Days</option>
                <option value="20">Past 20 Days</option>
                <option value="30" selected="selected">Past 30 Days</option>
                <option value="40">Past 40 Days</option>
                <option value="50">Past 50 Days</option>
                <option value="60">Past 60 Days</option>

            </select>
        </div>
        <div id="content" class="padding-20">
    </header>
    <div id="panel-1" class="panel panel-default" style="margin-top:10px">
        <div class="panel-heading">
            <span class="title elipsis" style="margin-bottom:8px">
                <strong> Site Fault Analysis
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
        <!-- /.row -->
        <!-- Main row -->
        <div class="panel-body" id="panelbody">
            <div class="table-responsive">

                <table class="table table-bordered" id="faultsite" width="100%" cellspacing="2">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Client</th>
                            <th style="width: auto;">BU</th>
                            <th style="width: auto;">Site Name</th>
							<th style="width: 150px;">Longitude </th>
							<th style="width: 150px;">Latitude </th>
                            <th style="width: auto;">Fault details</th>
                            <th style="width: auto;">Alert Date</th>
                            <th style="width: auto;">Fault Duration</th>

                        </tr>

                    </thead>
                </table>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/pages/faultsite.js')}}"></script>
@endsection