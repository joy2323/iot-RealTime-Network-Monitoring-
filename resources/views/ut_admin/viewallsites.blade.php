@extends('layout.app_ut')

@section('title', 'All Sites | Susej IoT')
@section('content')

<section id="middle">

    <div id="content" class="padding-20">

        <div id="panel-1" class="panel panel-default" style="margin-top:10px">
            <div class="panel-heading">
                <span class="title elipsis">
                    <strong> All Sites View
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
                    <table class="table table-bordered" id="allutsites" width="100%" cellspacing="2">
                        <thead>
                            <tr>
                                <th style="width: 100px;">Site ID</th>
                                <th style="width: 100px;;">Site Name</th>
                                <th style="width: 100px;">Number Of Uprisers</th>
                                <th style="width: 100px;"> Longitude</th>
                                <th style="width: 100px;"> Latitude</th>
                                <th style="width: 50px;">Action </th>
                            </tr>

                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/pages/siteuser.js')}}"></script>

@include('modals.edit-allutsite')


@endsection
