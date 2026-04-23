/**
 * =====================================================
 * UTILS.JS
 * Descrição: Funções utilitárias globais do sistema
 * =====================================================
 */

//--------------------------------------------------------------
// Funções de Navegação, URL e Armazenamento
//--------------------------------------------------------------

/**
 * Função para pegar parâmetros da URL
 * @param {string} name Nome do parâmetro na URL
 * @returns {string|null}
 */
function getUrlParameter(name) {
    if (!name) return null;
    const params = new URLSearchParams(window.location.search);
    return params.get(name);
}

/**
 * Função para alterar ou adicionar um parâmetro na URL sem recarregar a página
 * @param {string} param Nome do parâmetro
 * @param {string} value Valor do parâmetro
 * @return {void}
 */
function changeParameterInUrl(param, value) {
    const url = new URL(window.location.href);
    url.searchParams.set(param, value);
    window.history.replaceState({}, '', url.toString());
}

/**
 * Função para remover um parâmetro da URL sem recarregar a página
 * @param {string} param Nome do parâmetro
 * @return {void}
 */
function removeParameterFromUrl(param) {
    const url = new URL(window.location.href);
    url.searchParams.delete(param);
    window.history.replaceState({}, '', url.toString());
}

/**
 * Armazena dados no localStorage
 * @param {string} key Chave para armazenar o valor
 * @param {any} value Valor a ser armazenado (será convertido para JSON)
 */
function setLocalStorage(key, value) {
    localStorage.setItem(key, JSON.stringify(value));
}

/** Recupera dados do localStorage
 * @param {string} key Chave do valor a ser recuperado
 * @return {any} Valor recuperado (convertido de JSON), ou null se não existir
*/
function getLocalStorage(key) {
    const value = localStorage.getItem(key);

    if (!value) return null;

    try {
        return JSON.parse(value);
    } catch (e) {
        return value;
    }
}

/**
 * Limpa os dados do localStorage ou deleta um item específico
 * @param {string|null} key Chave do item a ser removido, ou null para limpar todos os dados
 */
function clearLocalStorage(key = null) {
    if (key) {
        localStorage.removeItem(key);
    } else {
        localStorage.clear();
    }
}

/**
 * Armazena dados no sessionStorage
 * @param {string} key Chave para armazenar o valor
 * @param {any} value Valor a ser armazenado (será convertido para JSON)
 */
function setSessionStorage(key, value) {
    sessionStorage.setItem(key, JSON.stringify(value));
}

/** Recupera dados do sessionStorage
 * @param {string} key Chave do valor a ser recuperado
 * @return {any} Valor recuperado (convertido de JSON), ou null se não existir
*/
function getSessionStorage(key) {
    const value = sessionStorage.getItem(key);

    if (!value) return null;

    try {
        return JSON.parse(value);
    } catch (e) {
        return value;
    }
}

/**
 * Limpa os dados do sessionStorage ou deleta um item específico
 * @param {string|null} key Chave do item a ser removido, ou null para limpar todos os dados
 */
function clearSessionStorage(key = null) {
    if (key) {
        sessionStorage.removeItem(key);
    } else {
        sessionStorage.clear();
    }
}

//--------------------------------------------------------------
// Funções de Loader
//--------------------------------------------------------------

/**
 * Faz o hide do loader
 * @param {number|null} timeout Tempo em ms para aguardar antes de esconder o loader
 * @return {void}
 */
function hideLoader(timeout = null) {
    if (timeout) {
        setTimeout(() => {
            $('#loader').css('visibility', 'hidden');
            $('#content').css('visibility', 'visible');
        }, timeout);
    } else {
        $('#loader').css('visibility', 'hidden');
        $('#content').css('visibility', 'visible');
    }
}

/**
 * Faz o show do loader
 * @return {void}
 */
function showLoader() {
    $('#loader').css('visibility', 'visible');
    $('#content').css('visibility', 'hidden');
}

//--------------------------------------------------------------
// Paginação
//--------------------------------------------------------------

/**
 * Gera o HTML da paginação usando Bootstrap 5.
 * Os links usam data-page para navegação via JS (preserva query string).
 * @param {number} current Página atual (1-based)
 * @param {number} total   Total de páginas
 * @returns {string} HTML da paginação ou '' quando só há 1 página
 */
