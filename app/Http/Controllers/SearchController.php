<?php

namespace App\Http\Controllers;

use App\Models\CategoryPrimary;
use App\Models\Company;
use App\Models\Location;
use App\Models\User;

class SearchController extends Controller
{
    /**
     * @return mixed
     */
//    public function categorySecondary()
//    {
//        return CategorySecondary::where('name', 'LIKE', '%' . request()->get('term') . '%')
//            ->limit(20)
//            ->pluck('id', 'name');
//    }

    public function categorySecondary()
    {
        return CategoryPrimary::
        with([
            'subcategories' => function ($query) {
                $query->where('name', 'LIKE', '%' . request()->get('term') . '%')->select('id', 'name', 'category_primary_id');
            }
        ])
            ->whereHas('subcategories', function ($query) {
                $query->where('name', 'LIKE', '%' . request()->get('term') . '%')->select('id', 'name', 'category_primary_id');
            })
            ->limit(20)->select('id', 'name')->get();
    }

    public function customer()
    {
        return User::where('role', 'customer')
            ->where(function ($query) {
                $query->where('email', 'LIKE', '%' . request()->get('term') . '%')
                    ->orWhere('first_name', 'LIKE', request()->get('term') . '%')
                    ->orWhere('last_name', 'LIKE', request()->get('term') . '%')
                    ->orWhere('client_number', 'LIKE', request()->get('term') . '%');
            })
            ->limit(20)
            ->pluck('id', 'email');
    }

    public function company()
    {
        return Company::where('name', 'LIKE', '%' . request()->get('term') . '%')
            ->limit(20)
            ->pluck('id', 'name');
    }

    public function location()
    {
        return Location::where('city', 'LIKE', request()->get('term') . '%')
            ->orWhere('zip_code', 'LIKE', request()->get('term') . '%')
            ->limit(20)
            ->select('id', 'zip_code', 'city', 'county')->get();
//            ->pluck('id', 'zip_code', 'city');
    }

}
