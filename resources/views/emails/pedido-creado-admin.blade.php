<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
</head>
<body style="margin:0; padding:0; background:#F6FBF4; font-family:Arial, sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0;">
<tr><td align="center">

<table width="640" cellpadding="0" cellspacing="0"
       style="background:#ffffff; border-radius:16px; border:1px solid #DDEEDD;
              box-shadow:0 20px 60px rgba(18,54,23,0.15); overflow:hidden;">

  <tr>
    <td style="background:#0B1A10; padding:22px 28px;">
      <div style="color:#92b95d; font-size:18px; font-weight:700;">La Herradura</div>
      <div style="color:rgba(234,243,234,.75); font-size:13px; margin-top:4px;">
        Nuevo pedido pendiente de aprobación
      </div>
    </td>
  </tr>

  <tr>
    <td style="padding:28px; color:#123617;">
      <h2 style="margin:0 0 10px 0; color:#123617;">Pedido #{{ $pedido->id }}</h2>

      @php
        $u = $pedido->user ?? null;
        $uName  = $u->name ?? '—';
        $uEmail = $u->email ?? '—';
        $uRut   = $u->rut ?? null;
        $uPhone = $u->phone ?? null;

        $tz = 'America/Santiago';
        $createdAt = $pedido->created_at ? $pedido->created_at->timezone($tz)->format('d-m-Y H:i') : null;
        $sentAt = now()->timezone($tz)->format('d-m-Y H:i');

        $pagoMetodo = $pedido->metodo_pago ?? ($pedido->pago_metodo ?? null);
        $pagoEstado = $pedido->estado_pago ?? ($pedido->pago_estado ?? null);

        $direccion = $pedido->direccion ?? null;
        $comuna    = $pedido->comuna ?? null;
        $ciudad    = $pedido->ciudad ?? null;
        $region    = $pedido->region ?? null;

        $notaInterna = $pedido->nota_interna ?? null;

        $extra = [];
        if ($direccion) $extra[] = $direccion;
        if ($comuna)    $extra[] = $comuna;
        if ($ciudad)    $extra[] = $ciudad;
        if ($region)    $extra[] = $region;
        $direccionTxt = trim(implode(', ', $extra));

        $comprobanteUrlFinal = null;
        if (!empty($comprobanteUrl)) {
          $raw = trim((string) $comprobanteUrl);

          $path = parse_url($raw, PHP_URL_PATH);
          $file = basename($path ?: $raw);

          if ($file && $file !== '/' && $file !== '.' && $file !== '..') {
            $comprobanteUrlFinal = route('comprobantes.ver', ['file' => $file]);
          }
        }
      @endphp

      <div style="font-size:14px; color:#3b6a33; line-height:1.6;">
        <div><b>Cliente:</b> {{ $uName }} ({{ $uEmail }})</div>
        @if(!empty($uRut))<div><b>RUT:</b> {{ $uRut }}</div>@endif
        @if(!empty($uPhone))<div><b>Teléfono:</b> {{ $uPhone }}</div>@endif
        @if(!empty($direccionTxt))<div><b>Dirección:</b> {{ $direccionTxt }}</div>@endif
        @if(!empty($createdAt))<div><b>Fecha pedido:</b> {{ $createdAt }} ({{ $tz }})</div>@endif
        <div><b>Enviado:</b> {{ $sentAt }} ({{ $tz }})</div>

        @if(!empty($pagoMetodo))<div><b>Método pago:</b> {{ $pagoMetodo }}</div>@endif
        @if(!empty($pagoEstado))<div><b>Estado pago:</b> {{ $pagoEstado }}</div>@endif

        @if($puntoNombre)<div><b>Punto:</b> {{ $puntoNombre }}</div>@endif
        @if($horaEstimada)<div><b>Hora estimada:</b> {{ $horaEstimada }}</div>@endif
        @if($mensajeCliente)<div><b>Mensaje:</b> {{ $mensajeCliente }}</div>@endif
        @if(!empty($notaInterna))<div><b>Nota interna:</b> {{ $notaInterna }}</div>@endif
      </div>

      <div style="height:16px;"></div>

      <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; font-size:14px;">
        <thead>
          <tr>
            <th align="left" style="padding:10px; background:#EAF6E7; border:1px solid #DDEEDD;">Producto</th>
            <th align="right" style="padding:10px; background:#EAF6E7; border:1px solid #DDEEDD;">Precio</th>
            <th align="right" style="padding:10px; background:#EAF6E7; border:1px solid #DDEEDD;">Cant.</th>
            <th align="right" style="padding:10px; background:#EAF6E7; border:1px solid #DDEEDD;">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          @foreach($items as $it)
            @php
              $name = $it['name'] ?? ($it->name ?? 'Producto');
              $price = (int)($it['price'] ?? ($it->price ?? 0));
              $qty = (int)($it['qty'] ?? ($it->qty ?? 1));
              $sub = $price * $qty;
            @endphp
            <tr>
              <td style="padding:10px; border:1px solid #DDEEDD;">{{ $name }}</td>
              <td align="right" style="padding:10px; border:1px solid #DDEEDD;">$ {{ number_format($price,0,',','.') }}</td>
              <td align="right" style="padding:10px; border:1px solid #DDEEDD;">{{ $qty }}</td>
              <td align="right" style="padding:10px; border:1px solid #DDEEDD;">$ {{ number_format($sub,0,',','.') }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" align="right" style="padding:12px; border:1px solid #DDEEDD; font-weight:700;">Total</td>
            <td align="right" style="padding:12px; border:1px solid #DDEEDD; font-weight:700;">
              $ {{ number_format((int)$subtotal,0,',','.') }}
            </td>
          </tr>
        </tfoot>
      </table>

      <div style="height:18px;"></div>

      @if($comprobanteUrlFinal)
        <div style="font-size:13px; color:#3b6a33; line-height:1.6;">
          <b>Comprobante:</b>
          <a href="{{ $comprobanteUrlFinal }}" target="_blank" rel="noopener noreferrer"
             style="color:#1e4e25; font-weight:700; word-break:break-all;">
            Ver comprobante
          </a>
        </div>

        <div style="margin-top:6px; font-size:11px; color:#3b6a33; opacity:.75; word-break:break-all;">
          {{ $comprobanteUrlFinal }}
        </div>
      @endif

      <div style="text-align:center; margin-top:22px;">
        <a href="{{ url('/admin/pedidos') }}"
           style="background:#1e4e25; color:#fff; padding:12px 18px; border-radius:12px;
                  text-decoration:none; font-weight:700; display:inline-block;">
          Ir a pedidos (Admin)
        </a>
      </div>

    </td>
  </tr>

  <tr>
    <td style="background:#F6FBF4; padding:16px; text-align:center; font-size:12px; color:#3b6a33;">
      © {{ date('Y') }} La Herradura · Notificación automática
    </td>
  </tr>

</table>

</td></tr>
</table>
</body>
</html>