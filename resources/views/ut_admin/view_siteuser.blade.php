@extends('layout.app_ut')

@section('title', 'All SiteUser | Susej IoT')
@section('content')

<section id="middle">
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default" style="margin-top:10px">
            <div class="panel-heading">
                <span class="title elipsis">
                    <strong> All SiteUser View
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
                    <table class="table table-bordered" id="allsiteuser" width="100%" cellspacing="2">
                        <thead>
                            <tr>
                                <th style="width: 120px;">SiteUser Name</th>
                                <th style="width: 120px;">Email </th>
                                <th style="width: 120px;"> Phone Number  </th>
                                <!-- <th class="sorting wid-20" tabindex="0" rowspan="1" colspan="1">HV Status</th> -->
                                <th style="width: 100px;">Address </th>
                                <!-- <th class="sorting wid-20" tabindex="0" rowspan="1" colspan="1">Alarm Status</th> -->
                                <!-- <th style="width: 70px;">Up B</th>
                                <th style="width: 70px;">Up D</th> -->
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


@include('modals.edit-siteuser')


@endsection
