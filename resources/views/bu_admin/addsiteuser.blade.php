@extends('layout.app_bu')

@section('title', 'Add Site || Susej IoT')
@section('content')
<section id="middle">
    <div id="content" class="padding-50">

        <div class="row">

            <div class="col">

                <!-- ------ -->
                <div class="panel panel-default">
                    <div class="panel-heading panel-heading-transparent">
                        <strong>Add Site to {{Auth::user()->dash_label2}}</strong>
                    </div>

                    <div class="panel-body">
                        <form class="" action="/addutsite" method="post" enctype="multipart/form-data"
                            data-success="Site User Added! Thank you!" data-toastr-position="top-right">
                            @csrf
                            <fieldset>

                                <div class="row" style="margin: 10px">
                                    <div class="form-group">
                                        <div class="col-md-12 col-sm-12">
                                            <select id="sitename" name="siteid" class="form-control select2">
                                                <option value="" selected="selected">Select site </option>
                                                @foreach($getAllSite as $data)
                                                <option value='{{ $data->id }}'>{{ $data->name }}</option>
                                                @endforeach
                                            </select>

                                        </div>

                                    </div>
                                </div>


                                <div class="row" style="margin: 10px">
                                    <div class="form-group">
                                        <div class="col-md-12 col-sm-12">
                                            <select id="selectut" name="selectutid" class="form-control select2">
                                                <option value="" selected="selected">Select
                                                    {{Auth::user()->dash_label2}} </option>
                                                @foreach($allUtAdmin as $data)
                                                <option value='{{ $data->id }}'>{{ $data->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                </div>
                                <div class="row" style="margin: 10px">
                                    <div class="col-md-12 col-sm-12 text-right">
                                        <button type="submit" class="btn btn-primary">ADD</button>
                                        <!-- SAVE -->
                                        </a>
                                    </div>
                                </div>

                            </fieldset>
                        </form>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection