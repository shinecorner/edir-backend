<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Models\CategoryEvent;
use App\Models\Company;
use App\Models\Event;
use App\Models\Location;
use App\Services\ImageResize;
use App\Services\StoreHelper;
use Carbon\Carbon;
use Yajra\Datatables\Facades\Datatables;

class CustomerEventController extends Controller
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
			return view('customer.event.index');
		} else {
			return view('customer.buy_premium');
		}
	}

	/**
	 * @return mixed
	 */
	public function datatables()
	{
		$data = Event::whereIn('company_id', $this->user_companies->pluck('id'))->with(['category', 'company'])->withTrashed()->select([
			'events.id',
			'events.name',
			'date_start',
			'date_end',
			'time_start',
			'time_end',
			'events.image',
			'events.video_url',
			'active',
			'approved',
			'events.created_at',
			'events.deleted_at',
			'category_event_id',
			'company_id',
			'events.location_id',
		])->leftJoin('locations', 'locations.id', '=', 'events.location_id');

		if(request()->has('company_id') && request()->get('company_id')) {
			$data->where('events.company_id', request()->get('company_id'));
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
			->addColumn('location', function ($row) {
				return $row->location->zip_code . ' ' . $row->location->city;
			})
			->filterColumn('location', function ($query, $keyword) {
				$query->whereRaw("locations.zip_code like ? or locations.city like ?", ["$keyword%", "$keyword%"]);
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
				$query->whereRaw("DATE_FORMAT(events.created_at,'%d.%m.%Y') like ?", ["%$keyword%"]);
			})
			->editColumn('approved', function ($row) {
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
                                <a class="dropdown-item" href="' . route('customer.event.form', $row->id) . '">
                                    <i class="font-icon font-icon-pencil color-blue mr5"></i> Bearbeiten
                                </a>
                                <a class="dropdown-item" href="' . route('customer.event.delete', $row->id) . '">
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
			->removeColumn('time_start')
			->removeColumn('date_end')
			->removeColumn('time_end')
			->removeColumn('deleted_at')
			->escapeColumns([])
			->make(true);
	}

    /**
     * @return $this
     */
    public function form($id = null)
    {
        return view('customer.event.form', [
            'data' => $id ? Event::whereIn('company_id', $this->user_companies->pluck('id'))->findOrFail($id) : null,
            'categories' => CategoryEvent::all()->pluck('name', 'id'),
			'user_companies' => $this->user_companies->pluck('name', 'id'),
        ]);
    }

    /**
     * @param EventRequest $request
     * @param ImageResize $imageResize
     * @param StoreHelper $storeHelper
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(EventRequest $request, ImageResize $imageResize, StoreHelper $storeHelper)
    {
        $data = Event::whereIn('company_id', $this->user_companies)->firstOrNew(['id' => $request->get('id')]);

        $storeRequest = $request->except([
            'date_start', 'date_end', 'valid_until', 'category_event_id', 'company_id', 'location_id', 'image',
			'seo_meta_title', 'seo_meta_description', 'approved'
        ]);

        $storeRequest += [
            'date_start' => Carbon::createFromFormat('d.m.Y', $request->date_start),
            'date_end' => Carbon::createFromFormat('d.m.Y', $request->date_end),
            'valid_until' => $request->valid_until ? Carbon::createFromFormat('d.m.Y', $request->valid_until) : null,
            'approved' => 0,
            'active' => $request->active ? 1 : 0,
            'image' => $imageResize->storeAndSyncImage($data, $request, 'event')
        ];

        $data->fill($storeRequest);
        $data->category()->associate(CategoryEvent::findOrFail($request->category_event_id));
        $data->company()->associate(Company::premium()->findOrFail($request->company_id));
        $data->location()->associate(Location::findOrFail($request->location_id));
        $data->save();

		$imageResize->storeAndSyncGallery($data, $request, 'gallery');
        $storeHelper->storeKeywords($request, $data);

        alert()->success(trans('messages.db.update'));

		return redirect()->route('customer.event.form', $data->id);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        // true remove from table
        Event::find($id)->delete();

        alert()->warning(trans('messages.db.delete'));

        return back();
    }
}


