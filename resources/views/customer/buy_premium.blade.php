@extends('layouts.master')

@section('custom-css')
    <link rel="stylesheet" href="/css/pages/others.css">
@endsection


@section('content')

    <div class="page-content">
        <div class="container-fluid">

            <div class="box-typical box-typical-full-height">
                <div class="add-customers-screen tbl">
                    <div class="add-customers-screen-in">
                        <div class="add-customers-screen-user" style="background-color: gold; color:black">
                            <i class="fa fa-trophy"></i>
                        </div>
                        <h2>Premium</h2>
                        <p class="lead color-blue-grey-lighter">Diese Funktion steht Ihnen nur mit einem Premium account zur Verfügung.</p>
                        <a href="#" class="btn btn-rounded btn-premium"><i class="fa fa-trophy m-r-1"></i>Premium erwerben</a>
                    </div>
                </div>
            </div><!--.box-typical-->

        </div>
    </div>
@stop
