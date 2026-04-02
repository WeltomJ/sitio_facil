<?php $pageTitle = 'Buscar Chácaras — Sítio Fácil'; ?>

<!-- SEARCH HERO -->
<div class="sf-search-hero">
    <div class="sf-search-hero-content">
        <h1 class="fw-bold mb-2" style="color:#fff; font-size:2rem;">Encontre sua chácara ideal</h1>
        <p class="mb-4" style="color:rgba(255,255,255,.85);">Alugue espaços incríveis direto com os proprietários</p>

        <!-- Busca Rápida -->
        <form method="GET" action="<?= BASE_URL ?>/chacaras" id="form-busca-rapida">
            <div class="sf-search-pill">
                <input class="sf-search-field" type="text" name="cidade"
                       placeholder="Para onde vai? Cidade ou bairro..."
                       value="<?= htmlspecialchars($filtros['cidade'] ?? '') ?>">
                <div class="sf-search-divider"></div>
                <input class="sf-search-field" type="number" name="capacidade" min="1"
                       placeholder="Pessoas"
                       value="<?= (int)($filtros['capacidade'] ?? 0) ?: '' ?>"
                       style="max-width:100px;">
                <div class="sf-search-divider"></div>
                <input class="sf-search-field" type="date" name="data_inicio"
                       min="<?= date('Y-m-d') ?>"
                       value="<?= htmlspecialchars($filtros['data_inicio'] ?? '') ?>"
                       placeholder="Check-in"
                       style="max-width:130px;">
                <div class="sf-search-divider"></div>
                <input class="sf-search-field" type="date" name="data_fim"
                       min="<?= date('Y-m-d') ?>"
                       value="<?= htmlspecialchars($filtros['data_fim'] ?? '') ?>"
                       placeholder="Check-out"
                       style="max-width:130px;">
                <button class="sf-search-btn" type="submit" title="Buscar">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="sf-search-container">
    <!-- Sidebar de Filtros -->
    <aside class="sf-filters-sidebar">
        <div class="sf-filters-header">
            <h3><i class="fas fa-filter"></i> Filtros</h3>
            <a href="<?= BASE_URL ?>/chacaras" class="sf-clear-filters">Limpar</a>
        </div>

        <form method="GET" action="<?= BASE_URL ?>/chacaras" id="form-filtros-completos">
            <!-- Preservar valores da busca rápida -->
            <?php if (!empty($filtros['cidade'])): ?>
                <input type="hidden" name="cidade" value="<?= htmlspecialchars($filtros['cidade']) ?>">
            <?php endif; ?>
            <?php if (!empty($filtros['data_inicio'])): ?>
                <input type="hidden" name="data_inicio" value="<?= htmlspecialchars($filtros['data_inicio']) ?>">
            <?php endif; ?>
            <?php if (!empty($filtros['data_fim'])): ?>
                <input type="hidden" name="data_fim" value="<?= htmlspecialchars($filtros['data_fim']) ?>">
            <?php endif; ?>

            <!-- Ordenação -->
            <div class="sf-filter-group">
                <label class="sf-filter-label">Ordenar por</label>
                <select name="ordenar" class="sf-filter-select" onchange="this.form.submit()">
                    <?php foreach ($opcoesOrdenacao as $valor => $label): ?>
                        <option value="<?= $valor ?>" <?= ($filtros['ordenar'] ?? 'relevancia') === $valor ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Capacidade -->
            <div class="sf-filter-group">
                <label class="sf-filter-label">Capacidade</label>
                <div class="sf-range-inputs">
                    <input type="number" name="capacidade" placeholder="Min"
                           value="<?= (int)($filtros['capacidade'] ?? 0) ?: '' ?>" min="1">
                    <span>a</span>
                    <input type="number" name="capacidade_max" placeholder="Max"
                           value="<?= (int)($filtros['capacidade_max'] ?? 0) ?: '' ?>" min="1">
                </div>
            </div>

            <!-- Preço -->
            <div class="sf-filter-group">
                <label class="sf-filter-label">Faixa de Preço</label>
                <div class="sf-price-range">
                    <div class="sf-range-inputs">
                        <div class="sf-price-input">
                            <span>R$</span>
                            <input type="number" name="preco_min" placeholder="Min"
                                   value="<?= (float)($filtros['preco_min'] ?? 0) ?: '' ?>" min="0" step="50">
                        </div>
                        <span>-</span>
                        <div class="sf-price-input">
                            <span>R$</span>
                            <input type="number" name="preco_max" placeholder="Max"
                                   value="<?= (float)($filtros['preco_max'] ?? 0) ?: '' ?>" min="0" step="50">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tipo de Cobrança -->
            <div class="sf-filter-group">
                <label class="sf-filter-label">Tipo de Cobrança</label>
                <div class="sf-filter-options">
                    <label class="sf-checkbox">
                        <input type="radio" name="tipo_cobranca" value=""
                               <?= empty($filtros['tipo_cobranca']) ? 'checked' : '' ?>>
                        <span>Qualquer</span>
                    </label>
                    <label class="sf-checkbox">
                        <input type="radio" name="tipo_cobranca" value="DIARIA"
                               <?= ($filtros['tipo_cobranca'] ?? '') === 'DIARIA' ? 'checked' : '' ?>>
                        <span>Por diária</span>
                    </label>
                    <label class="sf-checkbox">
                        <input type="radio" name="tipo_cobranca" value="PERIODO"
                               <?= ($filtros['tipo_cobranca'] ?? '') === 'PERIODO' ? 'checked' : '' ?>>
                        <span>Por período</span>
                    </label>
                </div>
            </div>

            <!-- Comodidades -->
            <?php if (!empty($comodidades)): ?>
                <div class="sf-filter-group">
                    <label class="sf-filter-label">Comodidades</label>
                    <div class="sf-filter-options sf-filter-scroll">
                        <?php foreach ($comodidades as $com): ?>
                            <label class="sf-checkbox">
                                <input type="checkbox" name="comodidades[]" value="<?= $com['id'] ?>"
                                       <?= in_array($com['id'], $filtros['comodidades'] ?? []) ? 'checked' : '' ?>>
                                <span><?= htmlspecialchars($com['nome']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <button type="submit" class="sf-btn-apply">Aplicar Filtros</button>
        </form>
    </aside>

    <!-- Resultados -->
    <div class="sf-results-area">
        <!-- Mensagens de erro -->
        <?php if (!empty($erros)): ?>
            <div class="sf-alert sf-alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <ul>
                    <?php foreach ($erros as $erro): ?>
                        <li><?= htmlspecialchars($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Header de resultados -->
        <div class="sf-results-header">
            <p class="sf-results-count">
                <?php if (empty($erros)): ?>
                    <strong><?= count($chacaras) ?></strong>
                    chácara<?= count($chacaras) !== 1 ? 's' : '' ?> encontrada<?= count($chacaras) !== 1 ? 's' : '' ?>
                <?php else: ?>
                    Corrija os erros para ver resultados
                <?php endif; ?>
            </p>

            <?php if (!empty(array_filter($filtros))): ?>
                <div class="sf-active-filters">
                    <?php if (!empty($filtros['cidade'])): ?>
                        <span class="sf-filter-tag"><?= htmlspecialchars($filtros['cidade']) ?> <a href="<?= BASE_URL ?>/chacaras?<?= http_build_query(array_diff_key($filtros, ['cidade' => ''])) ?>">×</a></span>
                    <?php endif; ?>
                    <?php if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])): ?>
                        <span class="sf-filter-tag">
                            <?= date('d/m', strtotime($filtros['data_inicio'])) ?> - <?= date('d/m', strtotime($filtros['data_fim'])) ?>
                            <a href="<?= BASE_URL ?>/chacaras?<?= http_build_query(array_diff_key($filtros, ['data_inicio' => '', 'data_fim' => ''])) ?>">×</a>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Lista de chácaras -->
        <?php if (empty($chacaras) && empty($erros)): ?>
            <div class="sf-empty-state">
                <span class="sf-empty-icon"><i class="fas fa-tree"></i></span>
                <p class="sf-empty-title">Nenhuma chácara encontrada</p>
                <p class="sf-empty-desc">Tente ajustar os filtros ou buscar por outra cidade/período.</p>
                <a href="<?= BASE_URL ?>/chacaras" class="btn btn-outline-secondary">Limpar filtros</a>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($chacaras as $c): ?>
                    <div class="col">
                        <div class="sf-card card h-100 <?= !empty($c['disponibilidade']['disponivel']) ? 'sf-card-available' : '' ?>"
                             data-chacara-id="<?= $c['id'] ?>">
                            <a href="<?= BASE_URL ?>/chacaras/<?= $c['id'] ?>" class="sf-card-link text-decoration-none">
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

                                    <!-- Badge de disponibilidade -->
                                    <?php if (!empty($c['disponibilidade'])): ?>
                                        <?php if ($c['disponibilidade']['disponivel']): ?>
                                            <span class="sf-badge sf-badge-success">
                                                <i class="fas fa-check-circle"></i> Disponível
                                            </span>
                                        <?php else: ?>
                                            <span class="sf-badge sf-badge-danger">
                                                <i class="fas fa-times-circle"></i> Indisponível
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <!-- Nota média -->
                                    <?php if ($c['nota_media']): ?>
                                        <span class="sf-card-rating-overlay">
                                            <i class="fas fa-star"></i>
                                            <?= number_format((float)$c['nota_media'], 1) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <p class="sf-card-title"><?= htmlspecialchars($c['nome']) ?></p>
                                    </div>
                                    <p class="sf-card-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?= htmlspecialchars($c['cidade'] . ', ' . $c['estado']) ?>
                                    </p>
                                    <p class="sf-card-capacity">
                                        <i class="fas fa-users"></i>
                                        até <?= $c['capacidade_maxima'] ?> pessoas
                                    </p>
                                    <p class="sf-card-price mt-2 mb-0">
                                        <strong>R$ <?= number_format((float)$c['preco_diaria'], 2, ',', '.') ?></strong>
                                        <span class="sf-price-unit"> / <?= $c['tipo_cobranca'] === 'DIARIA' ? 'diária' : 'período' ?></span>
                                    </p>
                                </div>
                            </a>

                            <!-- Verificação rápida de disponibilidade -->
                            <?php if (empty($c['disponibilidade']) && !empty($filtros['data_inicio']) && !empty($filtros['data_fim'])): ?>
                                <div class="sf-card-check" data-id="<?= $c['id'] ?>">
                                    <button class="sf-btn-check-availability"
                                            onclick="verificarDisponibilidade(<?= $c['id'] ?>, '<?= $filtros['data_inicio'] ?>', '<?= $filtros['data_fim'] ?>')">
                                        <i class="fas fa-calendar-check"></i> Verificar disponibilidade
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
/**
 * Verifica disponibilidade via AJAX
 */
