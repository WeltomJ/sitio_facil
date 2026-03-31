/**
 * theme.js — Alternador de tema Claro / Escuro
 * Sítio Fácil Design System
 *
 * Ordem de prioridade:
 *   1. Preferência salva em localStorage
 *   2. Preferência do sistema operacional (prefers-color-scheme)
 *   3. Padrão: claro
 *
 * Execute o script no <head> (inline snippet + este arquivo separado)
 * para evitar FOUC (flash of unstyled content).
 */

(function () {
    'use strict';

    const STORAGE_KEY = 'sf-theme';
    const DARK        = 'dark';
    const LIGHT       = 'light';

    /* ─── Detectar tema inicial ─────────────────────────────────────── */

    function getSystemTheme() {
        return window.matchMedia &&
               window.matchMedia('(prefers-color-scheme: dark)').matches
            ? DARK : LIGHT;
    }

    function getSavedTheme() {
        try { return localStorage.getItem(STORAGE_KEY); }
        catch (e) { return null; }
    }

    function saveTheme(theme) {
        try { localStorage.setItem(STORAGE_KEY, theme); }
        catch (e) { /* silencioso */ }
    }

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
        updateToggleButton(theme);
        updateMetaThemeColor(theme);
    }

    /* ─── Botão de toggle ───────────────────────────────────────────── */

    function updateToggleButton(theme) {
        const btn  = document.getElementById('btn-theme-toggle');
        if (!btn) return;

        const icon = btn.querySelector('i');
        const tip  = btn.querySelector('.sf-theme-tip');

        if (theme === DARK) {
            if (icon) icon.className = 'fas fa-sun';
            btn.setAttribute('title', 'Mudar para modo claro');
            btn.setAttribute('aria-label', 'Mudar para modo claro');
            if (tip) tip.textContent = 'Modo claro';
        } else {
            if (icon) icon.className = 'fas fa-moon';
            btn.setAttribute('title', 'Mudar para modo escuro');
            btn.setAttribute('aria-label', 'Mudar para modo escuro');
            if (tip) tip.textContent = 'Modo escuro';
        }
    }

    /* ─── Meta theme-color (barra do browser mobile) ───────────────── */

    function updateMetaThemeColor(theme) {
        let meta = document.querySelector('meta[name="theme-color"]');
        if (!meta) {
            meta = document.createElement('meta');
            meta.name = 'theme-color';
            document.head.appendChild(meta);
        }
        meta.content = theme === DARK ? '#0E1A10' : '#2E7D32';
    }

    /* ─── Animação de transição ao trocar tema ──────────────────────── */

    function toggleThemeWithAnimation() {
        const current = document.documentElement.getAttribute('data-bs-theme') || LIGHT;
        const next    = current === DARK ? LIGHT : DARK;

        // Adiciona classe de transição suave ao body
        document.body.style.transition =
            'background-color 0.35s cubic-bezier(0.4,0,0.2,1), ' +
            'color 0.35s cubic-bezier(0.4,0,0.2,1)';

        applyTheme(next);
        saveTheme(next);

        // Remove transição após a animação
        setTimeout(function () {
            document.body.style.transition = '';
        }, 400);
    }

    /* ─── Escutar mudanças no sistema ───────────────────────────────── */

    function listenSystemChanges() {
        const mq = window.matchMedia('(prefers-color-scheme: dark)');
        mq.addEventListener('change', function (e) {
            // Só aplica se o usuário não tiver salvo preferência manual
            if (!getSavedTheme()) {
                applyTheme(e.matches ? DARK : LIGHT);
            }
        });
    }

    /* ─── Inicialização ─────────────────────────────────────────────── */

    // Aplica imediatamente (mesmo antes do DOMContentLoaded)
    var initialTheme = getSavedTheme() || getSystemTheme();
    document.documentElement.setAttribute('data-bs-theme', initialTheme);

    // Quando o DOM estiver pronto, conecta o botão
    document.addEventListener('DOMContentLoaded', function () {
        var btn = document.getElementById('btn-theme-toggle');
        if (btn) {
            btn.addEventListener('click', toggleThemeWithAnimation);
        }

        // Atualiza o ícone conforme o tema atual
        updateToggleButton(document.documentElement.getAttribute('data-bs-theme') || LIGHT);
        updateMetaThemeColor(document.documentElement.getAttribute('data-bs-theme') || LIGHT);

        listenSystemChanges();
    });

})();
