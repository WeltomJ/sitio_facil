<?php $pageTitle = htmlspecialchars($chacara['nome']) . ' — Sítio Fácil'; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">

<!-- Galeria de fotos -->
<?php if (!empty($fotos)): ?>
    <?php
        $totalFotos  = count($fotos);
        $fotosMostra = array_slice($fotos, 0, 5);
        $temMais     = $totalFotos > 5;
    ?>
    <div class="sf-gallery-wrap mb-4">
        <div class="sf-gallery-grid" id="gallery-grid">
            <?php foreach ($fotosMostra as $i => $foto): ?>
                <div class="sf-gallery-cell <?= $i === 0 ? 'sf-gallery-cell--main' : '' ?>"
                     data-idx="<?= $i ?>">
                    <img src="<?= BASE_URL . htmlspecialchars($foto['url']) ?>"
                         alt="<?= htmlspecialchars($foto['descricao'] ?? $chacara['nome']) ?>"
                         loading="<?= $i === 0 ? 'eager' : 'lazy' ?>">
                    <?php if ($temMais && $i === 4): ?>
                        <div class="sf-gallery-overlay" data-lightbox-open="<?= $i ?>">
                            <i class="fas fa-images me-2"></i>Ver todas as <?= $totalFotos ?> fotos
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php if ($totalFotos === 1): ?>
                <!-- Preenche o espaço visual com células vazias -->
                <div class="sf-gallery-cell sf-gallery-cell--empty"></div>
                <div class="sf-gallery-cell sf-gallery-cell--empty"></div>
                <div class="sf-gallery-cell sf-gallery-cell--empty"></div>
                <div class="sf-gallery-cell sf-gallery-cell--empty"></div>
            <?php endif; ?>
        </div>
        <?php if ($temMais): ?>
            <button class="sf-gallery-btn-all" data-lightbox-open="0" type="button">
                <i class="fas fa-images me-2"></i>Ver todas as <?= $totalFotos ?> fotos
            </button>
        <?php endif; ?>
    </div>

    <!-- Lightbox (todas as fotos) -->
    <div class="sf-lightbox" id="sf-lightbox" role="dialog" aria-modal="true" aria-label="Galeria de fotos">
        <div class="sf-lightbox__backdrop"></div>
        <div class="sf-lightbox__content">
            <button class="sf-lightbox__close" id="lb-close" aria-label="Fechar galeria">
                <i class="fas fa-times"></i>
            </button>
            <button class="sf-lightbox__nav sf-lightbox__nav--prev" id="lb-prev" aria-label="Foto anterior">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="sf-lightbox__img-wrap">
                <img id="lb-img" src="" alt="" class="sf-lightbox__img">
                <div class="sf-lightbox__spinner" id="lb-spinner">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                </div>
            </div>
            <button class="sf-lightbox__nav sf-lightbox__nav--next" id="lb-next" aria-label="Próxima foto">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        <div class="sf-lightbox__footer">
            <span class="sf-lightbox__counter" id="lb-counter"></span>
            <span class="sf-lightbox__caption" id="lb-caption"></span>
        </div>
        <!-- Thumbnails -->
        <div class="sf-lightbox__thumbs" id="lb-thumbs">
            <?php foreach ($fotos as $i => $foto): ?>
                <button class="sf-lightbox__thumb" data-idx="<?= $i ?>" type="button"
                        aria-label="Ver foto <?= $i + 1 ?>">
                    <img src="<?= BASE_URL . htmlspecialchars($foto['url']) ?>"
                         alt="Foto <?= $i + 1 ?>" loading="lazy">
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
    (function () {
        var fotos = <?= json_encode(array_map(function($f) use ($chacara) {
            return [
                'src'     => BASE_URL . $f['url'],
                'caption' => $f['descricao'] ?? $chacara['nome'],
            ];
        }, $fotos)) ?>;

        var current = 0;
        var $lb      = document.getElementById('sf-lightbox');
        var $img     = document.getElementById('lb-img');
        var $spinner = document.getElementById('lb-spinner');
        var $counter = document.getElementById('lb-counter');
        var $caption = document.getElementById('lb-caption');
        var $thumbs  = document.querySelectorAll('.sf-lightbox__thumb');

        function openLightbox(idx) {
            current = ((idx % fotos.length) + fotos.length) % fotos.length;
            $lb.classList.add('sf-lightbox--open');
            document.body.style.overflow = 'hidden';
            loadImg(current);
        }

        function closeLightbox() {
            $lb.classList.remove('sf-lightbox--open');
            document.body.style.overflow = '';
            $img.src = '';
        }

        function loadImg(idx) {
            $spinner.style.display = 'flex';
            $img.style.opacity = '0';
            var src = fotos[idx].src;
            var tmp = new Image();
            tmp.onload = function () {
                $img.src = src;
                $img.alt = fotos[idx].caption;
                $img.style.opacity = '1';
                $spinner.style.display = 'none';
            };
            tmp.onerror = function () { $spinner.style.display = 'none'; };
            tmp.src = src;
            $counter.textContent = (idx + 1) + ' / ' + fotos.length;
            $caption.textContent = fotos[idx].caption;
            // Atualiza thumb ativa
            $thumbs.forEach(function (t, i) {
                t.classList.toggle('sf-lightbox__thumb--active', i === idx);
            });
            // Rola thumb ativa para o centro
            var activeThumb = document.querySelector('.sf-lightbox__thumb--active');
            if (activeThumb) activeThumb.scrollIntoView({ block: 'nearest', inline: 'center', behavior: 'smooth' });
            current = idx;
        }

        function prev() { loadImg((current - 1 + fotos.length) % fotos.length); }
        function next() { loadImg((current + 1) % fotos.length); }

        // Abrir pelo grid
        document.querySelectorAll('.sf-gallery-cell img').forEach(function (img, idx) {
            img.style.cursor = 'pointer';
            img.addEventListener('click', function () { openLightbox(idx); });
        });
        document.querySelectorAll('[data-lightbox-open]').forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.stopPropagation();
                openLightbox(parseInt(el.dataset.lightboxOpen) || 0);
            });
        });
        var $btnAll = document.querySelector('.sf-gallery-btn-all');
        if ($btnAll) $btnAll.addEventListener('click', function () { openLightbox(0); });

        document.getElementById('lb-close').addEventListener('click', closeLightbox);
        document.getElementById('lb-prev').addEventListener('click', prev);
        document.getElementById('lb-next').addEventListener('click', next);
        document.querySelector('.sf-lightbox__backdrop').addEventListener('click', closeLightbox);

        $thumbs.forEach(function (t) {
            t.addEventListener('click', function () { loadImg(parseInt(t.dataset.idx)); });
        });

        // Teclado
        document.addEventListener('keydown', function (e) {
            if (!$lb.classList.contains('sf-lightbox--open')) return;
            if (e.key === 'Escape')     closeLightbox();
            if (e.key === 'ArrowLeft')  prev();
            if (e.key === 'ArrowRight') next();
        });

        // Swipe mobile
        var touchStartX = null;
        $lb.addEventListener('touchstart', function (e) {
            touchStartX = e.touches[0].clientX;
        }, { passive: true });
        $lb.addEventListener('touchend', function (e) {
            if (touchStartX === null) return;
            var diff = touchStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 50) { diff > 0 ? next() : prev(); }
            touchStartX = null;
        }, { passive: true });
    })();
    </script>

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
            <?php if (!empty($chacara['locador_foto'])): ?>
                <img src="<?= BASE_URL . htmlspecialchars($chacara['locador_foto']) ?>"
                     alt="<?= htmlspecialchars($chacara['locador_nome']) ?>"
                     class="sf-avatar sf-avatar--photo" style="width:48px;height:48px;">
            <?php else: ?>
                <span class="sf-avatar sf-avatar--initial" style="width:48px;height:48px;font-size:1.1rem;">
                    <?= strtoupper(mb_substr($chacara['locador_nome'] ?? 'A', 0, 1)) ?>
                </span>
            <?php endif; ?>
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
                            <?php if (!empty($av['cliente_foto'])): ?>
                                <img src="<?= BASE_URL . htmlspecialchars($av['cliente_foto']) ?>"
                                     alt="<?= htmlspecialchars($av['cliente_nome']) ?>"
                                     class="sf-avatar sf-avatar--photo">
                            <?php else: ?>
                                <span class="sf-avatar sf-avatar--initial">
                                    <?= strtoupper(mb_substr($av['cliente_nome'] ?? 'U', 0, 1)) ?>
                                </span>
                            <?php endif; ?>
                            <div>
                                <p class="fw-semibold small mb-0"><?= htmlspecialchars($av['cliente_nome']) ?></p>
                                <p class="text-muted" style="font-size:.75rem;"><?= date('M Y', strtotime($av['criado_em'])) ?></p>
                            </div>
                        </div>
                        <div class="mb-1" style="font-size:.75rem;">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?= $i <= $av['nota'] ? '' : 'text-secondary' ?>" <?= $i <= $av['nota'] ? 'style="color:var(--sf-text-strong)"' : '' ?>></i>
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
                            <input type="text" id="data_inicio" name="data_inicio"
                                   placeholder="dd/mm/aaaa"
                                   class="border-0 bg-transparent fw-semibold small w-100"
                                   style="outline:none; cursor:pointer;" autocomplete="off">
                        </div>
                        <div class="sf-date-cell">
                            <div class="sf-date-cell-label">CHECK-OUT</div>
                            <input type="text" id="data_fim" name="data_fim"
                                   placeholder="dd/mm/aaaa"
                                   class="border-0 bg-transparent fw-semibold small w-100"
                                   style="outline:none; cursor:pointer;" autocomplete="off">
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

