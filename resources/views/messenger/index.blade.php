@extends('layouts.master')

@section('custom-css')
    <link rel="stylesheet" href="/css/pages/chat.css"/>
@endsection

@section('custom-js-code')
    <script>
        $(document).ready(function() {
            $('#user_id').select2({
                placeholder: 'Bitte wählen Sie einen Benutzer',
                minimumInputLength: 2,
                maximumSelectionLength: 1,
                allowClear: true,
                language: "de",
                ajax: {
                    url: '/kunden-suche',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            term: params.term
                        };
                    },
                    processResults: function (json) {
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

            var userOldValue = $('#userOldValue').data();
            if (userOldValue.id) {
                $('#user_id').append('<option value="' + userOldValue.id + '" selected="selected">' + userOldValue.name + '</option>');
                $('#user_id').trigger('change');
            }
        });
    </script>
@endsection

@section('content')

    <div class="page-content">
        <div class="container-fluid">

            @include('layouts.partials.messages')

            <div class="box-typical chat-container">
                <section class="chat-list">
                    <div class="chat-list-search">
                        <strong>Themen:</strong>
                        <div class="pull-right">
                            <span class="label label-primary"><i class="fa fa-unlock"></i> {{ $threads_open_count }}</span>
                            <span class="label label-default"><i class="fa fa-lock"></i> {{ $threads_closed_count }}</span>
                        </div>
                    </div><!--.chat-list-search-->
                    <div class="chat-list-in scrollable-block">
                        {{--/img/logo-mobile.png--}}
                        @if($threads_open_count > 0)
                            @foreach($threads as $thread)
                                <div class="chat-list-item">
                                    <div class="chat-list-item-photo">
                                        <img src="/img/avatar-64.png" alt="">
                                    </div>
                                    <div class="chat-list-item-header">
                                        <div class="chat-list-item-name">
                                            <a href="{{ route('messages.show', ['slug' => $thread->slug]) }}">
                                            <span class="name">{{ $thread->creator()->name }}</span>
                                            </a>
                                        </div>
                                        <div class="chat-list-item-date"></div>
                                    </div>
                                    <div class="chat-list-item-cont">
                                        <div class="chat-list-item-count" style="background-color: {{ ($thread->userUnreadMessagesCount(auth()->user()->id) ? 'red' : 'grey') }}">
                                            @if($thread->trashed())
                                                <i class="fa fa-lock"></i>
                                            @else
                                                {{ $thread->userUnreadMessagesCount(auth()->user()->id) }}/{{ $thread->messages->count() }}
                                            @endif
                                        </div>
                                    </div>
                                    <span class="name">{{ $thread->subject }}</span>
                                </div>
                            @endforeach
                        @endif
                    </div><!--.chat-list-in-->
                </section><!--.chat-list-->

                <section class="chat-area">
                    <div class="chat-area-in">
                        <div class="chat-area-header">
                            <div class="clean">
                                @if(isset($conversation))
                                    <div class="pull-left" style="font-size:1.2em; padding-left: 10px;">Thema: {{ $conversation->subject }} {{ isset($conversation) && $conversation->trashed() ? '(geschlossen)' : '' }}</div>
                                @else
                                    Kein Thema ausgewählt.
                                @endif
                            </div>
                            @if(isset($conversation))
                                    <div class="chat-area-header-action" style="vertical-align: middle;">
                                        {{ Form::open(['url' => route('messages.delete', ['slug' => $conversation->slug]), 'method' => 'delete']) }}
                                        <a href="{!! route('messages') !!}" class="btn btn-primary btn-sm btn-rounded pull-right m-l-1">
                                            <i class="fa fa-plus-circle m-r-1"></i>Neues Thema
                                        </a>
                                        @if(! $conversation->trashed())
                                            <button type="submit" class="btn btn-danger btn-rounded btn-sm pull-right m-l-1">
                                                <i class="fa fa-lock m-r-1"></i>Thema schließen
                                            </button>
                                        @else
                                            <a href="{!! route('messages.open', ['slug' => $conversation->slug]) !!}" class="btn btn-warning btn-sm btn-rounded pull-right m-l-1">
                                                <i class="fa fa-unlock m-r-1"></i>Thema öffnen
                                            </a>
                                        @endif
                                        {{ Form::close() }}
                                    </div>


                                    {{--</div>--}}
                                @endif
                        </div><!--.chat-area-header-->

                        <div class="chat-dialog-area">
                            @if(!isset($conversation))
                            <div class="chat-dialog-clean">
                                <div class="chat-dialog-clean-in">
                                    <i class="font-icon font-icon-mail-2"></i>
                                    <div class="caption">Wählen Sie ein Thema</div>
                                    <div class="txt">Bitte teilen Sie uns Ihr Anliegen mit.</div>
                                </div>
                            </div>
                            @else
                                @foreach($conversation->messages as $message)
                                <div class="chat-message {{ $message->user->can('manage-directory') ? 'selected' : '' }}">
                                    <div class="chat-message-photo">
                                        <img src={{ $message->user->can('manage-directory') ? "/img/logo-mobile.png" : "/img/avatar-64.png" }} alt="{{ $message->user->name }}" class="img-circle">
                                    </div>
                                    <div class="chat-message-header">
                                        <div class="tbl-row">
                                            <div class="tbl-cell tbl-cell-name">{{ $message->user->name }}</div>
                                            <div class="tbl-cell tbl-cell-date">{{ $message->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                    <div class="chat-message-content">
                                        <div class="chat-message-txt">{{ $message->body }}</div>
                                    </div>
                                </div><!--.chat-message-->
                                @endforeach
                            @endif
                        </div><!--.chat-dialog-area-->

                        <div class="chat-area-bottom">
                            {{ Form::open(['route' => ['messages.store']]) }}
                                <div class="form-group">
                                    @if(! isset($conversation))
                                        @can('manage-directory')
                                            <div class="row">
                                                <div class="col-md-12">
                                                    {{ Form::Fselect('Nachricht schreiben an', 'user_id') }}
                                                    <input type="hidden" id="userOldValue" data-id="{{ isset($data) && $data->user_id ? $data->user_id : null }}"
                                                           data-name="{{ isset($data) && $data->user_id ? $data->owner->email : null }}">
                                                </div>
                                            </div>
                                        @endcan
                                        {{ Form::Finput('Thema', 'subject') }}
                                    @else
                                        {{ Form::hidden('id', $conversation->id) }}
                                        {{ Form::hidden('subject', $conversation->subject) }}
                                    @endif
                                    {{ Form::Ftextarea('Neue Nachricht', 'message', null, isset($conversation) && $conversation->trashed() ? ['readonly'=>'readonly'] : []) }}
                                </div>
                                <button type="submit" class="btn btn-rounded float-left" {{ isset($conversation) && $conversation->trashed() ? 'disabled' : '' }}>Send</button>
                            {{ Form::close() }}
                        </div><!--.chat-area-bottom-->

                    </div><!--.chat-area-in-->
                </section><!--.chat-area-->
            </div><!--.chat-container-->
        </div>
    </div>
@stop

