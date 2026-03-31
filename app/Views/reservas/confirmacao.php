<?php
/** @var array $reserva */
/** @var array $pagamento */
/** @var int $dias */

$pageTitle = 'Reserva confirmada — Sítio Fácil';

$metodoLabels = [
    'PIX'      => ['PIX',               'fa-pix',         'text-success'],
    'CARTAO'   => ['Cartão de crédito', 'fa-credit-card', 'text-primary'],
    'SIMULADO' => ['Simulado',          'fa-gear',        'text-secondary'],
    'MANUAL'   => ['Manual',            'fa-handshake',   'text-secondary'],
];

$metodo   = $pagamento['metodo'] ?? 'SIMULADO';
$metLabel = $metodoLabels[$metodo][0] ?? $metodo;
$metIcon  = $metodoLabels[$metodo][1] ?? 'fa-credit-card';
$metColor = $metodoLabels[$metodo][2] ?? 'text-secondary';
?>

<div class="col-md-10 col-lg-8 col-xl-7 mx-auto">

    <!-- ── Cabeçalho de sucesso ──────────────────────────────────── -->
    <div class="text-center pt-4 pb-5">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4"
             style="width:80px;height:80px;background:#2E7D32;box-shadow:0 6px 24px rgba(46,125,50,.35);">
            <i class="fas fa-check text-white" style="font-size:2rem;"></i>
        </div>
        <h1 class="h3 fw-bold mb-2">Reserva solicitada!</h1>
        <p class="text-muted mb-4">
            Seu pagamento foi registrado. Agora é só aguardar o locador confirmar.
        </p>
        <span class="badge text-bg-warning rounded-pill px-3 py-2" style="font-size:.875rem;">
            <i class="fas fa-clock me-1"></i> Aguardando confirmação do locador
        </span>
    </div>

    <!-- ── Resumo da reserva ──────────────────────────────────────── -->
    <div class="card rounded-4 border mb-4">
        <div class="card-header bg-transparent border-bottom py-3 px-4">
            <h2 class="h6 fw-bold mb-0">
                <i class="fas fa-calendar-check me-2 text-success"></i>
                Resumo da reserva #<?= (int) $reserva['id'] ?>
            </h2>
        </div>

        <div class="card-body px-4 py-4">
            <div class="row gy-3">

                <div class="col-sm-6">
                    <p class="text-muted small mb-1">Chácara</p>
                    <p class="fw-semibold mb-0"><?= htmlspecialchars($reserva['chacara_nome']) ?></p>
                </div>

                <div class="col-sm-6">
                    <p class="text-muted small mb-1">Hóspedes</p>
                    <p class="fw-semibold mb-0">
                        <?= (int) $reserva['qtd_hospedes'] ?> pessoa<?= $reserva['qtd_hospedes'] > 1 ? 's' : '' ?>
                    </p>
                </div>

                <div class="col-sm-6">
                    <p class="text-muted small mb-1">Check-in</p>
                    <p class="fw-semibold mb-0">
                        <?= date('d/m/Y', strtotime($reserva['data_inicio'])) ?>
                        <?php if (!empty($reserva['horario_checkin'])): ?>
                            <span class="text-muted small">às <?= substr($reserva['horario_checkin'], 0, 5) ?>h</span>
                        <?php endif; ?>
                    </p>
                </div>

                <div class="col-sm-6">
                    <p class="text-muted small mb-1">Check-out</p>
                    <p class="fw-semibold mb-0">
                        <?= date('d/m/Y', strtotime($reserva['data_fim'])) ?>
                        <?php if (!empty($reserva['horario_checkout'])): ?>
                            <span class="text-muted small">às <?= substr($reserva['horario_checkout'], 0, 5) ?>h</span>
                        <?php endif; ?>
                    </p>
                </div>

                <div class="col-sm-6">
                    <p class="text-muted small mb-1">Duração</p>
                    <p class="fw-semibold mb-0"><?= $dias ?> diária<?= $dias > 1 ? 's' : '' ?></p>
                </div>

                <div class="col-sm-6">
                    <p class="text-muted small mb-1">Valor total</p>
                    <p class="fw-bold mb-0" style="font-size:1.2rem;">
                        R$ <?= number_format((float) $reserva['valor_total'], 2, ',', '.') ?>
                    </p>
                </div>

            </div>
        </div>

        <div class="card-footer bg-transparent border-top px-4 py-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas <?= $metIcon ?> <?= $metColor ?>"></i>
                    <span class="small fw-semibold"><?= htmlspecialchars($metLabel) ?></span>
                    <span class="badge text-bg-success rounded-pill">
                        <i class="fas fa-check me-1"></i>Pago
                    </span>
                </div>
                <span class="text-muted small">
                    Processado em <?= date('d/m/Y \à\s H:i') ?>
                </span>
            </div>
        </div>
    </div>

    <!-- ── O que acontece agora ──────────────────────────────────── -->
    <div class="card rounded-4 border mb-4">
        <div class="card-header bg-transparent border-bottom py-3 px-4">
            <h2 class="h6 fw-bold mb-0">
                <i class="fas fa-list-check me-2 text-primary"></i>
                O que acontece agora?
            </h2>
        </div>

        <div class="card-body px-4 py-2">

            <div class="d-flex gap-3 align-items-start py-3 border-bottom">
                <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0 rounded-circle text-white"
                      style="width:28px;height:28px;min-width:28px;background:#2E7D32;font-size:.65rem;margin-top:1px;">
                    <i class="fas fa-check"></i>
                </span>
                <div>
                    <p class="fw-semibold small mb-0">Reserva solicitada</p>
                    <p class="text-muted small mb-0">Seu pedido foi registrado com sucesso.</p>
                </div>
            </div>

            <div class="d-flex gap-3 align-items-start py-3 border-bottom">
                <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0 rounded-circle text-white"
                      style="width:28px;height:28px;min-width:28px;background:#2E7D32;font-size:.65rem;margin-top:1px;">
                    <i class="fas fa-check"></i>
                </span>
                <div>
                    <p class="fw-semibold small mb-0">Pagamento recebido</p>
                    <p class="text-muted small mb-0">Valor reservado até a confirmação.</p>
                </div>
            </div>

            <div class="d-flex gap-3 align-items-start py-3 border-bottom">
                <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0 rounded-circle"
                      style="width:28px;height:28px;min-width:28px;background:#FFC107;color:#333;font-size:.65rem;margin-top:1px;">
                    <i class="fas fa-clock"></i>
                </span>
                <div>
                    <p class="fw-semibold small mb-0">Aguardando locador</p>
                    <p class="text-muted small mb-0">O locador irá confirmar ou recusar em até 48h.</p>
                </div>
            </div>

            <div class="d-flex gap-3 align-items-start py-3">
                <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0 rounded-circle"
                      style="width:28px;height:28px;min-width:28px;background:#DDDDDD;color:#888;font-size:.65rem;margin-top:1px;">
                    <i class="fas fa-house-chimney"></i>
                </span>
                <div>
                    <p class="fw-semibold small mb-0 text-muted">Aproveite a chácara!</p>
                    <p class="text-muted small mb-0">Após confirmação, você receberá os detalhes de acesso.</p>
                </div>
            </div>

        </div>
    </div>

    <!-- ── Ações ─────────────────────────────────────────────────── -->
    <div class="d-flex flex-wrap gap-2 pb-4">
        <a href="<?= BASE_URL ?>/minhas-reservas" class="btn btn-primary">
            <i class="fas fa-calendar me-2"></i>Ver minhas reservas
        </a>
        <a href="<?= BASE_URL ?>/chacaras" class="btn btn-outline-secondary">
            <i class="fas fa-search me-2"></i>Explorar mais chácaras
        </a>
    </div>

</div>