function pagination(current, total) {
    if (total <= 1) return '';

    const dis  = (cond) => cond ? ' disabled' : '';
    const act  = (p)    => p === current ? ' active' : '';
    const link = (page, label, title) =>
        `<li class="page-item${dis(page < 1 || page > total || page === current)}">` +
        `<a class="page-link" href="javascript:void(0)" data-page="${page}"${title ? ` aria-label="${title}"` : ''}>` +
        `${label}</a></li>`;

    let html = `<nav aria-label="Paginação" class="mt-4 d-flex justify-content-center">` +
               `<ul class="pagination">`;

    // Primeira / Anterior
    html += link(1, '<i class="fas fa-angles-left"></i>', 'Primeira página');
    html += link(current - 1, '<i class="fas fa-chevron-left"></i>', 'Página anterior');

    // Janela de páginas (current ± 2) com ellipsis
    const from = Math.max(1, current - 2);
    const to   = Math.min(total, current + 2);

    if (from > 1) html += `<li class="page-item disabled"><a class="page-link">…</a></li>`;

    for (let p = from; p <= to; p++) {
        html += `<li class="page-item${act(p)}">` +
                `<a class="page-link" href="javascript:void(0)" data-page="${p}">${p}</a></li>`;
    }

    if (to < total) html += `<li class="page-item disabled"><a class="page-link">…</a></li>`;

    // Próxima / Última
    html += link(current + 1, '<i class="fas fa-chevron-right"></i>', 'Próxima página');
    html += link(total, '<i class="fas fa-angles-right"></i>', 'Última página');

    html += `</ul></nav>`;
    return html;
}

//--------------------------------------------------------------
// Máscaras de Input
//--------------------------------------------------------------

$(document).on("input", ".maskPhone", function () {
    let val = $(this).val().replace(/\D/g, "");

    if (val.length === 0) {
        $(this).val("");
        return;
    }

    if (val.length <= 2) {
        $(this).val("(" + val);
    } else if (val.length <= 6) {
        $(this).val(val.replace(/(\d{2})(\d+)/, "($1) $2"));
    } else if (val.length <= 10) {
        $(this).val(val.replace(/(\d{2})(\d{4})(\d{0,4})/, "($1) $2-$3"));
    } else if (val.length <= 11) {
        $(this).val(val.replace(/(\d{2})(\d{5})(\d{0,4})/, "($1) $2-$3"));
    } else {
        val = val.substring(0, 11);
        $(this).val(val.replace(/(\d{2})(\d{5})(\d{4})/, "($1) $2-$3"));
    }
}).each(function () {
    $(this).trigger("input");
});

$(document).on("input", ".maskCpfCnpj", function () {
    let val = $(this).val().replace(/\D/g, "");

    if (val.length <= 11) {
        val = val.substring(0, 11);
        if (val.length >= 4 && val.length <= 6) {
            val = val.replace(/(\d{3})(\d+)/, "$1.$2");
        } else if (val.length >= 7 && val.length <= 9) {
            val = val.replace(/(\d{3})(\d{3})(\d+)/, "$1.$2.$3");
        } else if (val.length === 10 || val.length === 11) {
            val = val.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, "$1.$2.$3-$4");
        }
    } else {
        val = val.substring(0, 14);
        if (val.length >= 3 && val.length <= 5) {
            val = val.replace(/(\d{2})(\d+)/, "$1.$2");
        } else if (val.length >= 6 && val.length <= 8) {
            val = val.replace(/(\d{2})(\d{3})(\d+)/, "$1.$2.$3");
        } else if (val.length >= 9 && val.length <= 12) {
            val = val.replace(/(\d{2})(\d{3})(\d{3})(\d+)/, "$1.$2.$3/$4");
        } else if (val.length === 13 || val.length === 14) {
            val = val.replace(
                /(\d{2})(\d{3})(\d{3})(\d{4})(\d{1,2})/,
                "$1.$2.$3/$4-$5"
            );
        }
    }

    $(this).val(val);
}).each(function () {
    $(this).trigger("input");
});

