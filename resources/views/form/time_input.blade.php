<fieldset class="form-group {{ $errors->has($name) ? ' error' : '' }}">
    {!! Form::label($name, $label, ['class' => 'form-label semibold']) !!}
    <div class="input-group clockpicker" data-autoclose="true">
        {!! Form::input('text', $name, null, ['class' => 'form-control']) !!}
        <span class="input-group-addon">
            <span class="glyphicon glyphicon-time font-icon"></span>
        </span>
    </div>

    @if ($errors->has($name))
        <div class="error-list" data-error-list="">
            <ul>
                <li>{{ $errors->first($name) }}</li>
            </ul>
        </div>
    @endif
</fieldset>