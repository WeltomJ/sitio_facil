<?php $pageTitle = 'Dashboard Locador — Sítio Fácil'; ?>

<!-- Cabeçalho -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">
            Olá, <?= htmlspecialchars(explode(' ', $_SESSION['nome'])[0]) ?>!
        </h1>
        <p class="text-muted small mb-0">Painel do Anfitrião</p>
    </div>
    <a href="<?= BASE_URL ?>/locador/chacaras/nova" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nova Chácara
    </a>
</div>

<!-- Stat cards -->
<?php
$pendentes   = array_filter($reservasLocador ?? [], fn($r) => $r['status'] === 'PENDENTE');
$confirmadas = array_filter($reservasLocador ?? [], fn($r) => $r['status'] === 'CONFIRMADA');
?>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="sf-stat-card">
            <span class="sf-stat-icon"><i class="fas fa-home"></i></span>
            <p class="sf-stat-value"><?= count($chacaras ?? []) ?></p>
            <p class="sf-stat-label">Chácaras</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="sf-stat-card">
            <span class="sf-stat-icon" style="color:#D97706;"><i class="fas fa-clock"></i></span>
            <p class="sf-stat-value" style="color:#D97706;"><?= count($pendentes) ?></p>
            <p class="sf-stat-label">Aguardando</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="sf-stat-card">
            <span class="sf-stat-icon"><i class="fas fa-check-circle"></i></span>
            <p class="sf-stat-value" style="color:var(--sf-primary);"><?= count($confirmadas) ?></p>
            <p class="sf-stat-label">Confirmadas</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Chácaras -->
    <div class="col-md-6">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 fw-bold mb-0">Minhas Chácaras</h2>
            <a href="<?= BASE_URL ?>/locador/chacaras" class="small fw-semibold text-body-emphasis">Ver todas</a>
        </div>
        <?php if (empty($chacaras)): ?>
            <div class="sf-empty-state py-4">
                <p class="sf-empty-title">Nenhuma chácara</p>
                <a href="<?= BASE_URL ?>/locador/chacaras/nova" class="btn btn-primary btn-sm mt-2">Cadastrar agora</a>
            </div>
        <?php else: ?>
            <?php foreach ($chacaras as $c): ?>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <p class="fw-semibold small mb-0"><?= htmlspecialchars($c['nome']) ?></p>
                        <p class="text-muted" style="font-size:.75rem;"><?= htmlspecialchars(($c['cidade'] ?? '') . '/' . ($c['estado'] ?? '')) ?></p>
                    </div>
                    <a href="<?= BASE_URL ?>/locador/chacaras/<?= $c['id'] ?>/editar"
                       class="small fw-semibold text-body-emphasis">Editar</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Reservas pendentes -->
    <div class="col-md-6">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 fw-bold mb-0">Reservas Pendentes</h2>
            <a href="<?= BASE_URL ?>/locador/reservas" class="small fw-semibold text-body-emphasis">Ver todas</a>
        </div>
        <?php if (empty($pendentes)): ?>
            <div class="sf-empty-state py-4">
                <p class="sf-empty-title">Nenhuma pendente</p>
            </div>
        <?php else: ?>
            <?php foreach ($pendentes as $r): ?>
                <div class="py-2 border-bottom">
                    <p class="fw-semibold small mb-0"><?= htmlspecialchars($r['chacara_nome']) ?></p>
                    <p class="text-muted mb-2" style="font-size:.75rem;">
                        <?= htmlspecialchars($r['cliente_nome']) ?> →
                        <?= date('d/m/Y', strtotime($r['data_inicio'])) ?> – <?= date('d/m/Y', strtotime($r['data_fim'])) ?>
                    </p>
                    <div class="d-flex gap-2">
                        <form method="POST" action="<?= BASE_URL ?>/locador/reservas/<?= $r['id'] ?>/confirmar">
                            <button class="btn btn-primary btn-sm" type="submit">Confirmar</button>
                        </form>
                        <form method="POST" action="<?= BASE_URL ?>/locador/reservas/<?= $r['id'] ?>/recusar">
                            <button class="btn btn-outline-danger btn-sm" type="submit">Recusar</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
