<?php

class PedidoSemState {
    private string $estado;
    private array $historico;
    
    const PENDENTE = 'pendente';
    const PROCESSANDO = 'processando';
    const ENVIADO = 'enviado';
    const ENTREGUE = 'entregue';
    const CANCELADO = 'cancelado';
    
    public function __construct() {
        $this->estado = self::PENDENTE;
        $this->historico = [];
        $this->adicionarHistorico("Pedido criado - Estado: Pendente");
    }
    
    public function prosseguir(): void {
        switch ($this->estado) {
            case self::PENDENTE:
                $this->estado = self::PROCESSANDO;
                $this->adicionarHistorico("Pedido sendo processado...");
                break;
            case self::PROCESSANDO:
                $this->estado = self::ENVIADO;
                $this->adicionarHistorico("Pedido enviado para entrega");
                break;
            case self::ENVIADO:
                $this->estado = self::ENTREGUE;
                $this->adicionarHistorico("Pedido entregue com sucesso!");
                break;
            case self::ENTREGUE:
                $this->adicionarHistorico("Pedido já foi entregue. Nenhuma ação necessária.");
                break;
            case self::CANCELADO:
                $this->adicionarHistorico("Pedido cancelado não pode prosseguir.");
                break;
        }
    }
    
    public function cancelar(): void {
        switch ($this->estado) {
            case self::PENDENTE:
                $this->estado = self::CANCELADO;
                $this->adicionarHistorico("Pedido cancelado.");
                break;
            case self::PROCESSANDO:
                $this->estado = self::CANCELADO;
                $this->adicionarHistorico("Processamento cancelado. Pedido será estornado.");
                break;
            case self::ENVIADO:
                $this->adicionarHistorico("Não é possível cancelar um pedido já enviado.");
                break;
            case self::ENTREGUE:
                $this->adicionarHistorico("Não é possível cancelar um pedido já entregue.");
                break;
            case self::CANCELADO:
                $this->adicionarHistorico("Pedido já está cancelado.");
                break;
        }
    }
    
    public function obterStatus(): string {
        return $this->estado;
    }
    
    public function obterDescricao(): string {
        switch ($this->estado) {
            case self::PENDENTE: return "Aguardando processamento";
            case self::PROCESSANDO: return "Em preparação";
            case self::ENVIADO: return "A caminho do cliente";
            case self::ENTREGUE: return "Entregue ao cliente";
            case self::CANCELADO: return "Pedido cancelado";
            default: return "Estado desconhecido";
        }
    }
    
    public function podeProsseguir(): bool {
        return in_array($this->estado, [self::PENDENTE, self::PROCESSANDO, self::ENVIADO]);
    }
    
    public function podeCancelar(): bool {
        return in_array($this->estado, [self::PENDENTE, self::PROCESSANDO]);
    }
    
    public function ehEstadoFinal(): bool {
        return in_array($this->estado, [self::ENTREGUE, self::CANCELADO]);
    }
    
    private function adicionarHistorico(string $mensagem): void {
        $timestamp = date('H:i:s');
        $this->historico[] = "[$timestamp] $mensagem";
    }
    
    public function obterHistorico(): array {
        return $this->historico;
    }
    
    public function obterInformacoes(): array {
        return [
            'status' => $this->estado,
            'descricao' => $this->obterDescricao(),
            'pode_prosseguir' => $this->podeProsseguir(),
            'pode_cancelar' => $this->podeCancelar(),
            'eh_final' => $this->ehEstadoFinal(),
            'historico' => $this->obterHistorico()
        ];
    }
}

class PedidoService {
    
    public function criarPedido(): PedidoSemState {
        return new PedidoSemState();
    }
    
    public function processarAcao(PedidoSemState $pedido, string $acao): array {
        try {
            switch ($acao) {
                case 'prosseguir':
                    $pedido->prosseguir();
                    break;
                case 'cancelar':
                    $pedido->cancelar();
                    break;
                default:
                    throw new Exception("Ação inválida: $acao");
            }
            
            return [
                'sucesso' => true,
                'pedido' => $pedido->obterInformacoes()
            ];
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'erro' => $e->getMessage(),
                'pedido' => $pedido->obterInformacoes()
            ];
        }
    }
    
    public function obterStatusPedido(PedidoSemState $pedido): array {
        return $pedido->obterInformacoes();
    }
}

$service = new PedidoService();

$pedido = $service->criarPedido();

$resultado1 = $service->processarAcao($pedido, 'prosseguir');
$resultado2 = $service->processarAcao($pedido, 'prosseguir');
$resultado3 = $service->processarAcao($pedido, 'prosseguir');
$resultado4 = $service->processarAcao($pedido, 'prosseguir');
$resultado5 = $service->processarAcao($pedido, 'cancelar');

$statusFinal = $service->obterStatusPedido($pedido);

$pedido2 = $service->criarPedido();
$service->processarAcao($pedido2, 'prosseguir');
$service->processarAcao($pedido2, 'cancelar');

return [
    'pedido1_fluxo_normal' => $statusFinal,
    'pedido2_com_cancelamento' => $service->obterStatusPedido($pedido2)
];
