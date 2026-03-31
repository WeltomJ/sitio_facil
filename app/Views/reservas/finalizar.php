<?php
/** @var array $chacara */
/** @var string $dataInicio */
/** @var string $dataFim */
/** @var int $qtdHospedes */
/** @var float $valorTotal */
/** @var int $dias */
/** @var array|null $foto */

$pageTitle = 'Confirmar reserva — Sítio Fácil';

// PIX simulado
$pixChave     = 'pagamentos@sitiofacil.com.br';
$pixTxId      = strtoupper(substr(md5(uniqid((string)rand(), true)), 0, 25));
$valorFmt     = number_format($valorTotal, 2, '.', '');
$pixEmv       = '00020126580014BR.GOV.BCB.PIX0136' . $pixChave .
                '52040000530398654' . sprintf('%02d', strlen($valorFmt)) . $valorFmt .
                '5802BR5910SitioFacil6009SaoPaulo62290525' . $pixTxId . '6304A1B2';
$pixQrUrl     = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=8&data=' . urlencode($pixEmv);
?>

<!-- breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/chacaras">Chácaras</a></li>
        <li class="breadcrumb-item">
            <a href="<?= BASE_URL ?>/chacaras/<?= (int)$chacara['id'] ?>">
                <?= htmlspecialchars($chacara['nome']) ?>
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Confirmar reserva</li>
    </ol>
</nav>

<h1 class="h4 fw-bold mb-1">Confirmar e pagar</h1>
<p class="text-muted small mb-4">Você não será cobrado agora. O locador precisa confirmar antes.</p>

