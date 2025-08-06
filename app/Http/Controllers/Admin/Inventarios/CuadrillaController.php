<?php

namespace App\Http\Controllers\Admin\Inventarios;

use App\Models\Cuadrilla;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\Inventarios\CuadrillaDataTable;
use App\DataTables\Utils\SeleccionarUsuariosDataTable;
use App\Http\Requests\Admin\Inventarios\CreateCuadrillaRequest;
use App\Http\Requests\Admin\Inventarios\UpdateCuadrillaRequest;
use App\Models\User;

class CuadrillaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CuadrillaDataTable $dataTable)
    {
        // Render the DataTable for Cuadrillas
        return $dataTable->render('admin.inventarios.cuadrillas.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SeleccionarUsuariosDataTable $dataTable)
    {   
        // Initialize the DataTable for selecting users
        $dataTable->setRole('user'); // Set the role to filter users
        $dataTable->setSelectedUsers([]); // Initialize selected users as empty

        return $dataTable->render('admin.inventarios.cuadrillas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCuadrillaRequest $request)
    {
        try {

            $data = [
                'nombre' => $request->nombre,
                'update_user_id' => auth()->id(),
                'estado' => true,
            ];

            $cuadrilla = Cuadrilla::create($data);

            if ($request->has('empleados')) {
                $cuadrilla->empleados()->sync($request->empleados);
            }

            return redirect()->route('admin.inventarios.cuadrillas.index')
                ->with('success', 'Cuadrilla creada exitosamente.');

        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['error' => 'Error al crear la cuadrilla: ' . $th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SeleccionarUsuariosDataTable $dataTable, string $id)
    {
        $cuadrilla = Cuadrilla::findOrFail($id);
        $empleados = $cuadrilla->empleados->pluck('id')->toArray();

        $dataTable->setRole('user'); // Set the role to filter users
        $dataTable->setSelectedUsers($empleados); // Initialize selected users as empty
        
        // Return the view to edit the specified Cuadrilla
        return $dataTable->render('admin.inventarios.cuadrillas.edit', [
            'cuadrilla' => $cuadrilla,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCuadrillaRequest $request, string $id)
    {
        try {
            $cuadrilla = Cuadrilla::findOrFail($id);
            $cuadrilla->update([
                'nombre' => $request->nombre,
                'update_user_id' => auth()->id(),
            ]);

            if ($request->has('empleados')) {
                $cuadrilla->empleados()->sync($request->empleados);
            }

            return redirect()->route('admin.inventarios.cuadrillas.index')
                ->with('success', 'Cuadrilla actualizada exitosamente.');

        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['error' => 'Error al actualizar la cuadrilla: ' . $th->getMessage()]);
        }
    }

    public function status(Request $request, string $id)
    {
        try {
            $cuadrilla = Cuadrilla::findOrFail($id);
            $cuadrilla->update(['estado' => !$cuadrilla->estado]);

            return response()->json(['success' => true, 'message' => 'Estado actualizado correctamente.']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar el estado: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $cuadrilla = Cuadrilla::findOrFail($id);
            $cuadrilla->delete();

            return redirect()->route('admin.inventarios.cuadrillas.index')
                ->with('success', 'Cuadrilla eliminada exitosamente.');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['error' => 'Error al eliminar la cuadrilla: ' . $th->getMessage()]);
        }
    }
}
