<?php

namespace App\Http\Controllers\Categories;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryPrimaryRequest;
use App\Models\CategoryPrimary;
use App\Services\ImageResize;
use App\Services\StoreHelper;
use Yajra\Datatables\Datatables;

class CategoryPrimaryController extends Controller
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
        return view('category.primary.index');
    }

    /**
     * @return mixed
     */
    public function datatables()
    {
        $data = CategoryPrimary::select([
            'id',
            'name',
            'slug',
            'image',
            'description',
            'count'
        ])->withCount('subcategories');

        return Datatables::of($data)
            ->editColumn('name', function ($row) {
                return '<strong>' . $row->name . '</strong>';
            })
            ->editColumn('image', function ($row) {
                return '<img src="' . $row->image('100x100') . '">';
            })
            ->editColumn('count', function ($row) {
                return '<span class="label label-default">' . $row->count . '</span>';
            })
            ->editColumn('subcategories_count', function ($row) {
                return '<span class="label label-default">' . $row->subcategories_count . '</span>';
            })
            ->editColumn('description', function ($row) {
                return $row->description ?
                    '<span class="fa fa-check"></span>'
                    :
                    '<span class="fa fa-remove"></span>';
            })
            ->addColumn('action', function ($row) {
                return '<div class="dropdown dropdown-status">
                            <button class="btn btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                Optionen
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="' . route('category.primary.form', $row->id) . '">
                                    <i class="font-icon font-icon-pencil color-blue mr5"></i> Bearbeiten
                                </a>
                                <a class="dropdown-item" href="' . route('category.primary.delete', $row->id) . '">
                                    <i class="font-icon font-icon-trash color-red mr5"></i> Löschen
                                </a>
                            </div>
                        </div>';
            })
            ->removeColumn('id')
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * @return $this
     */
    public function form($id = null)
    {
        return view('category.primary.form', [
            'data' => $id ? CategoryPrimary::with('keywords')->findOrFail($id) : null
        ]);
    }

    /**
     * @param CategoryPrimaryRequest $request
     * @param ImageResize $imageResize
     * @param StoreHelper $storeHelper
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CategoryPrimaryRequest $request, ImageResize $imageResize, StoreHelper $storeHelper)
    {
        $data = CategoryPrimary::firstOrNew(['id' => $request->get('id')]);

        $storeRequest = $request->except('image');
        $storeRequest += [
            'image' => $imageResize->storeAndSyncImage($data, $request, 'category-primary')
        ];

		$data->fill($storeRequest);
		$data->save();

        $storeHelper->storeKeywords($request, $data);

        alert()->success(trans('messages.db.update'));

        return redirect()->route('category.primary.form', $data->id);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $data = CategoryPrimary::find($id);
        $data->keywords()->delete();
        // todo
        // image del
        // subcat del
        $data->delete();

        alert()->warning(trans('messages.db.delete'));

        return back();
    }
}
