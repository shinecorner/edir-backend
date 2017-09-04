<?php

namespace App\Http\Controllers;

use App\Http\Requests\DealRequest;
use App\Models\CategoryDeal;
use App\Models\Company;
use App\Models\Deal;
use App\Services\ImageResize;
use App\Services\StoreHelper;
use Carbon\Carbon;
use Yajra\Datatables\Facades\Datatables;

class DealController extends Controller
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
        return view('deal.index');
    }

    /**
     * @return mixed
     */
    public function datatables()
    {
        $data = Deal::withTrashed()->with(['category', 'company'])->select([
            'deals.id',
            'deals.name',
            'deals.date_start',
            'deals.date_end',
            'deals.discount_type',
            'deals.discount_value',
            'deals.image',
            'deals.video_url',
            'deals.active',
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
			->editColumn('active', function ($row) {
				$class = $row->active == true ? 'fa-eye text-muted' : 'fa-eye-slash text-danger';

				return '<i class="fa ' . $class . '"></i>';
			})
            ->addColumn('action', function ($row) {
                return '<div class="dropdown dropdown-status">
                            <button class="btn btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                Optionen
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="' . route('deal.form', $row->id) . '">
                                    <i class="font-icon font-icon-pencil color-blue mr5"></i> Bearbeiten
                                </a>
                                <a class="dropdown-item" href="' . route('deal.delete', $row->id) . '">
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
        return view('deal.form', [
            'data' => $id ? Deal::findOrFail($id) : null,
            'categories' => CategoryDeal::all()->pluck('name', 'id'),
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
        $data = Deal::firstOrNew(['id' => $request->get('id')]);

        $storeRequest = $request->except([
            'date_start', 'date_end', 'category_id', 'company_id', 'image', 'image_gallery'
        ]);

        $storeRequest += [
            'date_start' => Carbon::createFromFormat('d.m.Y', $request->date_start),
            'date_end' => Carbon::createFromFormat('d.m.Y', $request->date_end),
			'active' => $request->active ? 1 : 0,
            'approved' => $request->approved ? 1 : 0,
            'image' => $imageResize->storeAndSyncImage($data, $request, 'deal')
        ];

        $imageResize->storeAndSyncGallery($data, $request, 'gallery');

        $data->fill($storeRequest);
        $data->category()->associate(CategoryDeal::findOrFail($request->category_deal_id));
        $data->company()->associate(Company::findOrFail($request->company_id));
        $data->save();

        $storeHelper->storeKeywords($request, $data);

        alert()->success(trans('messages.db.update'));

        return redirect()->route('deal.form', $data->id);
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