<div class="row g-4 align-items-start">

    <!-- ═══════════════════════════════════════
         COLUNA ESQUERDA — formulário de pagamento
    ════════════════════════════════════════ -->
    <div class="col-lg-7">

        <form method="POST"
              action="<?= BASE_URL ?>/chacaras/<?= (int)$chacara['id'] ?>/reservar"
              id="form-pagamento"
              novalidate>

            <!-- campos ocultos da reserva -->
            <input type="hidden" name="data_inicio"  value="<?= htmlspecialchars($dataInicio) ?>">
            <input type="hidden" name="data_fim"     value="<?= htmlspecialchars($dataFim) ?>">
            <input type="hidden" name="qtd_hospedes" value="<?= (int) $qtdHospedes ?>">
            <input type="hidden" name="valor_total"  value="<?= $valorTotal ?>">
            <input type="hidden" name="metodo_pagamento" id="metodo_pagamento" value="CARTAO">

            <!-- ── Seleção de método ─────────────────────── -->
            <div class="sf-section-block mb-4">
                <h2 class="h6 fw-bold mb-3">Forma de pagamento</h2>

                <div class="sf-pay-tabs d-flex gap-2 mb-4" role="group" aria-label="Forma de pagamento">
                    <button type="button"
                            class="sf-pay-tab active"
                            data-method="credito"
                            aria-pressed="true">
                        <i class="fas fa-credit-card me-1"></i> Crédito
                    </button>
                    <button type="button"
                            class="sf-pay-tab"
                            data-method="debito"
                            aria-pressed="false">
                        <i class="fas fa-credit-card me-1"></i> Débito
                    </button>
                    <button type="button"
                            class="sf-pay-tab"
                            data-method="pix"
                            aria-pressed="false">
                        <svg width="16" height="16" viewBox="0 0 512 512" fill="currentColor" class="me-1" style="vertical-align:-.15em">
                            <path d="M112.57 391.19c20.056 0 38.928-7.808 53.12-22l74.982-74.982c5.206-5.206 14.146-5.198 19.344 0l75.238 75.238c14.192 14.192 33.064 22 53.12 22h14.972l-95.048 95.046c-29.454 29.454-77.218 29.454-106.674 0l-95.29-95.302h6.236zm286.86-271.38c-20.056 0-38.928 7.808-53.12 22l-75.238 75.24c-5.332 5.332-13.866 5.42-19.166.248-.082-.082-.166-.166-.248-.248L176.572 141.814c-14.192-14.192-33.064-22-53.12-22h-6.228l95.29-95.302c29.454-29.454 77.218-29.454 106.674 0l95.048 95.046-14.8.262zm35.12 44.934-46.334-46.334h-8.356c-14.668 0-28.458 5.712-38.828 16.082l-75.238 75.24c-8.56 8.558-19.892 13.278-31.938 13.278-12.046 0-23.378-4.72-31.948-13.29L126.9 134.502c-10.37-10.37-24.16-16.082-38.83-16.082H79.45L33.118 164.752c-29.454 29.454-29.454 77.22 0 106.674l46.332 46.332h8.356c14.668 0 28.458-5.712 38.828-16.082l74.982-74.982c8.56-8.56 19.892-13.28 31.94-13.28 12.046 0 23.378 4.72 31.948 13.288l75.238 75.24c10.37 10.37 24.16 16.082 38.83 16.082h8.356l46.334-46.334c29.452-29.454 29.452-77.22-.002-106.674z"/>
                        </svg>
                        PIX
                    </button>
                </div>

                <!-- ─── CARTÃO (crédito / débito) ─────────── -->
                <div id="secao-cartao">

                    <!-- Mini-card visual -->
                    <div class="sf-card-visual mb-4" id="card-visual" aria-hidden="true">
                        <div class="sf-card-inner">
                            <div class="sf-card-front">
                                <div class="sf-card-chip"></div>
                                <p class="sf-card-number" id="cv-number">•••• •••• •••• ••••</p>
                                <div class="d-flex justify-content-between align-items-end mt-auto">
                                    <div>
                                        <div class="sf-card-label">Titular</div>
                                        <div class="sf-card-holder" id="cv-holder">NOME DO TITULAR</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="sf-card-label">Validade</div>
                                        <div class="sf-card-expiry" id="cv-expiry">MM/AA</div>
                                    </div>
                                    <div id="cv-brand" class="sf-card-brand">
                                        <i class="fas fa-credit-card fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Número do cartão -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="numero_cartao">Número do cartão</label>
                        <div class="input-group">
                            <input type="text"
                                   class="form-control"
                                   id="numero_cartao"
                                   name="numero_cartao"
                                   maxlength="19"
                                   placeholder="1234 5678 9012 3456"
                                   autocomplete="cc-number"
                                   inputmode="numeric">
                            <span class="input-group-text bg-transparent" id="brand-icon-wrap">
                                <i class="fas fa-credit-card text-muted" id="brand-icon"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Nome no cartão -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="nome_cartao">Nome impresso no cartão</label>
                        <input type="text"
                               class="form-control"
                               id="nome_cartao"
                               name="nome_cartao"
                               placeholder="NOME SOBRENOME"
                               autocomplete="cc-name"
                               style="text-transform:uppercase;">
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Validade -->
                        <div class="col-6">
                            <label class="form-label fw-semibold" for="validade_cartao">Validade</label>
                            <input type="text"
                                   class="form-control"
                                   id="validade_cartao"
                                   name="validade_cartao"
                                   maxlength="5"
                                   placeholder="MM/AA"
                                   autocomplete="cc-exp"
                                   inputmode="numeric">
                        </div>
                        <!-- CVV -->
                        <div class="col-6">
                            <label class="form-label fw-semibold" for="cvv_cartao">
                                CVV
                                <button type="button"
                                        class="btn btn-link p-0 ms-1 text-muted"
                                        tabindex="-1"
                                        data-bs-toggle="tooltip"
                                        title="Código de 3 dígitos no verso do cartão (4 dígitos para Amex)">
                                    <i class="fas fa-question-circle" style="font-size:.85rem;"></i>
                                </button>
                            </label>
                            <input type="text"
                                   class="form-control"
                                   id="cvv_cartao"
                                   name="cvv_cartao"
                                   maxlength="4"
                                   placeholder="•••"
                                   autocomplete="cc-csc"
                                   inputmode="numeric">
                        </div>
                    </div>

                    <!-- Parcelamento (apenas crédito) -->
                    <div id="secao-parcelamento" class="mb-3">
                        <label class="form-label fw-semibold" for="parcelas">Parcelamento</label>
                        <select class="form-select" id="parcelas" name="parcelas">
                            <?php
                            $opcoes = [1 => '', 2 => ' (sem juros)', 3 => ' (sem juros)'];
                            foreach ($opcoes as $n => $obs):
                                $vlParcela = number_format($valorTotal / $n, 2, ',', '.');
                            ?>
                                <option value="<?= $n ?>">
                                    <?= $n ?>× de R$ <?= $vlParcela ?><?= $obs ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>
                <!-- /secao-cartao -->

                <!-- ─── PIX ──────────────────────────────────── -->
                <div id="secao-pix" class="d-none">

                    <div class="text-center p-4 rounded-3 border mb-3" style="background:var(--sf-bg-alt);">
                        <p class="fw-semibold mb-3">Escaneie o QR Code com o seu banco</p>
                        <div class="d-inline-block p-2 bg-white rounded-3 border shadow-sm">
                            <img src="<?= $pixQrUrl ?>"
                                 alt="QR Code PIX"
                                 width="200" height="200"
                                 class="d-block"
                                 onerror="this.src='<?= BASE_URL ?>/img/qr-placeholder.png'">
                        </div>
                        <p class="small text-muted mt-2 mb-0">
                            <i class="fas fa-clock me-1"></i> Válido por <strong>30 minutos</strong>
                        </p>
                    </div>

                    <!-- Copia e cola -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">PIX Copia e Cola</label>
                        <div class="input-group">
                            <input type="text"
                                   class="form-control form-control-sm font-monospace"
                                   id="pix-code"
                                   value="<?= htmlspecialchars($pixEmv) ?>"
                                   readonly
                                   style="font-size:.72rem; letter-spacing:.02em;">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-copiar-pix">
                                <i class="fas fa-copy me-1"></i> Copiar
                            </button>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 px-3 small mb-3">
                        <i class="fas fa-info-circle me-1"></i>
                        Após o pagamento, o sistema confirma automaticamente em até <strong>1 minuto</strong>.
                        A reserva fica <em>pendente</em> até a aprovação do locador.
                    </div>

                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="sf-step-badge">1</span>
                        <span class="small">Abra o app do seu banco e acesse <strong>PIX → Pagar</strong>.</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="sf-step-badge">2</span>
                        <span class="small">Escaneie o QR code ou cole a chave copiada.</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="sf-step-badge">3</span>
                        <span class="small">Confirme o valor de <strong>R$ <?= number_format($valorTotal, 2, ',', '.') ?></strong> e clique em pagar.</span>
                    </div>

                </div>
                <!-- /secao-pix -->

            </div>
            <!-- /sf-section-block -->

            <!-- Segurança -->
            <div class="d-flex align-items-center gap-2 text-muted small mb-4">
                <i class="fas fa-lock"></i>
                <span>Seus dados são criptografados e protegidos com SSL.</span>
            </div>

            <!-- Botão submit -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg fw-bold py-3" id="btn-confirmar">
                    <i class="fas fa-shield-alt me-2"></i>
                    <span id="btn-confirmar-text">Confirmar Reserva</span>
                </button>
            </div>

            <p class="text-muted small text-center mt-3">
                Ao confirmar, você concorda com os
                <a href="#" class="text-decoration-underline">Termos de Uso</a> e a
                <a href="#" class="text-decoration-underline">Política de Cancelamento</a>.
            </p>

        </form>

    </div>
    <!-- /coluna esquerda -->

    <!-- ═══════════════════════════════════════
         COLUNA DIREITA — resumo da reserva
    ════════════════════════════════════════ -->
    <div class="col-lg-5">
        <div class="sf-booking-box sticky-top" style="top:calc(var(--sf-navbar-height) + 24px);">

            <!-- Chácara header -->
            <div class="d-flex gap-3 align-items-center mb-4">
                <?php if (!empty($foto)): ?>
                    <img src="<?= htmlspecialchars($foto['url']) ?>"
                         alt="<?= htmlspecialchars($chacara['nome']) ?>"
                         class="rounded-3 flex-shrink-0"
                         style="width:72px; height:72px; object-fit:cover;">
                <?php else: ?>
                    <div class="rounded-3 flex-shrink-0 d-flex align-items-center justify-content-center bg-light text-muted"
                         style="width:72px; height:72px;">
                        <i class="fas fa-tree fa-xl"></i>
                    </div>
                <?php endif; ?>
                <div class="overflow-hidden">
                    <p class="fw-semibold mb-0 text-truncate"><?= htmlspecialchars($chacara['nome']) ?></p>
                    <p class="text-muted small mb-0">
                        <?= htmlspecialchars(($chacara['cidade'] ?? '') . '/' . ($chacara['estado'] ?? '')) ?>
                    </p>
                    <?php if (!empty($chacara['nota_media']) && $chacara['nota_media'] > 0): ?>
                        <p class="small mb-0 fw-semibold">
                            <i class="fas fa-star" style="font-size:.7rem;"></i>
                            <?= number_format((float)$chacara['nota_media'], 1) ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <hr class="my-3">

            <!-- Sua viagem -->
            <h3 class="h6 fw-bold mb-3">Sua viagem</h3>

            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <p class="fw-semibold small mb-0">Datas</p>
                    <p class="text-muted small mb-0">
                        <?= date('d/m/Y', strtotime($dataInicio)) ?>
                        <i class="fas fa-arrow-right mx-1" style="font-size:.6rem;"></i>
                        <?= date('d/m/Y', strtotime($dataFim)) ?>
                    </p>
                </div>
                <div class="text-end">
                    <p class="small text-muted mb-0"><?= $dias ?> diária<?= $dias > 1 ? 's' : '' ?></p>
                </div>
            </div>

            <?php if ($chacara['horario_checkin']): ?>
                <div class="d-flex justify-content-between mb-2">
                    <p class="small mb-0 text-muted">Check-in</p>
                    <p class="small mb-0 fw-semibold"><?= substr($chacara['horario_checkin'], 0, 5) ?>h</p>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <p class="small mb-0 text-muted">Check-out</p>
                    <p class="small mb-0 fw-semibold"><?= substr($chacara['horario_checkout'], 0, 5) ?>h</p>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between mb-2">
                <p class="small mb-0 text-muted">Hóspedes</p>
                <p class="small mb-0 fw-semibold"><?= (int)$qtdHospedes ?> pessoa<?= $qtdHospedes > 1 ? 's' : '' ?></p>
            </div>

            <hr class="my-3">

            <!-- Detalhes de preço -->
            <h3 class="h6 fw-bold mb-3">Detalhes de preço</h3>

            <div class="d-flex justify-content-between mb-2">
                <span class="small">
                    R$ <?= number_format((float)$chacara['preco_diaria'], 2, ',', '.') ?>
                    × <?= $dias ?> diária<?= $dias > 1 ? 's' : '' ?>
                </span>
                <span class="small fw-semibold">R$ <?= number_format($valorTotal, 2, ',', '.') ?></span>
            </div>

            <hr class="my-3">

            <div class="d-flex justify-content-between">
                <span class="fw-bold">Total</span>
                <span class="fw-bold">R$ <?= number_format($valorTotal, 2, ',', '.') ?></span>
            </div>

            <p class="text-muted small mt-2 mb-0">
                <i class="fas fa-info-circle me-1"></i>
                Cobrança apenas após confirmação do locador.
            </p>

        </div>
    </div>
    <!-- /coluna direita -->

