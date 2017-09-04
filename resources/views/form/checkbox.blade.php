<fieldset class="form-group {{ $errors->has($name) ? ' error' : '' }}">

    @if($label)
        {!! Form::label($name, $label, ['class' => 'form-label semibold']) !!}
    @endif

    @foreach($values as $vlabel => $vvalue)
        <div class="checkbox-bird" style="padding-top: 8px; margin-bottom: 0;">
            {!! Form::checkbox($name, $vvalue) !!}
            {!! Form::label($name, $vlabel) !!}
        </div>
    @endforeach

    @if ($errors->has($name))
        <div class="error-list" data-error-list=""><ul><li>{{ $errors->first($name) }}</li></ul></div>
    @endif
</fieldset>