<fieldset class="form-group {{ $errors->has($name) ? ' error' : '' }}">
    {!! Form::label($name, $label, ['class' => 'form-label semibold']) !!}

    <div class="radio" style="padding-top: 8px; margin-bottom: 0px;">
        @foreach($values as $vlabel => $vvalue)
            <?php $randomId = uniqid(); ?>
            {!! Form::radio($name, $vvalue, null, ['id' => $randomId]) !!}
            {!! Form::label($randomId, $vlabel, ['style' => 'margin-right:10px;'] + $attributes) !!}
        @endforeach
    </div>
@if ($errors->has($name))
    <div class="error-list" data-error-list=""><ul><li>{{ $errors->first($name) }}</li></ul></div>
@endif
</fieldset>