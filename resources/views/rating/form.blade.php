@extends('layouts.master')

@section('custom-css')
    <link rel="stylesheet" href="/css/lib/summernote/summernote.css"/>
    <link rel="stylesheet" href="/css/pages/editor.css">
@endsection

@section('custom-js')
    <script src="/js/lib/summernote/summernote.min.js"></script>
    <script src="/js/lib/moment/moment-with-locales.min.js"></script>
    <script src="/js/lib/daterangepicker/daterangepicker.js"></script>
    <script>
        $(document).ready(function() {
            $('textarea').summernote({height: "300px"});

            $('.date, .date input').daterangepicker({
                "singleDatePicker": true,
                "autoUpdateInput": true,
                "autoApply": true,
                "locale": {
                    "format": "DD.MM.YYYY",
                }
            });

            $('.rating input:checked').closest('label').addClass('selected');
            $('.rating input').change(function () {
                $('.rating .selected').removeClass('selected');
                $(this).closest('label').addClass('selected');
            });

            $('#company_id').select2({
                placeholder: 'Bitte wählen Sie eine Firma',
                minimumInputLength: 2,
                allowClear: true,
                ajax: {
                    url: "{{ route('company-search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            term: params.term
                        };
                    },
                    processResults: function (json)
                    {
                        var myResults = [];
                        $.each(json, function (name, id) {
                            myResults.push({
                                id: id,
                                text: name
                            });
                        });

                        return {
                            results: myResults
                        };
                    }
                }
            });

            var companyOldValue = $('#companyOldValue').data();
            if(companyOldValue) {
                $('#company_id').append('<option value="'+companyOldValue.id+'" selected="selected">'+companyOldValue.name+'</option>');
                $('#company_id').trigger('change');
            }
        });
    </script>
@endsection

@section('content')
    <div class="page-content" id="vue">
        <div class="container-fluid">

            {!! Form::model(isset($data) ? $data : null, ['route' => 'rating.store', 'autocomplete' => 'off', 'files' => true]) !!}
            {!! Form::hidden('id', isset($data) ? $data->id : null) !!}

            <header class="section-header">
                <div class="tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <h3>Ein Rating {!! isset($data) ? 'bearbeiten' : 'erstellen' !!}</h3>
                            <ol class="breadcrumb breadcrumb-simple">
                                <li><a href="{!! route('rating') !!}">Rating</a></li>
                                @if(isset($data))
                                    <li><a href="{!! route('rating.form', $data->id) !!}">{{ $data->title }}</a></li>
                                @endif
                                <li class="active">{!! isset($data) ? 'Bearbeiten' : 'Erstellen' !!}</li>
                            </ol>
                        </div>
                        <div class="tbl-cell tbl-cell-action">
                            <button type="submit" class="btn btn-success-outline btn-rounded pull-right m-l-1">
                                <i class="fa fa-save p-r-05"></i> Speichern
                            </button>
                            <a href="{{ route('rating') }}" class="btn btn-primary-outline btn-rounded pull-right">
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
                                <div class="col-md-8">
                                    {{ Form::Fselect('Firma', 'company_id') }}
                                    <input type="hidden" id="companyOldValue" data-id="{{ isset($data) && $data->company_id ? $data->company_id : null }}" data-name="{{ isset($data) && $data->company_id ? $data->company->name : null }}">
                                </div>
                                <div class="col-md-2">{{ Form::Fcheckbox('Genehmigt', 'approved', ['Genehmigt' => '1']) }}</div>
                                <div class="col-md-2">{{ Form::Fcheckbox('Sichtbar', 'is_visible', ['Sichtbar' => '1']) }}</div>
                            </div>
                        </div>
                        <div class="card-block">
                            <h5 class="with-border">Bewertung</h5>

                            <div class="row listing-rating">
                                <div class="col-md-8">{{ Form::Finput('Titel', 'title') }}</div>
                                <div class="col-md-4">{{ Form::Finput('Name', 'name') }}</div>
                                <div class="col-md-8">
                                    <label for="name" class="form-label semibold">Bewertung</label>
                                    <div class="rating" style="padding-top:7px">
                                        <label>
                                            {{ Form::radio('rating', 5, isset($data) && $data->rating == 5 ? 'checked' : null) }}
                                        </label>
                                        <label>
                                            {{ Form::radio('rating', 4, isset($data) && $data->rating == 4 ? 'checked' : null) }}
                                        </label>
                                        <label>
                                            {{ Form::radio('rating', 3, isset($data) && $data->rating == 3 ? 'checked' : null) }}
                                        </label>
                                        <label>
                                            {{ Form::radio('rating', 2, isset($data) && $data->rating == 2 ? 'checked' : null) }}
                                        </label>
                                        <label>
                                            {{ Form::radio('rating', 1, isset($data) && $data->rating == 1 ? 'checked' : null) }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-2">{{ Form::Finput('IP-Adresse', 'ip_address', null, ['readonly'=>'readonly']) }}</div>
                                <div class="col-md-2">{{ Form::Finput('Directory ID', 'directory_id', null, ['readonly'=>'readonly']) }}</div>
                                <div class="col-md-12 m-t-1">{{ Form::Ftextarea('Text', 'description') }}</div>
                            </div>
                        </div>

                    </section>
                </div>

            </div>

            {!! Form::close() !!}

        </div><!--.container-fluid-->
    </div><!--.page-content-->
@endsection