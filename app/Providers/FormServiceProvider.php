<?php namespace App\Providers;

use Form;
use Illuminate\Support\ServiceProvider;

class FormServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
        Form::component('Finput', 'form.input', ['label', 'name', 'value' => null, 'attributes' => []]);
        Form::component('Fdate', 'form.date_input', ['label', 'name', 'value', 'attributes' => []]);
        Form::component('Ftime', 'form.time_input', ['label', 'name', 'value' => null, 'attributes' => []]);
        Form::component('Ftextarea', 'form.textarea', ['label', 'name', 'value' => null, 'attributes' => []]);
        Form::component('Fpassword', 'form.password', ['label', 'name', 'value' => null, 'attributes' => []]);
        Form::component('Fradio', 'form.radio', ['label', 'name', 'values' => [], 'attributes' => []]);
        Form::component('Fcheckbox', 'form.checkbox', ['label', 'name', 'values' => [], 'attributes' => []]);
        Form::component('Fselect', 'form.select', ['label', 'name', 'list' => [], 'values' => [], 'attributes' => []]);
        Form::component('Fselectmultiple', 'form.selectmultiple', ['label', 'name', 'list' => [], 'values' => [], 'attributes' => []]);
        Form::component('Fselectphoto', 'form.selectphoto', ['label', 'name', 'list' => [], 'values' => [], 'attributes' => []]);

        Form::component('Ffile', 'form.file', ['label', 'name', 'attributes' => []]);
	}
}
