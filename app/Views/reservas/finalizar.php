<?php
/** @var array $chacara */
/** @var string $dataInicio */
/** @var string $dataFim */
/** @var int $qtdHospedes */
/** @var float $valorTotal */
/** @var int $dias */
/** @var array|null $foto */
/** @var array $cartoesSalvos */

$pageTitle = 'Confirmar reserva — Sítio Fácil';

$bandeiras = [
    'VISA'       => 'fab fa-cc-visa text-primary',
    'MASTERCARD' => 'fab fa-cc-mastercard text-danger',
    'AMEX'       => 'fab fa-cc-amex text-info',
    'ELO'        => 'fas fa-credit-card text-warning',
    'HIPERCARD'  => 'fas fa-credit-card text-danger',
    'DISCOVER'   => 'fab fa-cc-discover text-warning',
];
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
<p class="text-muted small mb-4">Pagamento processado com segurança via Asaas.</p>

<div class="row g-4 align-items-start">

    <!-- ═══════════════════════════════════════
         COLUNA ESQUERDA — formulário de pagamento
    ════════════════════════════════════════ -->
    <div class="col-lg-7 order-2 order-lg-1">

        <form method="POST"
              action="<?= BASE_URL ?>/chacaras/<?= (int)$chacara['id'] ?>/reservar"
              id="form-pagamento"
              novalidate>

            <!-- campos ocultos da reserva -->
            <input type="hidden" name="data_inicio"      value="<?= htmlspecialchars($dataInicio) ?>">
            <input type="hidden" name="data_fim"         value="<?= htmlspecialchars($dataFim) ?>">
            <input type="hidden" name="qtd_hospedes"     value="<?= (int) $qtdHospedes ?>">
            <input type="hidden" name="valor_total"      value="<?= $valorTotal ?>">
            <input type="hidden" name="metodo_pagamento" id="metodo_pagamento" value="CARTAO">
            <input type="hidden" name="cartao_salvo_id"  id="cartao_salvo_id" value="">

            <!-- ── Seleção de método ─────────────────────── -->
            <div class="sf-section-block p-4 mb-4">
                <h2 class="h6 fw-bold mb-3">Forma de pagamento</h2>

                <div class="sf-pay-tabs d-flex gap-2 mb-4" role="group" aria-label="Forma de pagamento">
                    <button type="button" class="sf-pay-tab active" data-method="credito" aria-pressed="true">
                        <i class="fas fa-credit-card me-1"></i> Crédito
                    </button>
                    <button type="button" class="sf-pay-tab" data-method="pix" aria-pressed="false">
                        <i class="fa-brands fa-pix me-1"></i> PIX
                    </button>
                </div>

                <!-- ─── CARTÃO ───────────────────────────── -->
                <div id="secao-cartao">

                    <?php if (!empty($cartoesSalvos)): ?>
                    <!-- Cartões salvos -->
                    <div class="mb-4">
                        <p class="fw-semibold small mb-2">Cartões salvos</p>
                        <div class="d-flex flex-column gap-2" id="lista-cartoes-salvos">
                            <?php foreach ($cartoesSalvos as $cartao): ?>
                                <label class="sf-card-option d-flex align-items-center gap-3 px-3 py-2 rounded-3 border cursor-pointer"
                                       style="cursor:pointer;">
                                    <input type="radio" name="_cartao_radio" class="form-check-input mt-0 flex-shrink-0"
                                           value="<?= (int)$cartao['id'] ?>">
                                    <i class="<?= $bandeiras[strtoupper($cartao['bandeira'])] ?? 'fas fa-credit-card text-muted' ?> fa-lg flex-shrink-0"></i>
                                    <div class="flex-grow-1">
                                        <span class="fw-semibold small">
                                            <?= htmlspecialchars($cartao['bandeira']) ?> •••• <?= htmlspecialchars($cartao['final_cartao']) ?>
                                        </span>
                                        <span class="text-muted small ms-2">
                                            <?= htmlspecialchars($cartao['expiry_month']) ?>/<?= htmlspecialchars($cartao['expiry_year']) ?>
                                        </span>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                            <label class="sf-card-option d-flex align-items-center gap-3 px-3 py-2 rounded-3 border cursor-pointer"
                                   style="cursor:pointer;">
                                <input type="radio" name="_cartao_radio" class="form-check-input mt-0 flex-shrink-0"
                                       value="0" checked>
                                <i class="fas fa-plus-circle text-muted fa-lg flex-shrink-0"></i>
                                <span class="small fw-semibold">Adicionar novo cartão</span>
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Formulário de novo cartão -->
                    <div id="secao-novo-cartao" <?= !empty($cartoesSalvos) ? 'class="d-none"' : '' ?>>

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
                                <input type="text" class="form-control" id="numero_cartao" name="numero_cartao"
                                       maxlength="19" placeholder="1234 5678 9012 3456"
                                       autocomplete="cc-number" inputmode="numeric">
                                <span class="input-group-text bg-transparent" id="brand-icon-wrap">
                                    <i class="fas fa-credit-card text-muted" id="brand-icon"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Nome no cartão -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="nome_cartao">Nome impresso no cartão</label>
                            <input type="text" class="form-control" id="nome_cartao" name="nome_cartao"
                                   placeholder="NOME SOBRENOME" autocomplete="cc-name"
                                   style="text-transform:uppercase;">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold" for="validade_cartao">Validade</label>
                                <input type="text" class="form-control" id="validade_cartao" name="validade_cartao"
                                       maxlength="5" placeholder="MM/AA" autocomplete="cc-exp" inputmode="numeric">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold" for="cvv_cartao">
                                    CVV
                                    <button type="button" class="btn btn-link p-0 ms-1 text-muted" tabindex="-1"
                                            data-bs-toggle="tooltip"
                                            title="Código de 3 dígitos no verso do cartão">
                                        <i class="fas fa-question-circle" style="font-size:.85rem;"></i>
                                    </button>
                                </label>
                                <input type="text" class="form-control" id="cvv_cartao" name="cvv_cartao"
                                       maxlength="4" placeholder="•••" autocomplete="cc-csc" inputmode="numeric">
                            </div>
                        </div>

                        <!-- Parcelamento -->
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

                        <!-- CEP do titular -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="cep_titular">CEP do titular <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cep_titular" name="cep_titular"
                                   maxlength="9" placeholder="00000-000" inputmode="numeric">
                        </div>

                        <!-- Salvar cartão -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="salvar_cartao" name="salvar_cartao" value="1">
                            <label class="form-check-label small" for="salvar_cartao">
                                Salvar cartão para próximas compras
                            </label>
                        </div>

                    </div>
                    <!-- /secao-novo-cartao -->

                </div>
                <!-- /secao-cartao -->

                <!-- ─── PIX ──────────────────────────────────── -->
                <div id="secao-pix" class="d-none">
                    <div class="alert alert-info d-flex gap-2 py-3">
                        <i class="fa-brands fa-pix fa-lg mt-1 flex-shrink-0"></i>
                        <div>
                            <p class="fw-semibold mb-1">Pagamento via PIX</p>
                            <p class="small mb-0">
                                Após confirmar, o QR Code PIX será gerado pelo Asaas.
                                Você terá <strong>30 minutos</strong> para efetuar o pagamento.
                            </p>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="sf-step-badge">1</span>
                        <span class="small">Clique em <strong>Confirmar e gerar PIX</strong> abaixo.</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="sf-step-badge">2</span>
                        <span class="small">Escaneie o QR Code que aparecerá na tela seguinte.</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="sf-step-badge">3</span>
                        <span class="small">
                            Pague R$ <strong><?= number_format($valorTotal, 2, ',', '.') ?></strong> no seu banco e aguarde a confirmação automática.
                        </span>
                    </div>
                </div>
                <!-- /secao-pix -->

            </div>
            <!-- /sf-section-block -->

            <!-- Segurança -->
            <div class="d-flex align-items-center gap-2 text-muted small mb-4">
                <i class="fas fa-lock"></i>
                <span>Pagamento seguro via <strong>Asaas</strong>. Seus dados são criptografados.</span>
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
                <a href="<?= BASE_URL ?>/termos" target="_blank" class="text-decoration-underline">Termos de Uso</a> e a
                <a href="<?= BASE_URL ?>/cancelamento" target="_blank" class="text-decoration-underline">Política de Cancelamento</a>.
            </p>

        </form>

    </div>
    <!-- /coluna esquerda -->

    <!-- ═══════════════════════════════════════
         COLUNA DIREITA — resumo da reserva
    ════════════════════════════════════════ -->
    <div class="col-lg-5 order-1 order-lg-2">
        <div class="sf-booking-box sf-booking-box--checkout sticky-top" style="top:calc(var(--sf-navbar-height) + 24px);">

            <!-- Chácara header -->
            <div class="d-flex gap-3 align-items-center mb-4">
                <?php if (!empty($foto)): ?>
                    <img src="<?= BASE_URL . htmlspecialchars($foto['url']) ?>"
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


