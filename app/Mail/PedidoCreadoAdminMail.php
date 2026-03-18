<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PedidoCreadoAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $pedido,
        public array $items,
        public int $subtotal,
        public ?string $puntoNombre = null,
        public ?string $horaEstimada = null,
        public ?string $mensajeCliente = null,
        public ?string $comprobanteUrl = null
    ) {}

    public function build()
    {
        return $this->subject('🧾 Nuevo pedido pendiente · La Herradura #' . ($this->pedido->id ?? ''))
            ->view('emails.pedido-creado-admin');
    }
}