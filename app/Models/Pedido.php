<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'user_id',
        'estado',
        'total',

        'punto_encuentro_id',
        'punto_encuentro_confirmado_id',

        // snapshot cliente
        'cliente_nombre',
        'cliente_email',
        'cliente_rut',
        'cliente_phone',

        // cliente
        'hora_estimada_cliente',
        'mensaje_cliente',

        'comprobante_path',

        // admin
        'hora_estimada_confirmada',
        'mensaje_admin',

        'aprobado_por',
        'aprobado_en',

        'stock_devuelto',
    ];

    protected $casts = [
        'aprobado_en' => 'datetime',
        'stock_devuelto' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function puntoEncuentro()
    {
        return $this->belongsTo(PuntoEncuentro::class, 'punto_encuentro_id');
    }

    public function puntoEncuentroConfirmado()
    {
        return $this->belongsTo(PuntoEncuentro::class, 'punto_encuentro_confirmado_id');
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }
}