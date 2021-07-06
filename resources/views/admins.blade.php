@extends('layout.app')
@section('title', 'Admin | SUSEJ IoT- RealTime Network Monitoring')
@section('content')

<section id="middle">
    <header id="page-header">
        <ol class="breadcrumb">
            @if(Auth::user()->role =='Client admin')
            <li><a href="/client">Dashboard</a></li>
            @elseif(Auth::user()->role =='BU admin')
            <li><a href="/bu">Dashboard</a></li>
            @elseif(Auth::user()->role =='UT admin')
            <li><a href="/ut">Dashboard</a></li>
            @elseif(Auth::user()->role =='SiteUser admin')
            <li><a href="/dashboard">DDashboard</a></li>
            @endif
            <li class="active">Admin List</li>
        </ol>
    </header>
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default" style="margin-top:10px">
            <div class="panel-heading">
                <span class="title elipsis">
                    <strong>Administators
                    </strong>
                </span>
            </div>
            <!-- panel content -->
            <div class="panel-body">
                <button style="margin-bottom: 10px" data-name="Multiple" data-toggle="modal"
                    data-target="#modal-add-admin" data-id="All" class="btn btn-success" data-url="">Create
                    Admin</button>
                <table class="table table-bordered" id="admins" width="100%" cellspacing="2">
                    <thead>
                        <tr>
                            <th style="width: auto;">Email</th>
                            <th style="width: 150px;">Login count</th>
                            <th style="width: 200px;">Control status</th>
                            <th style="width: 200px;">Activation</th>
                            <th style="width: 200px;">Last login</th>
                            <th style="width: 100px;">Action</th>
                        </tr>

                    </thead>
                </table>
            </div>
        </div>
    </div>
    </div>
</section>

@include('modals.edit-admin')
@include('modals.confirm-delete')
@include('modals.add-admin')
<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/pages/admin_account.js?v=2')}}"></script>
@endsection