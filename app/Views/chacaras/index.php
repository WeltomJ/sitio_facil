<?php
$pageTitle = 'Buscar Chácaras — Sítio Fácil';

// Filtros ativos (exclui valores padrão/vazios)
$filtrosAtivos = array_filter($filtros, function ($v) {
    return $v !== '' && $v !== 0 && $v !== 0.0 && $v !== [];
});
unset($filtrosAtivos['ordenar']); // ordenar não conta como "filtro ativo"
$temFiltroAvancado = !empty($filtrosAtivos['estado'])
    || !empty($filtrosAtivos['preco_min'])
    || !empty($filtrosAtivos['preco_max'])
    || !empty($filtrosAtivos['nota_min'])
    || !empty($filtrosAtivos['comodidades']);
?>

<!-- SEARCH HERO -->
<div class="sf-search-hero">
    <div class="sf-search-hero-content">
        <h1 class="fw-bold mb-2" style="font-size:2rem;">Encontre sua chácara ideal</h1>
        <p class="mb-4 lead">Alugue espaços incríveis direto com os proprietários</p>

        <form method="GET" action="<?= BASE_URL ?>/chacaras" id="form-busca">
            <div class="sf-search-pill">
                <input class="sf-search-field" type="text" name="cidade"
                       placeholder="Cidade ou região..."
                       value="<?= htmlspecialchars($filtros['cidade'] ?? '') ?>">
                <div class="sf-search-divider"></div>
                <input class="sf-search-field sf-search-field--sm" type="number" name="capacidade" min="1"
                       placeholder="Pessoas"
                       value="<?= (int)($filtros['capacidade'] ?? 0) ?: '' ?>">
                <div class="sf-search-divider"></div>
                <input class="sf-search-field sf-search-field--date" type="date" name="data_inicio"
                       min="<?= date('Y-m-d') ?>"
                       value="<?= htmlspecialchars($filtros['data_inicio'] ?? '') ?>"
                       placeholder="Check-in">
                <div class="sf-search-divider"></div>
                <input class="sf-search-field sf-search-field--date" type="date" name="data_fim"
                       min="<?= date('Y-m-d') ?>"
                       value="<?= htmlspecialchars($filtros['data_fim'] ?? '') ?>"
                       placeholder="Check-out">
                <button class="sf-search-btn" type="submit" title="Buscar">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <!-- Mantém filtros avançados ao buscar pelo hero -->
            <?php foreach (['estado','preco_min','preco_max','nota_min','ordenar'] as $k): ?>
                <?php if (!empty($filtros[$k])): ?>
                    <input type="hidden" name="<?= $k ?>" value="<?= htmlspecialchars((string) $filtros[$k]) ?>">
                <?php endif; ?>
            <?php endforeach; ?>
            <?php foreach (($filtros['comodidades'] ?? []) as $cId): ?>
                <input type="hidden" name="comodidades[]" value="<?= (int) $cId ?>">
            <?php endforeach; ?>
        </form>
    </div>
</div>

