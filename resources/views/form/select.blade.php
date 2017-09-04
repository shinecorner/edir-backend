<fieldset class="form-group {{ $errors->has($name) ? ' error' : '' }}">
    {!! Form::label($name, $label, ['class' => 'form-label semibold']) !!}
    {!! Form::select($name, $list, null, ['class' => 'select2', 'data-placeholder' => $label] + $attributes) !!}
@if ($errors->has($name))
    <div class="error-list" data-error-list=""><ul><li>{{ $errors->first($name) }}</li></ul></div>
@endif
</fieldset>