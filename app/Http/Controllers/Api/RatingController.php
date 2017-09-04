<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Directory;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function storeApiRating(Request $request)
    {
        $rating = Rating::create($request->all());
        $company = Company::where('slug', $request->company)->first();
        $directory = Directory::find($request->directory_id);

        $rating->company()->associate($company);
        $rating->directory()->associate($directory);
        $rating->save();

        return response()->json([
            'success' => true
        ]);
    }
}
