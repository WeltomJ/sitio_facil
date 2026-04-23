<?php
/** @var array $dados */
$pageTitle = 'Dados de Recebimento — Sítio Fácil';

$bancos = [
    '001' => '001 — Banco do Brasil',
    '033' => '033 — Santander',
    '104' => '104 — Caixa Econômica Federal',
    '237' => '237 — Bradesco',
    '341' => '341 — Itaú',
    '077' => '077 — Inter',
    '260' => '260 — Nubank',
    '290' => '290 — PagBank',
    '336' => '336 — C6 Bank',
    '748' => '748 — Sicredi',
    '756' => '756 — Sicoob',
    '212' => '212 — Banco Original',
    '422' => '422 — Safra',
    '655' => '655 — Votorantim',
    '041' => '041 — Banrisul',
    '070' => '070 — BRB',
];
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dados de Recebimento</li>
    </ol>
</nav>

<div class="row justify-content-center">
    <div class="col-lg-7">

        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="sf-stat-icon" style="font-size:1.5rem;">
                <i class="fas fa-university"></i>
            </div>
            <div>
                <h1 class="h4 fw-bold mb-0">Dados de Recebimento</h1>
                <p class="text-muted small mb-0">Informe sua conta bancária para receber os pagamentos das reservas.</p>
            </div>
        </div>

        <?php if (!empty($dados)): ?>
            <div class="alert alert-success d-flex align-items-center gap-2 py-2 mb-4">
                <i class="fas fa-check-circle"></i>
                <span>Dados cadastrados. Você pode atualizá-los abaixo.</span>
            </div>
        <?php else: ?>
            <div class="alert alert-warning d-flex align-items-center gap-2 py-2 mb-4">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Você ainda não cadastrou seus dados bancários.</span>
            </div>
        <?php endif; ?>

        <div class="sf-section-block">
            <form method="POST" action="<?= BASE_URL ?>/locador/perfil" novalidate>

                <!-- Banco -->
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="banco">Banco <span class="text-danger">*</span></label>
                    <select class="form-select" id="banco" name="banco" required>
                        <option value="">Selecione o banco...</option>
                        <?php foreach ($bancos as $cod => $nome): ?>
                            <option value="<?= $cod ?>"
                                <?= ($dados['banco'] ?? '') === $cod ? 'selected' : '' ?>>
                                <?= htmlspecialchars($nome) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row g-3 mb-3">
                    <!-- Agência -->
                    <div class="col-5">
                        <label class="form-label fw-semibold" for="agencia">Agência <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               id="agencia"
                               name="agencia"
                               maxlength="10"
                               placeholder="0001"
                               value="<?= htmlspecialchars($dados['agencia'] ?? '') ?>"
                               required>
                    </div>
                    <!-- Conta -->
                    <div class="col-7">
                        <label class="form-label fw-semibold" for="conta">Conta <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               id="conta"
                               name="conta"
                               maxlength="20"
                               placeholder="12345-6"
                               value="<?= htmlspecialchars($dados['conta'] ?? '') ?>"
                               required>
                    </div>
                </div>

                <!-- Tipo de conta -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tipo de Conta <span class="text-danger">*</span></label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_conta" id="tipo_corrente"
                                   value="CORRENTE" <?= ($dados['tipo_conta'] ?? 'CORRENTE') === 'CORRENTE' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="tipo_corrente">Conta Corrente</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_conta" id="tipo_poupanca"
                                   value="POUPANCA" <?= ($dados['tipo_conta'] ?? '') === 'POUPANCA' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="tipo_poupanca">Poupança</label>
                        </div>
                    </div>
                </div>

                <!-- Nome do titular -->
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="nome_titular">Nome do Titular <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control"
                           id="nome_titular"
                           name="nome_titular"
                           maxlength="100"
                           placeholder="Nome completo conforme cadastro no banco"
                           value="<?= htmlspecialchars($dados['nome_titular'] ?? '') ?>"
                           required>
                </div>

                <!-- CPF/CNPJ do titular -->
                <div class="mb-4">
                    <label class="form-label fw-semibold" for="cpf_cnpj">CPF / CNPJ do Titular <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control"
                           id="cpf_cnpj"
                           name="cpf_cnpj"
                           maxlength="18"
                           placeholder="000.000.000-00 ou 00.000.000/0001-00"
                           value="<?= htmlspecialchars($dados['cpf_cnpj'] ?? '') ?>"
                           required>
                </div>

                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary fw-bold px-4">
                        <i class="fas fa-save me-2"></i>Salvar Dados
                    </button>
                    <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline-secondary">Voltar</a>
                </div>

            </form>
        </div>

        <div class="alert alert-info d-flex gap-2 mt-4 py-2 small">
            <i class="fas fa-info-circle mt-1 flex-shrink-0"></i>
            <span>
                Esses dados são informativos e serão usados para identificar sua conta de recebimento.
                Os pagamentos são processados via <strong>Asaas</strong> em ambiente sandbox (teste).
            </span>
        </div>

    </div>
</div>
