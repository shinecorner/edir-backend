<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use App\Models\Location;
use App\Models\User;
use App\Services\ImageResize;
use App\Services\StoreHelper;
use App\Traits\PdfHelper;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;

class CompanyController extends Controller
{
    use PdfHelper;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return view('company.index');
    }

    /**
     * @return mixed
     */
    public function datatables()
    {
        $data = Company::select([
            'companies.id',
            'companies.name',
            'companies.email',
            'companies.phone',
            'companies.fax',
            'companies.listing_level',
            'companies.listing_status',
            'locations.street_name',
            'locations.street_number',
            'locations.zip_code',
            'locations.city'
        ])->leftJoin('locations', 'locations.id', '=', 'companies.location_id');

        return Datatables::of($data)
            ->editColumn('name', function ($row) {
                return '<strong>' . $row->name . '</strong>';
            })
            ->editColumn('listing_level', function ($row) {
                $class = $row->listing_level == 'premium' ? 'text-warning' : 'text-muted';

                return '<i class="fa fa-trophy ' . $class . '"></i>';
            })
            ->editColumn('listing_status', function ($row) {
                $class = $row->listing_status == true ? 'fa-eye text-muted' : 'fa-eye-slash text-danger';

                return '<i class="fa ' . $class . '"></i>';
            })
            ->addColumn('address', function ($row) {
                return $row->street_name . " " . $row->street_number;
            })
            ->addColumn('action', function ($row) {
                return '<div class="dropdown dropdown-status">
                            <button class="btn btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                Optionen
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="' . route('company.form', $row->id) . '">
                                    <i class="font-icon font-icon-pencil color-blue mr5"></i> Bearbeiten
                                </a>
                                <a class="dropdown-item" href="' . route('company.delete', $row->id) . '">
                                    <i class="font-icon font-icon-trash color-red mr5"></i> Löschen
                                </a>
                            </div>
                        </div>';
            })
            ->removeColumn('street_name')
            ->removeColumn('street_number')
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * @return $this
     */
    public function form($id = null)
    {
        $company = $id ? Company::with(
            'location', 'keywords', 'owner', 'files', 'gallery_images', 'categories', 'opening_times'
        )->findOrFail($id) : null;

        $files = $id ? $this->filesJson($company) : '[]';

        $opening_times = [];
        if ($company) {
            $company->opening_times->each(function ($item) use (&$opening_times) {
                $opening_times[$item->weekday] = $item->toArray();
            });
        }

        return view('company.form', [
            'data' => $company,
            'files' => $files,
            'opening_times' => $opening_times
        ]);
    }


    /**
     * @param CompanyRequest $request
     * @param ImageResize $imageResize
     * @param StoreHelper $storeHelper
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CompanyRequest $request, ImageResize $imageResize, StoreHelper $storeHelper)
    {
        $data = Company::firstOrNew(['id' => $request->get('id')]);

        $storeRequest = $request->except([
            'listing_valid_until', 'category_secondary_ids', 'user_id', 'image', 'image_gallery', 'pdf_files',
        ]);

        $storeRequest += [
            'listing_valid_until' => Carbon::createFromFormat('d.m.Y', $request->listing_valid_until)->format('Y-m-d'),
            'listing_status' => $request->approved ? 1 : 0,
            'image' => $imageResize->storeAndSyncImage($data, $request, 'company')
        ];

        $data->fill($storeRequest);

        $data->owner()->dissociate();
        if ($request->user_id) {
            $data->owner()->associate(User::findOrFail($request->user_id));
        }

        $storeHelper->storeLocation($request, $data);

        $data->save();

        $imageResize->storeAndSyncGallery($data, $request, 'gallery');
        $this->storeAndSyncFiles($data, $request->get('fileuploader-list-pdf_files'), $request->pdf_files);

        $storeHelper->storeCategories($request->category_secondary_ids, $data);
        $storeHelper->storeKeywords($request, $data);
        $storeHelper->storeOpeningTimes($request, $data);

        alert()->success(trans('messages.db.update'));

        return redirect()->route('company.form', $data->id);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    /*public function delete($id)
    {
        $data = CategoryPrimary::find($id);
        $data->keywords()->delete();
        // todo
        // image del
        // subcat del
        $data->delete();

        alert()->warning(trans('messages.db.delete'));

        return back();
    }*/
}
