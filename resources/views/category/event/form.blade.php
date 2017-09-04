@extends('layouts.master')

@section('custom-css')
    <link rel="stylesheet" href="/css/lib/summernote/summernote.css"/>
    <link rel="stylesheet" href="/css/lib/fileuploader/jquery.fileuploader.css">
    <link rel="stylesheet" href="/css/pages/editor.css">
@endsection

@section('custom-js')
	<script src="/js/lib/summernote/summernote.min.js"></script>
    <script src="/js/lib/fileuploader/jquery.fileuploader.min.js"></script>
	<script>
		$(document).ready(function() {
			$('textarea').summernote({height: "300px"});

			$('.select2').select2({
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

            {!! Form::model(isset($data) ? $data : null, ['route' => 'category.event.store', 'autocomplete' => 'off', 'files' => true]) !!}
            {!! Form::hidden('id', isset($data) ? $data->id : null) !!}

            <header class="section-header">
                <div class="tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <h3>Eine Eventkategorie {!! isset($data) ? 'bearbeiten' : 'erstellen' !!}</h3>
                            <ol class="breadcrumb breadcrumb-simple">
                                <li><a href="{!! route('category.event') !!}">Eventkategorien</a></li>
                                @if(isset($data))
                                    <li><a href="{!! route('category.event.form', $data->id) !!}">{{ $data->name }}</a></li>
                                @endif
                                <li class="active">{!! isset($data) ? 'Bearbeiten' : 'Erstellen' !!}</li>
                            </ol>
                        </div>
                        <div class="tbl-cell tbl-cell-action">
                            <button type="submit" class="btn btn-success-outline btn-rounded pull-right m-l-1">
                                <i class="fa fa-save p-r-05"></i> Speichern
                            </button>
                            <a href="{{ route('category.event') }}" class="btn btn-primary-outline btn-rounded pull-right">
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
                    </section>
                </div>

            </div>

            {!! Form::close() !!}

        </div><!--.container-fluid-->
    </div><!--.page-content-->
@endsection