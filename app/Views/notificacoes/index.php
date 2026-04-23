<?php $pageTitle = 'Notificações — Sítio Fácil'; ?>

<h1 class="h3 fw-bold mb-4">Notificações</h1>

<?php if (empty($notificacoes)): ?>
    <div class="sf-empty-state">
        <div class="sf-empty-icon"><i class="fas fa-bell-slash"></i></div>
        <p class="sf-empty-title">Nenhuma notificação</p>
        <p class="sf-empty-desc">Quando houver novidades, elas aparecerão aqui.</p>
    </div>
<?php else: ?>
    <div class="list-group list-group-flush border rounded-3">
        <?php foreach ($notificacoes as $n): ?>
            <div class="list-group-item d-flex justify-content-between align-items-start gap-3 py-3">
                <div class="d-flex gap-2 align-items-start">
                    <?php if (!$n['lida']): ?>
                        <span class="sf-dot-unread mt-1 flex-shrink-0"></span>
                    <?php else: ?>
                        <span class="flex-shrink-0" style="width:8px;display:inline-block;"></span>
                    <?php endif; ?>
                    <div>
                        <p class="fw-semibold small mb-1"><?= htmlspecialchars($n['titulo']) ?></p>
                        <p class="text-muted mb-0" style="font-size:.75rem;"><?= htmlspecialchars($n['mensagem']) ?></p>
                    </div>
                </div>
                <span class="text-muted text-nowrap" style="font-size:.75rem;">
                    <?= date('d/m/Y H:i', strtotime($n['criado_em'])) ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
