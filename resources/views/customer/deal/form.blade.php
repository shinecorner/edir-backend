@extends('layouts.master')

@section('custom-css')
    <link rel="stylesheet" href="/css/lib/summernote/summernote.css"/>
    <link rel="stylesheet" href="/css/lib/fileuploader/jquery.fileuploader.css">
    <link rel="stylesheet" href="/css/pages/editor.css">
@endsection

@section('custom-js')
    <script src="/js/lib/summernote/summernote.min.js"></script>
    <script src="/js/lib/moment/moment-with-locales.min.js"></script>
    <script src="/js/lib/daterangepicker/daterangepicker.js"></script>
    <script src="/js/lib/fileuploader/jquery.fileuploader.min.js"></script>
    <script>
        $(document).ready(function() {
            $('textarea').summernote({height: "300px"});

            $('.date, .date input').daterangepicker({
                "singleDatePicker": true,
                "autoUpdateInput": true,
                "autoApply": true,
                "locale": {
                    "format": "DD.MM.YYYY"
                }
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
    <div class="page-content">
        <div class="container-fluid">

            {!! Form::model(isset($data) ? $data : null, ['route' => 'customer.deal.store', 'autocomplete' => 'off', 'files' => true]) !!}
            {!! Form::hidden('id', isset($data) ? $data->id : null) !!}

            <header class="section-header">
                <div class="tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <h3>Einen Deal {!! isset($data) ? 'bearbeiten' : 'erstellen' !!}</h3>
                            <ol class="breadcrumb breadcrumb-simple">
                                <li><a href="{!! route('customer.deal') !!}">Deal</a></li>
                                @if(isset($data))
                                    <li><a href="{!! route('customer.deal.form', $data->id) !!}">{{ $data->title }}</a></li>
                                @endif
                                <li class="active">{!! isset($data) ? 'Bearbeiten' : 'Erstellen' !!}</li>
                            </ol>
                        </div>
                        <div class="tbl-cell tbl-cell-action">
                            <button type="submit" class="btn btn-success-outline btn-rounded pull-right m-l-1">
                                <i class="fa fa-save p-r-05"></i> Speichern
                            </button>
                            <a href="{{ route('customer.deal') }}" class="btn btn-primary-outline btn-rounded pull-right">
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
                                    {{ Form::Fselect('Firma', 'company_id', $user_companies, isset($data) && $data->company_id ? $data->company_id : null, ['placeholder' => '']) }}                                </div>
                                <div class="col-md-6">{{ Form::Fselect('Kategorie', 'category_deal_id', $categories, isset($data) && $data->category_id ? $data->category_id : null, ['placeholder' => 'Kategorie']) }}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">{{ Form::Fselect('Rabattyp', 'discount_type', [''=>'', 'fixed'=>'Festpreis', 'percent'=>'Prozentual']) }}</div>
                                <div class="col-md-3">{{ Form::Finput('Rabattwert', 'discount_value') }}</div>
                                <div class="col-md-3">{{ Form::Fdate('Gültig von', 'date_start', isset($data) && $data->date_start ? $data->date_start->format('d.m.y') : null) }}</div>
                                <div class="col-md-3">{{ Form::Fdate('Gültig bis', 'date_end', isset($data) && $data->date_end ? $data->date_end->format('d.m.y') : null) }}</div>
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

                        <div class="card-block">

                            <h5 class="with-border">Bedingungen</h5>

                            <div class="row">
                                <div class="col-md-12">{{ Form::Ftextarea('Bedingungen', 'conditions') }}</div>
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