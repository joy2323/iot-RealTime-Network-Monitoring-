@extends('layout.app_chart')
@section('title', 'Chart | Susej IoT')
@section('content')
<section id="middle">
    <header id="page-header">
        <ol class="breadcrumb" style="text-align: left;  text-transform: capitalize;">

            @if(Auth::user()->role =='Client admin')
            <li><a href="/client">DT Dashboard</a></li>
            @elseif(Auth::user()->role =='BU admin')
            <li><a href="/bu">DT Dashboard</a></li>
            @elseif(Auth::user()->role =='UT admin')
            <li><a href="/ut">DT Dashboard</a></li>
            @elseif(Auth::user()->role =='SiteUser admin')
            <li><a href="/dashboard">DT Dashboard</a></li>
            @endif
            <li class="active">Site Chart</li>
        </ol>
    </header>
    <div id="content" class="padding-20" style="display:none">
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
            <div class="panel-body" id="panelbody">
                <h5 id="downstatus" style="text-align: left; font-weight: bold;">STATUS : LIVE</h5>
                <h3 id="site_name" style="text-align: center; margin-bottom:30px;font-weight: bold;"></h3>
                <div class="row" style="text-align: center;">
                    <div class="col-md-4 col-sm-6" id="env">
                        <h5>Environment Temperature</h5>
                        <div class="blue-square-container">
                            <div id="env_temp" class="blue-square circle">28.7&#176;C
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6" id="trans">
                        <h5>Transformer Temperature</h5>
                        <div class="blue-square-container">
                            <div id="trans_temp" class="blue-square circle">33.6&#176;C</div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6" id="level">
                        <h5>Transformer Oil Level</h5>
                        <div class="blue-square-container">
                            <div id="levellabel" class="blue-square circle">66&#x00025;</div>
                        </div>
                    </div>

                </div>
                <div id="container">
                    <h5 style="text-align: left; margin-top:30px;"> <b>INCOMING SUPPLY</b></h5>
                    <div style="border-bottom: thick solid blue; "></div>
                    <div class="row" id="chartview" style="text-align: center; ">
                        <div id="uprisera" class="col-md-3 col-sm-6">
                            <h5>UPRISER A</h5>
                            <div class="blue-square-container ">
                                <table id="tphase ">
                                    <tr id="ryb2 ">
                                        <td>
                                            <h5>R</h5>
                                        </td>
                                        <td>
                                            <h5>Y</h5>
                                        </td>
                                        <td>
                                            <h5>B</h5>
                                        </td>
                                    </tr>

                                    <tr id="phase ">

                                        <td><img id="upriserAR" src="{{asset('images/down.png')}}" height="300px "
                                                alt=" ">
                                        </td>
                                        <td><img id="upriserAY" src="{{asset('images/down.png')}}" height="300px "
                                                alt=" ">
                                        </td>
                                        <td><img id="upriserAB" src="{{asset('images/down.png')}}" height="300px "
                                                alt=" ">
                                        </td>
                                    </tr>
                                    <tr id="ryb2 ">
                                        <td>
                                            <h5>R</h5>
                                        </td>
                                        <td>
                                            <h5>Y</h5>
                                        </td>
                                        <td>
                                            <h5>B</h5>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                        </div>
                        <div id="upriserb" class="col-md-3 col-sm-6 ">
                            <h5>UPRISER B</h5>
                            <div class="blue-square-container ">
                                <table id="tphase ">
                                    <tr id="ryb2 ">
                                        <td>
                                            <h5>R</h5>
                                        </td>
                                        <td>
                                            <h5>Y</h5>
                                        </td>
                                        <td>
                                            <h5>B</h5>
                                        </td>
                                    </tr>
                                    <tr id="phase ">
                                        <td><img id="upriserBR" src="{{asset('images/down.png')}}" height="300px "
                                                alt=" ">
                                        </td>
                                        <td><img id="upriserBY" src="{{asset('images/down.png')}}" height="300px "
                                                alt=" ">
                                        </td>
                                        <td><img id="upriserBB" src="{{asset('images/down.png')}}" height="300px "
                                                alt=" ">
                                        </td>
                                    </tr>
                                    <tr id="ryb2 ">
                                        <td>
                                            <h5>R</h5>
                                        </td>
                                        <td>
                                            <h5>Y</h5>
                                        </td>
                                        <td>
                                            <h5>B</h5>
                                        </td>
                                    </tr>

                                </table>
                            </div>

                        </div>
                        <div id="upriserc" class="col-md-3 col-sm-6 ">
                            <h5>UPRISER C</h5>
                            <div class="blue-square-container ">

                                <table id="tphase ">
                                    <tr id="ryb2 ">
                                        <td>
                                            <h5>R</h5>
                                        </td>
                                        <td>
                                            <h5>Y</h5>
                                        </td>
                                        <td>
                                            <h5>B</h5>
                                        </td>
                                    </tr>
                                    <tr id="phase ">
                                        <td><img id="upriserCR" src="{{asset('images/down.png')}}" height="300px "
                                                alt=" ">
                                        </td>
                                        <td><img id="upriserCY" src="{{asset('images/down.png')}}" height="300px "
                                                alt=" ">
                                        </td>
                                        <td><img id="upriserCB" src="{{asset('images/down.png')}}" height="300px "
                                                alt=" ">
                                        </td>
                                    </tr>
                                    <tr id="ryb2 ">
                                        <td>
                                            <h5>R</h5>
                                        </td>
                                        <td>
                                            <h5>Y</h5>
                                        </td>
                                        <td>
                                            <h5>B</h5>
                                        </td>
                                    </tr>

                                </table>
                            </div>

                        </div>
                        <div id="upriserd" class="col-md-3 col-sm-6 ">
                            <h5>UPRISER D</h5>
                            <div class="blue-square-container ">
                                <table id="tphase ">
                                    <tr id="ryb2 ">
                                        <td>
                                            <h5>R</h5>
                                        </td>
                                        <td>
                                            <h5>Y</h5>
                                        </td>
                                        <td>
                                            <h5>B</h5>
                                        </td>
                                    </tr>
                                    <tr id="phase ">
                                        <td><img id="upriserDR" src="{{asset('images/down.png')}}" height="300px "
                                                alt=" ">
                                        </td>
                                        <td><img id="upriserDY" src="{{asset('images/down.png')}}" height="300px "
                                                alt=" ">
                                        </td>
                                        <td><img id="upriserDB" src="{{asset('images/down.png')}}" height="300px "
                                                alt=" ">
                                        </td>
                                    </tr>
                                    <tr id="ryb2 ">
                                        <td>
                                            <h5>R</h5>
                                        </td>
                                        <td>
                                            <h5>Y</h5>
                                        </td>
                                        <td>
                                            <h5>B</h5>
                                        </td>
                                    </tr>

                                </table>
                            </div>

                        </div>
                    </div>
                    <div style="border-bottom: thick solid blue; "></div>
                    <h5 style="text-align: left; "><b>OUTGOING SUPPLY <br> FROM FEEDER
                            PANEL</b></h5>

                </div>

                <div class="row " style="text-align: center; margin-bottom: 30px; ">
                    <div class="col-md-4 col-sm-6 ">
                        <h5>Red Phase Voltage</h5>
                        <div class="blue-square-container ">
                            <div id="R_volt" class="blue-square circlered ">28.7&#176;C
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 ">
                        <h5>Yellow Phase Voltage</h5>
                        <div class="blue-square-container ">
                            <div id="Y_volt" class="blue-square circleyellow ">33.6&#176;C</div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 ">
                        <h5>Blue Phase Voltage</h5>
                        <div class="blue-square-container ">
                            <div id="B_volt" class="blue-square circleblue ">66&#x00025;</div>
                        </div>
                    </div>

                </div>
                <div class="row " style="text-align: center">
                    <div class="col-md-4 col-sm-6 ">
                        <h5>Red Phase Current</h5>
                        <div class="blue-square-container ">
                            <div id="R_Amp" class="blue-square circlered ">28.7&#176;C
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 ">
                        <h5>Yellow Phase Current</h5>
                        <div class="blue-square-container ">
                            <div id="Y_Amp" class="blue-square circleyellow ">33.6&#176;C</div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 ">
                        <h5>Blue Phase Current</h5>
                        <div class="blue-square-container ">
                            <div id="B_Amp" class="blue-square circleblue ">66&#x00025;</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</section>
@endsection