<?php $pageTitle = 'Criar conta — Sítio Fácil'; ?>
<div class="row justify-content-center" style="padding: 2rem 0;">
    <div class="col-12 col-md-8 col-lg-7">

        <div class="text-center mb-4">
            <a href="<?= BASE_URL ?>/" class="d-inline-flex align-items-center gap-2 text-decoration-none sf-brand-link">
                <i class="fas fa-tree"></i> Sítio Fácil
            </a>
            <p class="mt-3 fw-bold fs-5 text-body-emphasis">Crie sua conta grátis</p>
            <p class="text-muted small">Encontre e anuncie chalés e sítios</p>
        </div>

        <div class="card shadow-sm border" style="border-radius:16px;">
            <div class="card-body p-4">
                <h1 class="h5 fw-bold mb-4">Informações básicas</h1>

                <form id="form-cadastro" method="POST" action="<?= BASE_URL ?>/cadastro" novalidate>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="nome">Nome completo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user text-muted"></i></span>
                                <input class="form-control" type="text" id="nome" name="nome" required autocomplete="name" placeholder="João da Silva">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="email">E-mail</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                                <input class="form-control" type="email" id="email" name="email" required autocomplete="email" placeholder="seu@email.com">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="senha">Senha</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                                <input class="form-control" type="password" id="senha" name="senha" required minlength="6" autocomplete="new-password" placeholder="Mínimo 6 caracteres">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="telefone">Telefone (opcional)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone text-muted"></i></span>
                                <input class="form-control maskPhone" type="tel" id="telefone" name="telefone" autocomplete="tel" placeholder="(92) 99999-9999">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" for="tipo_pessoa">Tipo de pessoa</label>
                            <select class="form-select" id="tipo_pessoa" name="tipo_pessoa" required>
                                <option value="PF">Pessoa Física</option>
                                <option value="PJ">Pessoa Jurídica</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold" for="cpf_cnpj">CPF / CNPJ</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-id-card text-muted"></i></span>
                                <input class="form-control maskCpf" type="text" id="cpf_cnpj" name="cpf_cnpj" required placeholder="000.000.000-00">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Perfil</label>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="perfil[]" value="CLIENTE" id="perfil_cliente" checked>
                                <label class="form-check-label" for="perfil_cliente">
                                    <i class="fas fa-user me-1"></i> Cliente (quero alugar)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="perfil[]" value="LOCADOR" id="perfil_locador">
                                <label class="form-check-label" for="perfil_locador">
                                    <i class="fas fa-home me-1"></i> Locador (tenho chácara para alugar)
                                </label>
                            </div>
                        </div>
                        <div class="form-text">Você pode ter os dois perfis ao mesmo tempo.</div>
                    </div>

                    <!-- Aceite obrigatório -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="aceite_termos" name="aceite_termos" value="1" required>
                            <label class="form-check-label small" for="aceite_termos">
                                Li e concordo com os
                                <a href="<?= BASE_URL ?>/termos" target="_blank" rel="noopener">Termos de Uso</a>,
                                a <a href="<?= BASE_URL ?>/privacidade" target="_blank" rel="noopener">Política de Privacidade</a>
                                e a <a href="<?= BASE_URL ?>/cancelamento" target="_blank" rel="noopener">Política de Cancelamento</a>
                                do Sítio Fácil. <span class="text-danger">*</span>
                            </label>
                        </div>
                    </div>

                    <div class="d-grid mt-3">
                        <button class="btn btn-primary btn-lg" type="submit">Criar conta</button>
                    </div>
                </form>

                <div class="sf-divider my-4">ou</div>

                <div class="d-grid">
                    <a href="<?= BASE_URL ?>/login" class="btn btn-outline-secondary">Já tenho conta — Entrar</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $("#tipo_pessoa").change(function() {
            const tipo = $(this).val();
            const cpfCnpjInput = $("#cpf_cnpj");
            cpfCnpjInput.val('');
            if (tipo === 'PF') {
                cpfCnpjInput.removeClass('maskCnpj').addClass('maskCpf');
            } else {
                cpfCnpjInput.removeClass('maskCpf').addClass('maskCnpj');
            }
        }).trigger('change');

        validateForm({
            formSelector: '#form-cadastro',
            rules: {
                nome: {
                    required: true,
                    minlength: 3
                },
                email: {
                    required: true,
                    email: true
                },
                senha: {
                    required: true,
                    minlength: 6
                },
                cpf_cnpj: {
                    required: true,
                    cpfCnpj: true,
                },
                tipo_pessoa: {
                    required: true
                },
                aceite_termos: {
                    required: true
                },
            },
            message: {
                nome: {
                    required: 'Informe seu nome completo.',
                    minlength: 'Mínimo 3 caracteres.'
                },
                email: {
                    required: 'Informe seu e-mail.',
                    email: 'E-mail inválido.'
                },
                senha: {
                    required: 'Informe uma senha.',
                    minlength: 'A senha deve ter no mínimo 6 caracteres.'
                },
                cpf_cnpj: {
                    required: 'Informe o CPF ou CNPJ.',
                    cpfCnpj: (value, element) => {
                        const tipo = $("#tipo_pessoa").val();
                        return tipo === 'PF' ? 'CPF inválido.' : 'CNPJ inválido.';
                    }
                },
                aceite_termos: {
                    required: 'Você deve aceitar os termos para criar uma conta.'
                },
            },
        });
    });
</script>