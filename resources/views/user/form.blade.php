@extends('layouts.master')

@section('custom-js')
	<script src="/js/lib/hide-show-password/bootstrap-show-password.min.js"></script>
	<script src="/js/lib/hide-show-password/bootstrap-show-password-init.js"></script>
@endsection

@section('content')
    <div class="page-content" id="vue">
        <div class="container-fluid">

            {!! Form::model(isset($data) ? $data : null, ['route' => 'user.store', 'autocomplete' => 'off']) !!}
            {!! Form::hidden('id', isset($data) ? $data->id : null) !!}

            <header class="section-header">
                <div class="tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <h3>Einen Benutzer {!! isset($data) ? 'bearbeiten' : 'erstellen' !!}</h3>
                            <ol class="breadcrumb breadcrumb-simple">
                                <li><a href="{!! route('user') !!}">Benutzer</a></li>
                                @if(isset($data))
                                    <li><a href="{!! route('user.form', $data->id) !!}">{{ $data->name }}</a></li>
                                @endif
                                <li class="active">{!! isset($data) ? 'Bearbeiten' : 'Erstellen' !!}</li>
                            </ol>
                        </div>
                        <div class="tbl-cell tbl-cell-action">
                            <button type="submit" class="btn btn-success-outline btn-rounded pull-right m-l-1">
                                <i class="fa fa-save p-r-05"></i> Speichern
                            </button>
                            <a href="{{ route('user') }}" class="btn btn-primary-outline btn-rounded pull-right">
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
                                <div class="col-md-3">{{ Form::Fselect('Anrede', 'gender', [''=>'', 'Herr'=>'Herr', 'Frau'=>'Frau']) }}</div>
                                <div class="col-md-3">{{ Form::Finput('Titel', 'title') }}</div>
                                <div class="col-md-6">{{ Form::Fselect('Gruppe', 'role', [''=>'', 'admin'=>'Administrator', 'employee'=>'Mitarbeiter', 'customer'=>'Kunde']) }}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">{{ Form::Finput('Vorname', 'first_name') }}</div>
                                <div class="col-md-6">{{ Form::Finput('Nachname', 'last_name') }}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">{{ Form::Finput('E-mailadresse', 'email') }}</div>
                                <div class="col-md-6">{{ Form::Fpassword('Passwort', 'password') }}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">{{ Form::Finput('Telefonnummer', 'phone_number') }}</div>
                                <div class="col-md-6">{{ Form::Finput('Kundennummer', 'client_number', null, ['readonly'=>'readonly']) }}</div>
                            </div>

                        </div>
                    </section>
                </div>

                <div class="col-md-12 col-lg-4">
                    <section class="card">
                        <div class="card-block">

                            <h5 class="with-border">Informationen</h5>

                            <article class="faq-item">
                                <div class="faq-item-circle">?</div>
                                <p>
                                    <strong>Passwort</strong>
                                    <br>
                                    pastry biscuit liquorice candy canes apple pie icing
                                    chupa chups. Bonbon bear claw sweet marshmallow ice cream powder icing.
                                </p>
                            </article>

                            <hr class="dashed">

                            <article class="faq-item">
                                <div class="faq-item-circle">?</div>
                                <p>
                                    <strong>Gruppe</strong>
                                    <br>
                                    Brownie pastry biscuit liquorice candy canes apple pie
                                    icing chupa chups. Bonbon bear claw sweet marshmallow ice cream powder icing.
                                </p>
                            </article>

                        </div>
                    </section>
                </div>

            </div>

            {!! Form::close() !!}

        </div><!--.container-fluid-->
    </div><!--.page-content-->
@endsection