<?php $pageTitle = 'Política de Cancelamento — Sítio Fácil'; ?>

<div class="row justify-content-center py-4">
<div class="col-12 col-lg-9 col-xl-8">

<div class="mb-4">
    <a href="<?= BASE_URL ?>/" class="text-muted small text-decoration-none">
        <i class="fas fa-arrow-left me-1"></i> Voltar ao início
    </a>
</div>

<div class="sf-section-block p-4 p-md-5">

    <h1 class="h3 fw-bold mb-1">Política de Cancelamento e Reembolso</h1>
    <p class="text-muted small mb-4">Última atualização: <strong>Abril de 2026</strong> &nbsp;|&nbsp; Versão: <strong>1.0</strong></p>

    <div class="alert border-0 mb-4" style="background:var(--sf-warning-bg);color:var(--sf-warning-text);border-radius:var(--sf-radius-md);">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Leia com atenção antes de confirmar sua reserva. As regras de cancelamento são vinculantes e se aplicam a todas as reservas realizadas na Plataforma.
    </div>

    <hr class="my-4">

    <!-- 1 -->
    <h2 class="h5 fw-bold mt-4 mb-3" id="c1">1. Cancelamento pelo Cliente</h2>

    <p>O prazo de antecedência é contado a partir da data do <strong>check-in</strong> confirmado. Os percentuais abaixo incidem sobre o <strong>valor total pago pelo Cliente</strong>, incluindo a taxa de serviço da Plataforma.</p>

    <div class="table-responsive my-3">
    <table class="table table-bordered text-center">
        <thead>
            <tr style="background:var(--sf-surface-2);">
                <th class="text-start">Antecedência do cancelamento</th>
                <th>Reembolso ao Cliente</th>
                <th>Retenção</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-start">Mais de 30 dias antes do check-in</td>
                <td class="text-success fw-bold">100%</td>
                <td>0%</td>
            </tr>
            <tr>
                <td class="text-start">Entre 15 e 30 dias antes do check-in</td>
                <td class="fw-bold" style="color:var(--sf-warning-text);">75%</td>
                <td>25%</td>
            </tr>
            <tr>
                <td class="text-start">Entre 7 e 14 dias antes do check-in</td>
                <td class="fw-bold" style="color:var(--sf-warning-text);">50%</td>
                <td>50%</td>
            </tr>
            <tr>
                <td class="text-start">Menos de 7 dias antes do check-in</td>
                <td class="text-danger fw-bold">0%</td>
                <td>100%</td>
            </tr>
        </tbody>
    </table>
    </div>

    <p><strong>1.1.</strong> O cancelamento pelo Cliente deve ser solicitado diretamente pela Plataforma, na página "Minhas Reservas". Cancelamentos solicitados por outros meios (e-mail, telefone, mensagem direta ao Locador) não serão considerados para fins de cômputo dos prazos.</p>
    <p><strong>1.2.</strong> O direito de arrependimento previsto no art. 49 do Código de Defesa do Consumidor (CDC) se aplica nas seguintes condições: a reserva foi realizada <strong>fora do estabelecimento comercial</strong> (o que é sempre o caso, por se tratar de contratação eletrônica) e o cancelamento é solicitado em até <strong>7 (sete) dias corridos</strong> a partir da data da reserva — desde que o check-in ainda não tenha ocorrido. Nesse caso, o reembolso é integral (100%), independentemente dos prazos acima.</p>
    <p><strong>1.3.</strong> A taxa de serviço do Sítio Fácil <strong>não é reembolsada</strong> nos casos em que a tabela acima prevê retenção, pois remunera serviços já prestados (intermediação, tecnologia, suporte). O reembolso parcial incide sobre o valor bruto pago.</p>

    <hr class="my-4">

    <!-- 2 -->
    <h2 class="h5 fw-bold mt-4 mb-3" id="c2">2. Cancelamento pelo Locador</h2>

    <p><strong>2.1.</strong> O Locador não pode cancelar reservas já confirmadas, salvo em caso de <strong>força maior devidamente comprovada</strong> (incêndio, inundação, interdição do imóvel por autoridade competente, etc.).</p>
    <p><strong>2.2.</strong> Caso o Locador cancele uma reserva confirmada sem motivo de força maior, o Cliente receberá <strong>reembolso integral (100%)</strong> do valor pago, e o Locador estará sujeito a:</p>
    <ul>
        <li>Penalidade equivalente a <strong>10% (dez por cento)</strong> do valor da reserva cancelada, descontada dos próximos repasses ou cobrada diretamente do Locador;</li>
        <li>Suspensão temporária ou definitiva do anúncio, a critério do Sítio Fácil;</li>
        <li>Avaliação negativa automática do cancelamento em seu perfil.</li>
    </ul>
    <p><strong>2.3.</strong> Para cancelamentos por força maior, o Locador deve comunicar o Sítio Fácil pelo e-mail <strong>contato@sitiofacil.com.br</strong> com documentação comprobatória, em até <strong>24 horas</strong> do evento. O reembolso ao Cliente será de 100% e não haverá penalidade ao Locador.</p>

    <hr class="my-4">

    <!-- 3 -->
    <h2 class="h5 fw-bold mt-4 mb-3" id="c3">3. Reservas com Pagamento PIX Pendente</h2>
    <p><strong>3.1.</strong> Reservas com pagamento PIX pendente que não forem confirmadas pelo pagador dentro do prazo de validade do QR Code (<strong>30 minutos</strong>) são automaticamente canceladas. Nenhum valor é retido, pois o pagamento não foi efetivado.</p>
    <p><strong>3.2.</strong> O Cliente pode gerar um novo QR Code iniciando uma nova reserva.</p>

    <hr class="my-4">

    <!-- 4 -->
    <h2 class="h5 fw-bold mt-4 mb-3" id="c4">4. Como Solicitar o Cancelamento</h2>
    <ol>
        <li>Acesse a Plataforma e faça login na sua conta;</li>
        <li>Acesse <strong>"Minhas Reservas"</strong>;</li>
        <li>Localize a reserva que deseja cancelar e clique em <strong>"Cancelar Reserva"</strong>;</li>
        <li>Confirme o cancelamento. O sistema exibirá o percentual de reembolso aplicável com base na data do check-in;</li>
        <li>O reembolso será processado automaticamente.</li>
    </ol>

    <hr class="my-4">

    <!-- 5 -->
    <h2 class="h5 fw-bold mt-4 mb-3" id="c5">5. Prazos de Reembolso</h2>
    <p>O reembolso ao Cliente, quando aplicável, é processado da seguinte forma:</p>

    <div class="table-responsive my-3">
    <table class="table table-bordered text-center">
        <thead>
            <tr style="background:var(--sf-surface-2);">
                <th class="text-start">Método de Pagamento Original</th>
                <th>Prazo do Reembolso</th>
                <th>Forma</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-start">PIX</td>
                <td>Até <strong>2 dias úteis</strong></td>
                <td>Estorno via PIX</td>
            </tr>
            <tr>
                <td class="text-start">Cartão de Crédito (à vista)</td>
                <td>Até <strong>2 faturas</strong> subsequentes</td>
                <td>Estorno na fatura do cartão</td>
            </tr>
            <tr>
                <td class="text-start">Cartão de Crédito (parcelado)</td>
                <td>Até <strong>2 faturas</strong> subsequentes</td>
                <td>Estorno proporcional das parcelas</td>
            </tr>
        </tbody>
    </table>
    </div>

    <p>Os prazos acima dependem também do processamento pela operadora do cartão e pela plataforma de pagamentos Asaas, podendo variar.</p>

    <hr class="my-4">

    <!-- 6 -->
    <h2 class="h5 fw-bold mt-4 mb-3" id="c6">6. Situações Não Cobertas pela Política</h2>
    <p>As seguintes situações <strong>não geram direito a reembolso</strong> por parte do Sítio Fácil:</p>
    <ul>
        <li>Divergências subjetivas entre as expectativas do Cliente e as características do imóvel devidamente descritas no anúncio;</li>
        <li>Condições climáticas adversas;</li>
        <li>Impossibilidade de chegada ao imóvel por responsabilidade do Cliente (problemas de transporte, documentação, etc.);</li>
        <li>Violações do Locador às regras do imóvel comunicadas previamente ao Cliente;</li>
        <li>Cancelamento após o check-in (no-show parcial).</li>
    </ul>
    <p>Em disputas entre Cliente e Locador não previstas nesta Política, o Sítio Fácil poderá mediar o conflito a seu critério, sem obrigação de resultado.</p>

    <hr class="my-4">

    <!-- 7 -->
    <h2 class="h5 fw-bold mt-4 mb-3" id="c7">7. Alterações desta Política</h2>
    <p>Esta Política pode ser alterada pelo Sítio Fácil mediante aviso com antecedência mínima de <strong>15 dias corridos</strong>. As novas regras se aplicam apenas a reservas realizadas após a entrada em vigor das alterações.</p>

    <hr class="my-4">

    <div class="mt-4 p-3 rounded-3" style="background:var(--sf-surface-2);font-size:.8rem;color:var(--sf-text-muted);">
        <strong>Dúvidas sobre cancelamento?</strong> Entre em contato pelo e-mail <strong>contato@sitiofacil.com.br</strong>.
        Para reclamações não resolvidas, você pode acessar a plataforma de resolução de conflitos
        <strong>consumidor.gov.br</strong>.
    </div>

</div><!-- sf-section-block -->

<div class="d-flex flex-wrap gap-3 justify-content-center mt-4 mb-2 small">
    <a href="<?= BASE_URL ?>/termos" class="text-muted text-decoration-none">Termos de Uso</a>
    <span class="text-muted">·</span>
    <a href="<?= BASE_URL ?>/privacidade" class="text-muted text-decoration-none">Política de Privacidade</a>
</div>

</div><!-- row -->
