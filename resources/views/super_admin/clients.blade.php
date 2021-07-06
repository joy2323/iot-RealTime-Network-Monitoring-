@extends('layout.app_super')
@section('title', 'Clients | SUSEJ IoT- RealTime Network Monitoring')
@section('content')

<section id="middle">
    <header id="page-header">
        <ol class="breadcrumb">
            <li><a href="/admin">Dashboard</a></li>
            <li class="active">Clients</li>
        </ol>
    </header>
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default" style="margin-top:10px">
            <div class="panel-heading">
                <span class="title elipsis">
                    <strong>Clients
                    </strong>
                </span>
            </div>
            <!-- panel content -->
            <div class="panel-body">
                <div class="table-responsive">
                    <a href="{{url('/create-client')}}" style="margin-bottom: 10px" class="btn btn-success delete_all"
                        id='create'>Create Client</a>
                    <table class="table table-bordered" id="client" width="100%" cellspacing="2">
                        <thead>
                            <tr>
                                <th style="width: auto;">Name</th>
                                <th style="width: 400px;">Address</th>
                                <th style="width: auto;">Email</th>
                                <th style="width: auto">Phone Number </th>
                                <th style="width: auto;">Installed Sites</th>
                                <th style="width: auto;">Control Status</th>
                                <th style="width: auto;">Account Status</th>
                                <th style="width: auto;">Action</th>
                            </tr>

                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@include('modals.edit-client')
@include('modals.confirm-delete')
@include('modals.loader')
<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/pages/allclient.js?v=2')}}"></script>
@endsection