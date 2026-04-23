<?php
$editando    = isset($chacara);
$pageTitle   = $editando ? 'Editar Chácara' : 'Cadastrar Chácara';
$acao        = $editando
    ? BASE_URL . '/locador/chacaras/' . $chacara['id']
    : BASE_URL . '/locador/chacaras';
$comodosSel  = $comodosSelecionados ?? [];
$fotosExist  = $fotos ?? [];
$precoFloat  = (float) ($chacara['preco_diaria'] ?? 0);
?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/locador/chacaras">Minhas Chácaras</a></li>
        <li class="breadcrumb-item active"><?= $editando ? 'Editar' : 'Nova Chácara' ?></li>
    </ol>
</nav>

<h1 class="h3 fw-bold mb-4">
    <?= $editando ? 'Editar Chácara' : 'Cadastrar Nova Chácara' ?>
</h1>

<form id="form-chacara" method="POST" action="<?= $acao ?>" enctype="multipart/form-data">

    <!-- INFORMAÇÕES GERAIS -->
    <div class="sf-section-block mb-4 p-4">
        <h2 class="h5 fw-bold mb-4">Informações Gerais</h2>

        <div class="mb-3">
            <label class="form-label fw-semibold" for="nome">Nome da chácara *</label>
            <input class="form-control" type="text" id="nome" name="nome"
                   value="<?= htmlspecialchars($chacara['nome'] ?? '') ?>"
                   placeholder="Ex.: Chácara Boa Vista">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold" for="descricao">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" rows="4"
                      placeholder="Descreva o local, atrações, regras..."><?= htmlspecialchars($chacara['descricao'] ?? '') ?></textarea>
        </div>

        <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold" for="capacidade_maxima">Capacidade máxima *</label>
                    <input class="form-control" type="number" id="capacidade_maxima"
                           name="capacidade_maxima" min="1"
                           value="<?= (int) ($chacara['capacidade_maxima'] ?? 10) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" for="preco_diaria">Preço por diária (R$) *</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input class="form-control maskMoney" type="text" id="preco_diaria" name="preco_diaria"
                               placeholder="0,00"
                               value="<?= $precoFloat > 0 ? number_format($precoFloat, 2, ',', '.') : '' ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" for="horario_checkin">Horário check-in</label>
                    <input class="form-control" type="time" id="horario_checkin" name="horario_checkin"
                           value="<?= htmlspecialchars(substr($chacara['horario_checkin'] ?? '14:00:00', 0, 5)) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" for="horario_checkout">Horário check-out</label>
                    <input class="form-control" type="time" id="horario_checkout" name="horario_checkout"
                           value="<?= htmlspecialchars(substr($chacara['horario_checkout'] ?? '10:00:00', 0, 5)) ?>">
                </div>
            </div>
    </div>

    <!-- ENDEREÇO -->
    <div class="sf-section-block mb-4 p-4">
        <h2 class="h5 fw-bold mb-4">Endereço</h2>

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold" for="cep">CEP</label>
                    <div class="input-group">
                        <input class="form-control maskCep" type="text" id="cep" name="cep"
                               value="<?= htmlspecialchars(
                                   ($v = preg_replace('/\D/', '', $chacara['cep'] ?? '')) && strlen($v) === 8
                                       ? substr($v, 0, 5) . '-' . substr($v, 5)
                                       : ($chacara['cep'] ?? '')
                               ) ?>"
                               placeholder="00000-000" maxlength="9">
                        <button class="btn btn-outline-secondary" type="button" id="btn-buscar-cep"
                                title="Buscar endereço pelo CEP">
                            <i class="fas fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold" for="logradouro">Logradouro *</label>
                    <input class="form-control" type="text" id="logradouro" name="logradouro"
                           value="<?= htmlspecialchars($chacara['logradouro'] ?? '') ?>"
                           placeholder="Rua, Estrada, Rodovia...">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold" for="numero">Número</label>
                    <input class="form-control" type="text" id="numero" name="numero"
                           value="<?= htmlspecialchars($chacara['numero'] ?? '') ?>" placeholder="S/N">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold" for="complemento">Complemento</label>
                    <input class="form-control" type="text" id="complemento" name="complemento"
                           value="<?= htmlspecialchars($chacara['complemento'] ?? '') ?>" placeholder="KM 15...">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold" for="bairro">Bairro</label>
                    <input class="form-control" type="text" id="bairro" name="bairro"
                           value="<?= htmlspecialchars($chacara['bairro'] ?? '') ?>">
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold" for="cidade">Cidade *</label>
                    <input class="form-control" type="text" id="cidade" name="cidade"
                           value="<?= htmlspecialchars($chacara['cidade'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold" for="estado">Estado *</label>
                    <input class="form-control" type="text" id="estado" name="estado"
                           maxlength="2" style="text-transform:uppercase;"
                           value="<?= htmlspecialchars($chacara['estado'] ?? '') ?>" placeholder="AM">
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold" for="latitude">Latitude</label>
                    <input class="form-control" type="number" id="latitude" name="latitude"
                           step="any" placeholder="-3.1190"
                           value="<?= htmlspecialchars($chacara['latitude'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" for="longitude">Longitude</label>
                    <input class="form-control" type="number" id="longitude" name="longitude"
                           step="any" placeholder="-60.0217"
                           value="<?= htmlspecialchars($chacara['longitude'] ?? '') ?>">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <p class="text-muted small mb-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Coordenadas para exibição no mapa. Obtenha no
                        <a href="https://maps.google.com" target="_blank" rel="noopener">Google Maps</a>.
                    </p>
                </div>
            </div>
    </div>

    <!-- COMODIDADES -->
    <?php if (!empty($comodidades)): ?>
        <div class="sf-section-block mb-4 p-4">
            <h2 class="h5 fw-bold mb-4">Comodidades</h2>
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-2">
                    <?php foreach ($comodidades as $como): ?>
                        <div class="col">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="comodidades[]"
                                       id="como_<?= $como['id'] ?>"
                                       value="<?= $como['id'] ?>"
                                       <?= in_array($como['id'], $comodosSel, false) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="como_<?= $como['id'] ?>">
                                    <?= htmlspecialchars($como['nome']) ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
        </div>
    <?php endif; ?>

    <!-- FOTOS -->
    <div class="sf-section-block mb-4 p-4">
        <h2 class="h5 fw-bold mb-4">Fotos</h2>

            <?php if ($editando && !empty($fotosExist)): ?>
            <!-- Fotos existentes -->
            <p class="fw-semibold text-muted small mb-2">Fotos cadastradas</p>
            <div class="sf-dropzone__previews mb-4" id="previews-existentes">
                <?php foreach ($fotosExist as $foto): ?>
                <div class="sf-dropzone__preview" id="foto-existing-<?= $foto['id'] ?>">
                    <div class="sf-dropzone__preview-wrap">
                        <img src="<?= BASE_URL . htmlspecialchars($foto['url']) ?>" alt="Foto da chácara">
                        <?php if ((int) $foto['ordem'] === 0): ?>
                        <span class="sf-dropzone__badge-principal"><i class="fas fa-star"></i> Principal</span>
                        <?php endif; ?>
                        <div class="sf-dropzone__preview-actions">
                            <button type="button" class="sf-dropzone__btn-principal btn-set-principal"
                                    data-foto-id="<?= $foto['id'] ?>"
                                    data-chacara-id="<?= $chacara['id'] ?>"
                                    title="Definir como principal"
                                    <?= (int) $foto['ordem'] === 0 ? 'disabled' : '' ?>>
                                <i class="fas fa-star"></i>
                            </button>
                            <button type="button" class="sf-dropzone__btn-remove btn-excluir-foto"
                                    data-foto-id="<?= $foto['id'] ?>"
                                    data-chacara-id="<?= $chacara['id'] ?>"
                                    title="Excluir foto">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Dropzone para novos uploads -->
            <p class="fw-semibold text-muted small mb-2">
                <?= $editando ? 'Adicionar novas fotos' : 'Fotos da chácara' ?>
            </p>
            <div class="sf-dropzone" id="sf-dropzone">
                <input type="file" id="fotos-input" name="fotos[]" multiple
                       accept="image/jpeg,image/png,image/webp,image/gif"
                       class="sf-dropzone__input">
                <input type="hidden" id="foto_principal_index" name="foto_principal_index" value="0">
                <div class="sf-dropzone__area" id="sf-dropzone-area">
                    <i class="fas fa-cloud-upload-alt sf-dropzone__icon"></i>
                    <p class="sf-dropzone__text">Arraste e solte as fotos aqui</p>
                    <p class="sf-dropzone__hint">ou <span class="sf-dropzone__link">clique para selecionar</span></p>
                    <p class="sf-dropzone__formats">JPG, PNG, WEBP ou GIF &middot; Máx. 5 MB cada</p>
                </div>
            </div>

            <div class="sf-dropzone__previews mt-3" id="new-previews" style="display:none;"></div>
    </div>

    <div class="d-flex gap-3 mt-2 mb-5">
        <button class="btn btn-primary btn-lg" type="submit">
            <?= $editando ? 'Salvar alterações' : 'Cadastrar chácara' ?>
        </button>
        <a href="<?= BASE_URL ?>/locador/chacaras" class="btn btn-outline-secondary btn-lg">Cancelar</a>
    </div>

