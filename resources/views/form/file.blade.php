@if($label)
    <fieldset class="form-group {{ $errors->has($name) ? ' error' : '' }}">
        {!! Form::label($name, $label, ['class' => 'form-label semibold']) !!}
        {!! Form::file($name, $attributes) !!}

        @if ($errors->has($name))
            <div class="error-list" data-error-list=""><ul><li>{{ $errors->first($name) }}</li></ul></div>
        @endif
    </fieldset>
@else
    {!! Form::file($name, $attributes) !!}
    @if ($errors->has($name))
        <div class="error-list" data-error-list=""><ul><li>{{ $errors->first($name) }}</li></ul></div>
    @endif
@endif