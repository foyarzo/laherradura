<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// ✅ Para enviar correo y loguear si falla
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use App\Models\Product;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\PuntoEncuentro;
use App\Models\User;

// ✅ Mailable para notificar a admins
use App\Mail\PedidoCreadoAdminMail;

class CartController extends Controller
{
    private function cartKey(): string
    {
        return 'cart';
    }

    public function index(Request $request)
    {
        $cart  = session()->get($this->cartKey(), []);
        $items = array_values($cart);

        $subtotal = 0;
        foreach ($items as $it) {
            $subtotal += ((int)($it['price'] ?? 0)) * ((int)($it['qty'] ?? 1));
        }

        $puntosEncuentro = PuntoEncuentro::query()
            ->where('activo', 1)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        return view('tienda.carrito', [
            'items'           => $items,
            'subtotal'        => $subtotal,
            'puntosEncuentro' => $puntosEncuentro,
        ]);
    }

    public function add(Request $request, Product $product)
    {
        if (!(bool)($product->is_active ?? false)) {
            return back()->with('error', 'Producto inactivo.');
        }

        $stock = (int)($product->stock ?? 0);
        if ($stock <= 0) {
            return back()->with('error', 'Producto sin stock.');
        }

        $cart = session()->get($this->cartKey(), []);
        $id   = (int)$product->id;

        $qtyToAdd = (int)($request->input('qty', 1));
        if ($qtyToAdd < 1) $qtyToAdd = 1;

        $currentQty = isset($cart[$id]) ? (int)$cart[$id]['qty'] : 0;
        $newQty     = $currentQty + $qtyToAdd;

        if ($newQty > $stock) $newQty = $stock;

        $fallback = asset('assets/img/no_image.png');
        $src      = $product->image ? asset($product->image) : $fallback;

        $cart[$id] = [
            'id'        => $id,
            'name'      => (string)$product->name,
            'price'     => (int)($product->price ?? 0),
            'stock'     => $stock,
            'image'     => $src,
            'qty'       => $newQty,
            'is_active' => (bool)($product->is_active ?? false),
        ];

        session()->put($this->cartKey(), $cart);

        return back()->with('success', 'Agregado al carrito.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'  => ['required', 'integer'],
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $id  = (int)$request->id;
        $qty = (int)$request->qty;

        $cart = session()->get($this->cartKey(), []);
        if (!isset($cart[$id])) {
            return back()->with('error', 'Ítem no encontrado en el carrito.');
        }

        $product = Product::find($id);
        if (!$product || !(bool)($product->is_active ?? false)) {
            return back()->with('error', 'Producto no disponible.');
        }

        $stock = (int)($product->stock ?? 0);
        if ($stock <= 0) {
            unset($cart[$id]);
            session()->put($this->cartKey(), $cart);
            return back()->with('error', 'Producto quedó sin stock y se removió del carrito.');
        }

        if ($qty > $stock) $qty = $stock;

        $cart[$id]['qty']   = $qty;
        $cart[$id]['stock'] = $stock;
        $cart[$id]['price'] = (int)($product->price ?? 0);

        session()->put($this->cartKey(), $cart);

        return back()->with('success', 'Carrito actualizado.');
    }

    public function remove(Product $product)
    {
        $cart = session()->get($this->cartKey(), []);
        $id   = (int)$product->id;

        unset($cart[$id]);
        session()->put($this->cartKey(), $cart);

        return back()->with('success', 'Ítem eliminado.');
    }

    public function clear()
    {
        session()->forget($this->cartKey());
        return back()->with('success', 'Carrito vacío.');
    }

public function checkout(Request $request)
{
    $data = $request->validate([
        'punto_encuentro_id'     => ['required', 'integer'],
        'hora_estimada_cliente'  => ['required', 'date'],
        'mensaje_cliente'        => ['nullable', 'string', 'max:5000'],

        // ✅ comprobante obligatorio
        'comprobante'            => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
    ]);

    $punto = PuntoEncuentro::query()
        ->whereKey((int)$data['punto_encuentro_id'])
        ->where('activo', 1)
        ->first();

    if (!$punto) {
        return back()
            ->with('error', 'El punto de encuentro seleccionado no está disponible.')
            ->withInput();
    }

    // ✅ VALIDAR HORARIO EN BACKEND (NO SE PUEDE SALTAR)
    try {
        // viene validado como date, Laravel lo parsea ok, pero lo normalizamos
        $dt = \Carbon\Carbon::parse($data['hora_estimada_cliente']);

        // opcional: si quieres forzar TZ app
        // $dt = $dt->setTimezone(config('app.timezone'));

        if (!$punto->estaDisponibleEn($dt)) {
            return back()
                ->withErrors([
                    'hora_estimada_cliente' => 'La fecha/hora elegida no calza con el horario del punto seleccionado.'
                ])
                ->withInput();
        }
    } catch (\Throwable $e) {
        return back()
            ->withErrors([
                'hora_estimada_cliente' => 'Fecha/hora inválida. Selecciona nuevamente.'
            ])
            ->withInput();
    }

    $puntoFinal = trim(
        ($punto->nombre ?? '') .
        (!empty($punto->direccion) ? ' — ' . $punto->direccion : '')
    );

    $cart = session()->get($this->cartKey(), []);
    if (empty($cart)) {
        return back()->with('error', 'Tu carrito está vacío.');
    }

    $user = $request->user();

    $comprobantePath = null;

    try {
        $file = $request->file('comprobante');
        $filename = 'comp_' . $user->id . '_' . now()->format('Ymd_His') . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
        $comprobantePath = $file->storeAs('comprobantes', $filename, 'public');

        $resolvedForMail = [];
        $pedidoItemsForMail = [];

        $pedido = DB::transaction(function () use ($cart, $user, $data, $punto, $puntoFinal, $comprobantePath, &$resolvedForMail, &$pedidoItemsForMail) {

            $total    = 0;
            $resolved = [];

            foreach ($cart as $row) {
                $productId = (int)($row['id'] ?? 0);
                $qty       = max(1, (int)($row['qty'] ?? 1));

                $product = Product::query()
                    ->whereKey($productId)
                    ->lockForUpdate()
                    ->first();

                if (!$product || !(bool)($product->is_active ?? false)) {
                    throw new \RuntimeException('Hay productos no disponibles en tu carrito.');
                }

                $stock = (int)($product->stock ?? 0);
                if ($stock < $qty) {
                    throw new \RuntimeException("Stock insuficiente para {$product->name}. Disponible: {$stock}");
                }

                $price     = (int)($product->price ?? 0);
                $lineTotal = $price * $qty;

                $total += $lineTotal;

                $resolved[] = [
                    'product'  => $product,
                    'qty'      => $qty,
                    'price'    => $price,
                    'subtotal' => $lineTotal,
                ];
            }

            $pedido = Pedido::create([
                'user_id'               => $user->id,
                'estado'                => 'pendiente_aprobacion',
                'total'                 => $total,

                'punto_encuentro_id'    => (int)$punto->id,
                'punto_encuentro'       => $puntoFinal,

                'hora_estimada_cliente' => $data['hora_estimada_cliente'],
                'mensaje_cliente'       => $data['mensaje_cliente'] ?? null,

                'comprobante_path'      => $comprobantePath,
            ]);

            foreach ($resolved as $it) {
                $pi = PedidoItem::create([
                    'pedido_id'  => $pedido->id,
                    'product_id' => $it['product']->id,
                    'nombre'     => (string)$it['product']->name,
                    'precio'     => $it['price'],
                    'cantidad'   => $it['qty'],
                    'subtotal'   => $it['subtotal'],
                ]);

                $it['product']->decrement('stock', $it['qty']);

                $pedidoItemsForMail[] = [
                    'name'  => (string)$pi->nombre,
                    'price' => (int)$pi->precio,
                    'qty'   => (int)$pi->cantidad,
                ];
            }

            $resolvedForMail = [
                'total' => (int)$total,
            ];

            return $pedido;
        });

        session()->forget($this->cartKey());

        try {
            $adminEmails = User::query()
                ->whereHas('roles', fn($q) => $q->where('slug', 'admin'))
                ->whereNotNull('email')
                ->pluck('email')
                ->unique()
                ->values()
                ->all();

            if (!empty($adminEmails)) {
                $comprobanteUrl = $pedido->comprobante_path
                    ? asset('storage/' . ltrim($pedido->comprobante_path, '/'))
                    : null;

                Mail::to($adminEmails)->send(new \App\Mail\PedidoCreadoAdminMail(
                    pedido: $pedido->loadMissing('user'),
                    items: $pedidoItemsForMail,
                    subtotal: (int)($pedido->total ?? ($resolvedForMail['total'] ?? 0)),
                    puntoNombre: $puntoFinal,
                    horaEstimada: (string)$data['hora_estimada_cliente'],
                    mensajeCliente: $data['mensaje_cliente'] ?? null,
                    comprobanteUrl: $comprobanteUrl
                ));
            }
        } catch (\Throwable $mailError) {
            Log::error('Error enviando correo de pedido a admins', [
                'pedido_id' => $pedido->id ?? null,
                'error' => $mailError->getMessage(),
            ]);
        }

        return redirect()->route('pedidos.show', $pedido->id)
            ->with('success', 'Pedido creado. Quedó pendiente de aprobación.');

    } catch (\Throwable $e) {
        if ($comprobantePath) {
            Storage::disk('public')->delete($comprobantePath);
        }

        return back()->with('error', $e->getMessage())->withInput();
    }
}
}