$(document).on("input", ".maskCpf", function () {
    let val = $(this).val().replace(/\D/g, "");

    if (val.length === 0) {
        $(this).val("");
        return;
    }

    val = val.substring(0, 11);
    if (val.length >= 4 && val.length <= 6) {
        val = val.replace(/(\d{3})(\d+)/, "$1.$2");
    } else if (val.length >= 7 && val.length <= 9) {
        val = val.replace(/(\d{3})(\d{3})(\d+)/, "$1.$2.$3");
    } else if (val.length === 10 || val.length === 11) {
        val = val.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, "$1.$2.$3-$4");
    }

    $(this).val(val);
}).each(function () {
    $(this).trigger("input");
});

$(document).on("input", ".maskCnpj", function () {
    let val = $(this).val().replace(/\D/g, "");

    if (val.length === 0) {
        $(this).val("");
        return;
    }

    val = val.substring(0, 14);
    if (val.length >= 3 && val.length <= 5) {
        val = val.replace(/(\d{2})(\d+)/, "$1.$2");
    } else if (val.length >= 6 && val.length <= 8) {
        val = val.replace(/(\d{2})(\d{3})(\d+)/, "$1.$2.$3");
    } else if (val.length >= 9 && val.length <= 12) {
        val = val.replace(/(\d{2})(\d{3})(\d{3})(\d+)/, "$1.$2.$3/$4");
    } else if (val.length === 13 || val.length === 14) {
        val = val.replace(
            /(\d{2})(\d{3})(\d{3})(\d{4})(\d{1,2})/,
            "$1.$2.$3/$4-$5"
        );
    }

    $(this).val(val);
}).each(function () {
    $(this).trigger("input");
});

$(document).on("input", ".maskCep", function () {
    let val = $(this).val().replace(/\D/g, "");

    if (val.length === 0) {
        $(this).val("");
        return;
    }

    val = val.substring(0, 8);
    if (val.length >= 5) {
        val = val.replace(/(\d{5})(\d+)/, "$1-$2");
    }

    $(this).val(val);
}).each(function () {
    $(this).trigger("input");
});

$(document).on("input", ".maskMoney", function () {
    let val = $(this).val().replace(/\D/g, "");
    if (val.length === 0) {
        $(this).val("0,00");
        return;
    }
    val = (parseFloat(val) / 100).toFixed(2);
    val = val.replace(".", ",");
    val = val.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
    $(this).val(val);
}).each(function () {
    $(this).trigger("input");
});

$(document).on("input", "[class*='maskDecimalNumber']", function () {
    let $el = $(this);
    let val = $el.val().replace(/\D/g, "");

    if (val.length === 0) {
        $el.val("");
        return;
    }

    let cls = $el.attr("class");
    let match = cls.match(/maskDecimalNumber-(\d+)/);
    let decimals = match ? parseInt(match[1]) : 2;

    val = (parseFloat(val) / Math.pow(10, decimals)).toFixed(decimals);
    val = val.replace(".", ",");

    let parts = val.split(",");
    parts[0] = parts[0].replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
    $el.val(parts.join(","));
}).each(function () {
    $(this).trigger("input");
});

$(document).on("input", ".maskDecimal", function () {
    let val = $(this).val();
    val = val.replace(/\./g, ",");
    val = val.replace(/[^0-9,+-∞]/g, "");
    const parts = val.split(",");
    if (parts.length > 2) {
        val = parts[0] + "," + parts.slice(1).join("").replace(/,/g, "");
    }

    if (val.startsWith(",")) {
        val = val.substring(1);
    }

    $(this).val(val);
}).each(function () {
    $(this).trigger("input");
});

//--------------------------------------------------------------
// Tooltips
//--------------------------------------------------------------

$(document).on('mouseenter', '[data-toggle="tooltip"]', function () {
    $(this).tooltip('show');

    $(this).one('mouseleave', function () {
        $(this).tooltip('hide');
    });

    $(this).one('click', function () {
        $(this).tooltip('hide');
    });
});

//--------------------------------------------------------------
// Funções de Formatação
//--------------------------------------------------------------

/**
 * Formata um número
 * @param {string | number} number Número a ser formatado
 * @param {number} decimals Número de casas decimais
 * @returns {string} Número formatado no padrão brasileiro
 */
