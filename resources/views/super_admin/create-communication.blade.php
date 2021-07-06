@extends('layout.app_super')

@section('title', 'Create Communication| SUSEJ IoT- RealTime Network Monitoring')
@section('content')



<section id="middle">
    <header id="page-header">
        <ol class="breadcrumb">
            <li><a href="/communication">Communication</a></li>
            <li class="active">Add Communication</li>
        </ol>
    </header>
    @if(session('status'))
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">x</button>
        <strong style="text-decoration: white;">{{ session('status') }}</strong>
    </div>
    @endif
    <!-- page title -->
    <header id="page-header">
        <h1>Add Communication</h1>
        <ol class="breadcrumb">
            <li><a href="#">Communication</a></li>
            <li class="active">Add Communication</li>
        </ol>
    </header>
    <!-- /page title -->
    <div id="content" class="padding-50">

        <div class="row">

            <div class="col">

                <!-- ------ -->
                <div class="panel panel-default">
                    <div class="panel-heading panel-heading-transparent">
                        <strong>Enter Communication Details</strong> <span style="font-size:10px">* Add communication
                            detail of those that are to receive alert notification</span>
                    </div>

                    <div class="panel-body">

                        <form class="" action="/save-communication" method="post" enctype="multipart/form-data"
                            data-success="Sent! Thank you!" data-toastr-position="top-right">
                            @csrf
                            <fieldset>
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-md-12 col-sm-12">
                                            <label> Select Client*</label>
                                            <select id="user" name="userid" class="form-control select2">
                                                <option value="{{Auth::user()->id}}" selected="selected">
                                                    {{Auth::user()->name}}</option>
                                                @foreach($getClient as $data)
                                                <option value='{{ $data->id }}'>{{ $data->name }}</option>
                                                @endforeach
                                            </select>

                                        </div>

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-md-12 col-sm-12">
                                            <label> Response Categories *</label>
                                            <select id="respondent" name="respondent" class="form-control select2">
                                                <option value="First responder" selected="selected">First responder
                                                </option>
                                                <option value="Second responder">Second responder</option>
                                                <option value="Third responder">Third responder</option>
                                                <option value="Fourth responder">Fourth responder</option>
                                                <option value="Fifth responder">Fifth responder</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12 col-sm-12" style="margin-top: 10px">
                                            <label>Email * <span style="font-size:10px">Enter offical email for
                                                    communication purpose</span></label>
                                            <input type="email" name="email" required value=""
                                                class="form-control required">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-md-12 col-sm-12">
                                            <label>Phone Number * <span style="font-size:10px">Enter offical phone
                                                    number for communication purpose</span></label>
                                            <input type="tel" id="phone_number" name="phone_number"
                                                onfocus="this.value=''" placeholder="234-1234567890"
                                                pattern="[2-4]{3}-[0-9]{10}" required class="form-control required">
                                        </div>

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 col-sm-12 text-right">
                                        <button type="submit" class="btn btn-primary">SAVE</button>
                                        <!-- <i class="fa fa-check"></i>  -->
                                        <!-- SAVE -->
                                        </a>
                                    </div>
                                </div>

                            </fieldset>



                        </form>

                    </div>

                </div>
                <!-- /----- -->

            </div>

        </div>

    </div>

    </div>

    </div>
</section>


@endsection