@extends('layout.app_bu')

@section('title', 'Dashboard | SUSEJ IoT- RealTime Network Monitoring')

@section('content')

<section id="middle">

    <!-- page title -->
    <header id="page-header">
        <div class="row">
            <div class="col-md-11">
                <h1 style="margin:0px"><strong>{{ Auth::user()->name }}</strong></h1>
                <h4>DT Sites Overview </h4>
            </div>
            <div class="col-md-1 col-sm-1" style="float: right">
                <input type="checkbox" checked data-toggle="toggle" data-on="Sound<br>Enabled"
                    data-off="Sound<br>Disabled" id="soundctrl" data-width="80" data-size="small" data-onstyle="success"
                    data-offstyle="danger">

            </div>
        </div>

        <div class="row">
            <div class="col-md-11">
                <table>
                    <tr>
                        <th style="padding-right: 50px" id="usersum"> <a href="#"> <b class="text-info">Users :
                                </b>{{ $sum }}</a> </th>
                        <!-- <th style="padding-right: 50px" id="devicesum"><b class="text-info"> Devices : </b>{{$device_sum}} </th> -->
                        <th style="padding-right: 50px" id="sitesum"> <a href="#"> <b class="text-info">Sites :
                                </b>{{ $site_sum }} </a>
                        </th>
                        <th style="padding-right: 50px" id="dsite"> <a href="#"> <b class="text-info">Down Sites :
                                </b>{{ $downsite }}</a>
                        </th>
                        <th id="lsite"> <a href="#"> <b class="text-info">Live Sites : </b>{{ $livesite }}</a> </th>
                    </tr>
                </table>
            </div>

        </div>

    </header>

    <div id="content" class="padding-20">
        <div class="">
            <h4 style="margin: auto;">Icon Description</h4>
            <div class="row">
                <div class="col-sm-3">
                    <i class="fa fa-circle text-success"></i> : Healthy
                </div>
                <div class="col-sm-3">
                    <i class="fa fa-bell text-warning"></i> : Alarm
                </div>
                <div class="col-sm-3">
                    <i class="fa fa-bell text-danger"></i> : Critical Alarm
                </div>
                <div class="col-sm-3">
                    <i class="fa fa-close text-info"></i> : Not Configured
                </div>
            </div>
        </div>

        <div id="panel-1" class="panel panel-default" style="margin-top:10px">
            <div class="panel-heading">
                <span class="title elipsis">
                    <strong> Site Chart View
                    </strong>
                </span>

                <!-- right options -->
                <ul class="options pull-right list-inline">
                    <li><a href="#" class="opt panel_colapse" data-toggle="tooltip" title="Colapse"
                            data-placement="bottom"></a></li>
                    <li><a href="#" class="opt panel_fullscreen hidden-xs" data-toggle="tooltip" title="Fullscreen"
                            data-placement="bottom"><i class="fa fa-expand"></i></a></li>
                </ul>
                <!-- /right options -->
            </div>
            <!-- panel content -->
            <div class="panel-body" id="panelbody">
                <div class="table-responsive">
                    <table class="table table-bordered" id="site" width="100%" cellspacing="2">
                        <thead>
                            <tr>
                                <th>Site ID</th>
                                <th style="width: auto;">Site Name</th>
                                <th style="width: 120px;">Env. Temp (&#8451;)</th>
                                <th style="width: 120px;">Today's Up Time </th>
                                <!-- <th class="sorting wid-20" tabindex="0" rowspan="1" colspan="1">HV Status</th> -->
                                <th style="width: 100px;">DT Status</th>
                                <!-- <th class="sorting wid-20" tabindex="0" rowspan="1" colspan="1">Alarm Status</th> -->
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
            </div>
        </div>

        <div id="mymap" style="width: auto;height: 800px;"></div>
    </div>
</section>
<script src="https://maps.google.com/maps/api/js?key=AIzaSyCn5ARfEvG7ivp5u-yX80YZF1DKHd8u7n4"></script>
<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/pages/bu.js')}}"></script>
<script type="text/javascript">
var markers = new Array();
var map = new google.maps.Map(document.getElementById('mymap'), {
    zoom: 12,
    center: new google.maps.LatLng(6.545454, 3.334343),
    mapTypeId: google.maps.MapTypeId.ROADMAP
});

$.ajax({
    type: "GET",
    url: "/location",
    success: function(data) {
        loadMarker(data);
        AutoCenter();
    },
    error: function(request, error) {
        window.location.reload();
    },
})
const loadMarker = (data) => {
    var alarm;
    var status;
    $.each(data, function(index, value) {

        if (value.DT_status == "0") {
            alarm = "{{asset('images/dtdown.png')}}";
            status = '<h5 style="margin:auto;width: 250px; word-wrap: break-word;"> ' + value.name +
                '</h5>' +
                '<p style="margin:auto"><b>DT Status:</b>, Down, No Alarm </p>' +
                '<p style="margin:auto"><b>Alarm Status:</b> DT Down </p>' +
                '<p style="margin:auto"><b>Last Update:</b> ' + value.updated_at + '</p>';
        } else if (value.DT_status == "1") {
            if (value.alarm_status == "0") {
                alarm = "{{asset('images/dtup.png')}}";
                status = '<h5 style="margin:auto;width: 250px; word-wrap: break-word;"> ' + value.name +
                    '</h5>' +
                    '<p style="margin:auto"><b>DT Status:</b>, Live </p>' +
                    '<p style="margin:auto"><b>Alarm Status:</b> No Alarm</p>' +
                    '<p style="margin:auto"><b>Last Update:</b> ' + value.updated_at + '</p>';
            } else {
                alarm = "{{asset('images/dtalarm.png')}}";
                status = '<h5 style="margin:auto;width: 250px; word-wrap: break-word;"> ' + value.name +
                    '</h5>' +
                    '<p style="margin:auto"><b>DT Status:</b>, Live </p>' +
                    '<p style="margin:auto"><b>Alarm Status:</b> Alarm</p>' +
                    '<p style="margin:auto"><b>Last Update:</b> ' + value.updated_at + '</p>';
            }

        } else {
            alarm = "{{asset('images/notconfig.png')}}";
            status = '<h5 style="margin:auto;width: 250px; word-wrap: break-word;"> ' + value.name +
                '</h5>' +
                '<p style="margin:auto"><b>DT Status:</b>  Not Activated </p>' +
                '<p style="margin:auto"><b>Alarm Status:</b> Not Activated </p>';
        }

        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(value.lat, value.long),
            icon: alarm,
            title: value.name,
            map: map
        });
        markers.push(marker);
        var infowindow = new google.maps.InfoWindow({
            content: status
        });

        // google.maps.event.addListener(marker, 'click', function() {
        //     infowindow.open(map, marker);
        // });
        marker.addListener('click', function() {

            if (!marker.open) {
                infowindow.open(map, marker);
                marker.open = true;
            } else {
                infowindow.close();
                marker.open = false;
            }
            marker.addListener(map, 'click', function() {
                infowindow.close();
                marker.open = false;
            });

        });

    });
}
// Sets the map on all markers in the array.
const setMapOnAll = (map) => {
    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(map);
    }
}

// Removes the markers from the map, but keeps them in the array.
const clearMarkers = () => {
    setMapOnAll(null);
}

// Deletes all markers in the array by removing references to them.
const deleteMarkers = () => {
    clearMarkers();
    markers = [];
}

const AutoCenter = () => {
    //  Create a new viewpoint bound
    var bounds = new google.maps.LatLngBounds();
    //  Go through each...
    $.each(markers, function(index, marker) {
        bounds.extend(marker.position);
    });
    //  Fit these bounds to the map
    map.fitBounds(bounds);
}

setInterval(() => {
    $.ajax({
        type: "GET",
        url: "/location",
        success: function(data) {
            deleteMarkers();
            loadMarker(data);
        },
        error: function(request, error) {
            window.location.reload();
        },
    })

}, 10000);
</script>


@endsection
