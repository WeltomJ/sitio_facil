<?php $pageTitle = htmlspecialchars($chacara['nome']) . ' — Sítio Fácil'; ?>

<!-- Galeria de fotos -->
<?php if (!empty($fotos)): ?>
    <div class="sf-gallery-grid mb-4" id="gallery-grid">
        <?php foreach (array_slice($fotos, 0, 5) as $i => $foto): ?>
            <div class="sf-gallery-cell" data-idx="<?= $i ?>">
                <img src="<?= htmlspecialchars($foto['url']) ?>"
                     alt="<?= htmlspecialchars($foto['descricao'] ?? $chacara['nome']) ?>">
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="sf-no-photo mb-4" style="border-radius:16px; height:360px; font-size:3rem;">
        <i class="fas fa-image text-secondary"></i>
        <p class="small mt-3 text-muted">Sem fotos cadastradas</p>
    </div>
<?php endif; ?>

<div class="row g-5">

    <!-- Coluna principal -->
    <div class="col-lg-8">

        <h1 class="fw-bold h3 mb-1"><?= htmlspecialchars($chacara['nome']) ?></h1>
        <p class="text-muted small mb-3">
            <i class="fas fa-map-marker-alt me-1"></i>
            <?= htmlspecialchars(
                ($chacara['logradouro'] ?? '') . ', ' .
                ($chacara['numero'] ?? '') . ' — ' .
                ($chacara['cidade'] ?? '') . '/' . ($chacara['estado'] ?? '')
            ) ?>
        </p>

        <!-- Chips rápidos -->
        <div class="d-flex flex-wrap gap-2 mb-4">
            <span class="sf-chip">
                <i class="fas fa-users"></i> até <?= (int) $chacara['capacidade_maxima'] ?> pessoas
            </span>
            <span class="sf-chip">
                <i class="fas fa-sign-in-alt"></i> Check-in <?= substr($chacara['horario_checkin'], 0, 5) ?>h
            </span>
            <span class="sf-chip">
                <i class="fas fa-sign-out-alt"></i> Check-out <?= substr($chacara['horario_checkout'], 0, 5) ?>h
            </span>
        </div>

        <hr class="my-4">

        <!-- Anfitrião -->
        <div class="d-flex align-items-center gap-3 mb-4">
            <span class="sf-avatar" style="width:48px; height:48px; font-size:1.25rem;">
                <i class="fas fa-user"></i>
            </span>
            <div>
                <p class="fw-semibold mb-0">Anfitrião: <?= htmlspecialchars($chacara['locador_nome']) ?></p>
                <?php if ($chacara['locador_telefone']): ?>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-phone me-1"></i><?= htmlspecialchars($chacara['locador_telefone']) ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <hr class="my-4">

        <!-- Descrição -->
        <?php if ($chacara['descricao']): ?>
            <div class="mb-4">
                <p class="lh-lg"><?= nl2br(htmlspecialchars($chacara['descricao'])) ?></p>
            </div>
            <hr class="my-4">
        <?php endif; ?>

        <!-- Comodidades -->
        <?php if (!empty($comodidades)): ?>
            <h2 class="h5 fw-bold mb-3">O que o lugar oferece</h2>
            <div class="row row-cols-2 row-cols-md-3 g-2 mb-4">
                <?php foreach ($comodidades as $como): ?>
                    <div class="col">
                        <div class="d-flex align-items-center gap-2 p-2 border rounded-3">
                            <i class="fas fa-check text-success" style="font-size:.75rem;"></i>
                            <span class="small"><?= htmlspecialchars($como['nome']) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <hr class="my-4">
        <?php endif; ?>

        <!-- Avaliações -->
        <div class="d-flex align-items-center gap-3 mb-3">
            <h2 class="h5 fw-bold mb-0">
                <i class="fas fa-star"></i>
                <?php if ($notaMedia > 0): ?>
                    <?= number_format($notaMedia, 1) ?> &middot; <?= count($avaliacoes) ?> avalia&ccedil;&atilde;o<?= count($avaliacoes) !== 1 ? 'ões' : '' ?>
                <?php else: ?>
                    Avaliações
                <?php endif; ?>
            </h2>
        </div>

        <?php if (empty($avaliacoes)): ?>
            <p class="text-muted small">Ainda não há avaliações para esta chácara.</p>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($avaliacoes as $av): ?>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="sf-avatar"><i class="fas fa-user"></i></span>
                            <div>
                                <p class="fw-semibold small mb-0"><?= htmlspecialchars($av['cliente_nome']) ?></p>
                                <p class="text-muted" style="font-size:.75rem;"><?= date('M Y', strtotime($av['criado_em'])) ?></p>
                            </div>
                        </div>
                        <div class="mb-1" style="font-size:.75rem;">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?= $i <= $av['nota'] ? 'text-dark' : 'text-secondary' ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <?php if ($av['comentario']): ?>
                            <p class="small lh-base mb-0"><?= htmlspecialchars($av['comentario']) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar: box de reserva -->
    <div class="col-lg-4">
        <div class="sf-booking-box sticky-top" style="top:calc(var(--sf-navbar-height) + 24px);">

            <div class="d-flex align-items-baseline gap-2 mb-3">
                <span class="fw-bold" style="font-size:1.5rem;">
                    R$&nbsp;<?= number_format((float) $chacara['preco_diaria'], 2, ',', '.') ?>
                </span>
                <span class="text-muted small">/ diária</span>
                <?php if ($notaMedia > 0): ?>
                    <span class="ms-auto small fw-semibold">
                        <i class="fas fa-star" style="font-size:.7rem;"></i>
                        <?= number_format($notaMedia, 1) ?>
                    </span>
                <?php endif; ?>
            </div>

            <?php if (isset($_SESSION['usuario_id'])): ?>
                <form method="GET" action="<?= BASE_URL ?>/chacaras/<?= $chacara['id'] ?>/checkout">

                    <div class="sf-date-grid mb-3">
                        <div class="sf-date-cell" style="border-right:1px solid var(--sf-border);">
                            <div class="sf-date-cell-label">CHECK-IN</div>
                            <input type="date" id="data_inicio" name="data_inicio"
                                   min="<?= date('Y-m-d') ?>" required
                                   class="border-0 bg-transparent fw-semibold small w-100"
                                   style="outline:none; cursor:pointer;">
                        </div>
                        <div class="sf-date-cell">
                            <div class="sf-date-cell-label">CHECK-OUT</div>
                            <input type="date" id="data_fim" name="data_fim"
                                   min="<?= date('Y-m-d') ?>" required
                                   class="border-0 bg-transparent fw-semibold small w-100"
                                   style="outline:none; cursor:pointer;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase" style="letter-spacing:.05em;" for="qtd_hospedes">Hóspedes</label>
                        <input class="form-control" type="number" id="qtd_hospedes" name="qtd_hospedes"
                               min="1" max="<?= $chacara['capacidade_maxima'] ?>" value="1" required>
                    </div>

                    <div class="d-grid">
                        <button class="btn btn-primary btn-lg fw-bold" type="submit">
                            Verificar disponibilidade
                        </button>
                    </div>

                    <p id="total-preview" class="text-center mt-2 small text-muted"></p>

                </form>
            <?php else: ?>
                <div class="d-grid mb-2">
                    <a href="<?= BASE_URL ?>/login" class="btn btn-primary btn-lg fw-bold">
                        Entre para reservar
                    </a>
                </div>
                <p class="text-center text-muted small">
                    Não tem conta? <a href="<?= BASE_URL ?>/cadastro" class="fw-semibold">Cadastre-se</a>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
(function () {
    const inicio  = document.getElementById('data_inicio');
    const fim     = document.getElementById('data_fim');
    const preview = document.getElementById('total-preview');
    const preco   = <?= (float) $chacara['preco_diaria'] ?>;

    function atualizar() {
        if (!inicio?.value || !fim?.value || !preview) return;
        const d1   = new Date(inicio.value);
        const d2   = new Date(fim.value);
        const dias = Math.max(0, Math.round((d2 - d1) / 86400000)) + 1;
        if (dias > 0 && d2 >= d1) {
            preview.textContent = dias + ' diária(s) × R$ ' +
                preco.toFixed(2).replace('.', ',') + ' = R$ ' +
                (dias * preco).toFixed(2).replace('.', ',');
        }
    }

    inicio?.addEventListener('change', atualizar);
    fim?.addEventListener('change', atualizar);
})();
</script>