async function verificarDisponibilidade(chacaraId, dataInicio, dataFim) {
    const btn = document.querySelector(`[data-id="${chacaraId}"] .sf-btn-check-availability`);
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando...';

    try {
        const response = await fetch(`${BASE_URL}/chacaras/${chacaraId}/disponibilidade?data_inicio=${dataInicio}&data_fim=${dataFim}`);
        const data = await response.json();

        const container = document.querySelector(`[data-id="${chacaraId}"]`).closest('.sf-card');

        if (data.disponivel) {
            container.classList.add('sf-card-available');
            container.querySelector('.sf-card-img-wrap').insertAdjacentHTML('beforeend', `
                <span class="sf-badge sf-badge-success">
                    <i class="fas fa-check-circle"></i> Disponível
                </span>
            `);
        } else {
            container.classList.add('sf-card-unavailable');
            container.querySelector('.sf-card-img-wrap').insertAdjacentHTML('beforeend', `
                <span class="sf-badge sf-badge-danger">
                    <i class="fas fa-times-circle"></i> Indisponível
                </span>
            `);
        }

        // Remove o botão
        btn.parentElement.remove();

    } catch (error) {
        console.error('Erro:', error);
        btn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Erro';
        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }, 2000);
    }
}

// Validação de datas no formulário
document.getElementById('form-busca-rapida').addEventListener('submit', function(e) {
    const dataInicio = this.querySelector('[name="data_inicio"]').value;
    const dataFim = this.querySelector('[name="data_fim"]').value;

    if (dataInicio && dataFim && dataFim < dataInicio) {
        e.preventDefault();
        alert('A data de término deve ser posterior à data de início');
    }
});
</script>

