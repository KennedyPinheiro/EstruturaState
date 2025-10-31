<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = include 'semState.php';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pedido Sem State</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
  <div class="container">
    <h1 class="mb-4 text-center">Exemplo Sem State</h1>
    <form method="post" class="text-center mb-4">
      <button type="submit" name="acao" value="executar" class="btn btn-primary px-4">Executar Pedido</button>
    </form>

    <?php if (isset($resultado)): ?>
      <div class="row g-4">
        <!-- Pedido 1 -->
        <div class="col-md-6">
          <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
              Pedido 1 (Fluxo Normal)
            </div>
            <div class="card-body">
              <p><strong>Status:</strong> <?= ucfirst($resultado['pedido1_fluxo_normal']['status']) ?></p>
              <p><strong>Descrição:</strong> <?= $resultado['pedido1_fluxo_normal']['descricao'] ?></p>
              <p><strong>Estado Final:</strong> <?= $resultado['pedido1_fluxo_normal']['eh_final'] ? 'Sim' : 'Não' ?></p>

              <h6 class="mt-3">Histórico:</h6>
              <ul class="list-group">
                <?php foreach ($resultado['pedido1_fluxo_normal']['historico'] as $log): ?>
                  <li class="list-group-item"><?= htmlspecialchars($log) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>

        <!-- Pedido 2 -->
        <div class="col-md-6">
          <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
              Pedido 2 (Com Cancelamento)
            </div>
            <div class="card-body">
              <p><strong>Status:</strong> <?= ucfirst($resultado['pedido2_com_cancelamento']['status']) ?></p>
              <p><strong>Descrição:</strong> <?= $resultado['pedido2_com_cancelamento']['descricao'] ?></p>
              <p><strong>Estado Final:</strong> <?= $resultado['pedido2_com_cancelamento']['eh_final'] ? 'Sim' : 'Não' ?></p>

              <h6 class="mt-3">Histórico:</h6>
              <ul class="list-group">
                <?php foreach ($resultado['pedido2_com_cancelamento']['historico'] as $log): ?>
                  <li class="list-group-item"><?= htmlspecialchars($log) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
