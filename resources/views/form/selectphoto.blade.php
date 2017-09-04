<?php
    Form::macro('selectphoto', function($name, $list = [], $selected = null, $options = [])
    {
        // When building a select box the "value" attribute is really the selected one
        // so we will use that when checking the model or session for a value which
        // should provide a convenient method of re-populating the forms on post.
        $selected = $this->getValueAttribute($name, $selected);
        $options['id'] = $this->getIdAttribute($name, $options);
        if (! isset($options['name'])) {
            $options['name'] = $name;
        }

        // We will simply loop through the options and build an HTML value for each of
        // them until we have an array of HTML declarations. Then we will join them
        // all together into one single HTML element that can be put on the form.
        $html = [];
        if (isset($options['placeholder'])) {
            $html[] = $this->placeholderOption($options['placeholder'], $selected);
            unset($options['placeholder']);
        }

        foreach ($list as $option) {
            $selected_attr = $this->getSelectedValue($option['value'], $selected);
            $option_attr = array('value' => e($option['value']), 'selected' => $selected_attr, 'data-photo' => $option['image'] );
            $html[] = '<option'.$this->html->attributes($option_attr).'>'.e($option['option']).'</option>';
            // $html[] = $this->getSelectOption($option['option'], $option['value'], $selected);
        }

        // Once we have all of this HTML, we can join this into a single element after
        // formatting the attributes into an HTML "attributes" string, then we will
        // build out a final select statement, which will contain all the values.
        $options = $this->html->attributes($options);
        $list = implode('', $html);
        return $this->toHtmlString("<select{$options}>{$list}</select>");
    });
?>

<fieldset class="form-group {{ $errors->has($name) ? ' error' : '' }}">
    {!! Form::label($name, $label, ['class' => 'form-label semibold']) !!}
    {!! Form::selectphoto($name, $list, null, ['class' => 'select2-photo', 'data-placeholder' => $label]) !!}

@if ($errors->has($name))
    <div class="error-list" data-error-list=""><ul><li>{{ $errors->first($name) }}</li></ul></div>
@endif
</fieldset>