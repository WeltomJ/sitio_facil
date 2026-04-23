<?php
/** @var array $usuario */
$pageTitle  = 'Meu Perfil — Sítio Fácil';
$fotoUrl    = $usuario['foto_url'] ?? null;
$inicial    = strtoupper(mb_substr($usuario['nome'] ?? 'U', 0, 1));
?>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Meu Perfil</li>
    </ol>
</nav>

<div class="row g-4 align-items-start">

    <!-- Coluna: foto + nome -->
    <div class="col-lg-4">
        <div class="sf-section-block text-center p-4">

            <!-- Avatar grande -->
            <div class="sf-avatar-perfil mx-auto mb-3" id="avatar-preview-wrap">
                <?php if ($fotoUrl): ?>
                    <img src="<?= BASE_URL . htmlspecialchars($fotoUrl) ?>" alt="Foto de perfil"
                         class="sf-avatar-perfil-img" id="avatar-preview">
                <?php else: ?>
                    <div class="sf-avatar-perfil-placeholder" id="avatar-placeholder">
                        <?= $inicial ?>
                    </div>
                    <img src="" alt="" class="sf-avatar-perfil-img d-none" id="avatar-preview">
                <?php endif; ?>
            </div>

            <p class="fw-bold mb-0"><?= htmlspecialchars($usuario['nome']) ?></p>
            <p class="text-muted small mb-3"><?= htmlspecialchars($usuario['email']) ?></p>

            <!-- Upload foto -->
            <form method="POST" action="<?= BASE_URL ?>/perfil/foto"
                  enctype="multipart/form-data" id="form-foto">
                <label class="btn btn-outline-primary btn-sm w-100 mb-2" for="input-foto">
                    <i class="fas fa-camera me-1"></i>
                    <?= $fotoUrl ? 'Trocar foto' : 'Adicionar foto' ?>
                </label>
                <input type="file" id="input-foto" name="foto_url"
                       accept="image/jpeg,image/png,image/webp" class="d-none">
            </form>

            <?php if ($fotoUrl): ?>
                <form method="POST" action="<?= BASE_URL ?>/perfil/foto/remover">
                    <button type="submit" class="btn btn-link btn-sm text-danger p-0"
                            onclick="return confirm('Remover sua foto de perfil?')">
                        <i class="fas fa-trash-alt me-1"></i>Remover foto
                    </button>
                </form>
            <?php endif; ?>

            <p class="text-muted mt-2" style="font-size:.72rem;">JPG, PNG ou WEBP · máx. 3 MB</p>

            <hr class="my-3">

            <div class="text-start">
                <p class="small text-muted mb-1">
                    <i class="fas fa-id-badge me-2"></i>
                    <strong>Perfil:</strong>
                    <?php
                    $perfis = explode(',', $usuario['perfil'] ?? '');
                    $labels = ['CLIENTE' => 'Cliente', 'LOCADOR' => 'Locador'];
                    echo implode(' · ', array_map(fn($p) => $labels[$p] ?? $p, $perfis));
                    ?>
                </p>
                <p class="small text-muted mb-1">
                    <i class="fas fa-calendar me-2"></i>
                    <strong>Membro desde:</strong>
                    <?= date('M/Y', strtotime($usuario['criado_em'] ?? 'now')) ?>
                </p>
                <?php if (!empty($usuario['cpf_cnpj'])): ?>
                    <p class="small text-muted mb-0">
                        <i class="fas fa-fingerprint me-2"></i>
                        <strong>CPF/CNPJ:</strong>
                        <?= '•••' . substr($usuario['cpf_cnpj'], -4) ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Coluna: formulário de dados -->
    <div class="col-lg-8">
        <div class="sf-section-block p-4">
            <h1 class="h5 fw-bold mb-4">Informações pessoais</h1>

            <form method="POST" action="<?= BASE_URL ?>/perfil" id="form-perfil" novalidate>

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="nome">Nome completo <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nome" name="nome"
                           value="<?= htmlspecialchars($usuario['nome'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">E-mail</label>
                    <input type="email" class="form-control" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>"
                           disabled readonly>
                    <div class="form-text">O e-mail não pode ser alterado.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold" for="telefone">Telefone / WhatsApp</label>
                    <input type="text" class="form-control maskPhone" id="telefone" name="telefone"
                           value="<?php
                               $tel = $usuario['telefone'] ?? '';
                               if ($tel && strlen($tel) === 11) {
                                   echo '(' . substr($tel,0,2) . ') ' . substr($tel,2,5) . '-' . substr($tel,7);
                               } elseif ($tel && strlen($tel) === 10) {
                                   echo '(' . substr($tel,0,2) . ') ' . substr($tel,2,4) . '-' . substr($tel,6);
                               } else {
                                   echo htmlspecialchars($tel);
                               }
                           ?>"
                           placeholder="(00) 00000-0000">
                </div>

                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary fw-bold px-4">
                        <i class="fas fa-save me-2"></i>Salvar alterações
                    </button>
                    <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline-secondary">Cancelar</a>
                </div>

            </form>
        </div>

        <!-- Links rápidos -->
        <div class="row g-3 mt-2">
            <?php if (str_contains($usuario['perfil'] ?? '', 'CLIENTE')): ?>
                <div class="col-sm-6">
                    <a href="<?= BASE_URL ?>/cliente/cartoes"
                       class="sf-section-block d-flex align-items-center gap-3 p-3 text-decoration-none">
                        <i class="fas fa-credit-card fa-lg text-primary"></i>
                        <div>
                            <p class="fw-semibold mb-0 small">Meus Cartões</p>
                            <p class="text-muted mb-0" style="font-size:.75rem;">Gerenciar cartões salvos</p>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6">
                    <a href="<?= BASE_URL ?>/minhas-reservas"
                       class="sf-section-block d-flex align-items-center gap-3 p-3 text-decoration-none">
                        <i class="fas fa-calendar-check fa-lg text-success"></i>
                        <div>
                            <p class="fw-semibold mb-0 small">Minhas Reservas</p>
                            <p class="text-muted mb-0" style="font-size:.75rem;">Ver histórico de estadias</p>
                        </div>
                    </a>
                </div>
            <?php endif; ?>
            <?php if (str_contains($usuario['perfil'] ?? '', 'LOCADOR')): ?>
                <div class="col-sm-6">
                    <a href="<?= BASE_URL ?>/locador/perfil"
                       class="sf-section-block d-flex align-items-center gap-3 p-3 text-decoration-none">
                        <i class="fas fa-university fa-lg text-warning"></i>
                        <div>
                            <p class="fw-semibold mb-0 small">Dados de Recebimento</p>
                            <p class="text-muted mb-0" style="font-size:.75rem;">Conta bancária para repasses</p>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6">
                    <a href="<?= BASE_URL ?>/locador/chacaras"
                       class="sf-section-block d-flex align-items-center gap-3 p-3 text-decoration-none">
                        <i class="fas fa-home fa-lg text-primary"></i>
                        <div>
                            <p class="fw-semibold mb-0 small">Minhas Chácaras</p>
                            <p class="text-muted mb-0" style="font-size:.75rem;">Gerenciar propriedades</p>
                        </div>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<script>
(function () {
    var input   = document.getElementById('input-foto');
    var preview = document.getElementById('avatar-preview');
    var placeholder = document.getElementById('avatar-placeholder');

    if (!input) return;

    input.addEventListener('change', function () {
        var file = this.files[0];
        if (!file) return;

        if (file.size > 3 * 1024 * 1024) {
            notification({ type: 'warning', message: 'A foto deve ter no máximo 3 MB.' });
            this.value = '';
            return;
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
            if (placeholder) placeholder.classList.add('d-none');
        };
        reader.readAsDataURL(file);

        // Envia automaticamente ao selecionar
        document.getElementById('form-foto').submit();
    });

    validateForm({
        formSelector: '#form-perfil',
        rules:    { nome: { required: true, minlength: 2 } },
        messages: { nome: { required: 'Informe seu nome', minlength: 'Mínimo 2 caracteres' } },
    });
})();
</script>
