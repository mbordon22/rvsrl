<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VehiculoController;
use App\Http\Controllers\Admin\Epp\EppElementoController;
use App\Http\Controllers\Admin\Epp\EppEntregasController;
use App\Http\Controllers\Admin\VehiculoCombustibleController;
use App\Http\Controllers\Admin\Inventarios\MaterialController;
use App\Http\Controllers\Admin\Inventarios\AlmacenController;
use App\Http\Controllers\Admin\Vehiculos\TipoVehiculoController;
use App\Http\Controllers\Admin\GestionContable\ClientesController;
use App\Http\Controllers\Admin\GestionContable\MediosPagoController;
use App\Http\Controllers\Admin\GestionContable\CentroCostoController;
use App\Http\Controllers\Admin\GestionContable\ProveedoresController;
use App\Http\Controllers\Admin\GestionContable\ComprobanteEgresoController;
use App\Http\Controllers\Admin\GestionContable\ComprobanteIngresoController;
use App\Http\Controllers\Admin\GestionContable\ParametrosContablesController;
use App\Http\Controllers\Admin\Inventarios\CuadrillaController;
use App\Http\Controllers\Admin\Inventarios\StockMaterialController;

Auth::routes(['register' => false, 'verify' => false]);

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
});

Route::group(['middleware' => ['auth'], 'as' => 'admin.', 'prefix' => 'admin'], function () {
    // Dashboard
    Route::get('dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('usuarios/status/{id}', [UserController::class, 'status'])->name('user.status');
    Route::get('usuarios', [UserController::class, 'index'])->name('user.index');
    Route::get('usuarios/nuevo', [UserController::class, 'create'])->name('user.create');
    Route::get('usuarios/{user}/editar', [UserController::class, 'edit'])->name('user.edit');
    Route::post('usuarios', [UserController::class, 'store'])->name('user.store');
    Route::put('usuarios/{user}', [UserController::class, 'update'])->name('user.update');
    Route::delete('usuarios/{user}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::get('usuarios/remove-image/{id}', [UserController::class, 'removeImage'])->name('user.removeImage');
    
    // Roles
    Route::get('roles/nuevo', [RoleController::class, 'create'])->name('role.create');
    Route::get('roles/{role}/editar', [RoleController::class, 'edit'])->name('role.edit');
    Route::resource('roles', RoleController::class)->names('role')->except('create', 'edit');

    // Vehiculos
    Route::get('vehiculos', [VehiculoController::class, 'index'])->name('vehiculo.index');
    Route::get('vehiculos/nuevo', [VehiculoController::class, 'create'])->name('vehiculo.create');
    Route::post('vehiculos', [VehiculoController::class, 'store'])->name('vehiculo.store');
    Route::put('vehiculos/{id}', [VehiculoController::class, 'update'])->name('vehiculo.update');
    Route::get('vehiculos/{vehiculo}/editar', [VehiculoController::class, 'edit'])->name('vehiculo.edit');
    Route::put('vehiculos/status/{vehiculo}', [VehiculoController::class, 'status'])->name('vehiculo.status');
    Route::delete('vehiculos/{vehiculo}', [VehiculoController::class, 'destroy'])->name('vehiculo.destroy');
    Route::get('vehiculos/remove-image/{id}', [VehiculoController::class, 'removeImage'])->name('vehiculo.removeImage');
    
    //Tipos de vehiculos
    Route::get('vehiculos/tipos-vehiculo', [TipoVehiculoController::class, 'index'])->name('vehiculo.tiposVehiculo.index');
    Route::post('vehiculos/tipos-vehiculo', [TipoVehiculoController::class, 'store'])->name('vehiculo.tiposVehiculo.store');
    Route::delete('vehiculos/tipos-vehiculo/{id}', [TipoVehiculoController::class, 'destroy'])->name('vehiculo.tiposVehiculo.destroy');

    //Vehiculos documentación
    Route::get('vehiculos/{vehiculo}/documentacion', [VehiculoController::class, 'documentos'])->name('vehiculo.documento.index');
    Route::get('vehiculos/documentacion/{id}', [VehiculoController::class, 'getDocumento'])->name('admin.vehiculo.documento.get');
    Route::post('vehiculos/{vehiculo}/documentacion', [VehiculoController::class, 'storeDocumento'])->name('vehiculo.documento.store');
    Route::put('vehiculos/documentacion/status/{documento}', [VehiculoController::class, 'statusDocumento'])->name('vehiculo.documento.status');
    Route::put('vehiculos/documentacion/{documento}', [VehiculoController::class, 'updateDocumento'])->name('vehiculo.documento.update');
    Route::delete('vehiculos/documentacion/{documento}', [VehiculoController::class, 'destroyDocumento'])->name('vehiculo.documento.destroy');
    
    //Vehiculos combustible
    Route::get('vehiculos/{vehiculo}/combustible', [VehiculoCombustibleController::class, 'index'])->name('vehiculo.combustible.index');
    Route::get('vehiculos/combustible/{id}', [VehiculoCombustibleController::class, 'show'])->name('vehiculo.combustible.show');
    Route::post('vehiculos/{vehiculo}/combustible', [VehiculoCombustibleController::class, 'store'])->name('vehiculo.combustible.store');
    Route::put('vehiculos/combustible/{combustible}', [VehiculoCombustibleController::class, 'update'])->name('vehiculo.combustible.update');
    Route::delete('vehiculos/combustible/{combustible}', [VehiculoCombustibleController::class, 'destroy'])->name('vehiculo.combustible.destroy');

    //Cargas Combustible (independiente)
    Route::prefix('cargas-combustible')->name('cargas-combustible.')->group(function () {
        Route::get('/', [VehiculoCombustibleController::class, 'indexCargas'])->name('index');
        Route::post('/', [VehiculoCombustibleController::class, 'storeCargas'])->name('store');
        Route::put('/{combustible}', [VehiculoCombustibleController::class, 'updateCargas'])->name('update');
        Route::delete('/{combustible}', [VehiculoCombustibleController::class, 'destroyCargas'])->name('destroy');
    });

    // Listado EPP
    Route::prefix('epp/elementos')->name('epp.elementos.')->group(function () {
        Route::get('/', [EppElementoController::class, 'index'])->name('index');
        Route::get('/nuevo', [EppElementoController::class, 'create'])->name('create');
        Route::put('/status/{epp}', [EppElementoController::class, 'status'])->name('status');
        Route::post('/', [EppElementoController::class, 'store'])->name('store');
        Route::get('/{epp}', [EppElementoController::class, 'show'])->name('show');
        Route::get('/{epp}/editar', [EppElementoController::class, 'edit'])->name('edit');
        Route::put('/{epp}', [EppElementoController::class, 'update'])->name('update');
        Route::delete('/{epp}', [EppElementoController::class, 'destroy'])->name('destroy');
    });

    // Listado Entregas EPP
    Route::prefix('epp/entregas')->name('epp.entregas.')->group(function () {
        Route::get('/', [EppEntregasController::class, 'index'])->name('index');
        Route::get('/nuevo', [EppEntregasController::class, 'create'])->name('create');
        Route::post('/', [EppEntregasController::class, 'store'])->name('store');
        Route::get('/{entrega}/editar', [EppEntregasController::class, 'edit'])->name('edit');
        Route::put('/{entrega}', [EppEntregasController::class, 'update'])->name('update');
        Route::get('/{entrega}', [EppEntregasController::class, 'show'])->name('show');
        Route::delete('/remove-fila/{id}', [EppEntregasController::class, 'removeFila'])->name('removeFila');
        Route::delete('/{entrega}', [EppEntregasController::class, 'destroy'])->name('destroy');
        Route::get('/pdf/{entrega}', [EppEntregasController::class, 'reportPDF'])->name('reportPDF');
    });

    //Modulo Gestion Contable
    Route::prefix('gestion-contable')->name('gestion-contable.')->group(function () {

        Route::prefix('egresos')->name('egresos.')->group(function () {
        
            Route::prefix('proveedores')->name('proveedores.')->group(function () {
                Route::get('/', [ProveedoresController::class, 'index'])->name('index');
                Route::get('/nuevo', [ProveedoresController::class, 'create'])->name('create');
                Route::post('/', [ProveedoresController::class, 'store'])->name('store');
                Route::get('/{id}/editar', [ProveedoresController::class, 'edit'])->name('edit');
                Route::put('/status/{id}', [ProveedoresController::class, 'status'])->name('status');
                Route::put('/{id}', [ProveedoresController::class, 'update'])->name('update');
                Route::get('/{id}', [ProveedoresController::class, 'show'])->name('show');
                Route::delete('/{id}', [ProveedoresController::class, 'destroy'])->name('destroy');
            });

            Route::prefix('comprobantes')->name('comprobantes.')->group(function () {
                Route::get('/', [ComprobanteEgresoController::class, 'index'])->name('index');
                Route::get('/nuevo', [ComprobanteEgresoController::class, 'create'])->name('create');
                Route::post('/', [ComprobanteEgresoController::class, 'store'])->name('store');
                Route::get('/{id}/editar', [ComprobanteEgresoController::class, 'edit'])->name('edit');
                Route::put('/status/{id}', [ComprobanteEgresoController::class, 'status'])->name('status');
                Route::put('/{id}', [ComprobanteEgresoController::class, 'update'])->name('update');
                Route::get('/{id}', [ComprobanteEgresoController::class, 'show'])->name('show');
                Route::delete('/{id}', [ComprobanteEgresoController::class, 'destroy'])->name('destroy');
            });
        });

        Route::prefix('ingresos')->name('ingresos.')->group(function () {

            Route::prefix('clientes')->name('clientes.')->group(function () {
                Route::get('/', [ClientesController::class, 'index'])->name('index');
                Route::get('/nuevo', [ClientesController::class, 'create'])->name('create');
                Route::post('/', [ClientesController::class, 'store'])->name('store');
                Route::get('/{id}/editar', [ClientesController::class, 'edit'])->name('edit');
                Route::put('/status/{id}', [ClientesController::class, 'status'])->name('status');
                Route::put('/{id}', [ClientesController::class, 'update'])->name('update');
                Route::get('/{id}', [ClientesController::class, 'show'])->name('show');
                Route::delete('/{id}', [ClientesController::class, 'destroy'])->name('destroy');
            });

            Route::prefix('comprobantes')->name('comprobantes.')->group(function () {
                Route::get('/', [ComprobanteIngresoController::class, 'index'])->name('index');
                Route::get('/nuevo', [ComprobanteIngresoController::class, 'create'])->name('create');
                Route::post('/', [ComprobanteIngresoController::class, 'store'])->name('store');
                Route::get('/{id}/editar', [ComprobanteIngresoController::class, 'edit'])->name('edit');
                Route::put('/status/{id}', [ComprobanteIngresoController::class, 'status'])->name('status');
                Route::put('/{id}', [ComprobanteIngresoController::class, 'update'])->name('update');
                Route::get('/{id}', [ComprobanteIngresoController::class, 'show'])->name('show');
                Route::delete('/{id}', [ComprobanteIngresoController::class, 'destroy'])->name('destroy');
            });
        });

        Route::prefix('centro-costo')->name('centro-costo.')->group(function () {
            Route::get('/', [CentroCostoController::class, 'index'])->name('index');
            Route::get('/{id}', [CentroCostoController::class, 'show'])->name('show');
            Route::post('/', [CentroCostoController::class, 'store'])->name('store');
            Route::put('/status/{id}', [CentroCostoController::class, 'status'])->name('status');
            Route::put('/{id}', [CentroCostoController::class, 'update'])->name('update');
            Route::delete('/{id}', [CentroCostoController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('parametros')->name('parametros.')->group(function () {
            Route::get('/', [ParametrosContablesController::class, 'index'])->name('index');
            Route::put('/{id}', [ParametrosContablesController::class, 'update'])->name('update');
        });

        Route::prefix('medio-pago')->name('medio-pago.')->group(function () {
            Route::get('/', [MediosPagoController::class, 'index'])->name('index');
            Route::get('/{id}', [MediosPagoController::class, 'show'])->name('show');
            Route::post('/', [MediosPagoController::class, 'store'])->name('store');
            Route::put('/status/{id}', [MediosPagoController::class, 'status'])->name('status');
            Route::put('/{id}', [MediosPagoController::class, 'update'])->name('update');
            Route::delete('/{id}', [MediosPagoController::class, 'destroy'])->name('destroy');
        });

    });

    //Modulo Inventarios y Stock
    Route::prefix('inventarios')->name('inventarios.')->group(function () {
        Route::prefix('materiales')->name('materiales.')->group(function () {
            Route::get('/', [MaterialController::class, 'index'])->name('index');
            Route::get('/nuevo', [MaterialController::class, 'create'])->name('create');
            Route::post('/', [MaterialController::class, 'store'])->name('store');
            Route::get('/{id}/editar', [MaterialController::class, 'edit'])->name('edit');
            Route::put('/status/{id}', [MaterialController::class, 'status'])->name('status');
            Route::put('/{id}', [MaterialController::class, 'update'])->name('update');
            Route::delete('/{id}', [MaterialController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('almacenes')->name('almacenes.')->group(function () {
            Route::get('/', [AlmacenController::class, 'index'])->name('index');
            Route::get('/nuevo', [AlmacenController::class, 'create'])->name('create');
            Route::post('/', [AlmacenController::class, 'store'])->name('store');
            Route::get('/{id}/editar', [AlmacenController::class, 'edit'])->name('edit');
            Route::put('/status/{id}', [AlmacenController::class, 'status'])->name('status');
            Route::put('/{id}', [AlmacenController::class, 'update'])->name('update');
            Route::delete('/{id}', [AlmacenController::class, 'destroy'])->name('destroy');
        });
        
        Route::prefix('cuadrillas')->name('cuadrillas.')->group(function () {
            Route::get('/', [CuadrillaController::class, 'index'])->name('index');
            Route::get('/nuevo', [CuadrillaController::class, 'create'])->name('create');
            Route::post('/', [CuadrillaController::class, 'store'])->name('store');
            Route::get('/{id}/editar', [CuadrillaController::class, 'edit'])->name('edit');
            Route::put('/status/{id}', [CuadrillaController::class, 'status'])->name('status');
            Route::put('/{id}', [CuadrillaController::class, 'update'])->name('update');
            Route::delete('/{id}', [CuadrillaController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('stock')->name('stock.')->group(function () {
            Route::get('/', [StockMaterialController::class, 'index'])->name('index');
            Route::get('/nuevo', [StockMaterialController::class, 'create'])->name('create');
            Route::get('/nuevoIngreso', [StockMaterialController::class, 'createIngreso'])->name('createIngreso');
            Route::get('/nuevoEgreso', [StockMaterialController::class, 'createEgreso'])->name('createEgreso');
            Route::get('/nuevoTransferencia', [StockMaterialController::class, 'createTransferencia'])->name('createTransferencia');
            Route::get('/nuevoAjuste', [StockMaterialController::class, 'createAjuste'])->name('createAjuste');
            Route::get('/historial/{id}', [StockMaterialController::class, 'historial'])->name('historial');
            
            Route::post('/nuevoIngreso', [StockMaterialController::class, 'storeIngreso'])->name('storeIngreso');
            Route::post('/nuevoEgreso', [StockMaterialController::class, 'storeEgreso'])->name('storeEgreso');
            Route::post('/nuevoTransferencia', [StockMaterialController::class, 'storeTransferencia'])->name('storeTransferencia');
            Route::post('/nuevoAjuste', [StockMaterialController::class, 'storeAjuste'])->name('storeAjuste');

            Route::get('/getMaterialByAlmacen/{id}/{almacenId}', [StockMaterialController::class, 'getMaterialByAlmacen'])->name('getMaterialByAlmacen');
            Route::get('/getMaterialByCuadrilla/{id}/{cuadrillaId}', [StockMaterialController::class, 'getMaterialByCuadrilla'])->name('getMaterialByCuadrilla');
            Route::get('/getMaterialsByAlmacen/{almacenId}', [StockMaterialController::class, 'getMaterialsByAlmacen'])->name('getMaterialsByAlmacen');
            Route::get('/getMaterialsByCuadrilla/{cuadrillaId}', [StockMaterialController::class, 'getMaterialsByCuadrilla'])->name('getMaterialsByCuadrilla');
        });
    });

    // Pages
    Route::put('page/status/{id}', [App\Http\Controllers\Admin\PageController::class, 'status'])->name('page.status');
    Route::resource('page', App\Http\Controllers\Admin\PageController::class);

    // Tags
    Route::put('tag/status/{id}', [App\Http\Controllers\Admin\TagController::class, 'status'])->name('tag.status');
    Route::resource('tag', App\Http\Controllers\Admin\TagController::class);

    // Blog
    Route::put('blog/status/{id}', [App\Http\Controllers\Admin\BlogController::class, 'status'])->name('blog.status');
    Route::resource('blog', App\Http\Controllers\Admin\BlogController::class);
    Route::get('blog/remove-image/{id}', [App\Http\Controllers\Admin\BlogController::class, 'removeImage'])->name('blog.removeImage');

    // Category
    Route::post('category/update-orders', [App\Http\Controllers\Admin\CategoryController::class, 'updateOrders'])->name('category.update.orders');
    Route::resource('category', App\Http\Controllers\Admin\CategoryController::class);

    // User_profile
    Route::get('edit-profile', [App\Http\Controllers\Admin\UserController::class, 'editProfile'])->name('user.edit-profile');
    Route::get('get-states', [App\Http\Controllers\Admin\UserController::class, 'getStates'])->name('user.get-states');
    Route::post('update-profile', [App\Http\Controllers\Admin\UserController::class, 'updateProfile'])->name('user.update-profile');

    //widgets
    Route::view('general-widget', 'widgets.general_widget')->name('general_widget');
    Route::view('chart-widget', 'widgets.chart_widget')->name('chart_widget');

    //page_layout
    Route::view('box-layout', 'page_layouts.box_layout')->name('box_layout');
    Route::view('rtl-layout', 'page_layouts.rtl_layout')->name('rtl_layout');
    Route::view('dark-layout', 'page_layouts.dark_layout')->name('dark_layout');
    Route::view('hide-on-scroll', 'page_layouts.hide_on_scroll')->name('hide_on_scroll');

    //projects
    Route::view('project-list', 'projects.list_project')->name('list_project');
    Route::view('project-create', 'projects.create_project')->name('create_project');

    //file manager
    Route::view('file-manager', 'file_manager')->name('file_manager');

    //users
    Route::view('profile', 'users.user_profile')->name('user_profile');
    Route::view('edit-profile-user', 'users.edit_profile')->name('edit_profile');
    Route::view('cards', 'users.user_cards')->name('user_cards');

    //learning
    Route::view('learning-list-view', 'learning.learning_list_view')->name('learning_list_view');
    Route::view('learning-detailed', 'learning.learning_detailed')->name('learning_detailed');
});
