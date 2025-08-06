<?php

namespace App\Http\Controllers\Admin\Inventarios;

use App\Models\Almacen;
use Illuminate\Http\Request;
use App\DataTables\Inventarios\AlmacenDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Inventarios\CreateAlmacenRequest;
use App\Http\Requests\Admin\Inventarios\UpdateAlmacenRequest;

class AlmacenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(AlmacenDataTable $dataTable)
    {
        return $dataTable->render('admin.inventarios.almacenes.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.inventarios.almacenes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAlmacenRequest $request)
    {
        try {

            $data = [
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'ubicacion' => $request->ubicacion,
            ];

            Almacen::create($data);

            return redirect()->route('admin.inventarios.almacenes.index')
                ->with('success', 'Almacén creado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error al crear el almacén: ' . $e->getMessage()]);
        }
    }

    public function status(string $id)
    {
        try {
            $almacen = Almacen::findOrFail($id);
            $almacen->estado = !$almacen->estado;
            $almacen->save();

            return response()->json(['success' => true, 'status' => $almacen->estado]);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' => 'Error al actualizar el estado del almacen: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $almacen = Almacen::findOrFail($id);
        return view('admin.inventarios.almacenes.edit', compact('almacen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAlmacenRequest $request, string $id)
    {
        try {
            $almacen = Almacen::findOrFail($id);

            $data = [
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'ubicacion' => $request->ubicacion,
            ];

            $almacen->update($data);

            return redirect()->route('admin.inventarios.almacenes.index')
                ->with('success', 'Almacén actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error al actualizar el almacén: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $almacen = Almacen::findOrFail($id);
            $almacen->delete();

            return redirect()->route('admin.inventarios.almacenes.index')
                ->with('success', 'Almacén eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error al eliminar el almacén: ' . $e->getMessage()]);
        }
    }
}