function formatNumber(number, decimals = 0) {
    if (number === null || number === undefined || isNaN(number)) return "0";
    return convertValue(number).toLocaleString("pt-BR", { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
}

/**
 * Formata um CPF
 * @param {string | number} cpf CPF sem formatação
 * @returns {string} CPF formatado
 */
function formatCPF(cpf) {
    cpf = cpf.toString().replace(/\D/g, "").padStart(11, "0");

    if (cpf.length !== 11) {
        return cpf;
    }
    return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
}

/**
 * Formata um número de telefone
 * @param {string | number} number Número de telefone sem formatação
 * @returns {string} Número de telefone formatado
 */
function formatPhone(number) {
    if (!number) return "";

    number = number.toString().replace(/\D/g, "");

    if (number.length <= 10) {
        return number.replace(/(\d{2})(\d{4})(\d{0,4})/, "($1) $2-$3");
    } else {
        return number.replace(/(\d{2})(\d{5})(\d{0,4})/, "($1) $2-$3");
    }
}

/**
 * Formata um CPF ou CNPJ
 * @param {string | number} cpfCnpj CPF ou CNPJ sem formatação
 * @returns {string} CPF ou CNPJ formatado
*/
function formatCpfCnpj(cpfCnpj) {
    if (!cpfCnpj) return "";

    cpfCnpj = cpfCnpj.toString().replace(/\D/g, "");

    if (cpfCnpj.length <= 11) {
        return cpfCnpj.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, "$1.$2.$3-$4");
    } else {
        return cpfCnpj.replace(
            /(\d{2})(\d{3})(\d{3})(\d{4})(\d{1,2})/,
            "$1.$2.$3/$4-$5"
        );
    }
}

/**
 * Formata um CEP
 * @param {string | number} cep CEP sem formatação
 * @returns {string} CEP formatado
 */
function formatCEP(cep) {
    if (!cep) return "";

    cep = cep.toString().replace(/\D/g, "");
    if (cep.length === 8) {
        return cep.replace(/(\d{5})(\d{3})/, "$1-$2");
    } else {
        return cep;
    }
}

/**
 * Formata um RG
 * @param {string | number} rg RG sem formatação
 * @returns {string} RG formatado
 */
function formatRG(rg) {
    if (!rg) return "";

    rg = rg.toString().replace(/\D/g, "");
    if (rg.length <= 9) {
        return rg.replace(/(\d{2})(\d{3})(\d{3})(\d{1})/, "$1.$2.$3-$4");
    } else {
        return rg.replace(/(\d{2})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
    }
}

/**
 * Converte uma string formatada em número float
 * @param {string|number|null|undefined} value Valor a ser convertido
 * @returns {number} Valor convertido
 */
function convertValue(value) {
    if (value === null || value === undefined) return 0;
    let str = String(value).trim().replace(/R\$|\s|%/g, "");
    if (/^-?\d+(\.\d+)?$/.test(str)) {
        return parseFloat(str);
    }
    const isBR = /,\d{1,}$/.test(str);

    if (isBR) {
        str = str.replace(/\./g, "").replace(",", ".");
    } else {
        str = str.replace(/,/g, "");
    }

    const num = parseFloat(str);
    return isNaN(num) ? 0 : num;
}

/**
 * Adiciona padding, prefixo e sufixo a uma string
 * @param {Object} params - Parâmetros da função.
 * @param {number|string} params.str - O valor a ser formatado.
 * @param {number} [params.length=7] - Comprimento total desejado da string.
 * @param {string} [params.fill='0'] - Caractere usado para preencher à esquerda.
 * @param {string} [params.prefix=''] - Prefixo a ser adicionado antes da string.
 * @param {string} [params.suffix=''] - Sufixo a ser adicionado após a string.
 * @returns {string} A string formatted with padding, prefix, and suffix applied.
 */
function padStr({ str, length = 7, fill = '', prefix = '', suffix = '' }) {
    return prefix + String(str).padStart(length, fill) + suffix;
}

/**
 * Formata um valor monetário em reais
 * @param {string|number} amount Valor a ser formatado
 * @param {boolean} [ignoreCurrency=false] Se true, ignora o símbolo de moeda
 * @returns {string} Valor formatado em reais
 */
function formatMoney(amount, ignoreCurrency = false) {
    let value = parseFloat(amount).toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL",
    });

    if (ignoreCurrency) {
        value = value.replace("R$", "").trim();
    }

    return value;
}

/**
 * Formata uma data
 * @param {string} data Data a ser formatada
 * @param {import("moment").MomentFormatSpecification} format Formato desejado
 * @returns {string} Data formatada
 */
