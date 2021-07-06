@extends('layout.app_super')

@section('title', 'All Installed Sites | Susej IoT')
@section('content')

<section id="middle">
    <header id="page-header">
        <ol class="breadcrumb">
            <li><a href="/admin">Dashboard</a></li>
            <li class="active">Installed Sites</li>
        </ol>
    </header>
    <div id="content" class="padding-20">
        <div class="col">

            <select id="selectclient" name="selectclient" class="form-control select2">
                <option value="All" selected="selected">All Client Sites </option>
                @foreach($user_info as $data)
                <option value='{{ $data->id }}'>{{ $data->name }}</option>
                @endforeach
            </select>

        </div>
        <div id="panel-1" class="panel panel-default" style="margin-top:10px">
            <div class="panel-heading">
                <span class="title elipsis">
                    <strong> All DT Sites
                    </strong>
                </span>
            </div>

            <!-- panel content -->
            <div class="panel-body">
                <div class="table-responsive">
                    <button style="margin-bottom: 10px" data-name="Multiple" data-toggle="modal"
                        data-target="#modal-delete-api" data-id="All" class="btn btn-success delete_all" id="delete"
                        data-url="" disabled>Delete
                        All Selected</button>
                    <table class="table table-bordered" id="dtsites" width="100%" cellspacing="2">
                        <thead>
                            <tr>

                                <th style="width:1px"><input type="checkbox" id="master"></th>
                                <th style="width: 200px;">Client</th>
                                <th style="width: 200px;">SerialNo. </th>
                                <th style="width: 200px;">Name </th>
                                <th style="width: 80px;">SIM No. </th>
                                <th style="width: 150px;">Install Date</th>
                                <th style="width: 150px;">Last Update</th>
                                <th style="width: 100px;">Latitude</th>
                                <th style="width: 100px;">Longitude</th>
                                <th style="width: 80px;">Action </th>
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
@include('modals.edit-client-site')
@include('modals.confirm-delete')
@include('modals.loader')
@endsection