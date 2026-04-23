<?php
/** @var array $cartoes */
$pageTitle = 'Meus Cartões — Sítio Fácil';

$bandeiras = [
    'VISA'       => 'fab fa-cc-visa text-primary',
    'MASTERCARD' => 'fab fa-cc-mastercard text-danger',
    'AMEX'       => 'fab fa-cc-amex text-info',
    'ELO'        => 'fas fa-credit-card text-warning',
    'HIPERCARD'  => 'fas fa-credit-card text-danger',
    'DISCOVER'   => 'fab fa-cc-discover text-warning',
    'DINERS'     => 'fab fa-cc-diners-club text-secondary',
];
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Meus Cartões</li>
    </ol>
</nav>

<div class="row g-4 align-items-start">

    <!-- Coluna: cartões salvos -->
    <div class="col-lg-7">
        <h1 class="h4 fw-bold mb-4">Meus Cartões</h1>

        <?php if (empty($cartoes)): ?>
            <div class="sf-section-block p-4 mb-4 text-center py-5">
                <i class="fas fa-credit-card fa-3x text-muted mb-3 d-block"></i>
                <p class="text-muted mb-0">Você ainda não tem cartões salvos.</p>
            </div>
        <?php else: ?>
            <div class="d-flex flex-column gap-3 mb-4">
                <?php foreach ($cartoes as $cartao): ?>
                    <div class="sf-section-block d-flex align-items-center justify-content-between py-3 px-4">
                        <div class="d-flex align-items-center gap-3">
                            <i class="<?= $bandeiras[strtoupper($cartao['bandeira'])] ?? 'fas fa-credit-card text-muted' ?> fa-2x"></i>
                            <div>
                                <p class="fw-semibold mb-0">
                                    <?= htmlspecialchars($cartao['bandeira']) ?>
                                    &nbsp;•••• <?= htmlspecialchars($cartao['final_cartao']) ?>
                                </p>
                                <p class="text-muted small mb-0">
                                    <?= htmlspecialchars($cartao['nome_titular']) ?> &bull;
                                    Val. <?= htmlspecialchars($cartao['expiry_month']) ?>/<?= htmlspecialchars($cartao['expiry_year']) ?>
                                </p>
                            </div>
                        </div>
                        <form method="POST"
                              action="<?= BASE_URL ?>/cliente/cartoes/<?= (int)$cartao['id'] ?>/remover"
                              onsubmit="return confirm('Remover este cartão?');">
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Remover cartão">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Adicionar novo cartão -->
        <div class="sf-section-block p-4">
            <h2 class="h6 fw-bold mb-4">
                <i class="fas fa-plus-circle me-2 text-success"></i>Adicionar Novo Cartão
            </h2>

            <form method="POST" action="<?= BASE_URL ?>/cliente/cartoes" id="form-add-cartao" novalidate>

                <!-- Mini card visual -->
                <div class="sf-card-visual mb-4" aria-hidden="true">
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

                <!-- Número -->
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="numero_cartao">Número do cartão <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="numero_cartao" name="numero_cartao"
                               maxlength="19" placeholder="1234 5678 9012 3456"
                               autocomplete="cc-number" inputmode="numeric" required>
                        <span class="input-group-text bg-transparent">
                            <i class="fas fa-credit-card text-muted" id="brand-icon"></i>
                        </span>
                    </div>
                </div>

                <!-- Nome -->
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="nome_cartao">Nome impresso no cartão <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nome_cartao" name="nome_cartao"
                           placeholder="NOME SOBRENOME" autocomplete="cc-name"
                           style="text-transform:uppercase;" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold" for="validade_cartao">Validade <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="validade_cartao" name="validade_cartao"
                               maxlength="5" placeholder="MM/AA"
                               autocomplete="cc-exp" inputmode="numeric" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold" for="cvv_cartao">CVV <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="cvv_cartao" name="cvv_cartao"
                               maxlength="4" placeholder="•••"
                               autocomplete="cc-csc" inputmode="numeric" required>
                    </div>
                </div>

                <!-- CEP do titular -->
                <div class="mb-4">
                    <label class="form-label fw-semibold" for="cep_titular">CEP do titular <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="cep_titular" name="cep_titular"
                           maxlength="9" placeholder="00000-000" inputmode="numeric" required>
                    <div class="form-text">CEP de cobrança do cartão.</div>
                </div>

                <button type="submit" class="btn btn-primary fw-bold w-100">
                    <i class="fas fa-plus me-2"></i>Salvar Cartão
                </button>

            </form>
        </div>

        <div class="d-flex align-items-center gap-2 mt-4 p-3 rounded-3 small" style="background:var(--sf-green-50);color:var(--sf-green-900);border:1px solid var(--sf-green-300);">
            <i class="fas fa-lock flex-shrink-0"></i>
            <span>Os dados do cartão são tokenizados com segurança via <strong>Asaas</strong>. O número completo nunca é armazenado no nosso servidor.</span>
        </div>

    </div>

    <!-- Coluna: info lateral -->
    <div class="col-lg-5 d-none d-lg-block">
        <div class="sf-booking-box">
            <h3 class="h6 fw-bold mb-3">
                <i class="fas fa-shield-alt me-2 text-success"></i>
                Por que salvar cartões?
            </h3>
            <ul class="list-unstyled small text-muted d-flex flex-column gap-2 mb-0">
                <li><i class="fas fa-check text-success me-2"></i>Pagamento mais rápido no checkout</li>
                <li><i class="fas fa-check text-success me-2"></i>Dados tokenizados — nunca expostos</li>
                <li><i class="fas fa-check text-success me-2"></i>Remova a qualquer momento</li>
            </ul>
        </div>

        <div class="mt-3 p-4 sf-section-block">
            <h3 class="h6 fw-bold mb-3">
                <i class="fas fa-info-circle me-2 text-primary"></i>
                Segurança dos dados
            </h3>
            <p class="small text-muted mb-0">
                Seus dados de cartão nunca passam pelos nossos servidores. A tokenização é feita diretamente pelo <strong>Asaas</strong>, parceiro certificado PCI DSS.
            </p>
        </div>
    </div>

