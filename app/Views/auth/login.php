<?php $pageTitle = 'Entrar — Sítio Fácil'; ?>

<div class="row justify-content-center" style="min-height:70vh; align-items:center; padding: 2rem 0;">
    <div class="col-12 col-sm-8 col-md-6 col-lg-4">

        <div class="text-center mb-4">
            <a href="<?= BASE_URL ?>/" class="d-inline-flex align-items-center gap-2 text-decoration-none sf-brand-link">
                <i class="fas fa-tree"></i> Sítio Fácil
            </a>
            <p class="mt-3 fw-bold fs-5 text-body-emphasis">Bem-vindo de volta</p>
            <p class="text-muted small">Entre na sua conta para continuar</p>
        </div>

        <div class="card shadow-sm border" style="border-radius:16px;">
            <div class="card-body p-4">
                <form method="POST" action="<?= BASE_URL ?>/login" novalidate>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="email">E-mail</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                            <input class="form-control" type="email" id="email" name="email"
                                   required autofocus autocomplete="email"
                                   placeholder="seu@email.com">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="senha">Senha</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                            <input class="form-control" type="password" id="senha" name="senha"
                                   required autocomplete="current-password"
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button class="btn btn-primary btn-lg" type="submit">Entrar</button>
                    </div>
                </form>

                <div class="sf-divider my-4">ou</div>

                <div class="d-grid">
                    <a href="<?= BASE_URL ?>/cadastro" class="btn btn-outline-secondary">Criar conta gratuita</a>
                </div>
            </div>
        </div>

        <p class="text-center mt-3 text-muted" style="font-size:.75rem;">
            Ao entrar, você concorda com nossos termos de uso e política de privacidade.
        </p>
    </div>
</div>
