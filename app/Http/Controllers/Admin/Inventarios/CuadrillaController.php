<?php

namespace App\Http\Controllers\Admin\Inventarios;

use App\Models\Cuadrilla;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\Inventarios\CuadrillaDataTable;
use App\Http\Requests\Admin\Inventarios\CreateCuadrillaRequest;
use App\Http\Requests\Admin\Inventarios\UpdateCuadrillaRequest;
use App\Models\User;
use Illuminate\Support\Collection;

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
    public function create()
    {
        return view('admin.inventarios.cuadrillas.create', [
            'usuarios' => $this->usuariosDisponibles(),
            'empleadosSeleccionados' => [],
        ]);
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
    public function edit(string $id)
    {
        $cuadrilla = Cuadrilla::with('empleados')->findOrFail($id);
        $seleccionados = $cuadrilla->empleados;

        return view('admin.inventarios.cuadrillas.edit', [
            'cuadrilla' => $cuadrilla,
            'usuarios' => $this->usuariosDisponibles($seleccionados),
            'empleadosSeleccionados' => $seleccionados->pluck('id')->values()->toArray(),
        ]);
    }

    /**
     * Build the pool of users that can be added to a cuadrilla.
     * Always includes the already-selected members (even if they no longer
     * match the active filter) so they can still be displayed/removed.
     */
    private function usuariosDisponibles(?Collection $incluir = null): Collection
    {
        $usuarios = User::query()->role('user')
            ->where('status', 1)
            ->where('system_reserve', 0)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        if ($incluir && $incluir->isNotEmpty()) {
            $usuarios = $usuarios->concat($incluir)->unique('id');
        }

        return $usuarios->map(function (User $u) {
            return [
                'id' => $u->id,
                'nombre' => trim($u->first_name . ' ' . $u->last_name) ?: $u->email,
                'rol' => optional($u->role)->name,
                'avatar' => $u->getFirstMedia('image')?->getUrl(),
                'inicial' => strtoupper(mb_substr($u->first_name ?: $u->email, 0, 1)),
            ];
        })->sortBy('nombre')->values();
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
