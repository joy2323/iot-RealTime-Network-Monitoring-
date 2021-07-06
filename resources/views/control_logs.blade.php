@extends('layout.app')

@section('title', 'Control Activites| Susej IoT')
@section('content')

<section id="middle">
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default" style="margin-top:10px">
            <div class="panel-heading">
                <span class="title elipsis">
                    <strong> DT Control Activities
                    </strong>
                </span>
            </div>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="form-group">
                    <div class="col" style="margin-left:10px;margin-right:10px;">
                        <input type="hidden" class="form-control datepicker" data-format="yyyy-mm-dd" data-lang="en"
                            data-RTL="false">
                        <label>Filter with Date</label>
                        <!-- range picker -->
                        <input type="text" id="rangepicker" class="form-control rangepicker" name="daterange" value=""
                            data-format="yyyy-mm-dd" data-from="" data-to="">

                    </div>
                </div>
            </div>
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
                            <th style="">Role</th>
                            <th style="width: auto;">User Name</th>
                            <th style="width: auto;">Site Name</th>
                            <th style="width: auto;">Command sent</th>
                            <th style="width: auto;">IP Address</th>
                            <th style="width: auto;">Date/Time</th>
                        </tr>

                    </thead>
                </table>
            </div>
        </div>
    </div>
    </div>
</section>

<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/pages/control_logs.js')}}"></script>
@endsection