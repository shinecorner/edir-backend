<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\DealRequest;
use App\Models\CategoryDeal;
use App\Models\Company;
use App\Models\Deal;
use App\Services\ImageResize;
use App\Services\StoreHelper;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;

class CustomerDealController extends Controller
{
	protected $user_companies;

    public function __construct()
    {
        $this->middleware('auth');
		$this->middleware(function ($request, $next) {
			$this->user_companies = Company::premium()->where('user_id', auth()->user()->id)->get();

			return $next($request);
		});
    }

	/**
	 * @return mixed
	 */
	public function index()
	{
		$companies_with_premium = $this->user_companies->count();
		if ($companies_with_premium > 0) {
			return view('customer.deal.index');
		} else {
			return view('customer.buy_premium');
		}
	}

	/**
	 * @return mixed
	 */
	public function datatables()
	{

		$data = Deal::whereIn('company_id', $this->user_companies->pluck('id'))->with(['category', 'company'])->select([
			'deals.id',
			'deals.name',
			'deals.date_start',
			'deals.date_end',
			'deals.discount_type',
			'deals.discount_value',
			'deals.image',
			'deals.video_url',
			'deals.approved',
			'deals.created_at',
			'deals.deleted_at',
			'deals.category_deal_id',
			'deals.company_id',
		]);

		if(request()->has('company_id') && request()->get('company_id')) {
			$data->where('deals.company_id', request()->get('company_id'));
		}

		return Datatables::of($data)
			->editColumn('name', function ($row) {
				return '<strong>' . $row->name . '</strong>';
			})
			->addColumn('company', function ($row) {
				return $row->company->name;
			})
			->addColumn('category', function ($row) {
				return $row->category->name;
			})
			->editColumn('image', function ($row) {
				return '<img src="' . $row->image('100x100') . '">';
			})
			->addColumn('date', function ($row) {
				return $row->timerange;
			})
			->filterColumn('date', function ($query, $keyword) {
				$query->whereRaw("STR_TO_DATE(?, '%d.%m.%Y') BETWEEN date_start and date_end", ["$keyword"]);
			})
			->editColumn('created_at', function ($row) {
				return $row->created_at->format('d.m.Y');
			})
			->filterColumn('created_at', function ($query, $keyword) {
				$query->whereRaw("DATE_FORMAT(deals.created_at,'%d.%m.%Y') like ?", ["%$keyword%"]);
			})
			->addColumn('discount', function ($row) {
				return $row->discount_value . ($row->discount_type == 'percent' ? ' %' : ' Eur');
			})
			->filterColumn('discount', function ($query, $keyword) {
				$query->whereRaw("discount_type LIKE ? or discount_value LIKE ?", ["%$keyword%", "%$keyword%"]);
			})
			->addColumn('approved', function ($row) {
				if ($row->approved) {
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
                                <a class="dropdown-item" href="' . route('customer.deal.form', $row->id) . '">
                                    <i class="font-icon font-icon-pencil color-blue mr5"></i> Bearbeiten
                                </a>
                                <a class="dropdown-item" href="' . route('customer.deal.delete', $row->id) . '">
                                    <i class="font-icon font-icon-trash color-red mr5"></i> Löschen
                                </a>
                            </div>
                        </div>';
			})
			->setRowClass(function ($row) {
				return $row->deleted_at ? 'table-danger' : '';
			})
			->removeColumn('id')
			->removeColumn('date_start')
			->removeColumn('date_end')
			->removeColumn('discount_type')
			->removeColumn('discount_value')
			->removeColumn('deleted_at')
			->removeColumn('category_deal_id')
			->removeColumn('company_id')
			->escapeColumns([])
			->make(true);
	}

    /**
     * @return $this
     */
    public function form($id = null)
    {
        return view('customer.deal.form', [
            'data' => $id ? Deal::whereIn('company_id', $this->user_companies->pluck('id'))->findOrFail($id) : null,
            'categories' => CategoryDeal::all()->pluck('name', 'id'),
			'user_companies' => $this->user_companies->pluck('name', 'id'),
        ]);
    }

    /**
     * @param DealRequest $request
     * @param ImageResize $imageResize
     * @param StoreHelper $storeHelper
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(DealRequest $request, ImageResize $imageResize, StoreHelper $storeHelper)
    {
        $data = Deal::whereIn('company_id', $this->user_companies)->firstOrNew(['id' => $request->get('id')]);

        $storeRequest = $request->except([
            'date_start', 'date_end', 'category_id', 'company_id', 'image', 'image_gallery', 'approved'
        ]);

        $storeRequest += [
            'date_start' => Carbon::createFromFormat('d.m.Y', $request->date_start),
            'date_end' => Carbon::createFromFormat('d.m.Y', $request->date_end),
            'approved' => 0,
            'image' => $imageResize->storeAndSyncImage($data, $request, 'deal')
        ];

        $data->fill($storeRequest);
        $data->category()->associate(CategoryDeal::findOrFail($request->category_deal_id));
        $data->company()->associate(Company::premium()->findOrFail($request->company_id));
        $data->save();

		$imageResize->storeAndSyncGallery($data, $request, 'gallery');
        $storeHelper->storeKeywords($request, $data);

        alert()->success(trans('messages.db.update'));

        return redirect()->route('customer.deal.form', $data->id);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        // true remove from table
        Deal::find($id)->delete();

        alert()->warning(trans('messages.db.delete'));

        return back();
    }
}
