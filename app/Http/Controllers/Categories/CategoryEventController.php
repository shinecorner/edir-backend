<?php

namespace App\Http\Controllers\Categories;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryEventRequest;
use App\Models\CategoryEvent;
use App\Services\ImageResize;
use App\Services\StoreHelper;
use Yajra\Datatables\Datatables;

class CategoryEventController extends Controller
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
        return view('category.event.index');
    }

    /**
     * @return mixed
     */
    public function datatables()
    {
        $data = CategoryEvent::select([
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
                                <a class="dropdown-item" href="' . route('category.event.form', $row->id) . '">
                                    <i class="font-icon font-icon-pencil color-blue mr5"></i> Bearbeiten
                                </a>
                                <a class="dropdown-item" href="' . route('category.event.delete', $row->id) . '">
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
        return view('category.event.form', [
            'data' => $id ? CategoryEvent::with('keywords')->findOrFail($id) : null
        ]);
    }

    /**
     * @param CategoryEventRequest $request
     * @param ImageResize $imageResize
     * @param StoreHelper $storeHelper
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CategoryEventRequest $request, ImageResize $imageResize, StoreHelper $storeHelper)
    {
        $data = CategoryEvent::firstOrNew(['id' => $request->get('id')]);

        $storeRequest = $request->except('image');
        $storeRequest += [
            'image' => $imageResize->storeAndSyncImage($data, $request, 'category-event')
        ];

        $data->fill($storeRequest);
        $data->save();

        $storeHelper->storeKeywords($request, $data);

        alert()->success(trans('messages.db.update'));

        return redirect()->route('category.event.form', $data->id);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $data = CategoryEvent::find($id);
        $data->keywords()->delete();
        // todo
        // image del
        // subcat del
        $data->delete();

        alert()->warning(trans('messages.db.delete'));

        return back();
    }
}
