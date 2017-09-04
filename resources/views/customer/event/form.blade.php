@extends('layouts.master')

@section('custom-css')
    <link rel="stylesheet" href="/css/lib/summernote/summernote.css"/>
    <link rel="stylesheet" href="/css/lib/fileuploader/jquery.fileuploader.css">
    <link rel="stylesheet" href="/css/pages/editor.css">
    <link rel="stylesheet" href="/css/lib/clockpicker/bootstrap-clockpicker.min.css">
@endsection

@section('custom-js')
    <script src="/js/lib/summernote/summernote.min.js"></script>
    <script src="/js/lib/moment/moment-with-locales.min.js"></script>
    <script src="/js/lib/daterangepicker/daterangepicker.js"></script>
    <script src="/js/lib/clockpicker/bootstrap-clockpicker.min.js"></script>
    <script src="/js/lib/fileuploader/jquery.fileuploader.min.js"></script>
    <script>
        $(document).ready(function() {
            $('textarea').summernote({height: "300px"});

            $('.date, .date input').daterangepicker({
                "singleDatePicker": true,
                "autoUpdateInput": true,
                "locale": {
                    "format": "DD.MM.YYYY"
                }
            });

            $('.clockpicker input').clockpicker({
                autoclose: true,
                donetext: 'Done',
                'default': 'now'
            });

            $('#company_id').select2({
                placeholder: 'Bitte wählen Sie eine Firma',
//                minimumInputLength: 2,
                allowClear: true,
                language: "de",
            });

            $('select[name=keywords\\[\\]]').select2({
                tags: true,
                multiple: true,
                maximumSelectionLength: 5,
                language: "de"
            });
        });
    </script>
@endsection

@section('content')
    <div class="page-content" id="vue">
        <div class="container-fluid">
            {!! Form::model(isset($data) ? $data : null, ['route' => 'customer.event.store', 'autocomplete' => 'off', 'files' => true]) !!}
            {!! Form::hidden('id', isset($data) ? $data->id : null) !!}

            <header class="section-header">
                <div class="tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <h3>Ein Event {!! isset($data) ? 'bearbeiten' : 'erstellen' !!}</h3>
                            <ol class="breadcrumb breadcrumb-simple">
                                <li><a href="{!! route('customer.event') !!}">Event</a></li>
                                @if(isset($data))
                                    <li><a href="{!! route('customer.event.form', $data->id) !!}">{{ $data->title }}</a></li>
                                @endif
                                <li class="active">{!! isset($data) ? 'Bearbeiten' : 'Erstellen' !!}</li>
                            </ol>
                        </div>
                        <div class="tbl-cell tbl-cell-action">
                            <button type="submit" class="btn btn-success-outline btn-rounded pull-right m-l-1">
                                <i class="fa fa-save p-r-05"></i> Speichern
                            </button>
                            <a href="{{ route('customer.event') }}" class="btn btn-primary-outline btn-rounded pull-right">
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
                                <div class="col-md-12">{{ Form::Finput('Titel', 'title') }}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::Fselect('Firma', 'company_id', $user_companies, isset($data) && $data->company_id ? $data->company_id : null, ['placeholder' => '']) }}
                                </div>
                                <div class="col-md-6">{{ Form::Fselect('Kategorie', 'category_event_id', $categories, isset($data) && $data->category_id ? $data->category_id : null, ['placeholder' => 'Kategorie']) }}</div>
                             </div>

                            <div class="row">
                                <div class="col-md-3">{{ Form::Fdate('Gültig bis', 'valid_until', isset($data) && $data->valid_until ? $data->valid_until->format('d.m.y') : null) }}</div>
                                <div class="col-md-3">{{ Form::Finput('Coupon code', 'discount_coupon') }}</div>
                                <div class="col-md-3">{{ Form::Fcheckbox('Sichtbar', 'active', ['Sichtbar' => '1']) }}</div>
                            </div>
                        </div>

                        <div class="card-block">
                            <h5 class="with-border">Uhrzeit & Ort</h5>

                            <div class="row">
                                <div class="col-md-3">{{ Form::Fdate('Datum von', 'date_start', isset($data) && $data->date_start ? $data->date_start->format('d.m.y') : null) }}</div>
                                <div class="col-md-3">{{ Form::Ftime('Uhrzeit von', 'time_start', isset($data) && $data->time_start ? $data->time_start : null) }}</div>
                                <div class="col-md-3">{{ Form::Fdate('Datum bis', 'date_end', isset($data) && $data->date_end ? $data->date_end->format('d.m.y') : null) }}</div>
                                <div class="col-md-3">{{ Form::Ftime('Uhrzeit bis', 'time_end', isset($data) && $data->time_end ? $data->time_end : null) }}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">{{ Form::Finput('Adresse', 'address') }}</div>
                                <div class="col-md-3">{{ Form::Finput('PLZ', 'address-plz', null, ['readonly'=>'readonly']) }}</div>
                                <div class="col-md-3">
                                    @include('form.partials.location', ['data' => $data])
                                </div>
                            </div>
                        </div>

                        <div class="card-block">
                            <h5 class="with-border">Beschreibung</h5>

                            <div class="row">
                                <div class="col-md-12">{{ Form::Finput('Zusammenfassung', 'summary') }}</div>
                                <div class="col-md-12">{{ Form::Fselectmultiple('Suchkeywörter', 'keywords[]', isset($data) && $data->keywords ? $data->keywords->pluck('keyword', 'keyword')->toArray() : [], null) }}</div>
                                <div class="col-md-12">{{ Form::Ftextarea('Beschreibung', 'description') }}</div>
                            </div>
                        </div>

                    </section>
                </div>

                <div class="col-md-12 col-lg-4">
                    <section class="card">
                        <div class="card-block">
                            <h5 class="with-border">Titelbild</h5>
                            <div class="row">
                                <div class="col-md-12">
                                   {{ Form::Ffile(null, 'image[]', ['class' => 'image-uploader', 'data-fileuploader-files' => isset($data) ? $data->imageJson('image') : null, 'data-fileuploader-limit' => 1]) }}
                                </div>
                            </div>
                        </div>

                        <div class="card-block">
                            <h5 class="with-border">Bildergalerie <small class="text-muted">( max. 5 Bilder )</small></h5>
                            <div class="row">
                                <div class="col-md-12">
                                    {{ Form::Ffile(null, 'image_gallery[]', ['class' => 'gallery-uploader', 'data-fileuploader-files' => isset($data) ? $data->imageJson('gallery') : null, 'data-fileuploader-limit' => 5]) }}
                                </div>
                            </div>
                        </div>

                        <div class="card-block">
                            <h5 class="with-border">Video</h5>

                            <div class="row">
                                <div class="col-md-12">{{ Form::Finput('Video Link', 'video_url') }}</div>
                            </div>
                        </div>
                    </section>
                </div>

            </div>

            {!! Form::close() !!}

        </div><!--.container-fluid-->
    </div><!--.page-content-->
@endsection