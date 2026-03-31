<?php $pageTitle = 'Buscar Chácaras — Sítio Fácil'; ?>

<!-- SEARCH HERO -->
<div class="sf-search-hero">
    <div class="sf-search-hero-content">
        <h1 class="fw-bold mb-2" style="color:#fff; font-size:2rem;">Encontre sua chácara ideal</h1>
        <p class="mb-4" style="color:rgba(255,255,255,.85);">Alugue espaços incríveis direto com os proprietários</p>

        <form method="GET" action="<?= BASE_URL ?>/chacaras">
            <div class="sf-search-pill">
                <input class="sf-search-field" type="text" name="cidade"
                       placeholder="Para onde vai? Cidade ou região..."
                       value="<?= htmlspecialchars($filtros['cidade'] ?? '') ?>">
                <div class="sf-search-divider"></div>
                <input class="sf-search-field" type="number" name="capacidade" min="1"
                       placeholder="Quantas pessoas?"
                       value="<?= (int)($filtros['capacidade'] ?? 0) ?: '' ?>"
                       style="max-width:160px;">
                <div class="sf-search-divider"></div>
                <input class="sf-search-field" type="date" name="data_inicio"
                       min="<?= date('Y-m-d') ?>"
                       value="<?= htmlspecialchars($filtros['data_inicio'] ?? '') ?>"
                       style="max-width:150px;">
                <div class="sf-search-divider"></div>
                <input class="sf-search-field" type="date" name="data_fim"
                       min="<?= date('Y-m-d') ?>"
                       value="<?= htmlspecialchars($filtros['data_fim'] ?? '') ?>"
                       style="max-width:150px;">
                <button class="sf-search-btn" type="submit" title="Buscar">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Resultados -->
<?php if (empty($chacaras)): ?>
    <div class="sf-empty-state">
        <span class="sf-empty-icon"><i class="fas fa-tree"></i></span>
        <p class="sf-empty-title">Nenhuma chácara encontrada</p>
        <p class="sf-empty-desc">Tente ajustar os filtros ou buscar por outra cidade.</p>
        <a href="<?= BASE_URL ?>/chacaras" class="btn btn-outline-secondary">Limpar filtros</a>
    </div>
<?php else: ?>
    <div class="d-flex align-items-center mb-4">
        <p class="text-muted small mb-0">
            <strong class="text-body-emphasis"><?= count($chacaras) ?></strong>
            chácara<?= count($chacaras) !== 1 ? 's' : '' ?> encontrada<?= count($chacaras) !== 1 ? 's' : '' ?>
        </p>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($chacaras as $c): ?>
            <div class="col">
                <a href="<?= BASE_URL ?>/chacaras/<?= $c['id'] ?>" class="sf-card-link card h-100 text-decoration-none">
                    <div class="sf-card-img-wrap">
                        <?php if (!empty($c['capa'])): ?>
                            <img src="<?= htmlspecialchars($c['capa']) ?>"
                                 alt="<?= htmlspecialchars($c['nome']) ?>"
                                 loading="lazy" class="sf-card-img">
                        <?php else: ?>
                            <div class="sf-no-photo">
                                <i class="fas fa-tree"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <p class="sf-card-title"><?= htmlspecialchars($c['nome']) ?></p>
                            <?php if ($c['nota_media']): ?>
                                <span class="sf-card-rating flex-shrink-0">
                                    <i class="fas fa-star"></i>
                                    <?= number_format((float)$c['nota_media'], 1) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="sf-card-location">
                            <?= htmlspecialchars($c['cidade'] . ', ' . $c['estado']) ?>
                        </p>
                        <p class="sf-card-location mb-0">
                            <i class="fas fa-users" style="font-size:.75rem;"></i>
                            até <?= $c['capacidade_maxima'] ?> pessoas
                        </p>
                        <p class="sf-card-price mt-2 mb-0">
                            <strong>R$ <?= number_format((float)$c['preco_diaria'], 2, ',', '.') ?></strong>
                            <span class="sf-price-unit"> / diária</span>
                        </p>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
