@extends('layout.app_super')

@section('title', 'APIs Management | Susej IoT')
@section('content')

<section id="middle">

    <header id="page-header" class="padding-20">
        <div class="row">
            <div class="col-md-11">
                <button type="button" id="genapikey" data-toggle="modal" data-target="#modal-gen-api"
                    class="btn btn-success">Generate API Key</button>
            </div>
        </div>
    </header>

    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default" style="margin-top:10px">
            <div class="panel-heading">
                <span class="title elipsis">
                    <strong> List of Client APIs
                    </strong>
                </span>


            </div>
            <!-- panel content -->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="apikeys" width="100%" cellspacing="2">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Client</th>
                                <th style="width: 100px;">API Key</th>
                                <th style="width: 100px;">Request Counts</th>
                                <th style="width: 120px;">Status</th>
                                <th style="width: 120px;">Update Date</th>
                                <th style="width: 50px;">Action</th>
                            </tr>

                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/pages/clientapi.js?v=0')}}"></script>
@include('modals.confirm-delete')
@include('modals.api-details')
@include('modals.apiKey-gen')


@endsection