@extends('layout.app_bu')

@section('title', 'Communication | SUSEJ IoT- RealTime Network Monitoring')
@section('content')

<section id="middle">
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
                    <table class="table table-bordered" id="communication" width="100%" cellspacing="2">
                        <thead>
                            <tr>
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
<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/pages/bu_communication.js')}}"></script>
@endsection