function formataData(data, format = "DD/MM/YYYY") {
    if (!data || data === "0000-00-00 00:00:00") return "";

    return moment(data).format(format);
}

/**
 * Formata uma string de diversas formas, preservando acentuação
 * @param {string} str String a ser formatada
 * @param {string} type Tipo de formatação: 'upper', 'lower', 'capital', 'title', 'camel', 'pascal', 'snake', 'kebab', 'sentence'
 * @returns {string} String formatada
 */
function formatString(str, type = 'capital') {
    if (!str || typeof str !== 'string') return '';

    const types = {
        // MAIÚSCULAS
        'upper': () => str.toUpperCase(),

        // minúsculas
        'lower': () => str.toLowerCase(),

        // Primeira letra maiúscula
        'capital': () => str.charAt(0).toUpperCase() + str.slice(1).toLowerCase(),

        // Cada Palavra Com Maiúscula (Ex: "Ação E Reação")
        'title': () => str.toLowerCase().replace(/(^|[^\p{L}])\p{L}/gu, char => char.toUpperCase()),

        // camelCase (Ex: "açãoPenal")
        'camel': () => {
            const words = str.toLowerCase().replace(/[^\p{L}\p{N}]+(.)/gu, (_, chr) => chr.toUpperCase());
            return words.charAt(0).toLowerCase() + words.slice(1);
        },

        // PascalCase (Ex: "AçãoPenal")
        'pascal': () => {
            return str.toLowerCase().replace(/[^\p{L}\p{N}]+(.)/gu, (_, chr) => chr.toUpperCase())
                .replace(/^./, char => char.toUpperCase());
        },

        // snake_case (Ex: "ação_penal")
        'snake': () => {
            return str.replace(/[\p{Lu}]/gu, letter => `_${letter.toLowerCase()}`)
                .replace(/[^\p{L}\p{N}]+/gu, '_')
                .replace(/^_+|_+$/g, '')
                .toLowerCase();
        },

        // kebab-case (Ex: "ação-penal")
        'kebab': () => {
            return str.replace(/[\p{Lu}]/gu, letter => `-${letter.toLowerCase()}`)
                .replace(/[^\p{L}\p{N}]+/gu, '-')
                .replace(/^-+|-+$/g, '')
                .toLowerCase();
        },

        // Primeira letra maiúscula, resto normal
        'sentence': () => {
            const trimmed = str.trim();
            return trimmed.charAt(0).toUpperCase() + trimmed.slice(1);
        }
    };

    return types[type] ? types[type]() : str;
}

/**
 * Exibe uma notificação toast customizada
 * @param {Object} data Objeto contendo as propriedades da notificação
 * @param {string} data.type Tipo da notificação: 'success', 'error', 'warning', 'info'
 * @param {string} data.message Mensagem a ser exibida
 * @param {string|null} data.title Título customizado (opcional)
 * @param {number} data.delay Tempo em ms para fechar automaticamente (padrão: 5000)
 * @param {function|null} data.callback Função de callback após exibir a notificação (opcional)
 * @param {string} data.placement Posição da notificação
 * @return {void}
 */
