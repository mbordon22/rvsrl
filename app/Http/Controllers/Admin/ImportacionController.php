<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\ImportacionDataTable;
use App\Http\Controllers\Controller;

class ImportacionController extends Controller
{
    public function index(ImportacionDataTable $dataTable)
    {
        abort_unless(
            auth()->user()->canAny(['listado_lpu.index', 'listado_materiales.index']),
            403
        );

        return $dataTable->render('admin.importaciones.index');
    }
}
