<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{AuthenticatedSessionController, RegistroController};
use App\Http\Controllers\{InventarioController, ProduccionController, ReportesController, EmpaquetadoController, MateriaPrimaController, RecetaController, ProduccionRealController, ControlCalidadController, NotificacionController, EnvioController, TrazabilidadController};
use App\Http\Controllers\Admin\UserController as AdminUserController;

// ===========================================
// 1. RUTAS DE INVITADOS (NO AUTENTICADOS)
// ===========================================
Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegistroController::class, 'create'])->name('register');
    Route::post('/register', [RegistroController::class, 'store']);
});

Route::get('/registro-exitoso', function () {
    return view('auth.registered');
})->name('register.success');

// ===========================================
// 2. RUTAS PROTEGIDAS (AUTENTICADAS)
// ===========================================
Route::middleware(['auth', 'user.status'])->group(function () {

    // ---------- LOGOUT Y DASHBOARD ----------
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', function () {
        return redirect()->route('inventario');
    })->name('dashboard');

    // ===========================================
    // NOTIFICACIONES
    // ===========================================
    Route::prefix('notificaciones')->name('notificaciones.')->group(function () {
        Route::get('/', [NotificacionController::class, 'index'])->name('index');
        Route::post('/marcar/{id}', [NotificacionController::class, 'marcar'])->name('marcar');
        Route::post('/marcar-todas', [NotificacionController::class, 'marcarTodas'])->name('marcar-todas');
        Route::get('/count', function() {
            return response()->json([
                'count' => App\Models\Alerta::where('usuario_id', auth()->id())->where('leida', false)->count()
            ]);
        })->name('count');
    });

    // ===========================================
    // INVENTARIO
    // ===========================================
    
    // Productos Terminados
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario');
    Route::get('/productos/{producto}/mermas', [InventarioController::class, 'historial'])->name('productos.mermas');
    Route::get('/qr-imagen/{producto}', [InventarioController::class, 'qrImagen'])->name('qr.imagen')->middleware('role:admin,operario');
    
    // Gestión de productos (solo admin)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/productos/create', [InventarioController::class, 'create'])->name('productos.create');
        Route::post('/productos', [InventarioController::class, 'store'])->name('productos.store');
        Route::get('/productos/{producto}/edit', [InventarioController::class, 'edit'])->name('productos.edit');
        Route::put('/productos/{producto}', [InventarioController::class, 'update'])->name('productos.update');
        Route::delete('/productos/{producto}', [InventarioController::class, 'destroy'])->name('productos.destroy');
    });

    // Recetas por producto
    Route::prefix('productos/{producto}')->name('recetas.')->middleware(['role:admin'])->group(function () {
        Route::get('/recetas', [RecetaController::class, 'index'])->name('index');
        Route::post('/recetas', [RecetaController::class, 'store'])->name('store');
        Route::delete('/recetas/{materia_prima_id}', [RecetaController::class, 'destroy'])->name('destroy');
    });

    // Recetas - Listado General
    Route::prefix('recetas')->name('recetas.')->middleware(['role:admin'])->group(function () {
        Route::get('/todas', [RecetaController::class, 'todas'])->name('todas');
        Route::post('/store', [RecetaController::class, 'storeGeneral'])->name('storeGeneral');
        Route::delete('/{producto_id}/{materia_prima_id}', [RecetaController::class, 'destroyGeneral'])->name('destroyGeneral');
    });

    // Materia Prima
    Route::get('/materia-prima', [MateriaPrimaController::class, 'index'])->name('materia-prima.index');
    Route::get('/materia-prima/{materia_prima}/movimientos', [MateriaPrimaController::class, 'movimientos'])->name('materia-prima.movimientos');
    Route::get('/materia-prima/por-vencer', [MateriaPrimaController::class, 'porVencer'])->name('materia-prima.por-vencer');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/materia-prima/create', [MateriaPrimaController::class, 'create'])->name('materia-prima.create');
        Route::post('/materia-prima', [MateriaPrimaController::class, 'store'])->name('materia-prima.store');
        Route::get('/materia-prima/{materia_prima}/edit', [MateriaPrimaController::class, 'edit'])->name('materia-prima.edit');
        Route::put('/materia-prima/{materia_prima}', [MateriaPrimaController::class, 'update'])->name('materia-prima.update');
        Route::delete('/materia-prima/{materia_prima}', [MateriaPrimaController::class, 'destroy'])->name('materia-prima.destroy');
        Route::get('/materia-prima/{materia_prima}/entrada', [MateriaPrimaController::class, 'entradaForm'])->name('materia-prima.entrada');
        Route::post('/materia-prima/{materia_prima}/entrada', [MateriaPrimaController::class, 'registrarEntrada'])->name('materia-prima.registrar-entrada');
        Route::get('/materia-prima/{materia_prima}/salida', [MateriaPrimaController::class, 'salidaForm'])->name('materia-prima.salida');
        Route::post('/materia-prima/{materia_prima}/salida', [MateriaPrimaController::class, 'registrarSalida'])->name('materia-prima.registrar-salida');
    });

    // Buscador QR
    Route::get('/buscar-producto', function (Illuminate\Http\Request $request) {
        $codigo = $request->input('codigo');
        $producto = App\Models\Producto::where('sku', $codigo)->first();
        if ($producto) {
            return response()->json(['id' => $producto->id, 'nombre' => $producto->nombre, 'sku' => $producto->sku]);
        }
        return response()->json(['error' => 'Producto no encontrado'], 404);
    })->name('buscar.producto')->middleware('role:admin,operario');

    // ===========================================
    // PRODUCCIÓN
    // ===========================================
    Route::middleware(['role:admin,operario'])->group(function () {
        Route::get('/produccion-real', [ProduccionRealController::class, 'create'])->name('produccion_real.create');
        Route::post('/produccion-real', [ProduccionRealController::class, 'store'])->name('produccion_real.store');
        Route::get('/produccion-real/historial', [ProduccionRealController::class, 'historial'])->name('produccion_real.historial');
        Route::get('/produccion', [ProduccionController::class, 'index'])->name('produccion');
        Route::post('/produccion/merma', [ProduccionController::class, 'storeMerma'])->name('produccion.merma.store');
        Route::get('/produccion/rapida/{producto}', [ProduccionController::class, 'rapida'])->name('produccion.rapida');
    });

    // ===========================================
    // EMPAQUETADO
    // ===========================================
    Route::middleware(['role:admin,operario'])->group(function () {
        Route::get('/empaquetado', [EmpaquetadoController::class, 'index'])->name('empaquetado');
        Route::post('/empaquetado/merma', [EmpaquetadoController::class, 'storeMerma'])->name('empaquetado.merma.store');
        Route::get('/empaquetado/rapida/{producto}', [EmpaquetadoController::class, 'rapida'])->name('empaquetado.rapida');
        Route::get('/escanear-merma', function () {
            return view('escanear-merma');
        })->name('escanear.merma');
    });

    // ===========================================
    // CONTROL DE CALIDAD
    // ===========================================
    Route::middleware(['role:admin,operario,auditor,analista'])->prefix('control-calidad')->name('control-calidad.')->group(function () {
        Route::get('/', [ControlCalidadController::class, 'index'])->name('index');
        Route::get('/create', [ControlCalidadController::class, 'create'])->name('create');
        Route::post('/', [ControlCalidadController::class, 'store'])->name('store');
        Route::get('/{controlCalidad}', [ControlCalidadController::class, 'show'])->name('show');
    });

    // ===========================================
    // DISTRIBUCIÓN / ENVÍOS
    // ===========================================
    Route::middleware(['role:admin,operario'])->prefix('envios')->name('envios.')->group(function () {
        Route::get('/', [EnvioController::class, 'index'])->name('index');
        Route::get('/create', [EnvioController::class, 'create'])->name('create');
        Route::post('/', [EnvioController::class, 'store'])->name('store');
        Route::get('/historial', [EnvioController::class, 'historial'])->name('historial');
        Route::get('/{envio}', [EnvioController::class, 'show'])->name('show');
        Route::patch('/{envio}/estado', [EnvioController::class, 'updateEstado'])->name('update-estado');
    });

    // ===========================================
    // REPORTES Y AUDITORÍA
    // ===========================================
    Route::middleware(['role:admin,operario,auditor,analista,empaquetador'])->group(function () {
        Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes');
        Route::get('/reportes/exportar-pdf', [ReportesController::class, 'exportPdf'])->name('reportes.export.pdf');
        Route::get('/reportes/exportar-csv', [ReportesController::class, 'exportCsv'])->name('reportes.export.csv');
        Route::get('/reportes/exportar-excel', [ReportesController::class, 'exportExcel'])->name('reportes.export.excel');
    });

    // ===========================================
    // TRAZABILIDAD
    // ===========================================
    Route::middleware(['role:admin,operario,auditor,analista'])->prefix('trazabilidad')->name('trazabilidad.')->group(function () {
        Route::get('/', [TrazabilidadController::class, 'index'])->name('index');
        Route::get('/buscar', [TrazabilidadController::class, 'buscar'])->name('buscar');
    });

    // ===========================================
    // ADMINISTRACIÓN DE USUARIOS
    // ===========================================
    Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {
        Route::get('/usuarios', [AdminUserController::class, 'index'])->name('usuarios');
        Route::get('/usuarios/{user}/approve', [AdminUserController::class, 'approveForm'])->name('usuarios.approve');
        Route::post('/usuarios/{user}/approve', [AdminUserController::class, 'approve'])->name('usuarios.approve.post');
        Route::post('/usuarios/{user}/reject', [AdminUserController::class, 'reject'])->name('usuarios.reject');
        Route::post('/usuarios/{user}/toggle-suspend', [AdminUserController::class, 'toggleSuspend'])->name('usuarios.toggle-suspend');
        Route::get('/usuarios/{user}/historial', [AdminUserController::class, 'historial'])->name('usuarios.historial');
    });
});

// ===========================================
// 3. REDIRECCIÓN POR DEFECTO
// ===========================================
Route::redirect('/', '/login');