<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Yajra\Datatables\Datatables;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return view('user/index');
    }

    /**
     * @return mixed
     */
    public function datatables()
    {
        $data = User::withTrashed()->select([
            'id',
            'client_number',
            'gender',
            'title',
            'first_name',
            'last_name',
            'email',
            'phone_number',
            'role',
            'created_at',
            'deleted_at'
        ]);

        return Datatables::of($data)
            ->addColumn('name', function ($row) {
                return '<strong>' . $row->name . '</strong>';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_date;
            })
            ->addColumn('action', function ($row) {
                return '<div class="dropdown dropdown-status">
                            <button class="btn btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                Optionen
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="' . route('user.form', $row->id) . '">
                                    <i class="font-icon font-icon-pencil color-blue mr5"></i> Bearbeiten
                                </a>
                                <a class="dropdown-item" href="' . route('user.delete', $row->id) . '">
                                    <i class="font-icon font-icon-trash color-red mr5"></i> Löschen
                                </a>
                            </div>
                        </div>';
            })
            ->editColumn('role', function ($row) {
                return '<span class="label label-default">' . $row->rolename . '</span>';
            })
            ->setRowClass(function ($user) {
                return $user->deleted_at ? 'table-danger' : '';
            })
            ->filterColumn('name', function ($query, $keyword) {
				$query->whereRaw("CONCAT_WS(' ', users.first_name, users.last_name) like ?", ["%{$keyword}%"]);
			})
            ->removeColumn('gender')
            ->removeColumn('title')
            ->removeColumn('first_name')
            ->removeColumn('last_name')
            ->removeColumn('deleted_at')
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * @return $this
     */
    public function form($id = null)
    {
        return view('user/form', [
            'data' => $id ? User::findOrFail($id) : null
        ]);
    }

    /**
     * @param UserRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request)
    {
        $requestData = $request->all();

        // wenn passwort leer (weil versteckt) dann nicht mit leer überschreiben
        if ($request->get('password') == "") {
            unset($requestData['password']);
        } else {
            $requestData['password'] = bcrypt($requestData['password']);
        }

        if ($request->get('role') == "customer" && !$request->has('id')) {
            $requestData['client_number'] = User::generateClientNumber();
        }

        $data = User::updateOrCreate(
            ['id' => $request->get('id')],
            $requestData
        );

        alert()->success(trans('messages.db.update'));

        return redirect()->route('user.form', $data->id);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        // true remove from table
        User::find($id)->delete();

        alert()->warning(trans('messages.db.delete'));

        return back();
    }
}
