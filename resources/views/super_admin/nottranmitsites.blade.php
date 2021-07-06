@extends('layout.app_super')

@section('title', 'Not Transmitting Sites | Susej IoT')
@section('content')

<section id="middle">
    <header id="page-header">
        <ol class="breadcrumb">
            <li><a href="/admin">Dashboard</a></li>
            <li class="active">Not Transmitting Sites</li>
        </ol>
    </header>
    <header id="page-header">
        <div class="col">

            <label>Number of Day For Sites Not Transmitting</label>
            <select id="day" name="day" class="form-control select2">
                <option value="2">Past 2 Days</option>
                <option value="5">Past 5 Days</option>
                <option value="10" selected="selected">Past 10 Days</option>
                <option value="15">Past 15 Days</option>
                <option value="20">Past 20 Days</option>
                <option value="30">Past 30 Days</option>
            </select>
        </div>
        <div id="content" class="padding-20">
    </header>
    <div id="panel-1" class="panel panel-default" style="margin-top:10px">
        <div class="panel-heading">
            <span class="title elipsis">
                <strong> Not Transmitting Sites
                </strong>
            </span>
        </div>
        <!-- panel content -->
        <div class="panel-body">
            <div class="table-responsive">

                <table class="table table-bordered" id="sitetransmit" width="100%" cellspacing="2">
                    <thead>
                        <tr>
                            <th style="width: 100;">Client</th>
                            <th style="width: 150;">BU/District</th>
                            <th style="width: 300px;">Name </th>
							<th style="width: 150px;">Longitude </th>
							<th style="width: 150px;">Latitude </th>
                            <th style="width: 200px;">SerialNo. </th>
                            <th style="width: 200px;">Transmit Status</th>
                            <th style="width: 200px;">Last Transmit Date</th>
                        </tr>

                    </thead>
                </table>
            </div>
        </div>
    </div>
    </div>
</section>
<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/pages/installedsites.js')}}"></script>
@endsection