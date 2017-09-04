<?php

namespace App\Http\Controllers;

use App\Http\Requests\DirectoryRequest;
use App\Models\Directory;
use Carbon\Carbon;
use Yajra\Datatables\Facades\Datatables;

class DirectoryController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function index()
	{
		return view('directory.index');
	}

	/**
	 * @return mixed
	 */
	public function datatables()
	{
		$data = Directory::select([
			'directories.id',
			'directories.name',
			'directories.api_token'
		]);

		return Datatables::of($data)
			->addColumn('action', function ($row) {
				return '<div class="dropdown dropdown-status">
                            <button class="btn btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                Optionen
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="' . route('directory.form', $row->id) . '">
                                    <i class="font-icon font-icon-pencil color-blue mr5"></i> Bearbeiten
                                </a>
                                <a class="dropdown-item" href="' . route('directory.delete', $row->id) . '">
                                    <i class="font-icon font-icon-trash color-red mr5"></i> Löschen
                                </a>
                            </div>
                        </div>';
			})
			->setRowClass(function ($row) {
				return $row->deleted_at ? 'table-danger' : '';
			})
			->escapeColumns([])
			->make(true);
	}

	/**
	 * @return $this
	 */
    /**
     * @param null $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function form($id = null)
	{
		return view('directory.form', [
			'data' => $id ? Directory::findOrFail($id) : null,
		]);
	}

	/**
	 * @param DirectoryRequest $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store(DirectoryRequest $request)
	{
		$data = Directory::updateOrCreate(
		    ['id' => $request->get('id')],
            $request->all()
        );

    	alert()->success(trans('messages.db.update'));

		return redirect()->route('directory.form', $data->id);
	}

	/**
	 * @param $id
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function delete($id)
	{
	    // softDelete
		Directory::find($id)->delete();
        // hard delete
        // Directory::find($id)->forceDelete();

		alert()->warning(trans('messages.db.delete'));

		return back();
	}
}