function notification(data = {
    type: 'info',
    message: '',
    title: null,
    delay: 5000,
    callback: null,
    placement: ''
}) {
    const defaults = {
        type: 'info',
        message: '',
        title: null,
        delay: 5000,
        callback: null,
        placement: 'top-end',
    };

    data = { ...defaults, ...data };

    const config = {
        success: { icon: 'fa-circle-check',       label: 'Sucesso'     },
        error:   { icon: 'fa-circle-xmark',        label: 'Erro'        },
        warning: { icon: 'fa-triangle-exclamation',label: 'Atenção'     },
        info:    { icon: 'fa-circle-info',          label: 'Informação'  },
    }[data.type] || { icon: 'fa-circle-info', label: 'Informação' };

    const title = data.title ?? config.label;
    const id    = 'sf-toast-' + Date.now();

    // Container único fixo no canto definido por placement
    const placement = data.placement || 'top-end';
    const containerId = 'sf-toast-container-' + placement.replace(/[^a-z]/g, '-');

    if (!$('#' + containerId).length) {
        $('body').append(
            `<div id="${containerId}" class="sf-toast-container" data-placement="${placement}" aria-live="polite" aria-atomic="true"></div>`
        );
    }

    const $toast = $(`
        <div id="${id}" class="sf-toast sf-toast--${data.type}" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="sf-toast__header">
                <i class="fas ${config.icon} sf-toast__icon"></i>
                <span class="sf-toast__title">${title}</span>
                <button type="button" class="sf-toast__close" aria-label="Fechar">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
            ${data.message ? `<div class="sf-toast__body">${data.message}</div>` : ''}
        </div>
    `);

    $('#' + containerId).append($toast);

    // Anima entrada
    requestAnimationFrame(() => $toast.addClass('sf-toast--show'));

    // Fechar ao clicar no X
    $toast.find('.sf-toast__close').on('click', () => closeToast($toast));

    // Auto-fechar
    if (data.delay > 0) {
        setTimeout(() => closeToast($toast), data.delay);
    }

    function closeToast($el) {
        $el.removeClass('sf-toast--show').addClass('sf-toast--hide');
        $el.one('transitionend', function () {
            $el.remove();
            if (!$('#' + containerId).children().length) {
                $('#' + containerId).remove();
            }
            if (typeof data.callback === 'function') data.callback();
        });
    }
}

/**
 * Exibe um diálogo de confirmação customizado
 * @param {Object} data Objeto contendo as propriedades do diálogo
 * @param {string} data.type Tipo do diálogo: 'success', 'error', 'warning', 'info', 'question'
 * @param {string} data.message Mensagem a ser exibida
 * @param {string|null} data.title Título customizado (opcional)
 * @param {string} data.confirmText Texto do botão de confirmar (padrão: 'Confirmar')
 * @param {string} data.cancelText Texto do botão de cancelar (padrão: 'Cancelar')
 * @param {string} data.confirmButtonClass Classe CSS do botão confirmar (padrão: 'btn-success')
 * @param {string} data.cancelButtonClass Classe CSS do botão cancelar (padrão: 'btn-secondary')
 * @param {boolean} data.isHtml Se true, permite HTML na mensagem
 * @param {string|null} data.customHtml HTML totalmente customizado para o corpo do modal
 * @param {string|null} data.inputSelector Se informado, retorna também o valor do input selecionado
 * @param {boolean} data.inputRequired Se true, exige preenchimento do inputSelector
 * @param {string} data.inputRequiredMessage Mensagem de validação para campo obrigatório
 * @param {function|null} data.onConfirm Callback de confirmação (alternativa ao Promise)
 * @return {Promise<boolean|{confirmed: boolean, inputValue: string}>}
 */
