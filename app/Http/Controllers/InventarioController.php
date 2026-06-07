<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Merma;
use App\Helpers\AuditHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::query();

        if ($request->filled('categoria') && $request->categoria != 'Todos') {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        $productos = $query->orderBy('nombre')->paginate(20);
        $stockTotal = Producto::sum('stock_actual');
        $valorTotal = Producto::sum(DB::raw('stock_actual * precio_unitario'));
        
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('inventario._content', compact('productos', 'stockTotal', 'valorTotal'));
        }
        
        return view('inventario.index', compact('productos', 'stockTotal', 'valorTotal'));
    }

    public function create()
    {
        return view('inventario.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'sku' => 'required|unique:products,sku',
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|string',
            'stock_actual' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'unidad' => 'nullable|string',
            'precio_unitario' => 'nullable|numeric|min:0',
        ]);

        $producto = Producto::create([
            'sku' => $request->sku,
            'nombre' => $request->nombre,
            'categoria' => $request->categoria,
            'stock_actual' => $request->stock_actual,
            'stock_minimo' => $request->stock_minimo,
            'unidad' => $request->unidad ?? 'kg',
            'precio_unitario' => $request->precio_unitario ?? 0,
        ]);

        AuditHelper::log('create_producto', $producto, null, $producto->toArray());

        return redirect()->route('inventario')->with('success', 'Producto creado.');
    }

    public function edit(Producto $producto)
    {
        return view('inventario.edit', compact('producto'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'sku' => 'required|unique:products,sku,' . $producto->id,
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|string',
            'stock_actual' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'unidad' => 'nullable|string',
            'precio_unitario' => 'nullable|numeric|min:0',
        ]);

        $oldValues = $producto->toArray();

        $producto->update([
            'sku' => $request->sku,
            'nombre' => $request->nombre,
            'categoria' => $request->categoria,
            'stock_actual' => $request->stock_actual,
            'stock_minimo' => $request->stock_minimo,
            'unidad' => $request->unidad ?? $producto->unidad,
            'precio_unitario' => $request->precio_unitario ?? $producto->precio_unitario,
        ]);

        AuditHelper::log('update_producto', $producto, $oldValues, $producto->toArray());

        return redirect()->route('inventario')->with('success', 'Producto actualizado.');
    }

    public function destroy(Producto $producto)
    {
        $oldValues = $producto->toArray();
        $producto->delete();
        AuditHelper::log('delete_producto', $producto, $oldValues, null);

        return redirect()->route('inventario')->with('success', 'Producto eliminado.');
    }

   public function historial(Producto $producto)
{
    $mermas = $producto->mermas()->with('usuario')->latest()->paginate(10);
    return view('inventario.historial', compact('producto', 'mermas'));
}
    public function qrImagen(Producto $producto)
    {
        $url = route('produccion.rapida', $producto->id);
        $qr = QrCode::format('png')->size(200)->margin(1)->generate($url);
        return response($qr)->header('Content-Type', 'image/png');
    }
}