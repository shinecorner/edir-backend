<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlogPostRequest;
use App\Models\BlogPost;
use App\Models\Directory;
use App\Models\User;
use App\Services\ImageResize;
use App\Services\StoreHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Yajra\Datatables\Datatables;

class BlogPostController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('blog.index');
    }

    /**
     * @return mixed
     */
    public function datatables()
    {
        $data = BlogPost::with('user', 'directory')->select([
            'id',
            'image',
            'name',
            'slug',
            'created_at',
            'user_id',
            'directory_id'
        ]);

        return Datatables::of($data)
            ->editColumn('image', function ($row) {
                return '<img src="' . $row->image('100x100') . '">';
            })
            ->add_column('user', function ($row) {
                return $row->user->name;
            })
            ->add_column('directory', function ($row) {
                return $row->directory->name;
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d.m.Y');
            })
            ->filterColumn('created_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(blog_posts.created_at,'%d.%m.%Y') like ?", ["%$keyword%"]);
            })
            ->addColumn('action', function ($row) {
                return '<div class="dropdown dropdown-status">
                            <button class="btn btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                Optionen
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="' . route('blog.form', $row->id) . '">
                                    <i class="font-icon font-icon-pencil color-blue mr5"></i> Bearbeiten
                                </a>
                                <a class="dropdown-item" href="' . route('blog.delete', $row->id) . '">
                                    <i class="font-icon font-icon-trash color-red mr5"></i> Löschen
                                </a>
                            </div>
                        </div>';
            })
            ->remove_column('id')
            ->remove_column('user_id')
            ->remove_column('directory_id')
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * @param null $id
     *
     * @return mixed
     */
    public function form($id = null)
    {
        return view('blog.form', [
            'directories' => Directory::pluck('name', 'id'),
            'data' => $id ? BlogPost::with('keywords')->findOrFail($id) : null,
        ]);
    }

    /**
     * @param BlogPostRequest $request
     * @param ImageResize $imageResize
     * @param StoreHelper $storeHelper
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(BlogPostRequest $request, ImageResize $imageResize, StoreHelper $storeHelper)
    {
        $data = BlogPost::firstOrNew(['id' => $request->get('id')]);

        $storeRequest = $request->except('image');
        $storeRequest += [
            'image' => $imageResize->storeAndSyncImage($data, $request, 'blog')
        ];

        $data->fill($storeRequest);
        $data->directory()->associate(Directory::findOrFail($request->directory_id));
        $data->user()->associate(User::findOrFail(auth()->user()->id));
        $data->save();

        $storeHelper->storeKeywords($request, $data);

        alert()->success(trans('messages.db.update'));

        return redirect()->route('blog.form', $data->id);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        try {
            BlogPost::findOrFail($id)->delete();
            // alert()->error(trans('messages.db.delete'));
        } catch (ModelNotFoundException $e) {
            // alert()->error('Der Datensatz konnte nicht gelöscht werden!');
        }

        return redirect()->back();
    }

}