<!-- BARRA DE FILTROS -->
<div class="sf-filter-bar my-4">
    <form method="GET" action="<?= BASE_URL ?>/chacaras" id="form-filtros">
        <!-- Campos do hero replicados como hidden -->
        <?php foreach (['cidade','capacidade','data_inicio','data_fim'] as $k): ?>
            <?php if (!empty($filtros[$k])): ?>
                <input type="hidden" name="<?= $k ?>" value="<?= htmlspecialchars((string) $filtros[$k]) ?>">
            <?php endif; ?>
        <?php endforeach; ?>

        <div class="d-flex align-items-center gap-2 flex-wrap">

            <!-- Ordenação -->
            <div class="sf-filter-select-wrap">
                <select name="ordenar" class="form-select form-select-sm sf-filter-select"
                        onchange="this.form.submit()">
                    <option value=""            <?= empty($filtros['ordenar'])               ? 'selected' : '' ?>>Relevância</option>
                    <option value="nota"        <?= ($filtros['ordenar'] ?? '') === 'nota'        ? 'selected' : '' ?>>Melhor avaliação</option>
                    <option value="preco_asc"   <?= ($filtros['ordenar'] ?? '') === 'preco_asc'   ? 'selected' : '' ?>>Menor preço</option>
                    <option value="preco_desc"  <?= ($filtros['ordenar'] ?? '') === 'preco_desc'  ? 'selected' : '' ?>>Maior preço</option>
                    <option value="recente"     <?= ($filtros['ordenar'] ?? '') === 'recente'     ? 'selected' : '' ?>>Mais recente</option>
                </select>
            </div>

            <div class="vr d-none d-sm-block"></div>

            <!-- Botão Filtros -->
            <button class="btn btn-outline-secondary d-flex align-items-center gap-2"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#filtros-avancados"
                    aria-expanded="<?= $temFiltroAvancado ? 'true' : 'false' ?>">
                <i class="fas fa-sliders-h"></i>
                Filtros
                <?php if ($temFiltroAvancado): ?>
                    <span class="badge rounded-pill text-bg-primary ms-1" id="badge-filtros"></span>
                <?php endif; ?>
            </button>

            <?php if ($temFiltroAvancado): ?>
                <a href="<?= BASE_URL ?>/chacaras<?= !empty($filtros['cidade']) ? '?cidade=' . urlencode($filtros['cidade']) : '' ?>"
                   class="btn btn-sm btn-link text-danger p-0 text-decoration-none">
                    <i class="fas fa-times-circle me-1"></i>Limpar filtros
                </a>
            <?php endif; ?>
        </div>

        <!-- Painel de filtros avançados -->
        <div class="collapse <?= $temFiltroAvancado ? 'show' : '' ?> mt-3" id="filtros-avancados">
            <div class="card card-body border rounded-3 p-4">
                <div class="row g-3">

                    <!-- Estado -->
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label small fw-semibold" for="f-estado">Estado (UF)</label>
                        <input type="text" class="form-control form-control-sm" id="f-estado"
                               name="estado" maxlength="2" placeholder="SP"
                               style="text-transform:uppercase;"
                               value="<?= htmlspecialchars($filtros['estado'] ?? '') ?>">
                    </div>

                    <!-- Preço -->
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label small fw-semibold">Preço por diária</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" class="form-control" name="preco_min"
                                   placeholder="Mín" min="0" step="50"
                                   value="<?= ($filtros['preco_min'] ?? 0) ?: '' ?>">
                            <span class="input-group-text">–</span>
                            <input type="number" class="form-control" name="preco_max"
                                   placeholder="Máx" min="0" step="50"
                                   value="<?= ($filtros['preco_max'] ?? 0) ?: '' ?>">
                        </div>
                    </div>

                    <!-- Nota mínima -->
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label small fw-semibold" for="f-nota">Avaliação mínima</label>
                        <select class="form-select form-select-sm" id="f-nota" name="nota_min">
                            <option value="">Qualquer</option>
                            <?php foreach ([3, 3.5, 4, 4.5, 5] as $n): ?>
                                <option value="<?= $n ?>" <?= (float)($filtros['nota_min'] ?? 0) === (float)$n ? 'selected' : '' ?>>
                                    <?= $n ?>+ ★
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Comodidades -->
                    <?php if (!empty($comodidades)): ?>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Comodidades</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php
                            $comodSel = array_map('intval', $filtros['comodidades'] ?? []);
                            foreach ($comodidades as $c):
                            ?>
                                <div class="form-check form-check-inline m-0">
                                    <input class="form-check-input" type="checkbox"
                                           name="comodidades[]"
                                           id="f-como-<?= $c['id'] ?>"
                                           value="<?= $c['id'] ?>"
                                           <?= in_array((int)$c['id'], $comodSel) ? 'checked' : '' ?>>
                                    <label class="form-check-label small" for="f-como-<?= $c['id'] ?>">
                                        <?= htmlspecialchars($c['nome']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="fas fa-search me-1"></i>Aplicar filtros
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>

<!-- Tags de filtros ativos -->
<?php
$tagsAtivas = [];
if (!empty($filtros['cidade']))       $tagsAtivas[] = ['Cidade: ' . $filtros['cidade'],       'cidade'];
if (!empty($filtros['estado']))       $tagsAtivas[] = ['Estado: ' . strtoupper($filtros['estado']), 'estado'];
if (!empty($filtros['capacidade']))   $tagsAtivas[] = ['Mín. ' . $filtros['capacidade'] . ' pessoas', 'capacidade'];
if (!empty($filtros['data_inicio']))  $tagsAtivas[] = ['De ' . date('d/m', strtotime($filtros['data_inicio'])), 'data_inicio'];
if (!empty($filtros['data_fim']))     $tagsAtivas[] = ['Até ' . date('d/m', strtotime($filtros['data_fim'])), 'data_fim'];
if (!empty($filtros['preco_min']))    $tagsAtivas[] = ['Mín. R$' . number_format($filtros['preco_min'], 0, ',', '.'), 'preco_min'];
if (!empty($filtros['preco_max']))    $tagsAtivas[] = ['Máx. R$' . number_format($filtros['preco_max'], 0, ',', '.'), 'preco_max'];
if (!empty($filtros['nota_min']))     $tagsAtivas[] = [$filtros['nota_min'] . '+ ★', 'nota_min'];
foreach (($filtros['comodidades'] ?? []) as $cId) {
    foreach (($comodidades ?? []) as $c) {
        if ((int)$c['id'] === (int)$cId) {
            $tagsAtivas[] = [$c['nome'], null]; // sem remoção individual para comodidades
            break;
        }
    }
}
?>
<?php if (!empty($tagsAtivas)): ?>
<div class="d-flex flex-wrap gap-2 mb-3">
    <?php foreach ($tagsAtivas as [$label, $key]): ?>
        <?php if ($key): ?>
            <?php
            $urlSemFiltro = $_GET;
            unset($urlSemFiltro[$key], $urlSemFiltro['page']);
            $href = BASE_URL . '/chacaras' . ($urlSemFiltro ? '?' . http_build_query($urlSemFiltro) : '');
            ?>
            <a href="<?= $href ?>"
               class="badge rounded-pill text-bg-light border text-decoration-none text-body-emphasis">
                <?= htmlspecialchars($label) ?> <i class="fas fa-times ms-1 small"></i>
            </a>
        <?php else: ?>
            <span class="badge rounded-pill text-bg-light border text-body-emphasis">
                <?= htmlspecialchars($label) ?>
            </span>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Resultados -->
<?php if (empty($chacaras) && ($currentPage ?? 1) === 1): ?>
    <div class="sf-empty-state">
        <span class="sf-empty-icon"><i class="fas fa-tree"></i></span>
        <p class="sf-empty-title">Nenhuma chácara encontrada</p>
        <p class="sf-empty-desc">Tente ajustar os filtros ou buscar por outra cidade.</p>
        <a href="<?= BASE_URL ?>/chacaras" class="btn btn-outline-secondary">Limpar filtros</a>
    </div>
<?php else: ?>
    <div class="d-flex align-items-center mb-4">
        <p class="text-muted small mb-0">
            <strong class="text-body-emphasis"><?= (int) ($totalItens ?? count($chacaras)) ?></strong>
            chácara<?= ($totalItens ?? count($chacaras)) !== 1 ? 's' : '' ?> encontrada<?= ($totalItens ?? count($chacaras)) !== 1 ? 's' : '' ?>
        </p>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($chacaras as $c): ?>
            <div class="col">
                <a href="<?= BASE_URL ?>/chacaras/<?= $c['id'] ?>" class="sf-card-link card h-100 text-decoration-none">
                    <div class="sf-card-img-wrap">
                        <?php if (!empty($c['capa'])): ?>
                            <img src="<?= BASE_URL . htmlspecialchars($c['capa']) ?>"
                                 alt="<?= htmlspecialchars($c['nome']) ?>"
                                 loading="lazy" class="sf-card-img">
                        <?php else: ?>
                            <div class="sf-no-photo"><i class="fas fa-tree"></i></div>
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

    <div id="sf-pagination"></div>
    <script>
    (function () {
        var el = document.getElementById('sf-pagination');
        if (el) el.innerHTML = pagination(<?= (int) ($currentPage ?? 1) ?>, <?= (int) ($totalPages ?? 1) ?>);
        document.addEventListener('click', function (e) {
            var link = e.target.closest('#sf-pagination [data-page]');
            if (!link) return;
            var p = parseInt(link.dataset.page);
            if (!p || p < 1) return;
            var url = new URL(window.location.href);
            url.searchParams.set('page', p);
            window.location.href = url.toString();
        });

        // Conta filtros ativos no badge
        var badge = document.getElementById('badge-filtros');
        if (badge) {
            var count = <?= count(array_filter([
                !empty($filtros['estado']),
                !empty($filtros['preco_min']),
                !empty($filtros['preco_max']),
                !empty($filtros['nota_min']),
                !empty($filtros['comodidades']),
            ])) ?>;
            badge.textContent = count;
        }

        // Uppercase no campo estado
        var fEstado = document.getElementById('f-estado');
        if (fEstado) fEstado.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });
    })();
    </script>
<?php endif; ?>
