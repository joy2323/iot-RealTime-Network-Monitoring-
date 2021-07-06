@extends('layout.app')

@section('title', 'Unauthozied | Susej IoT')
@section('content')

<section id="middle">


    <!-- page title -->
    <header id="page-header">
        <h1>Unauthozied</h1>
        <ol class="breadcrumb">
            @if(Auth::user()->role =='Super admin')
            <li><a href="/admin">Home</a></li>
            @elseif(Auth::user()->role =='Client admin')
            <li><a href="/client">Home</a></li>
            @elseif(Auth::user()->role =='SiteUser admin')
            <li><a href="/dashboard">Home</a>Hme</li>
            @else
            <li><a href="/bu">Home</a></li>
            @endif

            <li class="active">Error 401</li>
        </ol>
    </header>
    <!-- /page title -->


    <div id="content" class="padding-20">

        <div class="panel panel-default" style="text-align:center">
            <div class="panel-body">
                <p class="lead">
                    <span class="e404">401</span>
                    Not authorized to view the request page.<br />
                    Use the browser Back button to navigate to the page you come from.
                </p>
                @if(Auth::user()->role =='Super admin')
                <a class="btn btn-success btn-lg" href="/admin">Home</a>
                @elseif(Auth::user()->role =='Client admin')
                <a class="btn btn-success btn-lg" href="/client">Home</a>
                @elseif(Auth::user()->role =='SiteUser admin')
                <a class="btn btn-success btn-lg" href="/dashboard">Home</a>
                @else
                <a class="btn btn-success btn-lg" href="/bu">Home</a>
                @endif


            </div>
        </div>

    </div>
</section>


@endsection