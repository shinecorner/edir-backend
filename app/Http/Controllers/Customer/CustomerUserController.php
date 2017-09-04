<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;

class CustomerUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return $this
     */
    public function form()
    {
        return view('customer.profile.form', [
            'data' => auth()->user()
        ]);
    }

    /**
     * @param UserRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request)
    {
        $requestData = $request->except(['role', 'client_number']);

        // wenn passwort leer (weil versteckt) dann nicht mit leer überschreiben
        if ($request->get('password') == "") {
            unset($requestData['password']);
        } else {
            $requestData['password'] = bcrypt($requestData['password']);
        }

        auth()->user()->update($requestData);

        alert()->success(trans('messages.db.update'));

		return redirect()->route('customer.profile.form');
    }

}
