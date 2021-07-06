@extends('layout.app_bu')

@section('title', 'View UT Details| SUSEJ IoT- RealTime Network Monitoring')
@section('content')



<section id="middle">


    <!-- page title -->
    <header id="page-header">
        <h1>{{ $getUtDetail->name}} UT</h1>
        <ol class="breadcrumb">
            <li><a href="/viewut">View All UTs</a></li>
            <li class="active">UT Details</li>
        </ol>
    </header>
    <!-- /page title -->


    <div id="content" class="padding-20">

        <div class="panel panel-default">
            <div class="panel-body">

                <div class="row">

                    <div class="col-md-12 col-sm-12 text-left">
                        <input id="utid" type="hidden"  value="{{$getUtDetail->id}}">
                        <h4><strong>UT</strong> Details</h4>
                        <ul class="list-unstyled">
                            <li><strong> Name:</strong> {{ $getUtDetail->name}}</li>
                            <li><strong> Email Adddress:</strong> {{$getUtDetail->email}}</li>
                            <li><strong> Phone Number:</strong> {{$getUtDetail->phone_number }}</li>
                            <li><strong> Address:</strong> {{$getUtDetail->address}}</li>
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
                                <th style="width: 120px;">Trans. Temp (&#8451;) </th>
                                <!-- <th class="sorting wid-20" tabindex="0" rowspan="1" colspan="1">HV Status</th> -->
                                <th style="width: 100px;">DT Status</th>
                                <th style="width: 80px;">Upriser A</th>
                                <th style="width: 80px;">Upriser B</th>
                                <th style="width: 80px;">Upriser C</th>
                                <th style="width: 80px;">Upriser D</th>
                                <th style="width: 50px;">Action </th>
                            </tr>

                        </thead>
                    </table>
                </div>

                <hr class="nomargin-top" />

                
            </div>
        </div>

    </div>
</section>



@endsection