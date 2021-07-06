@extends('layout.app_bu')

@section('title', 'All UTs | Susej IoT')
@section('content')

<section id="middle">
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default" style="margin-top:10px">
            <div class="panel-heading">
                <span class="title elipsis">
                    <strong> All UT View
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
                    <table class="table table-bordered" id="allut" width="100%" cellspacing="2">
                        <thead>
                            <tr>
                                <th style="width: 120px;">UT Name</th>
                                <th style="width: 120px;">Email </th>
                                <th style="width: 120px;"> Phone Number </th>
                                <th style="width: 100px;">Address </th>
                                <th style="width: 50px;">Action </th>
                            </tr>

                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

@include('modals.edit-ut')

<!-- <script type="text/javascript" src="{{ asset('js/pages/allbu.js')}}"></script> -->

@endsection
