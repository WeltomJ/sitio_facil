<?php
$pageTitle = 'Minhas Chácaras';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">Minhas Chácaras</h1>
    <a class="btn btn-primary" href="<?= BASE_URL ?>/locador/chacaras/nova">
        <i class="fas fa-plus me-1"></i> Cadastrar Nova
    </a>
</div>

<?php if (empty($chacaras)): ?>
    <div class="sf-empty-state">
        <div class="sf-empty-icon"><i class="fas fa-home"></i></div>
        <p class="sf-empty-title">Nenhuma chácara cadastrada</p>
        <p class="sf-empty-desc">Adicione sua primeira propriedade e comece a receber hóspedes.</p>
        <a href="<?= BASE_URL ?>/locador/chacaras/nova" class="btn btn-primary mt-2">Cadastrar agora</a>
    </div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($chacaras as $c): ?>
            <div class="col">
                <div class="card h-100">
                    <div style="background:<?= $c['ativa'] ? 'var(--sf-primary)' : 'var(--bs-secondary-bg)' ?>; height:3px;"></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <p class="fw-bold mb-0 me-2"><?= htmlspecialchars($c['nome']) ?></p>
                            <span class="badge rounded-pill <?= $c['ativa'] ? 'text-bg-success' : 'text-bg-secondary' ?> flex-shrink-0">
                                <?= $c['ativa'] ? 'Ativa' : 'Inativa' ?>
                            </span>
                        </div>
                        <p class="text-muted small mb-2">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            <?= htmlspecialchars(($c['cidade'] ?? '') . ', ' . ($c['estado'] ?? '')) ?>
                        </p>
                        <p class="fw-bold mb-0" style="color:var(--sf-primary);">
                            R$ <?= number_format((float)$c['preco_diaria'], 2, ',', '.') ?>
                            <span class="fw-normal text-muted small"> / diária</span>
                        </p>
                    </div>
                    <div class="card-footer bg-transparent d-flex gap-2">
                        <a href="<?= BASE_URL ?>/chacaras/<?= $c['id'] ?>" class="btn btn-outline-secondary btn-sm flex-fill">Ver</a>
                        <a href="<?= BASE_URL ?>/locador/chacaras/<?= $c['id'] ?>/editar" class="btn btn-primary btn-sm flex-fill">Editar</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
