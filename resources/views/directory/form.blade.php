@extends('layouts.master')

@section('custom-css')
@endsection

@section('custom-js')
@endsection

@section('content')
    <div class="page-content" id="vue">
        <div class="container-fluid">

            {!! Form::model(isset($data) ? $data : null, ['route' => 'directory.store', 'autocomplete' => 'off', 'files' => true]) !!}
            {!! Form::hidden('id', isset($data) ? $data->id : null) !!}

            <header class="section-header">
                <div class="tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <h3>Ein Directory {!! isset($data) ? 'bearbeiten' : 'erstellen' !!}</h3>
                            <ol class="breadcrumb breadcrumb-simple">
                                <li><a href="{!! route('directory') !!}">Directory</a></li>
                                @if(isset($data))
                                    <li><a href="{!! route('directory.form', $data->id) !!}">{{ $data->title }}</a></li>
                                @endif
                                <li class="active">{!! isset($data) ? 'Bearbeiten' : 'Erstellen' !!}</li>
                            </ol>
                        </div>
                        <div class="tbl-cell tbl-cell-action">
                            <button type="submit" class="btn btn-success-outline btn-rounded pull-right m-l-1">
                                <i class="fa fa-save p-r-05"></i> Speichern
                            </button>
                            <a href="{{ route('directory') }}" class="btn btn-primary-outline btn-rounded pull-right">
                                <i class="fa fa-arrow-circle-o-left"></i> Zurück
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            @include('layouts.partials.messages')

            <div class="row">

                <div class="col-md-12 col-lg-8">
                    <section class="card">
                        <div class="card-block">

                            <h5 class="with-border">Stammdaten</h5>

                            <div class="row">
                                <div class="col-md-9">{{ Form::Finput('Name', 'name') }}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-9">{{ Form::Finput('API Token', 'api_token', (isset($data) ? $data->api_token : str_random(60))) }}</div>
                            </div>

                        </div>
                    </section>
                </div>



            </div>

            {!! Form::close() !!}

        </div><!--.container-fluid-->
    </div><!--.page-content-->
@endsection