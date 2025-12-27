<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Sale;
use App\Models\SaleDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $r)
    {
        $fechaInicio = $r->fechaInicio ?: Carbon::now()->format('Y-m-d');
        $fechaFin = $r->fechaFin ?: Carbon::now()->format('Y-m-d');
        $userId = $r->user_id ?: null;
        $tipo = $r->tipo ?: null; // Venta|Gasto

        $q = Sale::with([
            'mascota:id,nombre,propietario_nombre',
            'user:id,name,username',
            'details:id,sale_id,productoName,cantidad,precio,subtotal,producto_id',
            'details.producto:id,nombre,tipo',
            'veterinaria',
        ]);

        $q->whereBetween('fecha', [
            Carbon::parse($fechaInicio)->startOfDay(),
            Carbon::parse($fechaFin)->endOfDay()
        ]);

        if ($userId) $q->where('user_id', $userId);
        if ($tipo) $q->where('tipo', $tipo);

        $q->orderByDesc('fecha');

        return $q->get();
    }

    public function show($id)
    {
        return Sale::with([
            'mascota',
            'user',
            'details.producto',
        ])->findOrFail($id);
    }

    /**
     * CREA venta/gasto.
     * Body esperado (como tu front):
     * {
     *  mascota: {id}, total, productos:[{id, nombre, cantidadVenta, precioVenta, tipo}],
     *  comentarioDoctor, pago, tipo ("Venta"|"Gasto"), nombre, ci
     * }
     */
    public function store(Request $r)
    {
        $r->validate([
            'pago' => 'nullable|string',
            'tipo' => 'nullable|string', // Venta|Gasto
            'comentarioDoctor' => 'nullable|string',
            'total' => 'required|numeric',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'nullable|integer',
            'productos.*.nombre' => 'required|string',
            'productos.*.cantidadVenta' => 'required|numeric|min:1',
            'productos.*.precioVenta' => 'required|numeric|min:0',
            'mascota.id' => 'nullable|integer',
            'nombre' => 'nullable|string',
            'ci' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($r) {
            $user = auth()->user();

            $sale = Sale::create([
                'tipo' => $r->tipo ?: 'Venta',
                'pago' => $r->pago ?: 'Efectivo',
                'comentarioDoctor' => $r->comentarioDoctor,
                'fecha' => now(),
                'fechaCreacion' => now(),
                'facturado' => (bool)($r->facturado ?? false),
                'nombre' => $r->nombre,
                'ci' => $r->ci,
                'total' => $r->total,
                'anulado' => false,
                'mascota_id' => data_get($r->mascota, 'id'),
                'user_id' => $user?->id,
                'veterinaria_id' => $user?->veterinaria_id,
            ]);

            foreach ($r->productos as $p) {
                $cantidad = (int)($p['cantidadVenta'] ?? 1);
                $precio = (float)($p['precioVenta'] ?? 0);
                $subtotal = round($cantidad * $precio, 2);

                $detail = SaleDetail::create([
                    'fecha' => now(),
                    'productoName' => $p['nombre'] ?? $p['productoName'] ?? '',
                    'cantidad' => $cantidad,
                    'precio' => $precio,
                    'subtotal' => $subtotal,
                    'sale_id' => $sale->id,
                    'producto_id' => $p['id'] ?? null,
                    'mascota_id' => data_get($r->mascota, 'id'),
                    'user_id' => $user?->id,
                    'anulado' => false,
                ]);

                // STOCK: solo si es Venta y es un producto real con id
                if (($sale->tipo ?? 'Venta') === 'Venta' && !empty($detail->producto_id)) {
                    $prod = Producto::lockForUpdate()->find($detail->producto_id);
                    if ($prod) {
                        $prod->stock = max(0, (int)$prod->stock - $cantidad);
                        $prod->save();
                    }
                }
            }

            // recalcular total (por seguridad)
            $totalCalc = $sale->details()->sum('subtotal');
            $sale->total = $totalCalc;
            $sale->save();

            return [
                'sale' => $sale->load([
                    'mascota:id,nombre,propietario_nombre',
                    'user:id,name,username',
                    'details',
                    'veterinaria',
                ])
            ];
        });
    }

    public function update(Request $r, $id)
    {
        $sale = Sale::with('details')->findOrFail($id);

        if ($sale->anulado) {
            return response()->json(['message' => 'No se puede modificar una venta anulada.'], 422);
        }

        $r->validate([
            'tipo' => 'nullable|string',
            'pago' => 'nullable|string',
            'comentarioDoctor' => 'nullable|string',
            'nombre' => 'nullable|string',
            'ci' => 'nullable|string',
            'productos' => 'nullable|array',
        ]);

        return DB::transaction(function () use ($r, $sale) {
            // revert stock anterior (solo si era Venta)
            if (($sale->tipo ?? 'Venta') === 'Venta') {
                foreach ($sale->details as $d) {
                    if ($d->producto_id) {
                        $prod = Producto::lockForUpdate()->find($d->producto_id);
                        if ($prod) {
                            $prod->stock = (int)$prod->stock + (int)$d->cantidad;
                            $prod->save();
                        }
                    }
                }
            }

            // borrar detalles y recrear si llegan
            $sale->details()->delete();

            $sale->update([
                'tipo' => $r->tipo ?? $sale->tipo,
                'pago' => $r->pago ?? $sale->pago,
                'comentarioDoctor' => $r->comentarioDoctor ?? $sale->comentarioDoctor,
                'nombre' => $r->nombre ?? $sale->nombre,
                'ci' => $r->ci ?? $sale->ci,
            ]);

            $productos = $r->productos ?? [];
            foreach ($productos as $p) {
                $cantidad = (int)($p['cantidadVenta'] ?? 1);
                $precio = (float)($p['precioVenta'] ?? 0);
                $subtotal = round($cantidad * $precio, 2);

                $detail = SaleDetail::create([
                    'fecha' => now(),
                    'productoName' => $p['nombre'] ?? $p['productoName'] ?? '',
                    'cantidad' => $cantidad,
                    'precio' => $precio,
                    'subtotal' => $subtotal,
                    'sale_id' => $sale->id,
                    'producto_id' => $p['id'] ?? null,
                    'mascota_id' => $sale->mascota_id,
                    'user_id' => $sale->user_id,
                    'anulado' => false,
                ]);

                if (($sale->tipo ?? 'Venta') === 'Venta' && !empty($detail->producto_id)) {
                    $prod = Producto::lockForUpdate()->find($detail->producto_id);
                    if ($prod) {
                        $prod->stock = max(0, (int)$prod->stock - $cantidad);
                        $prod->save();
                    }
                }
            }

            $sale->total = $sale->details()->sum('subtotal');
            $sale->save();

            return $sale->load(['mascota','user','details']);
        });
    }

    public function anular($id)
    {
        $sale = Sale::with('details')->findOrFail($id);

        if ($sale->anulado) {
            return response()->json(['message' => 'La venta ya está anulada.'], 422);
        }

        return DB::transaction(function () use ($sale) {
            // revert stock si era Venta
            if (($sale->tipo ?? 'Venta') === 'Venta') {
                foreach ($sale->details as $d) {
                    if ($d->producto_id) {
                        $prod = Producto::lockForUpdate()->find($d->producto_id);
                        if ($prod) {
                            $prod->stock = (int)$prod->stock + (int)$d->cantidad;
                            $prod->save();
                        }
                    }
                }
            }

            $sale->anulado = true;
            $sale->save();

            $sale->details()->update(['anulado' => true]);

            return $sale->load(['mascota','user','details']);
        });
    }
}
