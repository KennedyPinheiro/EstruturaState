<?php

interface EstadoPedido {
    public function prosseguir(PedidoContexto $pedido);
    public function cancelar(PedidoContexto $pedido);
    public function obterStatus(): string;
}

class EstadoPendente implements EstadoPedido {
    public function prosseguir(PedidoContexto $pedido) {
        $pedido->definirEstado(new EstadoProcessando());
    }
    public function cancelar(PedidoContexto $pedido) {
        $pedido->definirEstado(new EstadoCancelado());
    }
    public function obterStatus(): string {
        return "pendente";
    }
}

class EstadoProcessando implements EstadoPedido {
    public function prosseguir(PedidoContexto $pedido) {
        $pedido->definirEstado(new EstadoEnviado());
    }
    public function cancelar(PedidoContexto $pedido) {
        $pedido->definirEstado(new EstadoCancelado());
    }
    public function obterStatus(): string {
        return "processando";
    }
}

class EstadoEnviado implements EstadoPedido {
    public function prosseguir(PedidoContexto $pedido) {
        $pedido->definirEstado(new EstadoEntregue());
    }
    public function cancelar(PedidoContexto $pedido) {
    }
    public function obterStatus(): string {
        return "enviado";
    }
}

class EstadoEntregue implements EstadoPedido {
    public function prosseguir(PedidoContexto $pedido) {
    }
    public function cancelar(PedidoContexto $pedido) {
    }
    public function obterStatus(): string {
        return "entregue";
    }
}

class EstadoCancelado implements EstadoPedido {
    public function prosseguir(PedidoContexto $pedido) {
    }
    public function cancelar(PedidoContexto $pedido) {
    }
    public function obterStatus(): string {
        return "cancelado";
    }
}

class PedidoContexto {
    private EstadoPedido $estado;
    public function __construct() {
        $this->estado = new EstadoPendente();
    }
    public function definirEstado(EstadoPedido $estado): void {
        $this->estado = $estado;
    }
    public function prosseguir(): void {
        $this->estado->prosseguir($this);
    }
    public function cancelar(): void {
        $this->estado->cancelar($this);
    }
    public function obterStatus(): string {
        return $this->estado->obterStatus();
    }
}