</div>

<!-- ════════════════════════════════
     CSS local
════════════════════════════════ -->
<style>
/* Tabs de método de pagamento */
.sf-pay-tabs { flex-wrap: wrap; }

.sf-pay-tab {
    display: inline-flex;
    align-items: center;
    padding: .5rem 1.1rem;
    border: 2px solid var(--sf-border);
    border-radius: 50px;
    background: transparent;
    color: var(--sf-text-strong);
    font-size: .875rem;
    font-weight: 500;
    cursor: pointer;
    transition: border-color .2s, background .2s, color .2s;
}
.sf-pay-tab:hover {
    border-color: var(--sf-gray-600);
    background: var(--sf-gray-50);
}
.sf-pay-tab.active {
    border-color: var(--sf-gray-800);
    background: var(--sf-gray-800);
    color: #fff;
}
[data-bs-theme="dark"] .sf-pay-tab.active {
    border-color: var(--sf-green-300);
    background: var(--sf-green-800);
    color: #fff;
}

/* Card visual */
.sf-card-visual {
    perspective: 1000px;
    height: 170px;
}
.sf-card-inner {
    position: relative;
    width: 100%;
    height: 100%;
}
.sf-card-front {
    position: absolute;
    inset: 0;
    border-radius: 16px;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    color: #fff;
    padding: 1.25rem 1.5rem;
    display: flex;
    flex-direction: column;
    box-shadow: 0 8px 32px rgba(0,0,0,0.35);
    overflow: hidden;
}
.sf-card-front::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,0.04);
}
.sf-card-front::after {
    content: '';
    position: absolute;
    bottom: -60px; left: -30px;
    width: 220px; height: 220px;
    border-radius: 50%;
    background: rgba(255,255,255,0.03);
}
.sf-card-chip {
    width: 40px; height: 30px;
    border-radius: 5px;
    background: linear-gradient(135deg, #d4af37 0%, #f0d060 50%, #d4af37 100%);
    margin-bottom: auto;
}
.sf-card-number {
    font-family: 'Courier New', monospace;
    font-size: 1.15rem;
    letter-spacing: .18em;
    margin: .5rem 0 .75rem;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}
.sf-card-label {
    font-size: .6rem;
    text-transform: uppercase;
    letter-spacing: .1em;
    opacity: .65;
    margin-bottom: .1rem;
}
.sf-card-holder,
.sf-card-expiry {
    font-size: .8rem;
    font-weight: 600;
    letter-spacing: .05em;
}
.sf-card-brand { margin-left: auto; opacity: .85; }

/* Step badges (PIX) */
.sf-step-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px; height: 22px;
    border-radius: 50%;
    background: var(--sf-green-800);
    color: #fff;
    font-size: .7rem;
    font-weight: 700;
    flex-shrink: 0;
}
</style>

