<!doctype html>
<html lang="en-US">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
    <title> @yield('title') | {{ Auth::user()->name }}</title>
    <meta name="description" content="" />
    <meta name="Susej" content="Susej IoT" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- mobile settings -->
    <meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=0" />

    <!-- WEB FONTS -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
        integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700,800&amp;subset=latin,latin-ext,cyrillic,cyrillic-ext"
        rel="stylesheet" type="text/css" />

    <!-- CORE CSS -->
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link type="text/css" href="{{ asset('assets/plugins/datatables/css/jquery.dataTables.min.css') }}" />

    <!-- THEME CSS -->

    <link href="{{ asset('assets/css/essentials.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/layout.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/chart.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/color_scheme/green.css') }}" rel="stylesheet" type="text/css" id="color_scheme" />
    <link type="text/css" rel="stylesheet" href="{{ asset('css/custom.css') }}" />
    <!--

		PAGE LEVEL STYLES -->
    <link href="{{ asset('assets/css/layout-datatables.css') }}" rel="stylesheet" type="text/css" />

</head>

<body>
    <!-- WRAPPER -->
    <div id="wrapper" class="clearfix">

        @include('partials.navbar')
        @if(Auth::user()->role =='Super admin')
        @include('partials.sidebar')
        @elseif(Auth::user()->role =='Client admin')
        @include('partials.client_sidebar')
        @elseif(Auth::user()->role =='SiteUser admin')
        @include('partials.site_sidebar')
        @else
        @include('partials.bu_sidebar')
        @endif

        @yield('content')
    </div>
    @include('modals.profile')
    <!-- JAVASCRIPT FILES -->
    <script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/apptable.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/datatables/js/dataTables.tableTools.min.js') }}">
    </script>
    <script type="text/javascript" src="{{ asset('assets/plugins/datatables/js/dataTables.colReorder.min.js') }}">
    </script>
    <script type="text/javascript" src="{{ asset('assets/plugins/datatables/js/dataTables.scroller.min.js') }}">
    </script>
    <script type="text/javascript" src="{{ asset('assets/plugins/datatables/dataTables.bootstrap.js') }}"></script>


    <script>
    $(document).ready(function() {
        var serial_number = "{{join('',explode('chart/',Request::path()))}}";
        document.getElementById("content").style.display = "none";
        get_data();
        // make panel body scrollable on fullscreen
        $('body').on('click', '.panel_fullscreen', function() {
            var check = $("#panel-1").hasClass("fullscreen");
            if (check) {
                $("#panelbody").css({
                    "max-height": "10",
                    "overflow-y": "scroll"
                });
            } else {
                $("#panelbody").css({
                    "max-height": "",
                    "overflow-y": ""
                });
            }
        });

        setInterval(() => {
            get_data();
        }, 5000);

		function number_format(number, decimals, decPoint, thousandsSep) {
			number = (number + "").replace(/[^0-9+\-Ee.]/g, "");
			var n = !isFinite(+number) ? 0 : +number;
			var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
			var sep = typeof thousandsSep === "undefined" ? "," : thousandsSep;
			var dec = typeof decPoint === "undefined" ? "." : decPoint;
			var s = "";

			var toFixedFix = function(n, prec) {
				if (("" + n).indexOf("e") === -1) {
					return +(Math.round(n + "e+" + prec) + "e-" + prec);
				} else {
					var arr = ("" + n).split("e");
					var sig = "";
					if (+arr[1] + prec > 0) {
						sig = "+";
					}
					return (+(
						Math.round(+arr[0] + "e" + sig + (+arr[1] + prec)) +
						"e-" +
						prec
					)).toFixed(prec);
				}
			};

			// @todo: for IE parseFloat(0.55).toFixed(0) = 0;
			s = (prec ? toFixedFix(n, prec).toString() : "" + Math.round(n)).split(
				"."
			);
			if (s[0].length > 3) {
				s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
			}
			if ((s[1] || "").length < prec) {
				s[1] = s[1] || "";
				s[1] += new Array(prec - s[1].length + 1).join("0");
			}

			return s.join(dec);
		}
        function get_data() {
            var uprisers = 0;
            let channel = 0;
            $.ajax({
                type: "GET",
                url: "/chartapi/" + serial_number,
                dataType: "json",
                success: function(result, status, xhr) {

                    if (result.status == "0") {
                        document.getElementById("downstatus").style.color = "red";
                        document.getElementById("downstatus").innerHTML = "SITE STATUS : DOWN";
                    } else {
                        document.getElementById("downstatus").style.color = "green";
                        document.getElementById("downstatus").innerHTML = "SITE STATUS : LIVE";
                    }

                    var i;
                    document.getElementById("site_name").innerHTML = result.detail["name"];
                    uprisers = result.detail["uprisers"];
					var env_data=result.analog_values["a1"];
                    document.getElementById("env_temp").innerHTML = number_format(env_data, 2)+
                        "&#176C";
                    if (result.analog_values['a2'] == "R") {
                        document.getElementById("trans").style.display = "none";
                    } else {
                        document.getElementById("trans_temp").innerHTML = result.analog_values[
                            "a2"] + "&#176C";
                    }
                    if (result.analog_values['a3'] == "R") {
                        document.getElementById("level").style.display = "none";
                    } else {
                        document.getElementById("levellabel").innerHTML = result.analog_values[
                            'a3'] + "%";;
                    }
                    if (result.analog_values['a2'] == "R" && result.analog_values['a3'] ==
                        "R") {
                        $('#env').removeClass('col-md-4 col-sm-6').addClass('col');
                    } else if (result.analog_values['a3'] == "R") {
                        $('#env').removeClass('col-md-4 col-sm-6').addClass(
                            'col-md-6 col-sm-6');
                        $('#trans').removeClass('col-md-4 col-sm-6').addClass(
                            'col-md-6 col-sm-6');
                    }
                    document.getElementById("R_volt").innerHTML = result.analog_values['a4'] +
                        "V";
                    document.getElementById("Y_volt").innerHTML = result.analog_values['a5'] +
                        "V";
                    document.getElementById("B_volt").innerHTML = result.analog_values['a6'] +
                        "V";
                    document.getElementById("R_Amp").innerHTML = result.analog_values['a7'] +
                        "A";
                    document.getElementById("Y_Amp").innerHTML = result.analog_values['a8'] +
                        "A";
                    document.getElementById("B_Amp").innerHTML = result.analog_values['a9'] +
                        "A";

                    var channel = uprisers * 3;
                    if (uprisers == 1) {
                        $('#uprisera').removeClass('col-md-3 col-sm-6').addClass('col');
                        $('#upriserb').hide();
                        $('#upriserc').hide();
                        $('#upriserd').hide();
                    } else if (uprisers == 2) {
                        $('#uprisera').removeClass('col-md-3 col-sm-6').addClass(
                            'col-md-6 col-sm-6');
                        $('#upriserb').removeClass('col-md-3 col-sm-6').addClass(
                            'col-md-6 col-sm-6');
                        $('#upriserc').hide();
                        $('#upriserd').hide();
                    } else if (uprisers == 3) {
                        $('#uprisera').removeClass('col-md-3 col-sm-6').addClass(
                            'col-md-4 col-sm-6');
                        $('#upriserb').removeClass('col-md-3 col-sm-6').addClass(
                            'col-md-4 col-sm-6');
                        $('#upriserc').removeClass('col-md-3 col-sm-6').addClass(
                            'col-md-4 col-sm-6');
                        $('#upriserd').hide();
                    } else {}
                    if (result.status == "0") {
                        for (var j = 0; j < uprisers; j++) {
                            if (j == 0) {
                                document.getElementById("upriserAR").src =
                                    "{{asset('images/down.png')}}";
                                document.getElementById("upriserAY").src =
                                    "{{asset('images/down.png')}}";
                                document.getElementById("upriserAB").src =
                                    "{{asset('images/down.png')}}";
                            } else if (j == 1) {
                                document.getElementById("upriserBR").src =
                                    "{{asset('images/down.png')}}";
                                document.getElementById("upriserBY").src =
                                    "{{asset('images/down.png')}}";
                                document.getElementById("upriserBB").src =
                                    "{{asset('images/down.png')}}";
                            } else if (j == 2) {
                                document.getElementById("upriserCR").src =
                                    "{{asset('images/down.png')}}";
                                document.getElementById("upriserCY").src =
                                    "{{asset('images/down.png')}}";
                                document.getElementById("upriserCB").src =
                                    "{{asset('images/down.png')}}";
                            } else if (j == 3) {
                                document.getElementById("upriserDR").src =
                                    "{{asset('images/down.png')}}";
                                document.getElementById("upriserDY").src =
                                    "{{asset('images/down.png')}}";
                                document.getElementById("upriserDB").src =
                                    "{{asset('images/down.png')}}";
                            }
                        }
                    } else {
                        for (var j = 0; j < uprisers; j++) {
                            if (j == 0) {
                                if (result.digital_values["d" + (j + 1)] == "0") {
                                    document.getElementById("upriserAR").src =
                                        '{{asset("images/fault.png ")}}';
                                } else if (result.digital_values["d" + (j + 1)] == "1") {
                                    document.getElementById("upriserAR").src =
                                        "{{asset('images/ok.png')}}";
                                } else {
                                    $('#uprisera').hide();

                                }
                                if (result.digital_values["d" + (j + 2)] == "0") {
                                    document.getElementById("upriserAY").src =
                                        "{{asset('images/fault.png')}}";
                                } else {
                                    document.getElementById("upriserAY").src =
                                        "{{asset('images/ok.png')}}";
                                }
                                if (result.digital_values["d" + (j + 3)] == "0") {
                                    document.getElementById("upriserAB").src =
                                        "{{asset('images/fault.png')}}";
                                } else {
                                    document.getElementById("upriserAB").src =
                                        "{{asset('images/ok.png')}}";
                                }
                            } else if (j == 1) {
                                if (result.digital_values["d" + (j + 3)] == "0") {
                                    document.getElementById("upriserBR").src =
                                        "{{asset('images/fault.png')}}";
                                } else if (result.digital_values["d" + (j + 3)] == "1") {
                                    document.getElementById("upriserBR").src =
                                        "{{asset('images/ok.png')}}";
                                } else {
                                    $('#upriserb').hide();

                                }
                                if (result.digital_values["d" + (j + 4)] == "0") {
                                    document.getElementById("upriserBY").src =
                                        "{{asset('images/fault.png')}}";
                                } else {
                                    document.getElementById("upriserBY").src =
                                        "{{asset('images/ok.png')}}";
                                }
                                if (result.digital_values["d" + (j + 5)] == "0") {
                                    document.getElementById("upriserBB").src =
                                        "{{asset('images/fault.png')}}";
                                } else {
                                    document.getElementById("upriserBB").src =
                                        "{{asset('images/ok.png')}}";
                                }
                            } else if (j == 2) {
                                if (result.digital_values["d" + (j + 5)] == "0") {
                                    document.getElementById("upriserCR").src =
                                        "{{asset('images/fault.png')}}";
                                } else if (result.digital_values["d" + (j + 5)] == "1") {
                                    document.getElementById("upriserCR").src =
                                        "{{asset('images/ok.png')}}";
                                } else {
                                    $('#upriserc').hide();

                                }
                                if (result.digital_values["d" + (j + 6)] == "0") {
                                    document.getElementById("upriserCY").src =
                                        "{{asset('images/fault.png')}}";
                                } else {
                                    document.getElementById("upriserCY").src =
                                        "{{asset('images/ok.png')}}";
                                }
                                if (result.digital_values["d" + (j + 7)] == "0") {
                                    document.getElementById("upriserCB").src =
                                        "{{asset('images/fault.png')}}";
                                } else {
                                    document.getElementById("upriserCB").src =
                                        "{{asset('images/ok.png')}}";
                                }
                            } else if (j == 3) {
                                if (result.digital_values["d" + (j + 7)] == "0") {
                                    document.getElementById("upriserDR").src =
                                        "{{asset('images/fault.png')}}";
                                } else if (result.digital_values["d" + (j + 7)] == "1") {
                                    document.getElementById("upriserDR").src =
                                        "{{asset('images/ok.png')}}";
                                } else {
                                    $('#upriserd').hide();

                                }
                                if (result.digital_values["d" + (j + 8)] == "0") {
                                    document.getElementById("upriserDY").src =
                                        "{{asset('images/fault.png')}}";
                                } else {
                                    document.getElementById("upriserDY").src =
                                        "{{asset('images/ok.png')}}";
                                }
                                if (result.digital_values["d" + (j + 9)] == "0") {
                                    document.getElementById("upriserDB").src =
                                        "{{asset('images/fault.png')}}";
                                } else {
                                    document.getElementById("upriserDB").src =
                                        "{{asset('images/ok.png')}}";
                                }
                            }
                        }
                    }
                    document.getElementById("content").style.display = "block";
                },
                error: function(xhr, status, error) {}
            });
        }
    });
    </script>

</body>

</html>