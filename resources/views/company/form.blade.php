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
    <script src="/js/pages/company.edit.min.js"></script>
    <script src="/js/pages/map.autocomplete.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.places.key') }}&libraries=places&hl=de&callback=initAutocomplete" async defer></script>
@endsection

@section('content')
    <div class="page-content" id="vue">
        <div class="container-fluid">

            {!! Form::model(isset($data) ? $data : null, ['route' => 'company.store', 'autocomplete' => 'off', 'files' => true]) !!}
            {!! Form::hidden('id', isset($data) ? $data->id : null) !!}

            <header class="section-header">
                <div class="tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <h3>Eine Firma {!! isset($data) ? 'bearbeiten' : 'erstellen' !!}</h3>
                            <ol class="breadcrumb breadcrumb-simple">
                                <li><a href="{!! route('company') !!}">Firma</a></li>
                                @if(isset($data))
                                    <li><a href="{!! route('company.form', $data->id) !!}">{{ $data->name }}</a></li>
                                @endif
                                <li class="active">{!! isset($data) ? 'Bearbeiten' : 'Erstellen' !!}</li>
                            </ol>
                        </div>
                        <div class="tbl-cell tbl-cell-action">
                            <button type="submit" class="btn btn-success-outline btn-rounded pull-right m-l-1">
                                <i class="fa fa-save p-r-05"></i> Speichern
                            </button>
                            <a href="{{ route('company') }}" class="btn btn-primary-outline btn-rounded pull-right">
                                <i class="fa fa-arrow-circle-o-left"></i> Zurück
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            @include('layouts.partials.messages')

            <div class="row">
                <div class="col-md-12 col-lg-8">

                    <section class="tabs-section">
                        <div class="tabs-section-nav">
                            <div class="tbl">
                                <ul class="nav" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#tabs-1-tab-1" role="tab" data-toggle="tab">
                                            <span class="nav-link-in">
                                                Firma
                                            </span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#tabs-1-tab-2" role="tab" data-toggle="tab">
                                            <span class="nav-link-in">
                                                Deals
                                            </span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#tabs-1-tab-3" role="tab" data-toggle="tab">
                                            <span class="nav-link-in">
                                                Events
                                            </span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#tabs-1-tab-4" role="tab" data-toggle="tab">
                                            <span class="nav-link-in">
                                                Ratings
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div><!--.tabs-section-nav-->

                        <div class="tab-content">

                            <div role="tabpanel" class="tab-pane fade in active" id="tabs-1-tab-1">
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
                                    <div class="row">
                                        <div class="col-md-12">
                                        {{ Form::Fselect('Account', 'user_id') }}
                                        <input type="hidden" id="userOldValue" data-id="{{ isset($data) && $data->user_id ? $data->user_id : null }}"
                                               data-name="{{ isset($data) && $data->user_id ? $data->owner->email : null }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="card-block">
                                    <h5 class="with-border">Listingdaten</h5>

                                    <div class="row">
                                        <div class="col-md-4">{{ Form::Fselect('Level', 'listing_level', [''=>'', 'basic' => 'Basis', 'premium' => 'Premium']) }}</div>
                                        <div class="col-md-4">{{ Form::Fdate('Gültig bis', 'listing_valid_until', isset($data) && $data->listing_valid_until ? $data->listing_valid_until->format('d.m.y') : \Carbon\Carbon::now()->addYears(3)->format('d.m.Y')) }}</div>
                                        <div class="col-md-4">{{ Form::Fcheckbox('Aktiviert', 'listing_status', ['Aktiviert' => '1']) }}</div>
                                    </div>
                                </div>
                                <div class="card-block">
                                    <h5 class="with-border">Anschrift</h5>

                                    @include('form.partials.location', ['data' => $data])
                                </div>
                                <div class="card-block">
                                    <h5 class="with-border">Kontaktdaten</h5>

                                    <div class="row">
                                        <div class="col-md-6">{{ Form::Finput('Email', 'email') }}</div>
                                        <div class="col-md-6">{{ Form::Finput('Internetadresse', 'www') }}</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">{{ Form::Finput('Telefon', 'phone') }}</div>
                                        <div class="col-md-4">{{ Form::Finput('Mobil', 'mobile') }}</div>
                                        <div class="col-md-4">{{ Form::Finput('Fax', 'fax') }}</div>
                                    </div>
                                </div>
                                <div class="card-block">
                                    <h5 class="with-border">Beschreibung</h5>

                                    <div class="row">
                                        <div class="col-md-12">{{ Form::Finput('Kurzbeschreibung', 'summary') }}</div>
                                        <div class="col-md-12">{{ Form::Fselectmultiple('Suchkeywörter', 'keywords[]', isset($data) && $data->keywords ? $data->keywords->pluck('keyword', 'keyword')->toArray() : [], null) }}</div>
                                        <div class="col-md-12">{{ Form::Ftextarea('Beschreibung', 'description') }}</div>
                                    </div>
                                </div>
                                <div class="card-block">
                                    <h5 class="with-border">Öffnungszeiten</h5>

                                    <div class="row m-b-2">
                                        <div class="col-md-1"><strong>Wochtentag</strong></div>
                                        <div class="col-md-2 text-right"><i class="fa fa-lock"></i></div>
                                        <div class="col-md-2 text-center">Von</div>
                                        <div class="col-md-2 text-center">Bis</div>
                                        <div class="col-md-1"></div>
                                        <div class="col-md-2 text-center">Von</div>
                                        <div class="col-md-2 text-center">Bis</div>
                                    </div>

                                    <div id="opening-times">
                                        @foreach(\App\Models\CompanyOpeningTimes::WEEKDAYS as $key => $weekday)
                                            <div class="row">
                                                <label for="feldanzahl" class="col-md-1 control-label" style="padding-top:8px;">{{ $weekday }}</label>

                                                <div class="col-md-2">
                                                    <div class="pull-right" style="padding-top:8px;">
                                                        {!! Form::checkbox($key.'[day_closed]', '1',
                                                            old($key.'[day_closed]') ? 'true' : (isset($opening_times[$key]) && $opening_times[$key]['day_closed'] ? 'true' : null))
                                                        !!}
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="input-group">
                                                    {!! Form::Finput(null, $key.'[open_time]', old($key.'[open_time]') ? :
                                                        (isset($opening_times[$key]) && $opening_times[$key]['open_time'] ? $opening_times[$key]['open_time'] :
                                                            (isset($opening_times[$key]) && $opening_times[$key]['day_closed'] ? null : '09:00') ),
                                                        isset($opening_times[$key]) && $opening_times[$key]['day_closed'] ? ['readonly'=>'readonly'] : [])
                                                    !!}
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="input-group">
                                                        {!! Form::Finput(null, $key.'[close_time]', old($key.'[close_time]') ? :
                                                            (isset($opening_times[$key]) && $opening_times[$key]['close_time'] ?  $opening_times[$key]['close_time'] :
                                                                (isset($opening_times[$key]) && $opening_times[$key]['day_closed'] ? null : '18:00') ),
                                                            isset($opening_times[$key]) && $opening_times[$key]['day_closed'] ? ['readonly'=>'readonly'] : [])
                                                        !!}
                                                    </div>
                                                </div>

                                                <div class="col-md-1">
                                                    <label class="field">
                                                        <p class="form-control-static text-center">und</p>
                                                    </label>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="input-group">
                                                        {!! Form::Finput(null, $key.'[open_time_additional]', old($key.'[open_time_additional]') ? :
                                                            (isset($opening_times[$key]) && $opening_times[$key]['open_time_additional'] ?
                                                                isset($opening_times[$key]) && $opening_times[$key]['open_time_additional'] : null),
                                                            isset($opening_times[$key]) && $opening_times[$key]['day_closed'] ? ['readonly'=>'readonly'] : [])
                                                        !!}
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="input-group">
                                                        {!! Form::Finput(null, $key.'[close_time_additional]', old($key.'[close_time_additional]') ? :
                                                            (isset($opening_times[$key]) && $opening_times[$key]['close_time_additional'] ?
                                                                isset($opening_times[$key]) && $opening_times[$key]['close_time_additional'] : null),
                                                            isset($opening_times[$key]) &&$opening_times[$key]['day_closed'] ? ['readonly'=>'readonly'] : [])
                                                        !!}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                </div>
                            </div><!--.tab-pane-->

                            @if(isset($data))
                                <div role="tabpanel" class="tab-pane fade" id="tabs-1-tab-2"> @include('company.partials.deals') </div><!--.tab-pane-->
                                <div role="tabpanel" class="tab-pane fade" id="tabs-1-tab-3"> @include('company.partials.events') </div><!--.tab-pane-->
                                <div role="tabpanel" class="tab-pane fade" id="tabs-1-tab-4"> @include('company.partials.ratings') </div><!--.tab-pane-->
                            @endif

                        </div><!--.tab-content-->
                    </section><!--.tabs-section-->
                </div>

                <div class="col-md-12 col-lg-4">
                    <section class="card">
                        <div class="card-block">
                            <h5 class="with-border">Titelbild</h5>
                            <div class="row">
                                <div class="col-md-12">
                                   {{ Form::Ffile(null, 'image[]', ['class' => 'image-uploader', 'data-fileuploader-files' => isset($data) ? $data->imageJson('image') : null, 'data-fileuploader-limit' => 1, 'accept' => '.jpg, .jpeg, .png .gif, .bmp']) }}
                                </div>
                            </div>
                        </div>
                        <div class="card-block">
                            <h5 class="with-border">Bildergalerie <small class="text-muted">( max. 5 Bilder )</small></h5>
                            <div class="row">
                                <div class="col-md-12">
                                    {{ Form::Ffile(null, 'image_gallery[]', ['class' => 'gallery-uploader',
                                        'data-fileuploader-files' => isset($data) ? $data->imageJson('gallery') : null,
                                        'data-fileuploader-limit' => 5, 'accept' => '.jpg, .jpeg, .png .gif, .bmp']) }}
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
                    <section class="card">
                        <div class="card-block">
                            <h5 class="with-border">Dokumente <small class="text-muted">( max. 5 pdf Dateien )</small></h5>
                            <div class="row">
                                <div class="col-md-12">
                                    {{ Form::Ffile(null, 'pdf_files[]', ['class' => 'file-uploader', 'data-fileuploader-files' => isset($files) ? $files : null, 'data-fileuploader-limit' => 5, 'accept' => '.pdf']) }}
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

            </div>

            {!! Form::close() !!}

        </div><!--.container-fluid-->
    </div><!--.page-content-->
@endsection