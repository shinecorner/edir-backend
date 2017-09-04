<?php $readOnly = null; ?>

@foreach($attributes as $attribute)
    @if($attribute == 'readonly')
        <?php $readOnly = "readonly"; ?>
    @endif
@endforeach

<fieldset class="form-group {{ $errors->has($name) ? ' error' : '' }}">
    {!! Form::label($name, $label, ['class' => 'form-label semibold']) !!}
    {!! Form::textarea($name, null, ['rows' => '3', 'class' => 'form-control', 'placeholder' => $label, $readOnly]) !!}
@if ($errors->has($name))
    <div class="error-list" data-error-list=""><ul><li>{{ $errors->first($name) }}</li></ul></div>
@endif
</fieldset>