function confirmDialog(data = {}) {
    const defaults = {
        type: 'question',
        message: '',
        title: null,
        confirmText: 'Confirmar',
        cancelText: 'Cancelar',
        confirmButtonClass: 'btn-primary',
        cancelButtonClass: 'btn-outline-secondary',
        isHtml: false,
        customHtml: null,
        inputSelector: null,
        inputRequired: false,
        inputRequiredMessage: 'Preencha este campo para continuar.',
        onConfirm: null,
    };

    data = { ...defaults, ...data };

    return new Promise((resolve) => {

        const types = {
            success: {
                title:       'Sucesso!',
                icon:        'fa-circle-check',
                accentColor: 'var(--sf-success-text)',
                borderVar:   '--sf-success-border',
            },
            error: {
                title:       'Erro!',
                icon:        'fa-circle-xmark',
                accentColor: 'var(--sf-danger-text)',
                borderVar:   '--sf-danger-border',
            },
            warning: {
                title:       'Atenção!',
                icon:        'fa-triangle-exclamation',
                accentColor: 'var(--sf-warning-text)',
                borderVar:   '--sf-warning-border',
            },
            info: {
                title:       'Informação',
                icon:        'fa-circle-info',
                accentColor: 'var(--sf-info-text)',
                borderVar:   '--sf-info-border',
            },
            question: {
                title:       'Confirmação',
                icon:        'fa-circle-question',
                accentColor: 'var(--sf-primary)',
                borderVar:   '--sf-primary',
            },
        };

        const config  = types[data.type] || types.question;
        const title   = data.title || config.title;
        const modalId = 'sf-confirm-' + Date.now();

        let actionTaken = false;

        let messageContent = '';
        if (data.customHtml) {
            messageContent = data.customHtml;
        } else {
            messageContent = data.isHtml
                ? `<div class="sf-confirm__message">${data.message}</div>`
                : `<p class="sf-confirm__message mb-0">${data.message}</p>`;
        }

        const modalHTML = `
        <div class="modal fade sf-confirm" id="${modalId}" tabindex="-1" aria-labelledby="${modalId}-title" aria-modal="true"
             data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content sf-confirm__content">

                    <div class="sf-confirm__header" style="--sf-confirm-accent:${config.accentColor}">
                        <span class="sf-confirm__icon-wrap">
                            <i class="fas ${config.icon}"></i>
                        </span>
                        <h5 class="sf-confirm__title" id="${modalId}-title">${title}</h5>
                        <button type="button" class="btn-close btn-close-sm ms-auto"
                                data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>

                    <div class="sf-confirm__body">
                        ${messageContent}
                    </div>

                    <div class="sf-confirm__footer">
                        <button type="button" class="btn ${data.cancelButtonClass}" data-action="cancel">
                            <i class="fas fa-xmark me-1"></i>${data.cancelText}
                        </button>
                        <button type="button" class="btn ${data.confirmButtonClass}" data-action="confirm">
                            <i class="fas fa-check me-1"></i>${data.confirmText}
                        </button>
                    </div>

                </div>
            </div>
        </div>`;

        $('body').append(modalHTML);
        const $modal   = $(`#${modalId}`);
        const bsModal  = new bootstrap.Modal($modal[0], { backdrop: 'static', keyboard: false });
        bsModal.show();

        function hideModal() {
            actionTaken = true;
            bsModal.hide();
        }

        // Confirmar
        $modal.find('[data-action="confirm"]').on('click', function () {
            let inputValue = '';

            if (data.inputSelector) {
                const $input = $modal.find(data.inputSelector);
                const $error = $modal.find('.sf-confirm__input-error');

                if ($input.length) {
                    inputValue = String($input.val() || '').trim();

                    if (data.inputRequired && !inputValue) {
                        $input.addClass('sf-input-invalid');
                        if ($error.length) $error.text(data.inputRequiredMessage).removeClass('d-none');
                        $input.trigger('focus');
                        return;
                    }

                    $input.removeClass('sf-input-invalid');
                    if ($error.length) $error.addClass('d-none').text('');
                }
            }

            hideModal();

            if (typeof data.onConfirm === 'function') data.onConfirm();

            if (data.inputSelector) {
                resolve({ confirmed: true, inputValue });
            } else {
                resolve(true);
            }
        });

        // Cancelar
        $modal.find('[data-action="cancel"]').on('click', function () {
            hideModal();
            resolve(false);
        });

        // Limpar DOM após fechar
        $modal.on('hidden.bs.modal', function () {
            if (!actionTaken) resolve(false);
            $modal.remove();
        });
    });
}

/**
 *
 * @param {*} options
 * Exibe uma mensagem de erro (função legada - use notification)
 * @deprecated Use notification() ao invés desta função
 */
function mensagemErro(mensagem, time = 2000, callback = null) {
    notification({
        type: 'error',
        message: mensagem,
        delay: time,
        callback: callback
    });
}

/**
 * Exibe uma mensagem de erro mini (função legada - use notification)
 * @deprecated Use notification() ao invés desta função
 */
function mensagemErroMini(mensagem) {
    notification({
        type: 'error',
        message: mensagem,
        delay: 2000
    });
}

/**
 * Exibe uma mensagem de sucesso (função legada - use notification)
 * @deprecated Use notification() ao invés desta função
 */
function mensagemSucesso(mensagem, time = 1000, callback = null) {
    notification({
        type: 'success',
        message: mensagem,
        delay: time,
        callback: callback
    });
}

//--------------------------------------------------------------
// Validação de Formulários
//--------------------------------------------------------------

/**
 * Valida um formulário usando jQuery Validate
 * @param {Object} options Opções de validação
 * @param {HTMLElement|string} options.formSelector Seletor do formulário
 * @param {Object} options.rules Regras de validação
 * @param {Object} options.message Mensagens de validação
 * @return {void}
 */