<style>
/* Container principal */
.sf-search-container {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 30px;
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

/* Sidebar de filtros */
.sf-filters-sidebar {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 20px;
    height: fit-content;
    position: sticky;
    top: 100px;
}

.sf-filters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.sf-filters-header h3 {
    margin: 0;
    font-size: 1.1rem;
    color: #333;
}

.sf-filters-header h3 i {
    color: #667eea;
    margin-right: 8px;
}

.sf-clear-filters {
    font-size: 0.85rem;
    color: #667eea;
    text-decoration: none;
}

.sf-clear-filters:hover {
    text-decoration: underline;
}

/* Grupos de filtros */
.sf-filter-group {
    margin-bottom: 25px;
}

.sf-filter-label {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
    font-size: 0.95rem;
}

.sf-filter-select {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: border-color 0.2s;
}

.sf-filter-select:focus {
    border-color: #667eea;
    outline: none;
}

/* Inputs de range */
.sf-range-inputs {
    display: flex;
    align-items: center;
    gap: 8px;
}

.sf-range-inputs input {
    flex: 1;
    padding: 10px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 0.95rem;
}

.sf-range-inputs input:focus {
    border-color: #667eea;
    outline: none;
}

.sf-range-inputs span {
    color: #666;
    font-size: 0.9rem;
}

/* Preço */
.sf-price-range {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}

.sf-price-input {
    display: flex;
    align-items: center;
    gap: 5px;
    flex: 1;
}

.sf-price-input span {
    color: #666;
    font-size: 0.9rem;
}

.sf-price-input input {
    width: 100%;
    padding: 8px;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
}

/* Opções de filtro (checkboxes/radios) */
.sf-filter-options {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.sf-filter-scroll {
    max-height: 200px;
    overflow-y: auto;
    padding-right: 5px;
}

.sf-checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    font-size: 0.9rem;
    color: #555;
}

.sf-checkbox input {
    width: 18px;
    height: 18px;
    accent-color: #667eea;
}

/* Botão aplicar */
.sf-btn-apply {
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.sf-btn-apply:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

/* Área de resultados */
.sf-results-area {
    min-width: 0;
}

.sf-results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.sf-results-count {
    color: #666;
    margin: 0;
}

.sf-results-count strong {
    color: #333;
}

/* Tags de filtros ativos */
.sf-active-filters {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.sf-filter-tag {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #e8eaf6;
    color: #3f51b5;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
}

.sf-filter-tag a {
    color: #3f51b5;
    text-decoration: none;
    font-weight: bold;
}

/* Cards aprimorados */
.sf-card {
    transition: transform 0.2s, box-shadow 0.2s;
    overflow: hidden;
}

.sf-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.sf-card-available {
    border: 2px solid #4caf50;
}

.sf-card-unavailable {
    opacity: 0.7;
}

.sf-card-available:hover {
    box-shadow: 0 8px 25px rgba(76, 175, 80, 0.2);
}

.sf-card-img-wrap {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.sf-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.sf-badge-success {
    background: #4caf50;
    color: white;
}

.sf-badge-danger {
    background: #f44336;
    color: white;
}

.sf-card-rating-overlay {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(255,255,255,0.95);
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #333;
}

.sf-card-rating-overlay i {
    color: #ffc107;
    margin-right: 3px;
}

.sf-card-capacity {
    color: #666;
    font-size: 0.9rem;
    margin: 5px 0;
}

.sf-card-capacity i {
    color: #667eea;
    margin-right: 5px;
}

/* Botão verificar disponibilidade */
.sf-card-check {
    padding: 0 15px 15px;
}

.sf-btn-check-availability {
    width: 100%;
    padding: 10px;
    background: #f8f9fa;
    border: 2px dashed #667eea;
    border-radius: 8px;
    color: #667eea;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.sf-btn-check-availability:hover {
    background: #667eea;
    color: white;
}

.sf-btn-check-availability:disabled {
    opacity: 0.7;
    cursor: wait;
}

/* Alertas */
.sf-alert {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.sf-alert-danger {
    background: #ffebee;
    color: #c62828;
    border: 1px solid #ffcdd2;
}

.sf-alert ul {
    margin: 0;
    padding-left: 20px;
}

/* Responsivo */
@media (max-width: 992px) {
    .sf-search-container {
        grid-template-columns: 1fr;
    }

    .sf-filters-sidebar {
        position: static;
        order: 2;
    }

    .sf-results-area {
        order: 1;
    }
}

@media (max-width: 576px) {
    .sf-search-pill {
        flex-direction: column;
        gap: 10px;
    }

    .sf-search-pill input {
        width: 100% !important;
        max-width: none !important;
    }

    .sf-search-divider {
        display: none;
    }

    .sf-results-header {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
