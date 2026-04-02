<?php
$pageTitle = 'Histórico de Reservas';

$statusConfig = [
    'PENDENTE'   => ['warning', 'Pendente', 'fa-clock'],
    'CONFIRMADA' => ['success', 'Confirmada', 'fa-check-circle'],
    'RECUSADA'   => ['danger', 'Recusada', 'fa-times-circle'],
    'CANCELADA'  => ['secondary', 'Cancelada', 'fa-ban'],
    'CONCLUIDA'  => ['info', 'Concluída', 'fa-flag-checkered'],
];

// Totais para cards
$totalReservas = count($reservas);
$receitaTotal = array_sum(array_map(fn($r) => $r['status'] === 'CONFIRMADA' ? $r['valor_total'] : 0, $reservas));
$reservasConfirmadas = count(array_filter($reservas, fn($r) => $r['status'] === 'CONFIRMADA'));
$reservasPendentes = count(array_filter($reservas, fn($r) => $r['status'] === 'PENDENTE'));
?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Histórico de Reservas</h1>
        <p class="text-muted mb-0">Relatório completo de todas as suas reservas</p>
    </div>
    <a href="<?= BASE_URL ?>/locador/reservas" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left me-2"></i>Voltar para Gerenciamento
    </a>
</div>

<!-- Cards de Resumo -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mb-1 opacity-75">Total de Reservas</p>
                        <h3 class="fw-bold mb-0"><?= $totalReservas ?></h3>
                    </div>
                    <i class="fas fa-calendar fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mb-1 opacity-75">Receita Total</p>
                        <h3 class="fw-bold mb-0">R$ <?= number_format($receitaTotal, 2, ',', '.') ?></h3>
                    </div>
                    <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mb-1 opacity-75">Taxa de Aprovação</p>
                        <h3 class="fw-bold mb-0"><?= $estatisticas['taxa_ocupacao'] ?>%</h3>
                    </div>
                    <i class="fas fa-chart-line fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mb-1 opacity-75">Pendentes</p>
                        <h3 class="fw-bold mb-0"><?= $reservasPendentes ?></h3>
                    </div>
                    <i class="fas fa-clock fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Coluna de Filtros -->
    <div class="col-lg-3">
        <div class="card shadow-sm sticky-top" style="top: 100px;">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= BASE_URL ?>/locador/reservas/historico">
                    <!-- Busca -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Buscar</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                            <input type="text" name="busca" class="form-control"
                                   placeholder="Cliente ou chácara..."
                                   value="<?= htmlspecialchars($filtros['busca'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            <?php foreach ($statusConfig as $st => $cfg): ?>
                                <option value="<?= $st ?>" <?= ($filtros['status'] ?? '') === $st ? 'selected' : '' ?>>
                                    <?= $cfg[1] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Chácara -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Chácara</label>
                        <select name="chacara_id" class="form-select">
                            <option value="">Todas</option>
                            <?php foreach ($chacaras as $ch): ?>
                                <option value="<?= $ch['id'] ?>" <?= ($filtros['chacara_id'] ?? '') == $ch['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ch['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Período -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Check-in a partir de</label>
                        <input type="date" name="data_inicio" class="form-control"
                               value="<?= htmlspecialchars($filtros['data_inicio'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Check-out até</label>
                        <input type="date" name="data_fim" class="form-control"
                               value="<?= htmlspecialchars($filtros['data_fim'] ?? '') ?>">
                    </div>

                    <!-- Ordenação -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Ordenar por</label>
                        <select name="ordenar" class="form-select">
                            <option value="recentes" <?= ($filtros['ordenar'] ?? '') === 'recentes' ? 'selected' : '' ?>>Mais recentes</option>
                            <option value="antigas" <?= ($filtros['ordenar'] ?? '') === 'antigas' ? 'selected' : '' ?>>Mais antigas</option>
                            <option value="data_inicio" <?= ($filtros['ordenar'] ?? '') === 'data_inicio' ? 'selected' : '' ?>>Data de check-in</option>
                            <option value="valor_desc" <?= ($filtros['ordenar'] ?? '') === 'valor_desc' ? 'selected' : '' ?>>Maior valor</option>
                            <option value="valor_asc" <?= ($filtros['ordenar'] ?? '') === 'valor_asc' ? 'selected' : '' ?>>Menor valor</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Aplicar Filtros
                        </button>
                        <a href="<?= BASE_URL ?>/locador/reservas/historico" class="btn btn-outline-secondary">
                            <i class="fas fa-undo me-2"></i>Limpar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Coluna de Resultados -->
    <div class="col-lg-9">
        <!-- Lista de Reservas -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Resultados</h5>
                <span class="badge bg-secondary"><?= count($reservas) ?> reserva(s)</span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($reservas)): ?>
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-inbox fa-3x text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-muted">Nenhuma reserva encontrada</h5>
                        <p class="text-muted mb-0">Tente ajustar os filtros para ver mais resultados.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Chácara</th>
                                    <th>Período</th>
                                    <th>Diárias</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Data da Reserva</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservas as $r):
                                    [$badgeClass, $label, $icon] = $statusConfig[$r['status']];
                                ?>
                                    <tr>
                                        <td><span class="text-muted">#<?= $r['id'] ?></span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;">
                                                    <i class="fas fa-user text-secondary small"></i>
                                                </div>
                                                <div>
                                                    <p class="fw-medium mb-0 small"><?= htmlspecialchars($r['cliente_nome']) ?></p>
                                                    <small class="text-muted"><?= htmlspecialchars($r['cliente_telefone'] ?? '—') ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($r['chacara_nome']) ?></td>
                                        <td>
                                            <div class="small">
                                                <div><?= date('d/m/Y', strtotime($r['data_inicio'])) ?></div>
                                                <div class="text-muted">até <?= date('d/m/Y', strtotime($r['data_fim'])) ?></div>
                                            </div>
                                        </td>
                                        <td><?= $r['diarias'] ?> noite(s)</td>
                                        <td><strong class="text-success">R$ <?= number_format($r['valor_total'], 2, ',', '.') ?></strong></td>
                                        <td>
                                            <span class="badge bg-<?= $badgeClass ?>">
                                                <i class="fas <?= $icon ?> me-1"></i><?= $label ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('d/m/Y H:i', strtotime($r['criado_em'])) ?>
                                            </small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Estatísticas por Status -->
        <?php if (!empty($estatisticas['por_status'])): ?>
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Reservas por Status</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($estatisticas['por_status'] as $stat):
                            $cor = $statusConfig[$stat['status']][0] ?? 'secondary';
                            $label = $statusConfig[$stat['status']][1] ?? $stat['status'];
                        ?>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center p-3 border rounded">
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-<?= $cor ?> fs-6 px-3 py-2"><?= $stat['total'] ?></span>
                                    </div>
                                    <div class="ms-3">
                                        <p class="fw-semibold mb-0"><?= $label ?></p>
                                        <p class="text-muted mb-0 small">
                                            R$ <?= number_format($stat['valor'] ?? 0, 2, ',', '.') ?> em receita
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Estatísticas por Chácara -->
        <?php if (!empty($estatisticas['por_chacara'])): ?>
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-home me-2"></i>Desempenho por Chácara</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Chácara</th>
                                    <th class="text-center">Total Reservas</th>
                                    <th class="text-end">Receita</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estatisticas['por_chacara'] as $ch): ?>
                                    <tr>
                                        <td><i class="fas fa-tree text-success me-2"></i><?= htmlspecialchars($ch['nome']) ?></td>
                                        <td class="text-center"><span class="badge bg-primary"><?= $ch['total'] ?></span></td>
                                        <td class="text-end fw-bold text-success">R$ <?= number_format($ch['receita'] ?? 0, 2, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
