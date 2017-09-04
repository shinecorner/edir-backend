<?php $readOnly = null; ?>
<?php $defaultAttributes = ['class' => 'form-control', 'placeholder' => $label]; ?>
<?php $extraAttributes = []; ?>
@foreach($attributes as $key => $attribute)
    <?php $extraAttributes[$key] = $attribute; ?>
@endforeach
<?php $defaultAttributes = array_merge($defaultAttributes, $extraAttributes); ?>

<fieldset class="form-group {{ $errors->has($name) ? ' error' : '' }}">
    @if($label)
        {!! Form::label($name, $label, ['class' => 'form-label semibold']) !!}
    @endif
    {!! Form::input('text', $name, $value, $defaultAttributes) !!}

@if ($errors->has($name))
    <div class="error-list" data-error-list=""><ul><li>{{ $errors->first($name) }}</li></ul></div>
@endif
</fieldset>