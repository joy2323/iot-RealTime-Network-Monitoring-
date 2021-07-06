@extends('layout.app_client')

@section('title', 'View BU Details| SUSEJ IoT- RealTime Network Monitoring')
@section('content')



<section id="middle">


    <!-- page title -->
    <header id="page-header">
        <h1>{{ $getBUDetails->name}} UT</h1>
        <ol class="breadcrumb">
            <li><a href="/viewall-bu">{{Auth::user()->dash_label1}}s</a></li>
            <li class="active">{{Auth::user()->dash_label1}} Details</li>
        </ol>
    </header>
    <!-- /page title -->


    <div id="content" class="padding-20">

        <div class="panel panel-default">
            <div class="panel-body">

                <div class="row">

                    <div class="col-md-12 col-sm-12 text-left">
                        <input id="utid" type="hidden" value="{{$getBUDetails->id}}">
                        <h4><strong>{{Auth::user()->dash_label1}} </strong> Details</h4>
                        <ul class="list-unstyled">
                            <li><strong> Name:</strong> {{ $getBUDetails->name}}</li>
                            <li><strong> Reponse Emails:</strong>
                                @foreach($communication as $data)
                                {{$data->email}} ,
                                @endforeach
                            </li>
                            <li><strong> Response Phone Numbers:</strong>
                                @foreach($communication as $data)
                                234{{$data->phone_number}} ,
                                @endforeach
                            </li>
                            <li><strong> Address:</strong> {{$getBUDetails->address}}</li>
                            <li><strong> Total Site Attached:</strong> {{$sitecount}}</li>
                        </ul>

                    </div>

                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="busite" width="100%" cellspacing="2">
                        <thead>
                            <tr>
                                <th>Site ID</th>
                                <th style="width: auto;">Site Name</th>
                                <th style="width: 120px;">Env. Temp (&#8451;)</th>
                                <!-- <th style="width: 120px;">Trans. Temp (&#8451;) </th> -->
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


<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/pages/allbu.js')}}"></script>

@endsection