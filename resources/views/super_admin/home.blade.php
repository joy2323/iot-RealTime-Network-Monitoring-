@extends('layout.app_super')

@section('title', 'Dashboard | SUSEJ IoT- RealTime Network Monitoring')

@section('content')


<style type="text/css">
.green {
    color: green;
}

.red {
    color: red;
}
</style>

<section id="middle">

    <!-- page title -->
    <header id="page-header">
        <h1> DT Sites Overview</h1>
        <div class="row">
            <div class="col-lg-8">
                <table>
                    <tr>
                        <th style="padding-right: 50px" id="usersum"> <a href="/clients"><b class="text-info">Clients :
                                </b>{{ $sum }}</a>
                        </th>
                        <th style="padding-right: 50px" id="sitesum"> <a href="/admin-sites"><b class="text-info">Sites
                                    :
                                </b>{{ $site_sum }}</a>
                        </th>
                        <th style="padding-right: 50px" id="nonactive"> <a href="/Not-Transmit"><b
                                    class=" text-info">Not-Transmitting
                                    :
                                </b>{{ $notactive }}</a>
                        </th>
                        <th style="padding-right: 50px" id="dsite"> <a href="#"> <b class=" text-info">Down Sites :
                                </b>{{ $downsite }}</a></th>
                        <th style="padding-right: 50px" id="lsite"> <a href="#"><b class=" text-info">Live Sites :
                                </b>{{ $livesite }} </a></th>
                        <th id="fsite"> <a href="/Fault-Sites"><b class=" text-info">Fault Check:
                                </b>{{ $faultsite }}
                            </a></th>
                    </tr>
                </table>
            </div>

        </div>

    </header>

    <div id="content" class="padding-20">
        <div class="col">

            <select id="selectclient" name="selectclient" class="form-control select2">
                <option value="All" selected="selected">All Client Sites Monitor</option>
                @foreach( $client as $data)
                <option value='{{ $data->id }}'>{{ $data->name }}</option>
                @endforeach
            </select>

        </div>
        <div id="panel-1" class="panel panel-default" style="margin-top:10px">
            <div class="panel-heading">
                <span class="title elipsis" style="margin-bottom:8px">
                    <strong> Client Preview
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
            <!-- /.row -->
            <!-- Main row -->
            <div class="panel-body" id="panelbody">
                <div class="table-responsive">
                    <table class="table " id="sitepreview" width="100%" cellspacing="2">
                        <thead>
                            <tr>
                                <th style="width: auto;  font-size: large">Client</th>
                                <th style="width: auto;  font-size: large">Sites Intalled</th>
                                <th style="width: auto;  font-size: large">Live Sites</th>
                                <th style="width: auto;  font-size: large">Down Sites</th>

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

<script type="text/javascript" src="{{ asset('js/pages/admin.js?v=0')}}"></script>
<script type="text/javascript">
var markers = new Array();
var map = new google.maps.Map(document.getElementById('mymap'), {
    zoom: 12,
    center: new google.maps.LatLng(6.545454, 3.334343),
    mapTypeId: google.maps.MapTypeId.ROADMAP
});

$.ajax({
    type: "GET",
    url: "/alllocation",
    success: function(data) {
        loadMarker(data);
        AutoCenter();
    },
    error: function(request, error) {
        // window.location.reload();
    },
})


function loadMarker(data) {
    var alarm;
    var status;
    $.each(data, function(index, value) {

        if (value.DT_status == "0") {
            alarm = "{{asset('images/dtdown.png')}}";
            status = '<h5 style="margin:auto;width: 250px; word-wrap: break-word;"> ' + value.name + '</h5>' +
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
            status = '<h5 style="margin:auto;width: 250px; word-wrap: break-word;"> ' + value.name + '</h5>' +
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
function setMapOnAll(map) {
    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(map);
    }
}

// Removes the markers from the map, but keeps them in the array.
function clearMarkers() {
    setMapOnAll(null);
}

// Deletes all markers in the array by removing references to them.
function deleteMarkers() {
    clearMarkers();
    markers = [];
}

function AutoCenter() {
    //  Create a new viewpoint bound
    var bounds = new google.maps.LatLngBounds();
    //  Go through each...
    $.each(markers, function(index, marker) {
        bounds.extend(marker.position);
    });
    //  Fit these bounds to the map
    map.fitBounds(bounds);
}

setInterval(function() {
    $.ajax({
        type: "GET",
        url: "/alllocation",
        success: function(data) {
            deleteMarkers();
            loadMarker(data);
        },
        error: function(request, error) {
            //  window.location.reload();
        },
    })

}, 10000);
</script>


@endsection