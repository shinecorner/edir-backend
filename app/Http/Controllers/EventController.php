<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\CategoryEvent;
use App\Models\Company;
use App\Models\Event;
use App\Models\Location;
use App\Services\ImageResize;
use App\Services\StoreHelper;
use Carbon\Carbon;
use Yajra\Datatables\Facades\Datatables;

class EventController extends Controller
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
        return view('event.index');
    }

    /**
     * @return mixed
     */
    public function datatables()
    {
        $data = Event::with(['category', 'company'])->withTrashed()->select([
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
            'locations.street_name',
            'locations.street_number',
            'locations.zip_code',
            'locations.city'
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
                return $row->zip_code . ' ' . $row->city;
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
                                <a class="dropdown-item" href="' . route('event.form', $row->id) . '">
                                    <i class="font-icon font-icon-pencil color-blue mr5"></i> Bearbeiten
                                </a>
                                <a class="dropdown-item" href="' . route('event.delete', $row->id) . '">
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
        return view('event.form', [
            'data' => $id ? Event::with('company', 'location', 'category', 'keywords', 'gallery_images')->findOrFail($id) : null,
            'categories' => CategoryEvent::all()->pluck('name', 'id'),
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
        $data = Event::firstOrNew(['id' => $request->get('id')]);

        $storeRequest = $request->except([
            'date_start', 'date_end', 'time_start', 'time_end', 'valid_until',
			'category_event_id', 'company_id', 'location_id', 'image'
        ]);

        $storeRequest += [
            'date_start' => Carbon::createFromFormat('d.m.Y', $request->date_start),
            'date_end' => Carbon::createFromFormat('d.m.Y', $request->date_end),
			'time_start' => $request->time_start ? Carbon::createFromFormat('H:i', $request->time_start)->format('H:i:s') : null,
			'time_end' => $request->time_end ? Carbon::createFromFormat('H:i', $request->time_end)->format('H:i:s') : null,
            'valid_until' => $request->valid_until ? Carbon::createFromFormat('d.m.Y', $request->valid_until) : null,
            'approved' => $request->approved ? 1 : 0,
            'active' => $request->active ? 1 : 0,
            'image' => $imageResize->storeAndSyncImage($data, $request, 'event')
        ];

        $data->fill($storeRequest);
        $data->category()->associate(CategoryEvent::findOrFail($request->category_event_id));
        $data->company()->associate(Company::findOrFail($request->company_id));
        $storeHelper->storeLocation($request, $data);
        $data->save();

		$imageResize->storeAndSyncGallery($data, $request, 'gallery');
		$storeHelper->storeKeywords($request, $data);

        alert()->success(trans('messages.db.update'));

        return redirect()->route('event.form', $data->id);
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


