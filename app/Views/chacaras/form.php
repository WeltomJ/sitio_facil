<?php
$editando    = isset($chacara);
$pageTitle   = $editando ? 'Editar Chácara' : 'Cadastrar Chácara';
$acao        = $editando
    ? BASE_URL . '/locador/chacaras/' . $chacara['id']
    : BASE_URL . '/locador/chacaras';
$comodosSel  = $comodosSelecionados ?? [];
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

<form method="POST" action="<?= $acao ?>">

    <!-- INFORMAÇÕES GERAIS -->
    <div class="card mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-bold mb-4">Informações gerais</h2>

            <div class="mb-3">
                <label class="form-label fw-semibold" for="nome">Nome da chácara *</label>
                <input class="form-control" type="text" id="nome" name="nome" required
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
                           name="capacidade_maxima" min="1" required
                           value="<?= (int) ($chacara['capacidade_maxima'] ?? 10) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" for="preco_diaria">Preço por diária (R$) *</label>
                    <input class="form-control" type="number" id="preco_diaria" name="preco_diaria"
                           min="0" step="0.01" required
                           value="<?= number_format((float) ($chacara['preco_diaria'] ?? 0), 2, '.', '') ?>">
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
    </div>

    <!-- ENDEREÇO -->
    <div class="card mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-bold mb-4">Endereço</h2>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="logradouro">Logradouro *</label>
                    <input class="form-control" type="text" id="logradouro" name="logradouro" required
                           value="<?= htmlspecialchars($chacara['logradouro'] ?? '') ?>"
                           placeholder="Rua, Estrada, Rodovia...">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold" for="numero">Número</label>
                    <input class="form-control" type="text" id="numero" name="numero"
                           value="<?= htmlspecialchars($chacara['numero'] ?? '') ?>" placeholder="S/N">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" for="complemento">Complemento</label>
                    <input class="form-control" type="text" id="complemento" name="complemento"
                           value="<?= htmlspecialchars($chacara['complemento'] ?? '') ?>" placeholder="KM 15, portão azul...">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold" for="bairro">Bairro</label>
                    <input class="form-control" type="text" id="bairro" name="bairro"
                           value="<?= htmlspecialchars($chacara['bairro'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" for="cidade">Cidade *</label>
                    <input class="form-control" type="text" id="cidade" name="cidade" required
                           value="<?= htmlspecialchars($chacara['cidade'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold" for="estado">Estado *</label>
                    <input class="form-control" type="text" id="estado" name="estado"
                           maxlength="2" required style="text-transform:uppercase;"
                           value="<?= htmlspecialchars($chacara['estado'] ?? '') ?>" placeholder="AM">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" for="cep">CEP</label>
                    <input class="form-control" type="text" id="cep" name="cep"
                           value="<?= htmlspecialchars($chacara['cep'] ?? '') ?>" placeholder="00000-000">
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
                        Coordenadas para exibição no mapa. Você pode obter no
                        <a href="https://maps.google.com" target="_blank" rel="noopener">Google Maps</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- COMODIDADES -->
    <?php if (!empty($comodidades)): ?>
        <div class="card mb-4">
            <div class="card-body p-4">
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
        </div>
    <?php endif; ?>

    <div class="d-flex gap-3 mt-2">
        <button class="btn btn-primary btn-lg" type="submit">
            <?= $editando ? 'Salvar alterações' : 'Cadastrar chácara' ?>
        </button>
        <a href="<?= BASE_URL ?>/locador/chacaras" class="btn btn-outline-secondary btn-lg">Cancelar</a>
    </div>

</form>