<script>
(function () {
    'use strict';

    // ── Método de pagamento ──────────────────────────────────────────────────
    const tabs        = document.querySelectorAll('.sf-pay-tab');
    const secCartao   = document.getElementById('secao-cartao');
    const secPix      = document.getElementById('secao-pix');
    const inputMetod  = document.getElementById('metodo_pagamento');
    const btnText     = document.getElementById('btn-confirmar-text');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => { t.classList.remove('active'); t.setAttribute('aria-pressed', 'false'); });
            tab.classList.add('active');
            tab.setAttribute('aria-pressed', 'true');

            if (tab.dataset.method === 'pix') {
                secCartao.classList.add('d-none');
                secPix.classList.remove('d-none');
                inputMetod.value = 'PIX';
                btnText.textContent = 'Confirmar e gerar PIX';
            } else {
                secCartao.classList.remove('d-none');
                secPix.classList.add('d-none');
                inputMetod.value = 'CARTAO';
                btnText.textContent = 'Confirmar Reserva';
            }
        });
    });

    // ── Cartões salvos vs novo cartão ────────────────────────────────────────
    const radios          = document.querySelectorAll('input[name="_cartao_radio"]');
    const secNovoCartao   = document.getElementById('secao-novo-cartao');
    const inputCartaoId   = document.getElementById('cartao_salvo_id');

    radios.forEach(radio => {
        radio.addEventListener('change', () => {
            const val = radio.value;
            if (val === '0') {
                secNovoCartao?.classList.remove('d-none');
                inputCartaoId.value = '';
            } else {
                secNovoCartao?.classList.add('d-none');
                inputCartaoId.value = val;
            }
        });
    });

    // Inicializa: se há cartões salvos, seleciona o primeiro por padrão
    const primeiroSalvo = document.querySelector('input[name="_cartao_radio"][value]:not([value="0"])');
    if (primeiroSalvo) {
        primeiroSalvo.checked = true;
        primeiroSalvo.dispatchEvent(new Event('change'));
    }

    // ── Formatação do número do cartão ───────────────────────────────────────
    const numInput  = document.getElementById('numero_cartao');
    const cvNumber  = document.getElementById('cv-number');
    const brandIcon = document.getElementById('brand-icon');

    numInput?.addEventListener('input', e => {
        let v = e.target.value.replace(/\D/g, '').substring(0, 16);
        e.target.value = v.replace(/(.{4})/g, '$1 ').trim();
        const d = v.padEnd(16, '•');
        cvNumber.textContent = d.slice(0,4)+' '+d.slice(4,8)+' '+d.slice(8,12)+' '+d.slice(12,16);
        detectBrand(v);
    });

    function detectBrand(n) {
        if (!brandIcon) return;
        if (/^4/.test(n))                    brandIcon.className = 'fab fa-cc-visa text-primary';
        else if (/^5[1-5]|^2[2-7]/.test(n)) brandIcon.className = 'fab fa-cc-mastercard text-danger';
        else if (/^3[47]/.test(n))           brandIcon.className = 'fab fa-cc-amex text-info';
        else if (/^6011|^65|^64[4-9]/.test(n)) brandIcon.className = 'fab fa-cc-discover text-warning';
        else                                 brandIcon.className = 'fas fa-credit-card text-muted';
    }

    // ── Nome ─────────────────────────────────────────────────────────────────
    const nomeInput = document.getElementById('nome_cartao');
    const cvHolder  = document.getElementById('cv-holder');
    nomeInput?.addEventListener('input', e => {
        e.target.value = e.target.value.toUpperCase();
        cvHolder.textContent = e.target.value || 'NOME DO TITULAR';
    });

    // ── Validade ─────────────────────────────────────────────────────────────
    const valInput = document.getElementById('validade_cartao');
    const cvExpiry = document.getElementById('cv-expiry');
    valInput?.addEventListener('input', e => {
        let v = e.target.value.replace(/\D/g, '').substring(0, 4);
        if (v.length >= 3) v = v.substring(0, 2) + '/' + v.substring(2);
        e.target.value = v;
        cvExpiry.textContent = v || 'MM/AA';
    });

    // ── CVV ──────────────────────────────────────────────────────────────────
    document.getElementById('cvv_cartao')?.addEventListener('input', e => {
        e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
    });

    // ── CEP ──────────────────────────────────────────────────────────────────
    document.getElementById('cep_titular')?.addEventListener('input', e => {
        let v = e.target.value.replace(/\D/g, '').substring(0, 8);
        if (v.length > 5) v = v.substring(0, 5) + '-' + v.substring(5);
        e.target.value = v;
    });

    // ── Submit: validação ────────────────────────────────────────────────────
    document.getElementById('form-pagamento')?.addEventListener('submit', function (e) {
        const btn   = document.getElementById('btn-confirmar');
        const metod = inputMetod.value;

        if (metod === 'CARTAO' && !inputCartaoId.value) {
            // Novo cartão — validar campos
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

    // ── Bootstrap tooltips ───────────────────────────────────────────────────
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });
})();
</script>
