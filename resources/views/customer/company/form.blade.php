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
    <script src="/js/pages/backend/company.edit.min.js"></script>
@endsection

@section('content')
    <div class="page-content" id="vue">
        <div class="container-fluid">

            {!! Form::model(isset($data) ? $data : null, ['route' => 'customer.company.store', 'autocomplete' => 'off', 'files' => true]) !!}
            {!! Form::hidden('id', isset($data) ? $data->id : null) !!}
            {!! Form::hidden('num_categories', isset($data) && $data->listing_level == 'premium' ? 5 : 1) !!}

            <header class="section-header">
                <div class="tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <h3>Eine Firma {!! isset($data) ? 'bearbeiten' : 'erstellen' !!}</h3>
                            <ol class="breadcrumb breadcrumb-simple">
                                <li>Firma</li>
                                @if(isset($data))
                                    <li><a href="{!! route('customer.company.form', $data->id) !!}">{{ $data->name }}</a></li>
                                @endif
                                <li class="active">{!! isset($data) ? 'Bearbeiten' : 'Erstellen' !!}</li>
                            </ol>
                        </div>
                        <div class="tbl-cell tbl-cell-action">
                            <button type="submit" class="btn btn-success-outline btn-rounded pull-right m-l-1">
                                <i class="fa fa-save p-r-05"></i> Speichern
                            </button>
                            <a href="{{ route('customer.company') }}" class="btn btn-primary-outline btn-rounded pull-right">
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
                                <div class="col-md-12">{{ Form::Finput('Name', 'name') }}</div>
                            </div>
                            <div class="row">
                                    <div class="col-md-12">
                                        {{ Form::Fselectmultiple('Kategorien', 'category_secondary_ids[]', isset($data) && $data->categories ? $data->categories->pluck('name', 'id') : [], null) }}
                                    </div>
                                @if(isset($data))
                                    @foreach($data->categories as $category)
                                    <input type="hidden" class="categorySecondaryOldValues" data-id="{{ $category->id }}" data-name="{{ $category->name }}">
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="card-block">
                            <h5 class="with-border">Anschrift</h5>

                            <div class="row">
                                <div class="col-md-6">{{ Form::Finput('Adresse', 'address') }}</div>
                                <div class="col-md-6">{{ Form::Finput('Adress-Zusatz', 'address_additional') }}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">{{ Form::Finput('PLZ', 'address-plz', null, ['readonly'=>'readonly']) }}</div>
                                <div class="col-md-4">
                                    @include('form.partials.location', ['data' => $data])
                                </div>
                                <div class="col-md-4">{{ Form::Finput('Landkreis', 'address-landkreis', null, ['readonly'=>'readonly']) }}</div>
                            </div>
                        </div>
                        <div class="card-block">
                            <h5 class="with-border">
                                Kontaktdaten
                                @if(isset($data) && $data->listing_level != 'premium')
                                    <span class="premium"><i class="fa fa-trophy m-r-1"></i>Premium</span>
                                @endif
                            </h5>

                            <div class="row">
                                <div class="col-md-6">
                                        {{ Form::Finput('Email', 'email', null, isset($data) && $data->listing_level != 'premium' ? ['readonly'=>'readonly'] : []) }}
                                </div>
                                <div class="col-md-6">
                                        {{ Form::Finput('Internetadresse', 'www', null, isset($data) && $data->listing_level != 'premium' ? ['readonly'=>'readonly'] : []) }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                        {{ Form::Finput('Telefon', 'phone', null, isset($data) && $data->listing_level != 'premium' ? ['readonly'=>'readonly'] : []) }}
                                </div>
                                <div class="col-md-4">
                                        {{ Form::Finput('Mobil', 'mobile', null, isset($data) && $data->listing_level != 'premium' ? ['readonly'=>'readonly'] : []) }}
                                </div>
                                <div class="col-md-4">
                                        {{ Form::Finput('Fax', 'fax', null, isset($data) && $data->listing_level != 'premium' ? ['readonly'=>'readonly'] : []) }}
                                </div>
                            </div>
                        </div>
                            <div class="card-block">
                                <h5 class="with-border">
                                    Beschreibung
                                    @if(isset($data) && $data->listing_level != 'premium')
                                        <span class="premium"><i class="fa fa-trophy m-r-1"></i>Premium</span>
                                    @endif
                                </h5>

                                <div class="row">
                                    <div class="col-md-12">{{ Form::Finput('Kurzbeschreibung', 'summary', null, isset($data) && $data->listing_level != 'premium' ? ['readonly'=>'readonly'] : []) }}</div>
                                    <div class="col-md-12">
                                        {{ Form::Fselectmultiple('Suchkeywörter', 'keywords[]',
                                            isset($data) && $data->keywords ? $data->keywords->pluck('keyword', 'keyword')->toArray() : [], null,
                                            isset($data) && $data->listing_level != 'premium' ? ['disabled'] : []) }}
                                    </div>
                                    <div class="col-md-12">{{ Form::Ftextarea('Beschreibung', 'description', null, isset($data) && $data->listing_level != 'premium' ? ['readonly'=>'readonly'] : []) }}</div>
                                </div>
                            </div>

                    </section>
                </div>

                <div class="col-md-12 col-lg-4">
                    <section class="card">
                        <div class="card-block">
                            <h5 class="with-border">
                                Titelbild
                                @if(isset($data) && $data->listing_level != 'premium')
                                    <span class="premium"><i class="fa fa-trophy m-r-1"></i>Premium</span>
                                @endif
                            </h5>
                            <div class="row">
                                <div class="col-md-12">
                                    {{ Form::Ffile(null, 'image[]', ['class' => 'image-uploader',
                                    'data-fileuploader-files' => isset($data) ? $data->imageJson('image') : null,
                                    'data-fileuploader-limit' => 1, 'accept' => '.jpg, .jpeg, .png .gif, .bmp',
                                    isset($data) && $data->listing_level != 'premium' ? 'readonly' : null]) }}
                                </div>
                            </div>
                        </div>
                            <div class="card-block">
                                <h5 class="with-border">
                                    Bildergalerie <small class="text-muted">( max. 5 Bilder )</small>
                                    @if(isset($data) && $data->listing_level != 'premium')
                                        <span class="premium"><i class="fa fa-trophy m-r-1"></i>Premium</span>
                                    @endif
                                </h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        {{ Form::Ffile(null, 'image_gallery[]', ['class' => 'gallery-uploader',
                                            'data-fileuploader-files' => isset($data) ? $data->imageJson('gallery') : null,
                                            'data-fileuploader-limit' => 5, 'accept' => '.jpg, .jpeg, .png .gif, .bmp',
                                         isset($data) && $data->listing_level != 'premium' ? 'readonly' : null]) }}
                                    </div>
                                </div>
                            </div>
                            <div class="card-block">
                                <h5 class="with-border">
                                    Video
                                    @if(isset($data) && $data->listing_level != 'premium')
                                        <span class="premium"><i class="fa fa-trophy m-r-1"></i>Premium</span>
                                    @endif
                                </h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        {{ Form::Finput('Video Link', 'video_url', null, isset($data) && $data->listing_level != 'premium' ? ['readonly'=>'readonly'] : []) }}
                                    </div>
                                </div>
                            </div>
                    </section>
                    <section class="card">
                        <div class="card-block">
                            <h5 class="with-border">
                                Dokumente <small class="text-muted">( max. 5 pdf Dateien )</small>
                                @if(isset($data) && $data->listing_level != 'premium')
                                    <span class="premium"><i class="fa fa-trophy m-r-1"></i>Premium</span>
                                @endif
                            </h5>
                            <div class="row">
                                <div class="col-md-12">
                                    {{ Form::Ffile(null, 'pdf_files[]', ['class' => 'file-uploader',
                                     'data-fileuploader-files' => isset($files) ? $files : null,
                                     'data-fileuploader-limit' => 5, 'accept' => '.pdf',
                                    isset($data) && $data->listing_level != 'premium' ? 'readonly' : null]) }}
                                </div>
                            </div>
                        </div>
                    </section>
                    @if(!isset($data) or (isset($data) && $data->listing_level != 'premium'))
                        <section class="card">
                            <div class="card-block">
                                <h5 class="with-border">Premium Info</h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <a href="#" class="btn btn-rounded btn-premium"><i class="fa fa-trophy m-r-1"></i>Premium erwerben</a>
                                    </div>
                                </div>
                            </div>
                        </section>
                    @endif

                </div>

            </div>

            {!! Form::close() !!}

        </div><!--.container-fluid-->
    </div><!--.page-content-->
@endsection