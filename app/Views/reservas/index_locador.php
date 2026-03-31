<?php
$pageTitle = 'Reservas Recebidas';
$statusConfig = [
    'PENDENTE'   => ['text-bg-warning',   'Pendente',   'fa-clock'],
    'CONFIRMADA' => ['text-bg-success',   'Confirmada', 'fa-check-circle'],
    'RECUSADA'   => ['text-bg-danger',    'Recusada',   'fa-times-circle'],
    'CANCELADA'  => ['text-bg-secondary', 'Cancelada',  'fa-ban'],
    'CONCLUIDA'  => ['text-bg-info',      'Concluída', 'fa-flag-checkered'],
];
?>

<h1 class="h3 fw-bold mb-4">Reservas Recebidas</h1>

<?php if (empty($reservas)): ?>
    <div class="sf-empty-state">
        <div class="sf-empty-icon"><i class="fas fa-inbox"></i></div>
        <p class="sf-empty-title">Nenhuma reserva recebida</p>
        <p class="sf-empty-desc">Quando alguém reservar sua chácara, aparecerá aqui.</p>
    </div>
<?php else: ?>
    <div class="table-responsive border rounded-3">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Chácara</th>
                    <th>Cliente</th>
                    <th>Telefone</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Hóspedes</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservas as $r):
                    [$badgeClass, $label, $icon] = $statusConfig[$r['status']];
                ?>
                    <tr>
                        <td class="fw-semibold"><?= htmlspecialchars($r['chacara_nome']) ?></td>
                        <td><?= htmlspecialchars($r['cliente_nome']) ?></td>
                        <td><small><?= htmlspecialchars($r['cliente_telefone'] ?? '—') ?></small></td>
                        <td><?= date('d/m/Y', strtotime($r['data_inicio'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($r['data_fim'])) ?></td>
                        <td><?= (int)($r['qtd_hospedes'] ?? 1) ?></td>
                        <td><strong>R$ <?= number_format((float)$r['valor_total'], 2, ',', '.') ?></strong></td>
                        <td>
                            <span class="badge rounded-pill <?= $badgeClass ?>">
                                <i class="fas <?= $icon ?> me-1"></i><?= $label ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($r['status'] === 'PENDENTE'): ?>
                                <div class="d-flex gap-1">
                                    <form method="POST"
                                          action="<?= BASE_URL ?>/locador/reservas/<?= $r['id'] ?>/confirmar"
                                          onsubmit="return confirm('Confirmar esta reserva?')">
                                        <button class="btn btn-success btn-sm" type="submit">
                                            <i class="fas fa-check me-1"></i> Confirmar
                                        </button>
                                    </form>
                                    <button class="btn btn-outline-danger btn-sm js-abrir-recusa"
                                            data-id="<?= $r['id'] ?>"
                                            data-chacara="<?= htmlspecialchars($r['chacara_nome']) ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modal-recusa">
                                        <i class="fas fa-times me-1"></i> Recusar
                                    </button>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal de recusa com motivo -->
    <div id="modal-recusa" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Recusar reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="form-recusa" method="POST" action="">
                    <div class="modal-body">
                        <p class="mb-3">Tem certeza que deseja recusar a reserva de <strong id="nome-recusa"></strong>?</p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Motivo (opcional):</label>
                            <textarea class="form-control" name="motivo" rows="3"
                                      placeholder="Explique o motivo da recusa..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" type="submit">
                            <i class="fas fa-times me-1"></i> Confirmar recusa
                        </button>
                        <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>
