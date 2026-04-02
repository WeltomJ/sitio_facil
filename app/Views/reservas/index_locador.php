<?php
$pageTitle = 'Gerenciar Reservas';

$statusConfig = [
    'PENDENTE'   => ['warning', 'Pendente',   'fa-clock', 'Aguardando sua aprovação'],
    'CONFIRMADA' => ['success', 'Confirmada', 'fa-check-circle', 'Reserva aprovada'],
    'RECUSADA'   => ['danger',  'Recusada',   'fa-times-circle', 'Você recusou esta reserva'],
    'CANCELADA'  => ['secondary', 'Cancelada', 'fa-ban', 'Reserva cancelada'],
    'CONCLUIDA'  => ['info',    'Concluída',  'fa-flag-checkered', 'Estadia finalizada'],
];

// Calcular totais
$totalPendentes = count(array_filter($reservas, fn($r) => $r['status'] === 'PENDENTE'));
$totalConfirmadas = count(array_filter($reservas, fn($r) => $r['status'] === 'CONFIRMADA'));
$receitaConfirmada = array_sum(array_map(fn($r) => $r['status'] === 'CONFIRMADA' ? $r['valor_total'] : 0, $reservas));

// Separar reservas por status para abas
$reservasPendentes = array_filter($reservas, fn($r) => $r['status'] === 'PENDENTE');
$reservasAtivas = array_filter($reservas, fn($r) => in_array($r['status'], ['CONFIRMADA']));
$reservasPassadas = array_filter($reservas, fn($r) => in_array($r['status'], ['CONCLUIDA', 'CANCELADA', 'RECUSADA']));
?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Gerenciar Reservas</h1>
        <p class="text-muted mb-0">Aprove ou recuse solicitações de reserva</p>
    </div>
    <a href="<?= BASE_URL ?>/locador/reservas/historico" class="btn btn-outline-primary">
        <i class="fas fa-history me-2"></i>Ver Histórico Completo
    </a>
</div>

