<?php
/** @var array $reserva */
/** @var array $pagamento */
/** @var int $dias */
/** @var string|null $pixQrBase64 */

$pageTitle = 'Confirmação de reserva — Sítio Fácil';

$metodo       = $pagamento['metodo'] ?? 'SIMULADO';
$pagtoStatus  = $pagamento['status'] ?? 'PAGO';
$isPix        = $metodo === 'PIX';
$isPixPending = $isPix && $pagtoStatus === 'PENDENTE';
?>

<div class="col-md-10 col-lg-8 col-xl-7 mx-auto">

    <?php if ($isPixPending): ?>
    <!-- ══════════════════════════════════════════════════
         PIX PENDENTE — mostra QR Code + polling
    ═══════════════════════════════════════════════════ -->
    <div class="text-center pt-4 pb-3">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4"
             style="width:80px;height:80px;background:#1565C0;box-shadow:0 6px 24px rgba(21,101,192,.35);">
            <i class="fa-brands fa-pix" style="font-size:2rem;color:#fff;"></i>
        </div>
        <h1 class="h3 fw-bold mb-2">Reserva criada!</h1>
        <p class="text-muted mb-3">Escaneie o QR Code abaixo para concluir o pagamento.</p>
        <span class="badge rounded-pill px-3 py-2 text-bg-warning" style="font-size:.875rem;" id="badge-status-pix">
            <i class="fas fa-clock me-1"></i> Aguardando pagamento PIX
        </span>
    </div>

    <!-- QR Code PIX -->
    <div class="card rounded-4 border mb-4">
        <div class="card-body text-center py-4 px-4">
            <p class="fw-semibold mb-3">Escaneie com o seu banco</p>

            <?php if (!empty($pixQrBase64)): ?>
                <div class="d-inline-block p-2 bg-white rounded-3 border shadow-sm mb-3">
                    <img src="data:image/png;base64,<?= htmlspecialchars($pixQrBase64) ?>"
                         alt="QR Code PIX"
                         width="220" height="220"
                         class="d-block"
                         id="pix-qr-img">
                </div>
            <?php else: ?>
                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-3 mb-3"
                     style="width:220px;height:220px;">
                    <i class="fas fa-qrcode fa-4x text-muted"></i>
                </div>
            <?php endif; ?>

            <p class="small text-muted mb-3">
                <i class="fas fa-clock me-1"></i>
                Válido por <strong>30 minutos</strong>
            </p>

            <?php if (!empty($pagamento['pix_codigo'])): ?>
            <!-- Copia e Cola -->
            <div class="mb-3 text-start">
                <label class="form-label fw-semibold small">PIX Copia e Cola</label>
                <div class="input-group">
                    <input type="text"
                           class="form-control form-control-sm font-monospace"
                           id="pix-code"
                           value="<?= htmlspecialchars($pagamento['pix_codigo']) ?>"
                           readonly
                           style="font-size:.68rem;letter-spacing:.02em;">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-copiar-pix">
                        <i class="fas fa-copy me-1"></i>Copiar
                    </button>
                </div>
            </div>
            <?php endif; ?>

            <!-- Status de verificação -->
            <div id="pix-verificando" class="alert alert-info py-2 small d-flex align-items-center gap-2">
                <span class="spinner-border spinner-border-sm flex-shrink-0" role="status"></span>
                Verificando pagamento automaticamente...
            </div>
            <div id="pix-pago" class="alert alert-success py-2 small d-none">
                <i class="fas fa-check-circle me-1"></i>
                <strong>Pagamento confirmado!</strong> Aguardando aprovação do locador.
            </div>

            <div class="row g-2 text-start mt-1">
                <div class="col-12">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="sf-step-badge">1</span>
                        <span class="small">Abra o app do seu banco e acesse <strong>PIX → Pagar</strong>.</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="sf-step-badge">2</span>
                        <span class="small">Escaneie o QR Code ou cole a chave copiada.</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="sf-step-badge">3</span>
                        <span class="small">
                            Confirme o valor de
                            <strong>R$ <?= number_format((float)$reserva['valor_total'], 2, ',', '.') ?></strong>
                            e conclua o pagamento.
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-transparent border-top px-4 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted small">Reserva #<?= (int)$reserva['id'] ?></span>
            <span class="fw-bold">R$ <?= number_format((float)$reserva['valor_total'], 2, ',', '.') ?></span>
        </div>
    </div>

    <script>
    (function () {
        const reservaId   = <?= (int)$reserva['id'] ?>;
        const baseUrl     = '<?= BASE_URL ?>';
        let   intervalId  = null;
        let   tentativas  = 0;
        const maxTentativas = 72; // 6 minutos (5s × 72)

        function verificar() {
            fetch(baseUrl + '/reservas/' + reservaId + '/status-pagamento')
                .then(r => r.json())
                .then(data => {
                    tentativas++;
                    if (data.pago) {
                        clearInterval(intervalId);
                        document.getElementById('pix-verificando').classList.add('d-none');
                        document.getElementById('pix-pago').classList.remove('d-none');

                        const badge = document.getElementById('badge-status-pix');
                        if (badge) {
                            badge.className = 'badge rounded-pill px-3 py-2 text-bg-success';
                            badge.innerHTML = '<i class="fas fa-check me-1"></i> Pagamento confirmado!';
                        }

                        // Redireciona para histórico após 3s
                        setTimeout(() => {
                            window.location.href = baseUrl + '/minhas-reservas';
                        }, 3000);
                    } else if (tentativas >= maxTentativas) {
                        clearInterval(intervalId);
                        document.getElementById('pix-verificando').innerHTML =
                            '<i class="fas fa-clock me-1"></i> Verificação pausada. Atualize a página se já pagou.';
                    }
                })
                .catch(() => {/* ignora erros de rede temporários */});
        }

        // Inicia polling a cada 5 segundos
        intervalId = setInterval(verificar, 5000);

        // Copiar código PIX
        document.getElementById('btn-copiar-pix')?.addEventListener('click', () => {
            const code = document.getElementById('pix-code');
            navigator.clipboard?.writeText(code.value).catch(() => {
                code.select();
                document.execCommand('copy');
            });
            const btn = document.getElementById('btn-copiar-pix');
            btn.innerHTML = '<i class="fas fa-check me-1"></i>Copiado!';
            btn.classList.replace('btn-outline-secondary', 'btn-success');
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-copy me-1"></i>Copiar';
                btn.classList.replace('btn-success', 'btn-outline-secondary');
            }, 2500);
        });
    })();
    </script>

    <?php else: ?>
    <!-- ══════════════════════════════════════════════════
         CARTÃO / SIMULADO — pagamento já processado
    ═══════════════════════════════════════════════════ -->

    <?php
    $metodoLabels = [
        'PIX'      => ['PIX',               'fa-brands fa-pix',  'text-success'],
        'CARTAO'   => ['Cartão de crédito', 'fas fa-credit-card','text-primary'],
        'SIMULADO' => ['Simulado',           'fas fa-gear',       'text-secondary'],
    ];
    $metLabel = $metodoLabels[$metodo][0] ?? $metodo;
    $metIcon  = $metodoLabels[$metodo][1] ?? 'fas fa-credit-card';
    $metColor = $metodoLabels[$metodo][2] ?? 'text-secondary';
    ?>

    <div class="text-center pt-4 pb-5">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4"
             style="width:80px;height:80px;background:var(--sf-green-800);box-shadow:0 6px 24px rgba(46,125,50,.35);">
            <i class="fas fa-check" style="font-size:2rem;color:#fff;"></i>
        </div>
        <h1 class="h3 fw-bold mb-2">Reserva solicitada!</h1>
        <p class="text-muted mb-4">
            Seu pagamento foi processado. Agora é só aguardar o locador confirmar.
        </p>
        <span class="badge text-bg-warning rounded-pill px-3 py-2" style="font-size:.875rem;">
            <i class="fas fa-clock me-1"></i> Aguardando confirmação do locador
        </span>
    </div>

    <?php endif; ?>

    <!-- ── Resumo da reserva ──────────────────────────────────────────── -->
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

        <?php if (!$isPixPending): ?>
        <div class="card-footer bg-transparent border-top px-4 py-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="<?= $metIcon ?> <?= $metColor ?>"></i>
                    <span class="small fw-semibold"><?= htmlspecialchars($metLabel) ?></span>
                    <span class="badge text-bg-success rounded-pill">
                        <i class="fas fa-check me-1"></i>Pago
                    </span>
                </div>
                <span class="text-muted small">Processado em <?= date('d/m/Y \à\s H:i') ?></span>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- ── O que acontece agora ──────────────────────────────────────── -->
    <div class="card rounded-4 border mb-4">
        <div class="card-header bg-transparent border-bottom py-3 px-4">
            <h2 class="h6 fw-bold mb-0">
                <i class="fas fa-list-check me-2 text-primary"></i>
                O que acontece agora?
            </h2>
        </div>
        <div class="card-body px-4 py-2">
            <div class="d-flex gap-3 align-items-start py-3 border-bottom">
                <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0 rounded-circle"
                      style="width:28px;height:28px;min-width:28px;background:var(--sf-green-800);color:#fff;font-size:.65rem;margin-top:1px;">
                    <i class="fas fa-check"></i>
                </span>
                <div>
                    <p class="fw-semibold small mb-0">Reserva solicitada</p>
                    <p class="text-muted small mb-0">Seu pedido foi registrado com sucesso.</p>
                </div>
            </div>
            <div class="d-flex gap-3 align-items-start py-3 border-bottom">
                <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0 rounded-circle"
                      style="width:28px;height:28px;min-width:28px;background:<?= $isPixPending ? 'var(--sf-warning-text)' : 'var(--sf-green-800)' ?>;color:#fff;font-size:.65rem;margin-top:1px;">
                    <i class="fas <?= $isPixPending ? 'fa-clock' : 'fa-check' ?>"></i>
                </span>
                <div>
                    <p class="fw-semibold small mb-0">Pagamento<?= $isPixPending ? ' (aguardando PIX)' : ' recebido' ?></p>
                    <p class="text-muted small mb-0">
                        <?= $isPixPending ? 'Efetue o pagamento PIX para prosseguir.' : 'Valor reservado até a confirmação.' ?>
                    </p>
                </div>
            </div>
            <div class="d-flex gap-3 align-items-start py-3 border-bottom">
                <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0 rounded-circle"
                      style="width:28px;height:28px;min-width:28px;background:var(--sf-warning-bg);color:var(--sf-warning-text);font-size:.65rem;margin-top:1px;">
                    <i class="fas fa-clock"></i>
                </span>
                <div>
                    <p class="fw-semibold small mb-0">Aguardando locador</p>
                    <p class="text-muted small mb-0">O locador irá confirmar ou recusar em até 48h.</p>
                </div>
            </div>
            <div class="d-flex gap-3 align-items-start py-3">
                <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0 rounded-circle"
                      style="width:28px;height:28px;min-width:28px;background:var(--sf-gray-100);color:var(--sf-text-muted);font-size:.65rem;margin-top:1px;">
                    <i class="fas fa-house-chimney"></i>
                </span>
                <div>
                    <p class="fw-semibold small mb-0 text-muted">Aproveite a chácara!</p>
                    <p class="text-muted small mb-0">Após confirmação, você receberá os detalhes de acesso.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Ações ─────────────────────────────────────────────────────── -->
    <div class="d-flex flex-wrap gap-2 pb-4">
        <a href="<?= BASE_URL ?>/minhas-reservas" class="btn btn-primary">
            <i class="fas fa-calendar me-2"></i>Ver minhas reservas
        </a>
        <a href="<?= BASE_URL ?>/chacaras" class="btn btn-outline-secondary">
            <i class="fas fa-search me-2"></i>Explorar mais chácaras
        </a>
    </div>

</div>

