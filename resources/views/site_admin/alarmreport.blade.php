@extends('layout.app_site')

@section('title', 'Alarm Report | Susej IoT')
@section('content')

<section id="middle">
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default" style="margin-top:10px">
            <div class="panel-heading">
                <span class="title elipsis">
                    <strong> Report Filter
                    </strong>
                </span>
            </div>
            <!-- panel content -->
            <div class="panel-body">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4 col-sm-4">
                            <label>Filter with Status</label>
                            <select id="status" name="status" class="form-control select2">
                                <option value="All">All</option>
                                <option value="Active" selected="selected">Unresolved</option>
                                <option value="Resolved">Resolved</option>
                            </select>

                        </div>
                        <div class="col-md-4 col-sm-4">
                            <input type="hidden" class="form-control datepicker" data-format="yyyy-mm-dd" data-lang="en"
                                data-RTL="false">
                            <label>Filter with Date</label>
                            <!-- range picker -->
                            <input type="text" id="rangepicker" class="form-control rangepicker" name="daterange"
                                value="All" data-format="yyyy-mm-dd" data-from="" data-to="">

                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div id="panel-1" class="panel panel-default" style="margin-top:10px">
            <div class="panel-heading">
                <span class="title elipsis">
                    <strong> Report View
                    </strong>
                </span>
            </div>
            <!-- panel content -->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="report" width="100%" cellspacing="2">
                        <thead>
                            <tr>
                                <!-- <th>Site ID</th> -->
                                <th style="width: auto;">Site Name</th>
                                <th style="width: auto;">Alarm Name</th>
                                <th style="width: auto;">Alarm Date</th>
                                <th style="width: auto;">Duration</th>
                                <th style="width: auto;">Status</th>
                            </tr>

                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/pages/site_report.js')}}"></script>
@endsection