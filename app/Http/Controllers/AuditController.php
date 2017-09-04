<?php

namespace App\Http\Controllers;

use OwenIt\Auditing\Models\Audit as AuditModel;
use Yajra\Datatables\Facades\Datatables;

class AuditController extends Controller
{

    /**
     * @return mixed
     */
    public function index()
    {
        /*$test = AuditModel::find(1);

        return $test->getModified();*/

        return view('audit.index');
    }

    /**
     * @return mixed
     */
    public function datatables()
    {
        $data = AuditModel::with('user')->select([
            'audits.id',
            'user_id',
            'event',
            'auditable_id',
            'auditable_type',
            'new_values',
            'old_values',
            'url',
            'ip_address',
            'audits.created_at',
            'audits.updated_at',
        ]);

        return Datatables::of($data)
            ->addColumn('user', function ($row) {
                return '<strong><a href="' . route("user.form", ["id" => $row->user->id]) . '">' . $row->user->email . '</a></strong>';
            })
            ->addColumn('type', function ($row) {
                return '<strong>' . $row->auditable_type . '</strong>';
            })
            ->addColumn('item', function ($row) {
                switch ($row->auditable_type) {
                    case 'Company':
                        return '<a href="' . route("company.form", ["id" => $row->auditable->id]) . '">' . $row->auditable->name . '</a>';
                }
            })
            ->editColumn('old_values', function ($row) {
                return $row->getModified();
            })
            ->editColumn('audits.created_at', function ($row) {
                return $row->created_at->format('d.m.Y H:i');
            })
            ->filterColumn('audits.created_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(audits.created_at,'%d.%m.%Y	') like ?", ["%$keyword%"]);
            })
            ->removeColumn('user_id')
            ->removeColumn('auditable_id')
            ->removeColumn('auditable_type')
            ->escapeColumns([])
            ->make(true);
    }

}
