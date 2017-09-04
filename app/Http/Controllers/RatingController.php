<?php

namespace App\Http\Controllers;


use App\Http\Requests\RatingRequest;
use App\Models\Company;
use App\Models\Rating;
use Yajra\Datatables\Facades\Datatables;

class RatingController extends Controller
{
    /**
     * RatingController constructor.
     */
	public function __construct()
	{
		$this->middleware('auth');
	}

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function index()
	{
		return view('rating.index');
	}

	/**
	 * @return mixed
	 */
	public function datatables()
	{
		$data = Rating::whereNotNull('ratings.company_id')->withTrashed()->with(['company'])->select([
			'ratings.id',
			'ratings.name',
			'ratings.title',
			'ratings.description',
			'ratings.rating',
			'ratings.approved',
			'ratings.created_at',
			'ratings.deleted_at',
			'ratings.company_id',
		]);

        if(request()->has('company_id') && request()->get('company_id')) {
            $data->where('ratings.company_id', request()->get('company_id'));
        }

		return Datatables::of($data)
			->editColumn('title', function ($row) {
				return '<strong>' . $row->title . '</strong>';
			})
			->addColumn('company', function ($row) {
				return $row->company->name;
			})
			->editColumn('description', function ($row) {
				if($row->trashed()) {
					return "<s>".str_limit($row->description, 50)."</s>";
				}
				else {
					return str_limit($row->description, 50);
				}
			})
			->editColumn('created_at', function ($row) {
				return $row->created_at->format('d.m.Y');
			})
			->filterColumn('created_at', function ($query, $keyword) {
				$query->whereRaw("DATE_FORMAT(ratings.created_at,'%d.%m.%Y') like ?", ["%$keyword%"]);
			})
			->addColumn('approved', function ($row) {
				if($row->trashed()) {
					return '<i class="font-icon font-icon-trash color-red mr5"></i>';
				}
				else if ($row->approved) {
					return '<i class="font-icon font-icon-check-circle color-blue mr5"></i>';
                } else {
					return '<i class="font-icon font-icon-circle-lined-error color-red mr5"></i>';
				}
			})
			->addColumn('action', function ($row) {
				return '<div class="dropdown dropdown-status">
                            <button class="btn btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                Optionen
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="' . route('rating.form', $row->id) . '">
                                    <i class="font-icon font-icon-pencil color-blue mr5"></i> Bearbeiten
                                </a>
                                <a class="dropdown-item" href="' . route('rating.delete', $row->id) . '">
                                    <i class="font-icon font-icon-trash color-red mr5"></i> Löschen
                                </a>
                            </div>
                        </div>';
			})
			->setRowClass(function ($row) {
				return $row->deleted_at ? 'table-danger' : '';
			})
			->removeColumn('company_id')
			->escapeColumns([])
			->make(true);
	}

    /**
     * @param null $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function form($id = null)
	{
		return view('rating.form', [
			'data' => $id ? Rating::findOrFail($id) : null,
		]);
	}

	/**
	 * @param RatingRequest $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store(RatingRequest $request)
	{
		$data = Rating::firstOrNew(['id' => $request->get('id')]);

		$storeRequest = $request->all();
		$storeRequest += [
			'approved' => $request->approved ? 1 : 0,
			'is_visible' => $request->is_visible ? 1 : 0
		];
		$data->fill($storeRequest);
		$data->company()->associate(Company::findOrFail($request->company_id));
		$data->save();

		alert()->success(trans('messages.db.update'));

		return redirect()->route('rating.form', $data->id);
	}

	/**
	 * @param $id
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function delete($id)
	{
		// true remove from table
		Rating::findOrFail($id)->delete();

		alert()->warning(trans('messages.db.delete'));

		return back();
	}
}