<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/pt.js"></script>
<script>
(function () {
    var preco   = <?= (float) $chacara['preco_diaria'] ?>;
    var preview = document.getElementById('total-preview');
    var $form   = document.querySelector('.sf-booking-box form');

    function atualizar(inicio, fim) {
        if (!inicio || !fim || !preview) return;
        var d1   = new Date(inicio);
        var d2   = new Date(fim);
        if (d2 < d1) return;
        // Mesmo dia = 1 diária (day use); dias consecutivos = diferença + 1
        var dias = Math.round((d2 - d1) / 86400000) + 1;
        preview.textContent = dias + ' diária' + (dias > 1 ? 's' : '') + ' × R$ ' +
            preco.toFixed(2).replace('.', ',') + ' = R$ ' +
            (dias * preco).toFixed(2).replace('.', ',');
    }

    var ocupados = []; // guardará os ranges após o fetch

    var fpFim;

    var fpInicio = flatpickr('#data_inicio', {
        locale: 'pt',
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        minDate: 'today',
        disableMobile: true,
        onChange: function (sel, dateStr) {
            if (!sel[0]) return;
            // Permite check-out no mesmo dia (1 diária) — só limpa se fim < início
            fpFim.set('minDate', sel[0]);
            if (fpFim.selectedDates[0] && fpFim.selectedDates[0] < sel[0]) {
                fpFim.clear();
            }
            atualizar(dateStr, fpFim.input.value);
        }
    });

    fpFim = flatpickr('#data_fim', {
        locale: 'pt',
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        minDate: 'today',
        disableMobile: true,
        onChange: function (sel, dateStr) {
            if (!sel[0] || !fpInicio.selectedDates[0]) return;

            var inicio = fpInicio.selectedDates[0];
            var fim    = sel[0];

            // Verifica se algum range ocupado intercepta o intervalo selecionado
            var conflito = ocupados.some(function (r) {
                var rDe  = new Date(r.from);
                var rAte = new Date(r.to);
                // há sobreposição se: início do range < fim selecionado E fim do range > início selecionado
                return rDe <= fim && rAte >= inicio;
            });

            if (conflito) {
                fpFim.clear();
                if (preview) preview.textContent = '';
                notification({ type: 'error', message: 'O período selecionado inclui datas já reservadas. Escolha outro intervalo.' });
                return;
            }

            atualizar(fpInicio.input.value, dateStr);
        }
    });

    // Carrega datas ocupadas e aplica nos dois calendários
    fetch(BASE_URL + '/chacaras/<?= (int) $chacara['id'] ?>/datas-ocupadas')
        .then(function (r) { return r.json(); })
        .then(function (ranges) {
            if (!ranges.length) return;
            ocupados = ranges;
            fpInicio.set('disable', ranges);
            fpFim.set('disable', ranges);
        });

    // Valida antes de submeter
    if ($form) {
        $form.addEventListener('submit', function (e) {
            if (!fpInicio.input.value || !fpFim.input.value) {
                e.preventDefault();
                notification({ type: 'warning', message: 'Selecione as datas de check-in e check-out.' });
            }
        });
    }
})();
</script>