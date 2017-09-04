<?php $disabled = null; ?>

@foreach($attributes as $attribute)
    @if($attribute == 'disabled')
		<?php $disabled = "disabled"; ?>
    @endif
@endforeach

<fieldset class="form-group {{ $errors->has($name) ? ' error' : '' }}">
    {!! Form::label($name, $label, ['class' => 'form-label semibold']) !!}

    <select name="{{ $name }}" class="select2" data-placeholder="{{ $label }}" multiple {{ $disabled }}>
    @if (is_array(old(str_replace([']', '['], [''], $name))))
        @foreach (old(str_replace([']', '['], [''], $name)) as $tag)
            <option value="{{ $tag }}" selected="selected">{{ $tag }}</option>
        @endforeach
    @elseif(is_array($list))
        @foreach ($list as $index => $value)
            <option value="{{ $index }}" selected="selected">{{ $value }}</option>
        @endforeach
    @endif
    </select>

@if ($errors->has($name))
    <div class="error-list" data-error-list=""><ul><li>{{ $errors->first($name) }}</li></ul></div>
@endif
</fieldset>