function validateForm(options = {
    formSelector,
    rules,
    message,
}) {
    if (!options.formSelector) return;

    if ($(options.formSelector).data('validator')) {
        $(options.formSelector).data('validator').destroy();
    }

    const form = $(options.formSelector).validate({
        errorElement: 'span',
        errorClass: 'sf-field-error',
        validClass: 'sf-field-valid',
        focusInvalid: false,
        ignore: "",
        onfocusout: function (element) {
            $(element).valid();
        },
        onkeyup: function (element, event) {
            if (event.which !== 9) $(element).valid();
        },
        onclick: function (element) {
            $(element).valid();
        },
        rules: options.rules,
        messages: options.messages || options.message,
        highlight: function (element) {
            const $el = $(element);
            $el.addClass('sf-input-invalid').removeClass('sf-input-valid');
            $el.closest('.input-group').addClass('sf-input-group-invalid');
        },
        unhighlight: function (element) {
            const $el = $(element);
            $el.removeClass('sf-input-invalid').addClass('sf-input-valid');
            $el.closest('.input-group').removeClass('sf-input-group-invalid');
        },
        errorPlacement: function (error, element) {
            const $wrap = element.closest('.input-group');
            if ($wrap.length) {
                $wrap.after(error);
            } else {
                element.after(error);
            }
        },
        submitHandler: options.submitHandler || function (formEl) {
            formEl.submit();
        },
    });

    // Métodos de validação customizados
    $.validator.addMethod("cpf", function (value, element) {
        if (!value) return true;

        const cpf = value.replace(/\D/g, "");

        if (cpf.length !== 11) return false;
        if (/^(\d)\1{10}$/.test(cpf)) return false;

        let soma = 0;
        for (let i = 0; i < 9; i++) {
            soma += parseInt(cpf.charAt(i)) * (10 - i);
        }
        let resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        if (resto !== parseInt(cpf.charAt(9))) return false;

        soma = 0;
        for (let i = 0; i < 10; i++) {
            soma += parseInt(cpf.charAt(i)) * (11 - i);
        }
        resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        if (resto !== parseInt(cpf.charAt(10))) return false;

        return true;
    }, "CPF inválido!");

    $.validator.addMethod("cnpj", function (value, element) {
        if (!value) return true;

        const cnpj = value.replace(/\D/g, "");

        if (cnpj.length !== 14) return false;
        if (/^(\d)\1{13}$/.test(cnpj)) return false;

        let tamanho = cnpj.length - 2;
        let numeros = cnpj.substring(0, tamanho);
        let digitos = cnpj.substring(tamanho);
        let soma = 0;
        let pos = tamanho - 7;

        for (let i = tamanho; i >= 1; i--) {
            soma += parseInt(numeros.charAt(tamanho - i)) * pos--;
            if (pos < 2) pos = 9;
        }

        let resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
        if (resultado !== parseInt(digitos.charAt(0))) return false;

        tamanho = tamanho + 1;
        numeros = cnpj.substring(0, tamanho);
        soma = 0;
        pos = tamanho - 7;

        for (let i = tamanho; i >= 1; i--) {
            soma += parseInt(numeros.charAt(tamanho - i)) * pos--;
            if (pos < 2) pos = 9;
        }

        resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
        if (resultado !== parseInt(digitos.charAt(1))) return false;

        return true;
    }, "CNPJ inválido!");

    $.validator.addMethod("cpfCnpj", function (value, element) {
        if (!value) return true;

        const limpo = value.replace(/\D/g, "");

        if (limpo.length === 11) {
            return $.validator.methods.cpf.call(this, value, element);
        } else if (limpo.length === 14) {
            return $.validator.methods.cnpj.call(this, value, element);
        }

        return false;
    }, "CPF/CNPJ inválido!");

    $.validator.addMethod("dateGte", function (value, element, selector) {
        const start = $(selector).val();
        if (!value || !start) return true;
        return new Date(value) >= new Date(start);
    }, "A data final deve ser maior ou igual à inicial.");

    $.validator.addMethod("numberGte", function (value, element, selector) {
        const other = $(selector).val();
        if (value === "" || value == null) return true;
        if (other === "" || other == null) return true;
        return parseFloat(value) >= parseFloat(other);
    }, "O valor máximo deve ser maior ou igual ao mínimo.");

    $.validator.addMethod("minMoney", function (value, element, min) {
        var v = convertValue(value);
        return !isNaN(v) && v >= min;
    }, "Valor inválido");
}