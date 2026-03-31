<?php
$pageTitle = 'Minhas Reservas';
$statusConfig = [
    'PENDENTE'   => ['text-bg-warning',   'Pendente',   'fa-clock'],
    'CONFIRMADA' => ['text-bg-success',   'Confirmada', 'fa-check-circle'],
    'RECUSADA'   => ['text-bg-danger',    'Recusada',   'fa-times-circle'],
    'CANCELADA'  => ['text-bg-secondary', 'Cancelada',  'fa-ban'],
    'CONCLUIDA'  => ['text-bg-info',      'Concluída', 'fa-flag-checkered'],
];
?>

<h1 class="h3 fw-bold mb-4">Minhas Reservas</h1>

<?php if (empty($reservas)): ?>
    <div class="sf-empty-state">
        <div class="sf-empty-icon"><i class="fas fa-calendar"></i></div>
        <p class="sf-empty-title">Nenhuma reserva ainda</p>
        <p class="sf-empty-desc">Explore chácaras e faça sua primeira reserva.</p>
        <a href="<?= BASE_URL ?>/chacaras" class="btn btn-primary mt-2">Buscar chácaras</a>
    </div>
<?php else: ?>
    <div class="table-responsive border rounded-3">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Chácara</th>
                    <th>Localização</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
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
                        <td>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <?= htmlspecialchars(($r['cidade'] ?? '') . '/' . ($r['estado'] ?? '')) ?>
                            </small>
                        </td>
                        <td><?= date('d/m/Y', strtotime($r['data_inicio'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($r['data_fim'])) ?></td>
                        <td><strong>R$ <?= number_format((float)$r['valor_total'], 2, ',', '.') ?></strong></td>
                        <td>
                            <span class="badge rounded-pill <?= $badgeClass ?>">
                                <i class="fas <?= $icon ?> me-1"></i><?= $label ?>
                            </span>
                        </td>
                        <td>
                            <?php if (in_array($r['status'], ['PENDENTE', 'CONFIRMADA'])): ?>
                                <form method="POST"
                                      action="<?= BASE_URL ?>/reservas/<?= $r['id'] ?>/cancelar"
                                      onsubmit="return confirm('Deseja cancelar esta reserva?')">
                                    <button class="btn btn-outline-danger btn-sm" type="submit">
                                        <i class="fas fa-times me-1"></i> Cancelar
                                    </button>
                                </form>
                            <?php elseif ($r['status'] === 'CONCLUIDA'): ?>
                                <button class="btn btn-outline-warning btn-sm js-open-avaliacao"
                                        data-bs-toggle="modal"
                                        data-bs-target="#avaliacao-<?= $r['id'] ?>">
                                    <i class="fas fa-star me-1"></i> Avaliar
                                </button>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modais de avaliação -->
    <?php foreach ($reservas as $r): if ($r['status'] !== 'CONCLUIDA') continue; ?>
        <div id="avaliacao-<?= $r['id'] ?>" class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:16px;">
                    <div class="modal-header">
                        <h5 class="modal-title">Avaliar: <?= htmlspecialchars($r['chacara_nome']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <form method="POST" action="<?= BASE_URL ?>/reservas/<?= $r['id'] ?>/avaliar">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nota (1 a 5):</label>
                                <select class="form-select" name="nota" required>
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <option value="<?= $i ?>"><?= $i ?> estrela<?= $i > 1 ? 's' : '' ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Comentário (opcional):</label>
                                <textarea class="form-control" name="comentario" rows="3"
                                          placeholder="Conte sua experiência..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-warning" type="submit">
                                <i class="fas fa-star me-1"></i> Enviar avaliação
                            </button>
                            <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
