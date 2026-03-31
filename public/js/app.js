/* ============================================================
   app.js — comportamentos globais com jQuery
   ============================================================ */

$(function () {

    // ── Modais Bootstrap 5 ───────────────────────────────────────────────
    // Popular dados no modal de recusa antes de abrir
    $(document).on('click', '.js-abrir-recusa', function () {
        const id      = $(this).data('id');
        const chacara = $(this).data('chacara');
        const urlBase = $('meta[name="base-url"]').attr('content') || (typeof BASE_URL !== 'undefined' ? BASE_URL : '');
        $('#nome-recusa').text(chacara || 'chácara');
        $('#form-recusa').attr('action', urlBase + '/locador/reservas/' + id + '/recusar');
    });

    // ── Valida datas de check-in/out ─────────────────────────────────────
    const $checkin  = $('#data_inicio');
    const $checkout = $('#data_fim');

    $checkin.on('change', function () {
        if ($checkout.length) {
            $checkout.attr('min', $(this).val());
            if ($checkout.val() && $checkout.val() < $(this).val()) {
                $checkout.val('');
            }
        }
    });

    // ── Formato automático de CPF/CNPJ ───────────────────────────────────
    $('#cpf_cnpj').on('input', function () {
        let v = $(this).val().replace(/\D/g, '');
        if (v.length <= 11) {
            v = v.replace(/(\d{3})(\d)/, '$1.$2')
                 .replace(/(\d{3})(\d)/, '$1.$2')
                 .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        } else {
            v = v.replace(/^(\d{2})(\d)/, '$1.$2')
                 .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
                 .replace(/\.(\d{3})(\d)/, '.$1/$2')
                 .replace(/(\d{4})(\d)/, '$1-$2');
        }
        $(this).val(v);
    });

    // ── Formato automático de CEP ─────────────────────────────────────────
    $('#cep').on('input', function () {
        let v = $(this).val().replace(/\D/g, '').slice(0, 8);
        if (v.length > 5) v = v.replace(/(\d{5})(\d)/, '$1-$2');
        $(this).val(v);
    });

    // ── Tipo de pessoa: ajusta placeholder do CPF/CNPJ ───────────────────
    $('#tipo_pessoa').on('change', function () {
        const $campo = $('#cpf_cnpj');
        if ($(this).val() === 'PJ') {
            $campo.attr('placeholder', '00.000.000/0001-00');
        } else {
            $campo.attr('placeholder', '000.000.000-00');
        }
    });

});
