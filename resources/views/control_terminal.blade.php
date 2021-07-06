@extends('layout.app_CtrlTerminal')
@section('title', 'DT Control | Susej IoT')
@section('content')

<style>
hr {
    margin-top: 1rem;
    margin-bottom: 1rem;
    border: 0;
    border-top: 2px solid rgba(0, 0, 0, 0.1);
}
</style>
<section id="middle">
    <header id="page-header">
        <ol class="breadcrumb">
            <li><a href="/control">Control Dashboard</a></li>
            <li class="active">Control Terminal</li>
        </ol>
    </header>
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default" style="margin-top:10px;height:100%;">

            <div class="" style="text-align:center">
                <span class="title elipsis">
                    <strong style="font-size:25px;"> DT Remote Control Terminal
                    </strong>
                </span>
            </div>
            <div>
                <label for="info" style="font-weight: bold; font-style: italic;font-size:12px;">*Note: <br>Please use
                    with
                    caution<br>In case of Tripped CB,
                    Kindly
                    send OFF command <br>Wait
                    for aleast 1 minute to 2 minutes for the Circuit Breaker to reset<br>Before send
                    the ON command to close the Circuit Breaker </label>
            </div>
            <hr />

            <div class="row" style="margin-bottom:10px; margin-top:20px">
                <div class="col-sm-3">
                    <span>Feedback signal indicators:</span>
                </div>
                <div class="col-sm-3">
                    <i class="fa fa-2x fa-bolt text-success"></i> : CB Turned ON
                </div>

                <div class="col-sm-3">
                    <i class="fa fa-2x fa-bolt text-warning"></i> : CB Tripped
                </div>
                <div class="col-sm-3" style="text-align:right">
                    <i class="fa fa-2x fa-bolt text-danger"></i> : CB Turned OFF
                </div>

            </div>
            <div class="panel-body" id="panelbody">

                <div class="row">
                    <div class="row" style="margin-top:20px;margin-bottom:30px;">
                        <div class="col">
                            <div class="card-body">
                                <div class="container">

                                    <div class="row" style="text-align: center; ">
                                        <div class="row">
                                            <div class="col">
                                                <h2 id="sitename" style="font-weight: bold; margin-right:10px;">
                                                </h2>
                                                <h5 id="downstatus" style="font-weight: bold; margin-right:10px;">
                                                    STATUS :
                                                    LIVE</h5>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr />


                        <div class="row" style="margin-top:20px;margin-bottom:30px;" id="ctrl">
                            <div class="col">
                                <div class="card-body">
                                    <div class="container">
                                        <div class="row " style="text-align: center; ">
                                            <input id='serial_number' name="serial_number"
                                                value="{{$control_data->serial_number}}" type="hidden">

                                            <div class="col">
                                                <span class="row ">MAIN CB CONTROL</span>
                                                <input class="row " type="checkbox" checked data-toggle="toggle"
                                                    data-width="200" id="ctrlbut" data-height="150"
                                                    data-onstyle="success" data-offstyle="danger">
                                                <div class="row" style="margin-top:20px">
                                                    <i class="fa fa-bolt fa-5x text-success" id="fbM"></i>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="row" style="margin-top:30px;margin-bottom:30px;" id="ctrl">
                            <div class="col">
                                <div class="card-body">
                                    <div class="row " style="text-align: center; ">
                                        <input id='serial_number' name="serial_number" value="" type="hidden">
                                        <div class="col-md-3" id="upA">
                                            <p class="row" style="margin:0px">UPRISER A CB</p>
                                            <input class="row " type="checkbox" checked data-toggle="toggle"
                                                data-width="160" id="ctrlbutA" data-height="80" data-onstyle="success"
                                                data-offstyle="danger">
                                            <div class="row" style="margin-top:20px">
                                                <i class="fa fa-bolt fa-5x text-success" id="fbA"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-3" id="upB">
                                            <div class="row">
                                                <p class="row" style="margin:0px">UPRISER B CB </p>
                                                <input class="row" type="checkbox" checked data-toggle="toggle"
                                                    data-width="160" id="ctrlbutB" data-height="80"
                                                    data-onstyle="success" data-offstyle="danger">
                                            </div>
                                            <div class="row" style="margin-top:20px">
                                                <i class="fa fa-bolt fa-5x text-success" id="fbB"></i>
                                            </div>

                                        </div>
                                        <div class="col-md-3" id="upC">
                                            <p class="row" style="margin:0px">UPRISER C CB </p>
                                            <input class="row " type="checkbox" checked data-toggle="toggle"
                                                data-width="160" id="ctrlbutC" data-height="80" data-onstyle="success"
                                                data-offstyle="danger">
                                            <div class="row" style="margin-top:20px">
                                                <i class="fa fa-bolt fa-5x text-success" id="fbC"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-3" id="upD">
                                            <p class="row" style="margin:0px">UPRISER D CB</p>
                                            <input class="row " type="checkbox" checked data-toggle="toggle"
                                                data-width="160" id="ctrlbutD" data-height="80" data-onstyle="success"
                                                data-offstyle="danger">
                                            <div class="row" style="margin-top:20px">
                                                <i class="fa fa-bolt fa-5x text-success" id="fbD"></i>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                        <hr />

                        <div class="row"
                            style="display: flex; justify-content: center;  align-items: center; margin-top:50px">
                            <div class="col">
                                <button type="button" id="sender" style=" height: 80px;" data-toggle="modal"
                                    data-target="#modal-control" data-name="{{$control_data->name}}"
                                    data-status="{{$control_data->DT_status}}" class="btn btn-success btn-lg">Send
                                    Command</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

