<?php

namespace App\Http\Controllers\Categories;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryDealRequest;
use App\Models\CategoryDeal;
use App\Services\ImageResize;
use App\Services\StoreHelper;
use Yajra\Datatables\Datatables;

class CategoryDealController extends Controller
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
        return view('category.deal.index');
    }

    /**
     * @return mixed
     */
    public function datatables()
    {
        $data = CategoryDeal::select([
            'id',
            'name',
            'slug',
            'image',
            'description',
            'count'
        ]);

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
                                <a class="dropdown-item" href="' . route('category.deal.form', $row->id) . '">
                                    <i class="font-icon font-icon-pencil color-blue mr5"></i> Bearbeiten
                                </a>
                                <a class="dropdown-item" href="' . route('category.deal.delete', $row->id) . '">
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
        return view('category.deal.form', [
            'data' => $id ? CategoryDeal::with('keywords')->findOrFail($id) : null
        ]);
    }

    /**
     * @param CategoryDealRequest $request
     * @param ImageResize $imageResize
     * @param StoreHelper $storeHelper
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CategoryDealRequest $request, ImageResize $imageResize, StoreHelper $storeHelper)
    {
        $data = CategoryDeal::firstOrNew(['id' => $request->get('id')]);

        $storeRequest = $request->except('image');
        $storeRequest += [
            'image' => $imageResize->storeAndSyncImage($data, $request, 'category-deal')
        ];

        $data->fill($storeRequest);
        $data->save();

        $storeHelper->storeKeywords($request, $data);

        alert()->success(trans('messages.db.update'));

        return redirect()->route('category.deal.form', $data->id);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $data = CategoryDeal::find($id);
        $data->keywords()->delete();
        // todo
        // image del
        // subcat del
        $data->delete();

        alert()->warning(trans('messages.db.delete'));

        return back();
    }
}
