@extends('layout.app_ut')

@section('title', 'Add Sites To SiteUser || Susej IoT')
@section('content')
<section id="middle">
    <div id="content" class="padding-50">

        <div class="row">

            <div class="col">

                <!-- ------ -->
                <div class="panel panel-default">
                    <div class="panel-heading panel-heading-transparent">
                        <strong>Add Site to SiteUser</strong>
                    </div>

                    <div class="panel-body">
                        <form class="" action="/addsiteuser_site" method="post" enctype="multipart/form-data" data-success="Site have been Assigned to User Added! Thank you!" data-toastr-position="top-right">
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
                                            <select id="selectut" name="selectsiteuserid" class="form-control select2">
                                                <option value="" selected="selected">Select UT </option>
                                                @foreach($allSiteUserAdmin as $data)
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