</section>
@include('modals.confirm-control')
<!-- JAVASCRIPT FILES -->
<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
<script>
var serial_number = $("input[name=serial_number]").val();
var jsondata = @json($control_data);
var ctrl_enable = jsondata.ctrl_enable;
$('#sitename').text(jsondata.name);
if (jsondata.DT_status == 0) {
    $('#downstatus').text('STATUS: DOWN');
    $('#downstatus').css("color", "red");
} else {

    $('#downstatus').text('STATUS : LIVE');
    $('#downstatus').css("color", "green");
}
var uprisers = jsondata.uprisers
if (jsondata.c1 == '1' || jsondata.c1 == '2') {
    $('#ctrlbut').prop('checked', true);
} else {
    $('#ctrlbut').prop('checked', false);
}
if (jsondata.z1 == '1') {
    if ($("#fbM").hasClass("text-danger")) {
        $("#fbM")
            .removeClass("text-danger")
            .addClass("text-success");
    }
    if ($("#fbM").hasClass("text-warning")) {
        $("#fbM")
            .removeClass("text-warning")
            .addClass("text-success");
    }

} else if (jsondata.c1 == '2') {
    if ($("#fbM").hasClass("text-danger")) {
        $("#fbM")
            .removeClass("text-danger")
            .addClass("text-warning");
    }
    if ($("#fbM").hasClass("text-success")) {
        $("#fbM")
            .removeClass("text-success")
            .addClass("text-warning");
    }
} else {
    if ($("#fbM").hasClass("text-warning")) {
        $("#fbM")
            .removeClass("text-warning")
            .addClass("text-danger");
    }
    if ($("#fbM").hasClass("text-success")) {
        $("#fbM")
            .removeClass("text-success")
            .addClass("text-danger");
    }

}

if (ctrl_enable == 1) {
    if (uprisers == 1) {
        $('#upA').removeClass('col-md-3').addClass('col');
        $('#upB').hide();
        $('#upC').hide();
        $('#upD').hide();
        if (jsondata.c2 == '1' || jsondata.c2 == '2') {
            $('#ctrlbutA').prop('checked', true);
        } else {
            $('#ctrlbutA').prop('checked', false);
        }


        if (jsondata.z2 == "1") {
            if ($("#fbA").hasClass("text-danger")) {
                $("#fbA")
                    .removeClass("text-danger")
                    .addClass("text-success");
            }
            if ($("#fbA").hasClass("text-warning")) {
                $("#fbA")
                    .removeClass("text-warning")
                    .addClass("text-success");
            }

        } else if (jsondata.c2 == "2") {
            if ($("#fbA").hasClass("text-danger")) {
                $("#fbA")
                    .removeClass("text-danger")
                    .addClass("text-warning");
            }
            if ($("#fbA").hasClass("text-success")) {
                $("#fbA")
                    .removeClass("text-success")
                    .addClass("text-warning");
            }
        } else {

            if ($("#fbA").hasClass("text-warning")) {
                $("#fbA")
                    .removeClass("text-warning")
                    .addClass("text-danger");
            }
            if ($("#fbA").hasClass("text-success")) {
                $("#fbA")
                    .removeClass("text-success")
                    .addClass("text-danger");
            }

        }

    } else if (uprisers == 2) {
        $('#upA').removeClass('col-md-3').addClass(
            'col-md-6 col-sm-6');
        $('#upB').removeClass('col-md-3').addClass(
            'col-md-6 col-sm-6');

        $('#upC').hide();
        $('#upD').hide();
        if (jsondata.c2 == '1' || jsondata.c2 == '2') {
            $('#ctrlbutA').prop('checked', true);
        } else {
            $('#ctrlbutA').prop('checked', false);
        }
        if (jsondata.c3 == '1' || jsondata.c3 == '2') {
            $('#ctrlbutB').prop('checked', true);
        } else {
            $('#ctrlbutB').prop('checked', false);
        }

        if (jsondata.z2 == "1") {
            if ($("#fbA").hasClass("text-danger")) {
                $("#fbA")
                    .removeClass("text-danger")
                    .addClass("text-success");
            }
            if ($("#fbA").hasClass("text-warning")) {
                $("#fbA")
                    .removeClass("text-warning")
                    .addClass("text-success");
            }

        } else if (jsondata.c2 == "2") {
            if ($("#fbA").hasClass("text-danger")) {
                $("#fbA")
                    .removeClass("text-danger")
                    .addClass("text-warning");
            }
            if ($("#fbA").hasClass("text-success")) {
                $("#fbA")
                    .removeClass("text-success")
                    .addClass("text-warning");
            }
        } else {

            if ($("#fbA").hasClass("text-warning")) {
                $("#fbA")
                    .removeClass("text-warning")
                    .addClass("text-danger");
            }
            if ($("#fbA").hasClass("text-success")) {
                $("#fbA")
                    .removeClass("text-success")
                    .addClass("text-danger");
            }

        }

        if (jsondata.z3 == "1") {
            if ($("#fbB").hasClass("text-danger")) {
                $("#fbB")
                    .removeClass("text-danger")
                    .addClass("text-success");
            }
            if ($("#fbB").hasClass("text-warning")) {
                $("#fbB")
                    .removeClass("text-warning")
                    .addClass("text-success");
            }
        } else if (jsondata.c3 == "2") {
            if ($("#fbB").hasClass("text-danger")) {
                $("#fbB")
                    .removeClass("text-danger")
                    .addClass("text-warning");
            }
            if ($("#fbB").hasClass("text-success")) {
                $("#fbB")
                    .removeClass("text-success")
                    .addClass("text-warning");
            }
        } else {
            if ($("#fbB").hasClass("text-warning")) {
                $("#fbB")
                    .removeClass("text-warning")
                    .addClass("text-danger");
            }
            if ($("#fbB").hasClass("text-success")) {
                $("#fbB")
                    .removeClass("text-success")
                    .addClass("text-danger");
            }
        }


    } else if (uprisers == 3) {
        $('#upA').removeClass('col-md-3').addClass(
            'col-md-4 col-sm-6');
        $('#upB').removeClass('col-md-3').addClass(
            'col-md-4 col-sm-6');
        $('#upC').removeClass('col-md-3').addClass(
            'col-md-4 col-sm-6');
        $('#upD').hide();
        if (jsondata.c2 == '1' || jsondata.c2 == '2') {
            $('#ctrlbutA').prop('checked', true);
        } else {
            $('#ctrlbutA').prop('checked', false);
        }
        if (jsondata.c3 == '1' || jsondata.c3 == '2') {
            $('#ctrlbutB').prop('checked', true);
        } else {
            $('#ctrlbutB').prop('checked', false);
        }

        if (jsondata.c4 == '1' || jsondata.c4 == '2') {
            $('#ctrlbutC').prop('checked', true);
        } else {
            $('#ctrlbutC').prop('checked', false);
        }

        if (jsondata.z2 == "1") {
            if ($("#fbA").hasClass("text-danger")) {
                $("#fbA")
                    .removeClass("text-danger")
                    .addClass("text-success");
            }
            if ($("#fbA").hasClass("text-warning")) {
                $("#fbA")
                    .removeClass("text-warning")
                    .addClass("text-success");
            }

        } else if (jsondata.c2 == "2") {
            if ($("#fbA").hasClass("text-danger")) {
                $("#fbA")
                    .removeClass("text-danger")
                    .addClass("text-warning");
            }
            if ($("#fbA").hasClass("text-success")) {
                $("#fbA")
                    .removeClass("text-success")
                    .addClass("text-warning");
            }
        } else {

            if ($("#fbA").hasClass("text-warning")) {
                $("#fbA")
                    .removeClass("text-warning")
                    .addClass("text-danger");
            }
            if ($("#fbA").hasClass("text-success")) {
                $("#fbA")
                    .removeClass("text-success")
                    .addClass("text-danger");
            }

        }

        if (jsondata.z3 == "1") {
            if ($("#fbB").hasClass("text-danger")) {
                $("#fbB")
                    .removeClass("text-danger")
                    .addClass("text-success");
            }
            if ($("#fbB").hasClass("text-warning")) {
                $("#fbB")
                    .removeClass("text-warning")
                    .addClass("text-success");
            }
        } else if (jsondata.c3 == "2") {
            if ($("#fbB").hasClass("text-danger")) {
                $("#fbB")
                    .removeClass("text-danger")
                    .addClass("text-warning");
            }
            if ($("#fbB").hasClass("text-success")) {
                $("#fbB")
                    .removeClass("text-success")
                    .addClass("text-warning");
            }
        } else {
            if ($("#fbB").hasClass("text-warning")) {
                $("#fbB")
                    .removeClass("text-warning")
                    .addClass("text-danger");
            }
            if ($("#fbB").hasClass("text-success")) {
                $("#fbB")
                    .removeClass("text-success")
                    .addClass("text-danger");
            }
        }


        if (jsondata.z4 == "1") {
            if ($("#fbC").hasClass("text-danger")) {
                $("#fbC")
                    .removeClass("text-danger")
                    .addClass("text-success");
            }
            if ($("#fbC").hasClass("text-warning")) {
                $("#fbC")
                    .removeClass("text-warning")
                    .addClass("text-success");
            }
        } else if (jsondata.c4 == "2") {
            if ($("#fbC").hasClass("text-warning")) {
                $("#fbC")
                    .removeClass("text-warning")
                    .addClass("text-danger");
            }
            if ($("#fbC").hasClass("text-success")) {
                $("#fbC")
                    .removeClass("text-success")
                    .addClass("text-danger");
            }
        } else {
            if ($("#fbC").hasClass("text-warning")) {
                $("#fbC")
                    .removeClass("text-warning")
                    .addClass("text-danger");
            }
            if ($("#fbC").hasClass("text-success")) {
                $("#fbC")
                    .removeClass("text-success")
                    .addClass("text-danger");
            }
        }

    } else {

        if (jsondata.c2 == '1' || jsondata.c2 == '2') {
            $('#ctrlbutA').prop('checked', true);
        } else {
            $('#ctrlbutA').prop('checked', false);
        }
        if (jsondata.c3 == '1' || jsondata.c3 == '2') {
            $('#ctrlbutB').prop('checked', true);
        } else {
            $('#ctrlbutB').prop('checked', false);
        }

        if (jsondata.c4 == '1' || jsondata.c4 == '2') {
            $('#ctrlbutC').prop('checked', true);
        } else {
            $('#ctrlbutC').prop('checked', false);
        }
        if (jsondata.c5 == '1' || jsondata.c5 == '2') {
            $('#ctrlbutD').prop('checked', true);
        } else {
            $('#ctrlbutD').prop('checked', false);
        }

        if (jsondata.z2 == "1") {
            if ($("#fbA").hasClass("text-danger")) {
                $("#fbA")
                    .removeClass("text-danger")
                    .addClass("text-success");
            }
            if ($("#fbA").hasClass("text-warning")) {
                $("#fbA")
                    .removeClass("text-warning")
                    .addClass("text-success");
            }

        } else if (jsondata.c2 == "2") {
            if ($("#fbA").hasClass("text-danger")) {
                $("#fbA")
                    .removeClass("text-danger")
                    .addClass("text-warning");
            }
            if ($("#fbA").hasClass("text-success")) {
                $("#fbA")
                    .removeClass("text-success")
                    .addClass("text-warning");
            }
        } else {

            if ($("#fbA").hasClass("text-warning")) {
                $("#fbA")
                    .removeClass("text-warning")
                    .addClass("text-danger");
            }
            if ($("#fbA").hasClass("text-success")) {
                $("#fbA")
                    .removeClass("text-success")
                    .addClass("text-danger");
            }

        }

        if (jsondata.z3 == "1") {
            if ($("#fbB").hasClass("text-danger")) {
                $("#fbB")
                    .removeClass("text-danger")
                    .addClass("text-success");
            }
            if ($("#fbB").hasClass("text-warning")) {
                $("#fbB")
                    .removeClass("text-warning")
                    .addClass("text-success");
            }
        } else if (jsondata.c3 == "2") {
            if ($("#fbB").hasClass("text-danger")) {
                $("#fbB")
                    .removeClass("text-danger")
                    .addClass("text-warning");
            }
            if ($("#fbB").hasClass("text-success")) {
                $("#fbB")
                    .removeClass("text-success")
                    .addClass("text-warning");
            }
        } else {
            if ($("#fbB").hasClass("text-warning")) {
                $("#fbB")
                    .removeClass("text-warning")
                    .addClass("text-danger");
            }
            if ($("#fbB").hasClass("text-success")) {
                $("#fbB")
                    .removeClass("text-success")
                    .addClass("text-danger");
            }
        }
        if (jsondata.z4 == "1") {
            if ($("#fbC").hasClass("text-danger")) {
                $("#fbC")
                    .removeClass("text-danger")
                    .addClass("text-success");
            }
            if ($("#fbC").hasClass("text-warning")) {
                $("#fbC")
                    .removeClass("text-warning")
                    .addClass("text-success");
            }
        } else if (jsondata.c4 == "2") {
            if ($("#fbC").hasClass("text-warning")) {
                $("#fbC")
                    .removeClass("text-warning")
                    .addClass("text-danger");
            }
            if ($("#fbC").hasClass("text-success")) {
                $("#fbC")
                    .removeClass("text-success")
                    .addClass("text-danger");
            }
        } else {
            if ($("#fbC").hasClass("text-warning")) {
                $("#fbC")
                    .removeClass("text-warning")
                    .addClass("text-danger");
            }
            if ($("#fbC").hasClass("text-success")) {
                $("#fbC")
                    .removeClass("text-success")
                    .addClass("text-danger");
            }
        }

        if (jsondata.z5 == "1") {
            if ($("#fbD").hasClass("text-danger")) {
                $("#fbD")
                    .removeClass("text-danger")
                    .addClass("text-success");
            }
            if ($("#fbD").hasClass("text-warning")) {
                $("#fbD")
                    .removeClass("text-warning")
                    .addClass("text-success");
            }
        } else if (jsondata.c5 == "2") {
            if ($("#fbD").hasClass("text-warning")) {
                $("#fbD")
                    .removeClass("text-warning")
                    .addClass("text-danger");
            }
            if ($("#fbD").hasClass("text-success")) {
                $("#fbD")
                    .removeClass("text-success")
                    .addClass("text-danger");
            }
        } else {
            if ($("#fbD").hasClass("text-warning")) {
                $("#fbD")
                    .removeClass("text-warning")
                    .addClass("text-danger");
            }
            if ($("#fbD").hasClass("text-success")) {
                $("#fbD")
                    .removeClass("text-success")
                    .addClass("text-danger");
            }
        }
    }
} else if (ctrl_enable == 2) {
    $('#upA').hide();
    $('#upB').hide();
    $('#upC').hide();
    $('#upD').hide();
}
</script>

@endsection