<!-- ════════════════════════════════
     JS
════════════════════════════════ -->
<script>
(function () {
    'use strict';

    // ── Selecionar método de pagamento ───────────────────────
    const tabs       = document.querySelectorAll('.sf-pay-tab');
    const secCartao  = document.getElementById('secao-cartao');
    const secPix     = document.getElementById('secao-pix');
    const secParcel  = document.getElementById('secao-parcelamento');
    const inputMetod = document.getElementById('metodo_pagamento');
    const btnText    = document.getElementById('btn-confirmar-text');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => { t.classList.remove('active'); t.setAttribute('aria-pressed', 'false'); });
            tab.classList.add('active');
            tab.setAttribute('aria-pressed', 'true');

            const m = tab.dataset.method;

            if (m === 'pix') {
                secCartao.classList.add('d-none');
                secPix.classList.remove('d-none');
                inputMetod.value = 'PIX';
                btnText.textContent = 'Confirmar e aguardar pagamento PIX';
            } else {
                secCartao.classList.remove('d-none');
                secPix.classList.add('d-none');
                inputMetod.value = 'CARTAO';
                btnText.textContent = 'Confirmar Reserva';
                secParcel.style.display = m === 'debito' ? 'none' : '';
            }
        });
    });

    // ── Formatação do número do cartão ──────────────────────
    const numInput   = document.getElementById('numero_cartao');
    const cvNumber   = document.getElementById('cv-number');
    const brandIcon  = document.getElementById('brand-icon');

    numInput && numInput.addEventListener('input', e => {
        let v = e.target.value.replace(/\D/g, '').substring(0, 16);
        e.target.value = v.replace(/(.{4})/g, '$1 ').trim();
        // Atualiza card visual
        const disp = v.padEnd(16, '•');
        cvNumber.textContent = disp.substring(0, 4) + ' ' + disp.substring(4, 8) + ' ' + disp.substring(8, 12) + ' ' + disp.substring(12, 16);
        // Detecção de bandeira
        detectBrand(v);
    });

    function detectBrand(n) {
        if (!brandIcon) return;
        if (/^4/.test(n)) {
            brandIcon.className = 'fab fa-cc-visa text-primary';
        } else if (/^5[1-5]/.test(n) || /^2[2-7]/.test(n)) {
            brandIcon.className = 'fab fa-cc-mastercard text-danger';
        } else if (/^3[47]/.test(n)) {
            brandIcon.className = 'fab fa-cc-amex text-info';
        } else if (/^6011|^65|^64[4-9]/.test(n)) {
            brandIcon.className = 'fab fa-cc-discover text-warning';
        } else {
            brandIcon.className = 'fas fa-credit-card text-muted';
        }
    }

    // ── Formatação do nome ───────────────────────────────────
    const nomeInput = document.getElementById('nome_cartao');
    const cvHolder  = document.getElementById('cv-holder');

    nomeInput && nomeInput.addEventListener('input', e => {
        const v = e.target.value.toUpperCase();
        e.target.value = v;
        cvHolder.textContent = v || 'NOME DO TITULAR';
    });

    // ── Formatação da validade ───────────────────────────────
    const valInput = document.getElementById('validade_cartao');
    const cvExpiry = document.getElementById('cv-expiry');

    valInput && valInput.addEventListener('input', e => {
        let v = e.target.value.replace(/\D/g, '').substring(0, 4);
        if (v.length >= 3) v = v.substring(0, 2) + '/' + v.substring(2);
        e.target.value = v;
        cvExpiry.textContent = v || 'MM/AA';
    });

    // ── CVV: apenas números ──────────────────────────────────
    document.getElementById('cvv_cartao')?.addEventListener('input', e => {
        e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
    });

    // ── PIX: copiar código ───────────────────────────────────
    document.getElementById('btn-copiar-pix')?.addEventListener('click', () => {
        const code = document.getElementById('pix-code');
        code.select();
        navigator.clipboard?.writeText(code.value).catch(() => document.execCommand('copy'));
        const btn = document.getElementById('btn-copiar-pix');
        btn.innerHTML = '<i class="fas fa-check me-1"></i> Copiado!';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-outline-secondary');
        setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-copy me-1"></i> Copiar';
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 2500);
    });

    // ── Submit: feedback visual ──────────────────────────────
    document.getElementById('form-pagamento')?.addEventListener('submit', function (e) {
        const btn   = document.getElementById('btn-confirmar');
        const metod = inputMetod.value;

        if (metod === 'CARTAO') {
            const num  = numInput?.value.replace(/\s/g, '') ?? '';
            const nome = nomeInput?.value.trim() ?? '';
            const val  = valInput?.value.trim() ?? '';
            const cvv  = document.getElementById('cvv_cartao')?.value.trim() ?? '';

            if (num.length < 13 || !nome || val.length < 5 || cvv.length < 3) {
                e.preventDefault();
                alert('Por favor, preencha todos os dados do cartão corretamente.');
                return;
            }
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Processando...';
    });

    // ── Bootstrap tooltips ───────────────────────────────────
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });
})();
</script>
