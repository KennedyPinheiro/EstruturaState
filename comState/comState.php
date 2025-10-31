<?php
session_start();

abstract class EstadoPedido {
    abstract public function prosseguir($pedido);
    abstract public function cancelar($pedido);
    abstract public function obterStatus();
    abstract public function obterDescricao();
    abstract public function obterIcone();
}

class EstadoPendente extends EstadoPedido {
    public function prosseguir($pedido) {
        $pedido->definirEstado(new EstadoProcessando());
        return "🔄 Prosseguindo para processamento...";
    }

    public function cancelar($pedido) {
        $pedido->definirEstado(new EstadoCancelado());
        return "❌ Pedido cancelado.";
    }

    public function obterStatus() { return "Pendente"; }
    public function obterDescricao() { return "Aguardando processamento"; }
    public function obterIcone() { return "⏳"; }
}

class EstadoProcessando extends EstadoPedido {
    public function prosseguir($pedido) {
        $pedido->definirEstado(new EstadoEnviado());
        return "🚚 Pedido enviado para entrega.";
    }

    public function cancelar($pedido) {
        $pedido->definirEstado(new EstadoCancelado());
        return "🔄 Processamento cancelado - Pedido estornado.";
    }

    public function obterStatus() { return "Processando"; }
    public function obterDescricao() { return "Em preparação"; }
    public function obterIcone() { return "⚙️"; }
}

class EstadoEnviado extends EstadoPedido {
    public function prosseguir($pedido) {
        $pedido->definirEstado(new EstadoEntregue());
        return "✅ Pedido entregue com sucesso!";
    }

    public function cancelar($pedido) {
        return "⚠️ Não é possível cancelar um pedido já enviado.";
    }

    public function obterStatus() { return "Enviado"; }
    public function obterDescricao() { return "A caminho do cliente"; }
    public function obterIcone() { return "🚚"; }
}

class EstadoEntregue extends EstadoPedido {
    public function prosseguir($pedido) {
        return "ℹ️ Pedido já foi entregue.";
    }

    public function cancelar($pedido) {
        return "⚠️ Não é possível cancelar um pedido já entregue.";
    }

    public function obterStatus() { return "Entregue"; }
    public function obterDescricao() { return "Entregue ao cliente"; }
    public function obterIcone() { return "✅"; }
}

class EstadoCancelado extends EstadoPedido {
    public function prosseguir($pedido) {
        return "⚠️ Pedido cancelado não pode prosseguir.";
    }

    public function cancelar($pedido) {
        return "ℹ️ Pedido já está cancelado.";
    }

    public function obterStatus() { return "Cancelado"; }
    public function obterDescricao() { return "Pedido cancelado"; }
    public function obterIcone() { return "❌"; }
}

class PedidoContexto {
    public $estado;

    public function __construct() {
        $this->estado = new EstadoPendente();
    }

    public function definirEstado($estado) {
        $this->estado = $estado;
    }

    public function prosseguir() {
        return $this->estado->prosseguir($this);
    }

    public function cancelar() {
        return $this->estado->cancelar($this);
    }

    public function obterStatus() {
        return $this->estado->obterStatus();
    }

    public function obterDescricao() {
        return $this->estado->obterDescricao();
    }

    public function obterIcone() {
        return $this->estado->obterIcone();
    }
}

if (!isset($_SESSION['pedido'])) {
    $_SESSION['pedido'] = serialize(new PedidoContexto());
}

$pedido = unserialize($_SESSION['pedido']);
$acao = $_POST['acao'] ?? null;

$resposta = [];

if ($acao === 'prosseguir') {
    $mensagem = $pedido->prosseguir();
    $resposta = [
        'mensagem' => $mensagem,
        'status' => $pedido->obterStatus(),
        'icone' => $pedido->obterIcone(),
        'descricao' => $pedido->obterDescricao()
    ];
} elseif ($acao === 'cancelar') {
    $mensagem = $pedido->cancelar();
    $resposta = [
        'mensagem' => $mensagem,
        'status' => $pedido->obterStatus(),
        'icone' => $pedido->obterIcone(),
        'descricao' => $pedido->obterDescricao()
    ];
} elseif ($acao === 'resetar') {
    $pedido = new PedidoContexto();
    $_SESSION['pedido'] = serialize($pedido);
    $resposta = [
        'mensagem' => '🔄 Novo pedido criado!',
        'status' => $pedido->obterStatus(),
        'icone' => $pedido->obterIcone(),
        'descricao' => $pedido->obterDescricao()
    ];
}

$_SESSION['pedido'] = serialize($pedido);

header('Content-Type: application/json');
echo json_encode($resposta);