</form>

<script>
$(function () {
    /* ─── DROPZONE ─── */
    var newFiles     = [];
    var principalIdx = 0;
    var $area        = $('#sf-dropzone-area');
    var $input       = $('#fotos-input');
    var $previews    = $('#new-previews');
    var $prinIdx     = $('#foto_principal_index');

    $area.on('click', function () { $input.trigger('click'); });

    $area.on('dragover dragenter', function (e) {
        e.preventDefault();
        $area.addClass('sf-dropzone__area--drag');
    });

    $area.on('dragleave', function (e) {
        e.preventDefault();
        $area.removeClass('sf-dropzone__area--drag');
    });

    $area.on('drop', function (e) {
        e.preventDefault();
        $area.removeClass('sf-dropzone__area--drag');
        addFiles(e.originalEvent.dataTransfer.files);
    });

    $input.on('change', function () {
        addFiles(this.files);
        this.value = '';
    });

    function addFiles(fileList) {
        var added = 0;
        for (var i = 0; i < fileList.length; i++) {
            var f = fileList[i];
            if (!f.type.startsWith('image/')) {
                notification({ type: 'warning', message: '"' + f.name + '" não é uma imagem válida.' });
                continue;
            }
            if (f.size > 5 * 1024 * 1024) {
                notification({ type: 'warning', message: '"' + f.name + '" excede 5 MB e foi ignorada.' });
                continue;
            }
            newFiles.push(f);
            added++;
        }
        if (added > 0) renderPreviews();
    }

    function renderPreviews() {
        $previews.empty();
        if (newFiles.length === 0) { $previews.hide(); return; }
        $previews.show();

        newFiles.forEach(function (f, idx) {
            var isPrincipal = idx === principalIdx;
            var reader = new FileReader();
            var $item = $(
                '<div class="sf-dropzone__preview" data-idx="' + idx + '">' +
                    '<div class="sf-dropzone__preview-wrap">' +
                        '<img alt="' + escHtml(f.name) + '">' +
                        (isPrincipal ? '<span class="sf-dropzone__badge-principal"><i class="fas fa-star"></i> Principal</span>' : '') +
                        '<div class="sf-dropzone__preview-actions">' +
                            '<button type="button" class="sf-dropzone__btn-principal btn-new-principal" data-idx="' + idx + '" title="Definir como principal"' + (isPrincipal ? ' disabled' : '') + '>' +
                                '<i class="fas fa-star"></i>' +
                            '</button>' +
                            '<button type="button" class="sf-dropzone__btn-remove btn-new-remove" data-idx="' + idx + '" title="Remover foto">' +
                                '<i class="fas fa-times"></i>' +
                            '</button>' +
                        '</div>' +
                    '</div>' +
                    '<span class="sf-dropzone__preview-name">' + escHtml(f.name) + '</span>' +
                '</div>'
            );
            reader.onload = function (e) { $item.find('img').attr('src', e.target.result); };
            reader.readAsDataURL(f);
            $previews.append($item);
        });
    }

    function escHtml(str) {
        return $('<span>').text(str).html();
    }

    $(document).on('click', '.btn-new-remove', function () {
        var idx = parseInt($(this).data('idx'));
        newFiles.splice(idx, 1);
        if (principalIdx >= newFiles.length) principalIdx = Math.max(0, newFiles.length - 1);
        $prinIdx.val(principalIdx);
        renderPreviews();
    });

    $(document).on('click', '.btn-new-principal', function () {
        principalIdx = parseInt($(this).data('idx'));
        $prinIdx.val(principalIdx);
        renderPreviews();
    });

    /* ─── EXCLUIR FOTO EXISTENTE ─── */
    $(document).on('click', '.btn-excluir-foto', function () {
        var fotoId    = $(this).data('foto-id');
        var chacaraId = $(this).data('chacara-id');
        var $preview  = $(this).closest('.sf-dropzone__preview');

        confirmDialog({
            title:   'Excluir foto',
            message: 'Tem certeza que deseja excluir esta foto? A ação não pode ser desfeita.',
            onConfirm: function () {
                $.post(BASE_URL + '/locador/chacaras/' + chacaraId + '/fotos/' + fotoId + '/excluir',
                    function (res) {
                        if (res.ok) {
                            $preview.fadeOut(250, function () { $(this).remove(); });
                            notification({ type: 'success', message: 'Foto excluída com sucesso.' });
                        } else {
                            notification({ type: 'error', message: res.msg || 'Erro ao excluir foto.' });
                        }
                    }
                ).fail(function () {
                    notification({ type: 'error', message: 'Erro na requisição. Tente novamente.' });
                });
            }
        });
    });

    /* ─── DEFINIR PRINCIPAL (existente) ─── */
    $(document).on('click', '.btn-set-principal', function () {
        var $btn      = $(this);
        var fotoId    = $btn.data('foto-id');
        var chacaraId = $btn.data('chacara-id');
        var $preview  = $btn.closest('.sf-dropzone__preview');

        $.post(BASE_URL + '/locador/chacaras/' + chacaraId + '/fotos/' + fotoId + '/principal',
            function (res) {
                if (res.ok) {
                    $('#previews-existentes .sf-dropzone__badge-principal').remove();
                    $('#previews-existentes .btn-set-principal').prop('disabled', false);
                    $btn.prop('disabled', true);
                    $preview.find('.sf-dropzone__preview-wrap').prepend(
                        '<span class="sf-dropzone__badge-principal"><i class="fas fa-star"></i> Principal</span>'
                    );
                    notification({ type: 'success', message: 'Foto principal atualizada.' });
                } else {
                    notification({ type: 'error', message: res.msg || 'Erro ao definir foto principal.' });
                }
            }
        ).fail(function () {
            notification({ type: 'error', message: 'Erro na requisição. Tente novamente.' });
        });
    });

    /* ─── BUSCA POR CEP ─── */
    $('#btn-buscar-cep').on('click', buscarCep);
    $('#cep').on('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); buscarCep(); }
    });

    function buscarCep() {
        var cep = $('#cep').val().replace(/\D/g, '');
        if (cep.length !== 8) {
            notification({ type: 'warning', message: 'Digite um CEP válido com 8 dígitos.' });
            return;
        }
        var $btn  = $('#btn-buscar-cep');
        var $icon = $btn.find('i');
        $btn.prop('disabled', true);
        $icon.removeClass('fa-magnifying-glass').addClass('fa-spinner fa-spin');

        $.getJSON('https://viacep.com.br/ws/' + cep + '/json/')
            .done(function (data) {
                if (data.erro) {
                    notification({ type: 'error', message: 'CEP não encontrado.' });
                    return;
                }
                $('#logradouro').val(data.logradouro || '').trigger('change');
                $('#bairro').val(data.bairro     || '').trigger('change');
                $('#cidade').val(data.localidade || '').trigger('change');
                $('#estado').val((data.uf        || '').toUpperCase()).trigger('change');
                notification({ type: 'success', message: 'Endereço preenchido automaticamente.' });
                $('#numero').trigger('focus');
            })
            .fail(function () {
                notification({ type: 'error', message: 'Erro ao consultar o CEP. Tente novamente.' });
            })
            .always(function () {
                $btn.prop('disabled', false);
                $icon.removeClass('fa-spinner fa-spin').addClass('fa-magnifying-glass');
            });
    }

    validateForm({
        formSelector: '#form-chacara',
        rules: {
            nome:              { required: true, minlength: 3 },
            capacidade_maxima: { required: true, min: 1 },
            preco_diaria:      { required: true, minMoney: 0.01 },
            logradouro:        { required: true },
            cidade:            { required: true },
            estado:            { required: true, minlength: 2, maxlength: 2 },
        },
        messages: {
            nome:              { required: 'Informe o nome da chácara', minlength: 'Mínimo 3 caracteres' },
            capacidade_maxima: { required: 'Informe a capacidade máxima', min: 'Deve ser ao menos 1' },
            preco_diaria:      { required: 'Informe o preço da diária', minMoney: 'O preço deve ser maior que zero' },
            logradouro:        { required: 'Informe o logradouro' },
            cidade:            { required: 'Informe a cidade' },
            estado:            { required: 'Informe o estado', minlength: 'Use a sigla com 2 letras', maxlength: 'Use a sigla com 2 letras' },
        },
        submitHandler: function (formEl) {
            var $btn = $(formEl).find('[type=submit]');
            $btn.prop('disabled', true).html(
                '<i class="fas fa-spinner fa-spin me-2"></i>' +
                ($btn.text().trim().startsWith('Salvar') ? 'Salvando...' : 'Cadastrando...')
            );

            if (newFiles.length > 0) {
                try {
                    if (typeof DataTransfer !== 'undefined') {
                        var dt = new DataTransfer();
                        newFiles.forEach(function (f) { dt.items.add(f); });
                        document.getElementById('fotos-input').files = dt.files;
                    }
                } catch (e) {
                    // DataTransfer não suportado — o input nativo já tem os arquivos
                }
            }

            formEl.submit();
        }
    });
});
</script>

