@extends('layout.app_bu')

@section('title', 'View UT Details| SUSEJ IoT- RealTime Network Monitoring')
@section('content')



<section id="middle">


    <!-- page title -->
    <header id="page-header">
        <h1>{{ $getUtDetail[0]->name}} {{Auth::user()->dash_label2}}</h1>
        <ol class="breadcrumb">
            <li><a href="/view-ut">{{Auth::user()->dash_label1}}s</a></li>
            <li class="active">{{ $getUtDetail[0]->name}} {{Auth::user()->dash_label2}} Details</li>
        </ol>
    </header>
    <!-- /page title -->


    <div id="content" class="padding-20">

        <div class="panel panel-default">
            <div class="panel-body">

                <div class="row">

                    <div class="col-md-12 col-sm-12 text-left">
                        <input id="utid" type="hidden" value="{{$getUtDetail[0]->id}}">
                        <h4><strong>{{Auth::user()->dash_label2}}</strong> Details</h4>
                        <ul class="list-unstyled">
                            <li><strong> Name:</strong> {{ $getUtDetail[0]->name}}</li>
                            <li><strong> Email Adddress:</strong> {{$getUtDetail[0]->email}}</li>
                            <li><strong> Phone Number:</strong> {{$getUtDetail[0]->phone_number }}</li>
                            <li><strong> Address:</strong> {{$getUtDetail[0]->address}}</li>
                            <li><strong> Total Site Attached:</strong> {{$sitecount}}</li>
                        </ul>

                    </div>

                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="utsite" width="100%" cellspacing="2">
                        <thead>
                            <tr>
                                <th>Site ID</th>
                                <th style="width: auto;">Site Name</th>
                                <th style="width: 120px;">Env. Temp (&#8451;)</th>
                                <th style="width: 120px;">Today's Up Time </th>
                                <th style="width: 100px;">DT Status</th>
                                <th style="width: 80px;">Upriser A</th>
                                <th style="width: 80px;">Upriser B</th>
                                <th style="width: 80px;">Upriser C</th>
                                <th style="width: 80px;">Upriser D</th>
                                <th style="width: 50px;">Action
                                </th>
                            </tr>

                        </thead>
                    </table>
                </div>

                <hr class="nomargin-top" />


            </div>
        </div>

    </div>
</section>
<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/pages/allut.js')}}"></script>
@endsection