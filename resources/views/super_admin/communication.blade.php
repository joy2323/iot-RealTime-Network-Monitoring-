@extends('layout.app_super')
@section('title', 'Communication | SUSEJ IoT- RealTime Network Monitoring')
@section('content')

<section id="middle">
    <header id="page-header">
        <ol class="breadcrumb">
            <li><a href="/admin">Dashboard</a></li>
            <li class="active">Communication</li>
        </ol>
    </header>
    <div id="content" class="padding-20">

        <div id="panel-1" class="panel panel-default" style="margin-top:10px">
            <div class="panel-heading">
                <span class="title elipsis">
                    <strong> Communication
                    </strong>

                </span>
            </div>
            <!-- panel content -->
            <div class="panel-body">
                <div class="table-responsive">
                    <div class="row">
                        <div class="col-md-10">
                            <button style="margin-bottom: 10px" data-name="Multiple" data-toggle="modal"
                                data-target="#modal-delete-api" data-id="All" class="btn btn-success delete_all"
                                id="delete" data-url="" disabled>Delete
                                All Selected</button>
                        </div>
                        <div class="col-md-2" style="text-align:left">
                            <label for="" style="font-weight: bold;">Global communication control</label><br>
                            <label for="">Email </label> <input style="margin-right:100px" name="enablemail"
                                id="enablemail" type="checkbox" value="0">
                            <label for="">SMS </label> <input name="enablesms" id="enablesms" type="checkbox" value="0">
                        </div>

                    </div>
                    <hr>
                    <table class="table table-bordered" id="communication" width="100%" cellspacing="2">
                        <thead>
                            <tr>

                                <th style="width:1px"><input type="checkbox" id="master"></th>
                                <th style="width: auto;">Owner</th>
                                <th style="width: auto;">Role</th>
                                <th style="width: auto;">Email</th>
                                <th style="width: auto">Phone Number </th>
                                <th style="width: auto;">Response category</th>
                                <th style="width: auto;">Response Time</th>
                                <th style="width: auto;">Notification Status</th>
                                <th style="width: auto;">Action</th>
                            </tr>

                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@include('modals.edit-communication')
@include('modals.confirm-delete')
<div class="modal fade" id="ajax" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="text-center">
                <img src="/images/loaders/7.gif" alt="" />
            </div>

        </div>
    </div>
</div>
<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/pages/communication.js')}}"></script>
@endsection