<!-- Cards de Estatísticas Rápidas -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-warning border-start-4 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Pendentes</p>
                        <h3 class="fw-bold mb-0"><?= $totalPendentes ?></h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-clock text-warning fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-success border-start-4 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Confirmadas</p>
                        <h3 class="fw-bold mb-0"><?= $totalConfirmadas ?></h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-check-circle text-success fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-info border-start-4 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Próximos Check-ins</p>
                        <h3 class="fw-bold mb-0"><?= $estatisticas['proximas'] ?? 0 ?></h3>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-calendar-check text-info fa-lg"></i>
                    </div>
                </div>
                <p class="text-muted small mt-2 mb-0">Próximos 7 dias</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-primary border-start-4 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Receita Confirmada</p>
                        <h3 class="fw-bold mb-0">R$ <?= number_format($receitaConfirmada, 2, ',', '.') ?></h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-dollar-sign text-primary fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Abas de Reservas -->
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <ul class="nav nav-tabs card-header-tabs" id="reservasTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active position-relative" id="pendentes-tab" data-bs-toggle="tab" data-bs-target="#pendentes" type="button">
                    Pendentes
                    <?php if ($totalPendentes > 0): ?>
                        <span class="badge bg-warning text-dark ms-1"><?= $totalPendentes ?></span>
                    <?php endif; ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ativas-tab" data-bs-toggle="tab" data-bs-target="#ativas" type="button">
                    Confirmadas
                    <?php if ($totalConfirmadas > 0): ?>
                        <span class="badge bg-success ms-1"><?= $totalConfirmadas ?></span>
                    <?php endif; ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="passadas-tab" data-bs-toggle="tab" data-bs-target="#passadas" type="button">
                    Finalizadas
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content" id="reservasTabsContent">
            <!-- Aba Pendentes -->
            <div class="tab-pane fade show active" id="pendentes">
                <?php if (empty($reservasPendentes)): ?>
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-inbox fa-3x text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-muted">Nenhuma reserva pendente</h5>
                        <p class="text-muted mb-0">Quando receber solicitações, elas aparecerão aqui.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Chácara</th>
                                    <th>Período</th>
                                    <th>Hóspedes</th>
                                    <th>Valor</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservasPendentes as $r): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width:40px;height:40px;">
                                                    <i class="fas fa-user text-secondary"></i>
                                                </div>
                                                <div>
                                                    <p class="fw-semibold mb-0"><?= htmlspecialchars($r['cliente_nome']) ?></p>
                                                    <small class="text-muted"><?= htmlspecialchars($r['cliente_telefone'] ?? 'Sem telefone') ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-medium"><?= htmlspecialchars($r['chacara_nome']) ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="far fa-calendar text-muted me-2"></i>
                                                <div>
                                                    <div><?= date('d/m/Y', strtotime($r['data_inicio'])) ?></div>
                                                    <div class="text-muted small">até <?= date('d/m/Y', strtotime($r['data_fim'])) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                <i class="fas fa-users me-1"></i><?= (int)$r['qtd_hospedes'] ?> hóspedes
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">R$ <?= number_format($r['valor_total'], 2, ',', '.') ?></span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex gap-2 justify-content-end">
                                                <form method="POST" action="<?= BASE_URL ?>/locador/reservas/<?= $r['id'] ?>/confirmar"
                                                      onsubmit="return confirm('Confirmar esta reserva?')" class="d-inline">
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-check me-1"></i> Aceitar
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-outline-danger btn-sm js-abrir-recusa"
                                                        data-id="<?= $r['id'] ?>"
                                                        data-cliente="<?= htmlspecialchars($r['cliente_nome']) ?>"
                                                        data-chacara="<?= htmlspecialchars($r['chacara_nome']) ?>"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalRecusa">
                                                    <i class="fas fa-times me-1"></i> Recusar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Aba Confirmadas -->
            <div class="tab-pane fade" id="ativas">
                <?php if (empty($reservasAtivas)): ?>
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-calendar-check fa-3x text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-muted">Nenhuma reserva confirmada</h5>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Chácara</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Hóspedes</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservasAtivas as $r):
                                    $hoje = date('Y-m-d');
                                    $emAndamento = ($r['data_inicio'] <= $hoje && $r['data_fim'] >= $hoje);
                                    $checkinHoje = $r['data_inicio'] === $hoje;
                                ?>
                                    <tr class="<?= $checkinHoje ? 'table-warning' : '' ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width:40px;height:40px;">
                                                    <i class="fas fa-user text-success"></i>
                                                </div>
                                                <div>
                                                    <p class="fw-semibold mb-0"><?= htmlspecialchars($r['cliente_nome']) ?></p>
                                                    <small class="text-muted"><?= htmlspecialchars($r['cliente_telefone'] ?? 'Sem telefone') ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($r['chacara_nome']) ?></td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($r['data_inicio'])) ?>
                                            <?php if ($checkinHoje): ?>
                                                <span class="badge bg-warning text-dark ms-1">Hoje!</span>
                                            <?php elseif ($emAndamento): ?>
                                                <span class="badge bg-info ms-1">Em andamento</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($r['data_fim'])) ?></td>
                                        <td><?= (int)$r['qtd_hospedes'] ?></td>
                                        <td><span class="fw-bold">R$ <?= number_format($r['valor_total'], 2, ',', '.') ?></span></td>
                                        <td><span class="badge bg-success"><i class="fas fa-check me-1"></i>Confirmada</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Aba Finalizadas -->
            <div class="tab-pane fade" id="passadas">
                <?php if (empty($reservasPassadas)): ?>
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-history fa-3x text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-muted">Nenhuma reserva finalizada</h5>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Chácara</th>
                                    <th>Período</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservasPassadas as $r):
                                    [$badgeClass, $label, $icon] = $statusConfig[$r['status']];
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($r['cliente_nome']) ?></td>
                                        <td><?= htmlspecialchars($r['chacara_nome']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($r['data_inicio'])) ?> - <?= date('d/m/Y', strtotime($r['data_fim'])) ?></td>
                                        <td>R$ <?= number_format($r['valor_total'], 2, ',', '.') ?></td>
                                        <td><span class="badge bg-<?= $badgeClass ?>"><i class="fas <?= $icon ?> me-1"></i><?= $label ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Recusa -->
<div class="modal fade" id="modalRecusa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-times-circle text-danger me-2"></i>Recusar Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formRecusa" method="POST" action="">
                <div class="modal-body">
                    <p>Você está recusando a reserva de <strong id="nomeClienteRecusa"></strong> para <strong id="nomeChacaraRecusa"></strong>.</p>
                    <div class="mb-3">
                        <label class="form-label">Motivo da recusa (opcional)</label>
                        <textarea name="motivo" class="form-control" rows="3"
                                  placeholder="Explique o motivo para o cliente..."></textarea>
                        <div class="form-text">O cliente receberá este motivo por notificação.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i>Confirmar Recusa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Preenche modal de recusa
document.querySelectorAll('.js-abrir-recusa').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const cliente = this.dataset.cliente;
        const chacara = this.dataset.chacara;

        document.getElementById('formRecusa').action = '<?= BASE_URL ?>/locador/reservas/' + id + '/recusar';
        document.getElementById('nomeClienteRecusa').textContent = cliente;
        document.getElementById('nomeChacaraRecusa').textContent = chacara;
    });
});
</script>
