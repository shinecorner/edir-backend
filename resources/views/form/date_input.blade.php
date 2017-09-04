<fieldset class="form-group {{ $errors->has($name) ? ' error' : '' }}">
    {!! Form::label($name, $label, ['class' => 'form-label semibold']) !!}
    <div class="input-group date">
        {!! Form::input('text', $name, $value, ['class' => 'form-control']) !!}
        <span class="input-group-addon"><i class="font-icon font-icon-calend"></i></span>
    </div>

    @if ($errors->has($name))
        <div class="error-list" data-error-list="">
            <ul>
                <li>{{ $errors->first($name) }}</li>
            </ul>
        </div>
    @endif
</fieldset>