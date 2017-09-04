<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use App\Models\Location;
use App\Services\ImageResize;
use App\Services\StoreHelper;
use App\Traits\PdfHelper;
use Yajra\Datatables\Datatables;


class CustomerCompanyController extends Controller
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
		return view('customer.company.index');
	}

	/**
	 * @return mixed
	 */
	public function datatables()
	{
		$data = Company::where('user_id', auth()->user()->id)->select([
			'companies.id',
			'companies.name',
			'companies.address',
			'companies.email',
			'companies.phone',
			'companies.fax',
			'companies.listing_level',
			'companies.listing_status',
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
			->addColumn('action', function ($row) {
				return '<div class="dropdown dropdown-status">
                            <button class="btn btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                Optionen
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="' . route('customer.company.form', $row->id) . '">
                                    <i class="font-icon font-icon-pencil color-blue mr5"></i> Bearbeiten
                                </a>
                                <a class="dropdown-item" href="' . route('customer.company.delete', $row->id) . '">
                                    <i class="font-icon font-icon-trash color-red mr5"></i> Löschen
                                </a>
                            </div>
                        </div>';
			})
			->escapeColumns([])
			->make(true);
	}

    /**
     * @return $this
     */
    public function form($id = null)
    {
//		->orWhereNull('user_id')
    	$company = $id ? Company::where('user_id', auth()->user()->id)->with('location', 'keywords', 'owner', 'files',
			'gallery_images', 'categories')->findOrFail($id) : null;
		$files = $id ?  $this->filesJson($company) : '[]';

        return view('customer.company.form', [
            'data' => $company,
			'files' => $files
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
        $data = Company::firstOrNew(['user_id' => auth()->user()->id, 'id' => $request->get('id')]);

        //fields exlcuded for all listing levels
        $exclude_fields = [
			'slug', 'listing_level', 'listing_status', 'listing_valid_until', 'category_secondary_ids',
			'user_id', 'image', 'image_gallery', 'pdf_files', 'seo_meta_title', 'seo_meta_description'
		];

        //these fields can be saved with premium listing level only
        if($data->listing_level != 'premium') {
			$exclude_fields += [
				'email', 'phone', 'mobile', 'fax', 'www', 'video_url', 'summary', 'description'
			];
		}
        $storeRequest = $request->except($exclude_fields);

        $storeRequest += [
//        	'listing_status' => 0, // = review status?
            'image' => $imageResize->storeAndSyncImage($data, $request, 'company-logo')
        ];

        $data->fill($storeRequest);
		$data->owner()->associate(auth()->user());
        $data->location()->associate(Location::findOrFail($request->location_id));

//		dd($storeRequest);
        $data->save();

        //premium only
		if($data->listing_level == 'premium') {
			$imageResize->storeAndSyncGallery($data, $request, 'gallery');
			$this->storeAndSyncFiles($data, $request->get('fileuploader-list-pdf_files'), $request->pdf_files);
		}

		$storeHelper->storeCategories($request->category_secondary_ids, $data);
        $storeHelper->storeKeywords($request, $data);

        alert()->success(trans('messages.db.update'));

        return redirect()->route('customer.company.form', $data->id);
    }

}
