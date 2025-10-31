<?php

class PedidoSemState
{
    private $estado = 'pendente'; 
    private $numero;
    private $itens = [];
    private $valorTotal = 0;

    public function __construct($numero)
    {
        $this->numero = $numero;
    }

    public function adicionarItem($item, $valor)
    {
        if ($this->estado !== 'pendente') {
            throw new Exception("Não é possível adicionar itens a um pedido {$this->estado}");
        }
        
        $this->itens[] = $item;
        $this->valorTotal += $valor;
        echo "Item '{$item}' adicionado. Valor total: R$ {$this->valorTotal}\n";
    }

    public function pagar()
    {
        if ($this->estado !== 'pendente') {
            throw new Exception("Pedido já foi {$this->estado}");
        }
        
        $this->estado = 'pago';
        echo "Pedido {$this->numero} pago com sucesso!\n";
    }

    public function enviar()
    {
        if ($this->estado !== 'pago') {
            throw new Exception("Só é possível enviar pedidos pagos. Estado atual: {$this->estado}");
        }
        
        $this->estado = 'enviado';
        echo "Pedido {$this->numero} enviado para entrega!\n";
    }

    public function entregar()
    {
        if ($this->estado !== 'enviado') {
            throw new Exception("Só é possível entregar pedidos enviados. Estado atual: {$this->estado}");
        }
        
        $this->estado = 'entregue';
        echo "Pedido {$this->numero} entregue com sucesso!\n";
    }

    public function cancelar()
    {
        if (!in_array($this->estado, ['pendente', 'pago'])) {
            throw new Exception("Não é possível cancelar um pedido {$this->estado}");
        }
        
        $this->estado = 'cancelado';
        echo "Pedido {$this->numero} cancelado!\n";
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getInfo()
    {
        return "Pedido #{$this->numero} - Estado: {$this->estado} - Valor: R$ {$this->valorTotal}";
    }
}

echo "=== SEM PADRÃO STATE ===\n";
$pedido1 = new PedidoSemState("001");
$pedido1->adicionarItem("Camiseta", 50);
$pedido1->adicionarItem("Calça", 100);
$pedido1->pagar();
$pedido1->enviar();
$pedido1->entregar();
echo $pedido1->getInfo() . "\n\n";
