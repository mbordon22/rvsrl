<?php

namespace App\Http\Controllers\Admin\Inventarios;

use App\DataTables\Inventarios\MaterialDataTable;
use App\Enums\UnidadMedidaMaterial;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Inventarios\CreateMaterialRequest;
use App\Http\Requests\Admin\Inventarios\UpdateMaterialRequest;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(MaterialDataTable $dataTable)
    {
        $materiales = Material::all();
        return $dataTable->render('admin.inventarios.materiales.index', compact('materiales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $unidades = UnidadMedidaMaterial::cases();
        // Return the view to create a new material
        return view('admin.inventarios.materiales.create', compact('unidades'));       
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateMaterialRequest $request)
    {
        try {
            $data = [
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'descripcion_larga' => $request->descripcion_larga,
                'unidad_medida' => $request->unidad_medida,
                'estado' => $request->estado ?? 1,
                'insert_user_id' => auth()->id()
            ];
    
            Material::create($data);
    
            return redirect()->route('admin.inventarios.materiales.index')
                ->with('success', 'Material creado exitosamente.');
        
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al crear el material: ' . $e->getMessage()]);
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
        $material = Material::findOrFail($id);
        $unidades = UnidadMedidaMaterial::cases();
        // Return the view to edit the material
        return view('admin.inventarios.materiales.edit', compact('material', 'unidades'));
    }

    public function status(Request $request, string $id)
    {
        try {
            $material = Material::findOrFail($id);
            $material->estado = !$material->estado;
            $material->update_user_id = auth()->id();
            $material->save();

            return response()->json(['success' => true, 'status' => $material->estado]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar el estado del material: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMaterialRequest $request, string $id)
    {
        try {
            $material = Material::findOrFail($id);
            $data = [
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'descripcion_larga' => $request->descripcion_larga,
                'unidad_medida' => $request->unidad_medida,
                'update_user_id' => auth()->id()
            ];

            $material->update($data);

            return redirect()->route('admin.inventarios.materiales.index')
                ->with('success', 'Material actualizado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al actualizar el material: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $material = Material::findOrFail($id);
            $material->update_user_id = auth()->id();
            $material->delete();

            return redirect()->route('admin.inventarios.materiales.index')
                ->with('success', 'Material eliminado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al eliminar el material: ' . $e->getMessage()]);
        }
    }
}