</div>


<script>
(function () {
    const numInput  = document.getElementById('numero_cartao');
    const nomeInput = document.getElementById('nome_cartao');
    const valInput  = document.getElementById('validade_cartao');
    const cvvInput  = document.getElementById('cvv_cartao');
    const cvNumber  = document.getElementById('cv-number');
    const cvHolder  = document.getElementById('cv-holder');
    const cvExpiry  = document.getElementById('cv-expiry');
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
        if (/^4/.test(n))               brandIcon.className = 'fab fa-cc-visa text-primary';
        else if (/^5[1-5]|^2[2-7]/.test(n)) brandIcon.className = 'fab fa-cc-mastercard text-danger';
        else if (/^3[47]/.test(n))      brandIcon.className = 'fab fa-cc-amex text-info';
        else if (/^6011|^65|^64[4-9]/.test(n)) brandIcon.className = 'fab fa-cc-discover text-warning';
        else                            brandIcon.className = 'fas fa-credit-card text-muted';
    }

    nomeInput?.addEventListener('input', e => {
        e.target.value = e.target.value.toUpperCase();
        cvHolder.textContent = e.target.value || 'NOME DO TITULAR';
    });

    valInput?.addEventListener('input', e => {
        let v = e.target.value.replace(/\D/g, '').substring(0, 4);
        if (v.length >= 3) v = v.substring(0, 2) + '/' + v.substring(2);
        e.target.value = v;
        cvExpiry.textContent = v || 'MM/AA';
    });

    cvvInput?.addEventListener('input', e => {
        e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
    });

    document.getElementById('cep_titular')?.addEventListener('input', e => {
        let v = e.target.value.replace(/\D/g, '').substring(0, 8);
        if (v.length > 5) v = v.substring(0, 5) + '-' + v.substring(5);
        e.target.value = v;
    });

    document.getElementById('form-add-cartao')?.addEventListener('submit', function (e) {
        const num  = numInput?.value.replace(/\s/g, '') ?? '';
        const nome = nomeInput?.value.trim() ?? '';
        const val  = valInput?.value.trim() ?? '';
        const cvv  = cvvInput?.value.trim() ?? '';
        if (num.length < 13 || !nome || val.length < 5 || cvv.length < 3) {
            e.preventDefault();
            alert('Preencha todos os dados do cartão corretamente.');
        }
    });
})();
</script>
