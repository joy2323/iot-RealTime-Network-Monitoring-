@extends('layout.app')
@section('title', 'Injection Station | Susej IoT')
@section('content')
<section id="middle" style="">
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default" style="margin-top:10px">

            <header class="panel-heading" id="page-header">
                <ol class="breadcrumb" style="text-align: left;  text-transform: capitalize;">
                    @if(Auth::user()->role =='Super admin')
                    <li><a href="/admin-injection-station">Injection Dashboard</a></li>
                    @else
                    <li><a href="/injection-stations">Injection Dashboard</a></li>
                    @endif
                    <li class="active">Injection Chart</li>
                </ol>
                <div class="row">
                    <div class="col-md-11">
                        <h1 style="margin:0px"><strong>{{ $getData->name }}</strong></h1>
                        <h4>Injection Station Overview </h4>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-11">
                        <table>
                            <tr>

                                <th style="padding-right: 50px" id="usersum"> <a href="#"> <b class="text-info">Number
                                            of Feeders
                                            :
                                        </b>{{ $getData->uprisers }}</a> </th>

                            </tr>
                        </table>
                    </div>

                </div>


            </header>

            <div class="panel-body" id="panelbody">
                <h5 id="downstatus" style="text-align: left; font-weight: bold;"></h5>

                <div id="container">
                    <h5 style="text-align: left; margin-top:30px;"> <b>INCOMING SUPPLY</b></h5>
                    <div style="border-bottom: thick solid blue; "></div>
                    <div class="row" id="chartview" style="text-align:center;width: 100%">

                    </div>
                    <div style="border-bottom: thick solid blue; "></div>
                    <h5 style="text-align: left; "><b>OUTGOING FEEDER SUPPLY</b></h5>

                </div>

            </div>
        </div>
    </div>

</section>

<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
<script>
$(document).ready(function() {
    var idnum = "{{join('',explode('injection-view/',Request::path()))}}";
    var getData = @json($getData);
    var feeders = getData.uprisers;
    var details = @json($getDetails);
    var labels = @json($getLabel);
    var stationstatus = @json($getStatus);
    var label = Object.values(labels);
    var detail = Object.values(details);
    var padding = '28px';
    if (feeders <= 6) {
        padding = '40px';
    } else if (feeders >= 12) {
        padding = '25px';
    }

    if (stationstatus.DT_status == 0) {
        $("#downstatus").text('STATUS : DOWN')
        $('#downstatus').css('color', 'red');

    } else {
        $("#downstatus").text('STATUS : LIVE')
        $('#downstatus').css('color', 'green');

    }
    for (let i = 0; i < feeders; i++) {

        var feedername = label[i];
        var status = detail[i];
        var id = 'feeder' + (i + 1);

        if (stationstatus.DT_status == '0') {
            var feederLine =
                "<div class='col' style='display: inline-block;margin: 0 auto;padding:" + padding + "'>" +
                "<h5>" + feedername + "</h5>" +
                "<img id='" + id + "'src='{{ asset('images/down.png')}}' height='300px' alt=''>" +
                "</div>"
        } else {
            if (status == '1') {
                var feederLine =
                    "<div class='col' style='display: inline-block;margin: 0 auto;padding:" + padding + "'>" +
                    "<h5>" + feedername + "</h5>" +
                    "<img id='" + id + "'src='{{ asset('images/ok.png')}}' height='300px' alt=''>" +
                    "</div>"
            } else if (status == 0) {
                var feederLine =
                    "<div class='col' style='display: inline-block;margin: 0 auto;padding:" + padding + "'>" +
                    "<h5>" + feedername + "</h5>" +
                    "<img id='" + id + "'src='{{ asset('images/fault.png')}}' height='300px' alt=''>" +
                    "</div>"
            }
        }
        $("#chartview").append(feederLine);
    }

    setInterval(() => {
        $.ajax({
            method: 'Get',
            url: "/injection-data",
            data: {
                id: idnum,
            },
            success: function(data) {
                var feeders = data.feeders;
                var stationstatus = data.getStatus.DT_status;
                var detail = Object.values(data.getDetails);
                if (stationstatus == 0) {
                    $("#downstatus").text('STATUS : DOWN')
                    $('#downstatus').css('color', 'red');

                } else {
                    $("#downstatus").text('STATUS : LIVE')
                    $('#downstatus').css('color', 'green');

                }
                for (let i = 0; i < feeders; i++) {
                    var status = detail[i];
                    var id = 'feeder' + (i + 1);
                    if (stationstatus == '0') {
                        $("#" + id).attr("src", "{{ asset('images/down.png')}}");

                    } else {
                        if (status == '1') {
                            $("#" + id).attr("src", "{{ asset('images/ok.png')}}");
                        } else if (status == 0) {
                            $("#" + id).attr("src", "{{ asset('images/fault.png')}}");
                        }
                    }
                }
            }

        })
    }, 5000);

});
</script>

@endsection