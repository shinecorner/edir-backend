<?php

namespace App\Http\Controllers\Customer;


use App\Http\Controllers\Controller;
use App\Models\Rating;
use Yajra\Datatables\Facades\Datatables;

class CustomerRatingController extends Controller
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
		return view('customer.rating.index');
	}

	/**
	 * @return mixed
	 */
	public function datatables()
	{
		$user_companies = auth()->user()->companies->pluck(['id']);

		$data = Rating::approved()->select([
			'ratings.id',
			'ratings.title',
			'ratings.description',
			'ratings.rating',
			'ratings.is_visible',
            'ratings.created_at',
            'companies.name as companyname',
        ])->whereIn('ratings.company_id', $user_companies)
          ->leftJoin('companies', 'companies.id', '=', 'ratings.company_id');

        if(request()->has('company_id') && request()->get('company_id')) {
            $data->where('ratings.company_id', request()->get('company_id'));
        }

        // todo add button for toggle visible status

		return Datatables::of($data)
			->editColumn('title', function ($row) {
				return '<strong>' . $row->title . '</strong>';
			})
			->editColumn('description', function ($row) {
				return str_limit($row->description, 50);
			})
			->editColumn('created_at', function ($row) {
				return $row->created_at->format('d.m.Y');
			})
			->filterColumn('created_at', function ($query, $keyword) {
				$query->whereRaw("DATE_FORMAT(ratings.created_at,'%d.%m.%Y') like ?", ["%$keyword%"]);
			})
			->addColumn('is_visible', function ($row) {
				if ($row->is_visible) {
					return '<i class="font-icon font-icon-check-circle color-blue mr5"></i>';
                } else {
					return '<i class="font-icon font-icon-circle-lined-error color-red mr5"></i>';
				}
			})
			->escapeColumns([])
			->make(true);
	}

	/**
	 * @return $this
	 */
	public function form($id = null)
	{
		$user_companies = auth()->user()->companies->pluck(['id']);

		return view('customer.rating.form', [
			'data' => $id ? Rating::whereIn('company_id', $user_companies)->findOrFail($id) : null,
		]);
	}

}
