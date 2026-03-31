<?php $pageTitle = 'Dashboard — Sítio Fácil'; ?>

<!-- Cabeçalho -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">
            Olá, <?= htmlspecialchars(explode(' ', $_SESSION['nome'])[0]) ?>!
        </h1>
        <p class="text-muted small mb-0">Seja bem-vindo ao seu painel de viagens.</p>
    </div>
    <a href="<?= BASE_URL ?>/chacaras" class="btn btn-primary">Buscar Chácaras</a>
</div>

<!-- Stat cards -->
<?php
$pendentes   = array_filter($reservas, fn($r) => $r['status'] === 'PENDENTE');
$confirmadas = array_filter($reservas, fn($r) => $r['status'] === 'CONFIRMADA');
$concluidas  = array_filter($reservas, fn($r) => $r['status'] === 'CONCLUIDA');
?>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="sf-stat-card">
            <span class="sf-stat-icon"><i class="fas fa-clock"></i></span>
            <p class="sf-stat-value"><?= count($pendentes) ?></p>
            <p class="sf-stat-label">Pendentes</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="sf-stat-card">
            <span class="sf-stat-icon"><i class="fas fa-check-circle"></i></span>
            <p class="sf-stat-value" style="color:var(--sf-primary);"><?= count($confirmadas) ?></p>
            <p class="sf-stat-label">Confirmadas</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="sf-stat-card">
            <span class="sf-stat-icon"><i class="fas fa-flag-checkered"></i></span>
            <p class="sf-stat-value"><?= count($concluidas) ?></p>
            <p class="sf-stat-label">Concluídas</p>
        </div>
    </div>
</div>

<!-- Reservas recentes -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h5 fw-bold mb-0">Reservas recentes</h2>
    <a href="<?= BASE_URL ?>/minhas-reservas" class="small fw-semibold text-body-emphasis">Ver todas</a>
</div>

<?php
$badgeClass = [
    'PENDENTE'   => 'text-bg-warning',
    'CONFIRMADA' => 'text-bg-success',
    'RECUSADA'   => 'text-bg-danger',
    'CANCELADA'  => 'text-bg-secondary',
    'CONCLUIDA'  => 'text-bg-info',
];
?>

<?php if (empty($reservas)): ?>
    <div class="sf-empty-state">
        <div class="sf-empty-icon"><i class="fas fa-calendar"></i></div>
        <p class="sf-empty-title">Nenhuma reserva ainda</p>
        <p class="sf-empty-desc">Explore chácaras e faça sua primeira reserva.</p>
        <a href="<?= BASE_URL ?>/chacaras" class="btn btn-primary mt-2">Buscar agora</a>
    </div>
<?php else: ?>
    <?php foreach (array_slice($reservas, 0, 5) as $r): ?>
        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
            <div>
                <p class="fw-semibold mb-1"><?= htmlspecialchars($r['chacara_nome']) ?></p>
                <p class="text-muted small mb-0">
                    <?= date('d/m/Y', strtotime($r['data_inicio'])) ?> → <?= date('d/m/Y', strtotime($r['data_fim'])) ?>
                </p>
            </div>
            <div class="text-end">
                <p class="fw-bold small mb-1">R$&nbsp;<?= number_format((float)$r['valor_total'], 2, ',', '.') ?></p>
                <span class="badge rounded-pill <?= $badgeClass[$r['status']] ?? 'text-bg-secondary' ?>">
                    <?= $r['status'] ?>
                </span>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
