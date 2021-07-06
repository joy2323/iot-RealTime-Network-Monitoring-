@extends('layout.app_client')

@section('title', 'All BUs | Susej IoT')
@section('content')

<section id="middle">
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default" style="margin-top:10px">
            <div class="panel-heading">
                <span class="title elipsis">
                    <strong> {{Auth::user()->dash_label1}} View
                    </strong>
                </span>

                <!-- right options -->
                {{-- <ul class="options pull-right list-inline">
                    <li><a href="#" class="opt panel_colapse" data-toggle="tooltip" title="Colapse"
                            data-placement="bottom"></a></li>
                    <li><a href="#" class="opt panel_fullscreen hidden-xs" data-toggle="tooltip" title="Fullscreen"
                            data-placement="bottom"><i class="fa fa-expand"></i></a></li>
                    </ul> --}}
                <!-- /right options -->
            </div>
            <!-- panel content -->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="allbu" width="100%" cellspacing="2">
                        <thead>
                            <tr>
                                <th style="width: 120px;">{{Auth::user()->dash_label1}}</th>
                                <th style="width: 120px;">Primary Email </th>
                                <th style="width: 120px;">Primary Phone Number </th>
                                <th style="width: 100px;">Address </th>
                                <th style="width: 100px;">Control Authorized </th>
                                <th style="width: 50px;">Action </th>
                            </tr>

                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

@include('modals.edit-buinfo')

<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/pages/allbu.js?v=1')}}"></script>

@endsection