<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PedidosExport;
use App\Models\Pedido;
use App\Models\Product;
use App\Models\PuntoEncuentro;

class PedidoController extends Controller
{
    private function puntosEncuentroActivos()
    {
        return PuntoEncuentro::query()
            ->where('activo', 1)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();
    }

    public function index(Request $request)
    {
        $query = Pedido::query()
            ->with(['user'])
            ->latest();

        if (!$request->user()->hasRole('admin')) {
            $query->where('user_id', $request->user()->id);
        }

        $pedidos = $query->paginate(20);

        return view('pedidos.index', compact('pedidos'));
    }

    public function admin(Request $request)
    {
        if (!$request->user()->hasRole('admin')) {
            abort(403);
        }

        $pedidos = Pedido::with(['user', 'items'])
            ->orderByRaw("CASE
                WHEN estado = 'pendiente_aprobacion' THEN 0
                WHEN estado = 'aprobado' THEN 1
                WHEN estado = 'rechazado' THEN 2
                ELSE 3
            END")
            ->orderByDesc('created_at')
            ->get();

        $puntosEncuentro = $this->puntosEncuentroActivos();

        return view('pedidos.admin', compact('pedidos', 'puntosEncuentro'));
    }

    public function create()
    {
        $puntosEncuentro = $this->puntosEncuentroActivos();

        return view('pedidos.create', compact('puntosEncuentro'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'punto_encuentro_id' => ['required', 'integer', 'exists:punto_encuentros,id'],
            'hora_estimada_cliente' => ['nullable', 'string', 'max:255'],
            'mensaje_cliente' => ['nullable', 'string', 'max:2000'],
        ]);

        $pedido = Pedido::create([
            'user_id' => $user->id,
            'estado' => 'pendiente_aprobacion',
            'total' => 0,

            'punto_encuentro_id' => (int) $data['punto_encuentro_id'],

            'cliente_nombre' => (string) ($user->name ?? ''),
            'cliente_email'  => (string) ($user->email ?? ''),
            'cliente_rut'    => (string) ($user->rut ?? ''),
            'cliente_phone'  => (string) ($user->phone ?? ''),

            'hora_estimada_cliente' => $data['hora_estimada_cliente'] ?? null,
            'mensaje_cliente' => $data['mensaje_cliente'] ?? null,

            'stock_devuelto' => false,
            'aprobado_por' => null,
            'aprobado_en' => null,
        ]);

        return redirect()
            ->route('pedidos.show', $pedido)
            ->with('success', 'Pedido creado correctamente.');
    }

    public function show(Request $request, Pedido $pedido)
    {
        if (!$request->user()->hasRole('admin') && (int) $pedido->user_id !== (int) $request->user()->id) {
            abort(403);
        }

        $pedido->load(['items', 'user']);

        $puntosEncuentro = $this->puntosEncuentroActivos();

        return view('pedidos.show', compact('pedido', 'puntosEncuentro'));
    }

    public function edit(Request $request, Pedido $pedido)
    {
        if (!$request->user()->hasRole('admin')) {
            abort(403);
        }

        $pedido->load(['items', 'user']);

        $puntosEncuentro = $this->puntosEncuentroActivos();

        return view('pedidos.edit', compact('pedido', 'puntosEncuentro'));
    }

    public function update(Request $request, Pedido $pedido)
    {
        if (!$request->user()->hasRole('admin')) {
            abort(403);
        }

        $data = $request->validate([
            'punto_encuentro_confirmado_id' => ['nullable', 'integer'],
            'hora_estimada_confirmada' => ['nullable', 'string', 'max:255'],
            'mensaje_admin' => ['nullable', 'string', 'max:2000'],
        ]);

        $puntoConfirmadoId = !empty($data['punto_encuentro_confirmado_id'])
            ? (int) $data['punto_encuentro_confirmado_id']
            : null;

        if ($puntoConfirmadoId) {
            $p = PuntoEncuentro::query()
                ->whereKey($puntoConfirmadoId)
                ->where('activo', 1)
                ->first();

            if ($p) {
                $pedido->punto_encuentro_confirmado_id = $p->id;
            }
        } else {
            $pedido->punto_encuentro_confirmado_id = null;
        }

        $pedido->hora_estimada_confirmada = $data['hora_estimada_confirmada'] ?? $pedido->hora_estimada_confirmada;
        $pedido->mensaje_admin = $data['mensaje_admin'] ?? $pedido->mensaje_admin;
        $pedido->save();

        return redirect()
            ->route('pedidos.show', $pedido)
            ->with('success', 'Pedido actualizado correctamente.');
    }

    public function destroy(Request $request, Pedido $pedido)
    {
        if (!$request->user()->hasRole('admin')) {
            abort(403);
        }

        $pedido->delete();

        return redirect()
            ->route('pedidos.index')
            ->with('success', 'Pedido eliminado correctamente.');
    }

    public function aprobar(Request $request, Pedido $pedido)
    {
        if (!$request->user()->hasRole('admin')) {
            abort(403);
        }

        if (($pedido->estado ?? 'pendiente_aprobacion') !== 'pendiente_aprobacion') {
            return back()->with('error', 'Este pedido ya fue procesado.');
        }

        $data = $request->validate([
            'punto_encuentro_confirmado_id' => ['nullable', 'integer'],
            'hora_estimada_confirmada'      => ['nullable', 'string', 'max:255'],
            'mensaje_admin'                 => ['nullable', 'string', 'max:2000'],
        ]);

        $puntoConfirmadoId = !empty($data['punto_encuentro_confirmado_id'])
            ? (int) $data['punto_encuentro_confirmado_id']
            : null;

        if ($puntoConfirmadoId) {
            $p = PuntoEncuentro::query()
                ->whereKey($puntoConfirmadoId)
                ->where('activo', 1)
                ->first();

            $pedido->punto_encuentro_confirmado_id = $p?->id;
        }

        $pedido->estado = 'aprobado';
        $pedido->hora_estimada_confirmada = $data['hora_estimada_confirmada'] ?? $pedido->hora_estimada_confirmada;
        $pedido->mensaje_admin = $data['mensaje_admin'] ?? null;
        $pedido->aprobado_por = $request->user()->id;
        $pedido->aprobado_en = now();
        $pedido->save();

        return back()->with('success', 'Pedido aprobado.');
    }

    public function rechazar(Request $request, Pedido $pedido)
    {
        if (!$request->user()->hasRole('admin')) {
            abort(403);
        }

        if (($pedido->estado ?? 'pendiente_aprobacion') !== 'pendiente_aprobacion') {
            return back()->with('error', 'Este pedido ya fue procesado.');
        }

        $data = $request->validate([
            'mensaje_admin' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $pedido, $data) {
            $pedido = Pedido::query()->lockForUpdate()->findOrFail($pedido->id);

            if (!empty($pedido->stock_devuelto)) {
                $pedido->update([
                    'estado'        => 'rechazado',
                    'mensaje_admin' => $data['mensaje_admin'] ?? null,
                    'aprobado_por'  => $request->user()->id,
                    'aprobado_en'   => now(),
                ]);
                return;
            }

            $pedido->update([
                'estado'        => 'rechazado',
                'mensaje_admin' => $data['mensaje_admin'] ?? null,
                'aprobado_por'  => $request->user()->id,
                'aprobado_en'   => now(),
            ]);

            $pedido->loadMissing('items');

            foreach ($pedido->items as $it) {
                $productId = $it->product_id ?? null;
                $qty       = (int) ($it->cantidad ?? 0);

                if (!$productId || $qty <= 0) continue;

                Product::query()
                    ->whereKey($productId)
                    ->lockForUpdate()
                    ->increment('stock', $qty);
            }

            $pedido->stock_devuelto = true;
            $pedido->save();
        });

        return back()->with('success', 'Pedido rechazado y stock devuelto.');
    }

    public function dashboard(Request $request)
    {
        $estadoMap = [
            'pendiente' => ['pendiente_aprobacion', 'pendiente'],
            'aceptado'  => ['aceptado', 'aprobado', 'confirmado'],
            'cancelado' => ['cancelado', 'rechazado', 'anulado'],
        ];

        $estadoLabels = [
            'pendiente_aprobacion' => 'Pendiente aprobación',
            'pendiente'            => 'Pendiente',
            'aceptado'             => 'Aceptado',
            'aprobado'             => 'Aprobado',
            'confirmado'           => 'Confirmado',
            'cancelado'            => 'Cancelado',
            'rechazado'            => 'Rechazado',
            'anulado'              => 'Anulado',
        ];

        $base = Pedido::query();

        if ($request->filled('desde')) {
            $base->whereDate('created_at', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $base->whereDate('created_at', '<=', $request->hasta);
        }

        if ($request->filled('estado')) {
            $key = $request->estado;
            $estados = $estadoMap[$key] ?? [$key];
            $base->whereIn('estado', $estados);
        }

        $rows = (clone $base)
            ->with('user')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $qKpi = clone $base;

        $totalPedidos = (clone $qKpi)->count();
        $pendientes   = (clone $qKpi)->whereIn('estado', $estadoMap['pendiente'])->count();
        $aceptados    = (clone $qKpi)->whereIn('estado', $estadoMap['aceptado'])->count();
        $cancelados   = (clone $qKpi)->whereIn('estado', $estadoMap['cancelado'])->count();
        $montoTotal   = (clone $qKpi)->whereIn('estado', $estadoMap['aceptado'])->sum('total');

        $kpis = [
            'total'           => $totalPedidos,
            'pendientes'      => $pendientes,
            'aceptados'       => $aceptados,
            'cancelados'      => $cancelados,
            'monto_total'     => $montoTotal,
            'ticket_promedio' => $aceptados > 0 ? (int) round($montoTotal / $aceptados) : 0,
        ];

        $ventasPorDia = (clone $base)
            ->selectRaw('DATE(created_at) as fecha, SUM(CASE WHEN estado IN ("aceptado","aprobado","confirmado") THEN total ELSE 0 END) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha')
            ->get()
            ->map(function ($row) {
                return [
                    'fecha' => $row->fecha,
                    'total' => (int) $row->total,
                ];
            })
            ->values();

        $pedidosPorEstado = (clone $base)
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->orderBy('estado')
            ->get()
            ->map(function ($row) use ($estadoLabels) {
                return [
                    'estado' => $row->estado,
                    'label'  => $estadoLabels[$row->estado] ?? ucfirst(str_replace('_', ' ', $row->estado)),
                    'total'  => (int) $row->total,
                ];
            })
            ->values();

        $topProductos = [];

        return view('pedidos.dashboard', [
            'rows'             => $rows,
            'kpis'             => $kpis,
            'ventasPorDia'     => $ventasPorDia,
            'pedidosPorEstado' => $pedidosPorEstado,
            'filters' => [
                'desde'  => $request->desde,
                'hasta'  => $request->hasta,
                'estado' => $request->estado,
            ],
            'topProductos' => $topProductos,
            'estadoLabels' => $estadoLabels,
        ]);
    }
    public function export(Request $request)
    {
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');
        $estado = $request->input('estado');

        if (!$desde) {
            $primerPedido = Pedido::query()->orderBy('created_at', 'asc')->first();
            $desde = $primerPedido?->created_at
                ? Carbon::parse($primerPedido->created_at)->format('Y-m-d')
                : Carbon::now()->format('Y-m-d');
        }

        if (!$hasta) {
            $hasta = Carbon::now()->format('Y-m-d');
        }

        $fechaGeneracion = Carbon::now()->format('Y-m-d_H-i');

        $partes = [
            'laherradura',
            'reporte',
            'pedidos',
            'desde-' . $desde,
            'hasta-' . $hasta,
        ];

        if ($estado) {
            $partes[] = 'estado-' . $estado;
        }

        $partes[] = 'generado-' . $fechaGeneracion;

        $nombre = implode('_', $partes) . '.xlsx';

        return Excel::download(
            new PedidosExport($request),
            $nombre,
            \Maatwebsite\Excel\Excel::XLSX,
            ['includeCharts' => true]
        );
    }
}