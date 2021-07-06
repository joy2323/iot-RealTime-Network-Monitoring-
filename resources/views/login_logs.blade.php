@extends('layout.app')

@section('title', 'Login Activites| Susej IoT')
@section('content')

<section id="middle">
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default" style="margin-top:10px">
            <div class="panel-heading">
                <span class="title elipsis">
                    <strong>Login Activities
                    </strong>
                </span>
            </div>
        </div>
        <div id="panel-1" class="panel panel-default" style="margin-top:10px">

            <!-- panel content -->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="controllog" width="100%" cellspacing="2">
                        <thead>
                            <tr>
                                <!-- <th>Site ID</th> -->
                                <th style="width: auto;">Role</th>
                                <th style="width: 200px;">Email</th>
                                <th style="width: 500px;">Agent</th>
                                <th style="width: auto;">IP Address</th>
                                <th style="width: 300px;">Date/Time</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/pages/login_logs.js')}}"></script>
@endsection