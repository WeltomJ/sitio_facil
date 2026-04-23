<?php $pageTitle = 'Política de Privacidade — Sítio Fácil'; ?>

<div class="row justify-content-center py-4">
<div class="col-12 col-lg-9 col-xl-8">

<div class="mb-4">
    <a href="<?= BASE_URL ?>/" class="text-muted small text-decoration-none">
        <i class="fas fa-arrow-left me-1"></i> Voltar ao início
    </a>
</div>

<div class="sf-section-block p-4 p-md-5">

    <h1 class="h3 fw-bold mb-1">Política de Privacidade</h1>
    <p class="text-muted small mb-1">Última atualização: <strong>Abril de 2026</strong> &nbsp;|&nbsp; Versão: <strong>1.0</strong></p>
    <p class="text-muted small mb-4">Em conformidade com a Lei Geral de Proteção de Dados Pessoais (LGPD — Lei 13.709/2018)</p>

    <div class="alert border-0 mb-4" style="background:var(--sf-info-bg,#eff6ff);color:var(--sf-info-text,#1d4ed8);border-radius:var(--sf-radius-md);">
        <i class="fas fa-shield-alt me-2"></i>
        Sua privacidade é fundamental para nós. Esta Política explica de forma clara quais dados coletamos, como os usamos, com quem os compartilhamos e quais são seus direitos como titular.
    </div>

    <!-- Sumário -->
    <nav class="mb-5">
        <p class="fw-semibold small text-uppercase mb-2" style="letter-spacing:.05em;">Sumário</p>
        <ol class="small" style="columns:2; column-gap:2rem; padding-left:1.2rem;">
            <li><a href="#p1" class="text-muted text-decoration-none">Controlador de Dados</a></li>
            <li><a href="#p2" class="text-muted text-decoration-none">Encarregado (DPO)</a></li>
            <li><a href="#p3" class="text-muted text-decoration-none">Dados Coletados</a></li>
            <li><a href="#p4" class="text-muted text-decoration-none">Finalidades e Base Legal</a></li>
            <li><a href="#p5" class="text-muted text-decoration-none">Compartilhamento</a></li>
            <li><a href="#p6" class="text-muted text-decoration-none">Retenção de Dados</a></li>
            <li><a href="#p7" class="text-muted text-decoration-none">Direitos do Titular</a></li>
            <li><a href="#p8" class="text-muted text-decoration-none">Segurança</a></li>
            <li><a href="#p9" class="text-muted text-decoration-none">Transferência Internacional</a></li>
            <li><a href="#p10" class="text-muted text-decoration-none">Cookies</a></li>
            <li><a href="#p11" class="text-muted text-decoration-none">Menores de Idade</a></li>
            <li><a href="#p12" class="text-muted text-decoration-none">Alterações desta Política</a></li>
        </ol>
    </nav>

    <hr class="my-4">

    <!-- 1 -->
    <h2 class="h5 fw-bold mt-4 mb-3" id="p1">1. Controlador de Dados</h2>
    <p>O controlador dos dados pessoais tratados nesta Política é:</p>
    <div class="p-3 rounded-3 mb-3" style="background:var(--sf-surface-2);font-size:.875rem;">
        <strong>Sítio Fácil Tecnologia Ltda.</strong><br>
        CNPJ: <strong>11.222.333/0001-81</strong><br>
        Endereço: <strong>Av. Djalma Batista, 1.661, Sala 801, Chapada, Manaus – AM, CEP 69.050-010</strong><br>
        E-mail de contato: <strong>contato@sitiofacil.com.br</strong>
    </div>

    <hr class="my-4">

    <!-- 2 -->
    <h2 class="h5 fw-bold mt-4 mb-3" id="p2">2. Encarregado pelo Tratamento de Dados (DPO)</h2>
    <p>O Encarregado pelo Tratamento de Dados Pessoais (Data Protection Officer — DPO), conforme exigido pela LGPD (art. 41), pode ser contactado pelo e-mail: <strong>privacidade@sitiofacil.com.br</strong>.</p>
    <p>O DPO é responsável por receber comunicações dos titulares de dados e da Autoridade Nacional de Proteção de Dados (ANPD).</p>

    <hr class="my-4">

    <!-- 3 -->
    <h2 class="h5 fw-bold mt-4 mb-3" id="p3">3. Dados Pessoais Coletados</h2>
    <p>Coletamos dados pessoais nas seguintes situações:</p>

    <p class="fw-semibold mb-1">3.1. Dados fornecidos pelo Usuário no cadastro:</p>
    <ul>
        <li>Nome completo;</li>
        <li>Endereço de e-mail;</li>
        <li>CPF ou CNPJ;</li>
        <li>Número de telefone (opcional);</li>
        <li>Foto de perfil (opcional);</li>
        <li>Senha (armazenada de forma criptografada — hash bcrypt).</li>
    </ul>

    <p class="fw-semibold mb-1">3.2. Dados fornecidos pelo Locador:</p>
    <ul>
        <li>Informações do imóvel (endereço, descrição, fotos, comodidades, preço);</li>
        <li>Dados bancários para repasse (banco, agência, conta, nome do titular, CPF/CNPJ).</li>
    </ul>

    <p class="fw-semibold mb-1">3.3. Dados gerados pelo uso da Plataforma:</p>
    <ul>
        <li>Histórico de reservas e pagamentos;</li>
        <li>Avaliações e comentários publicados;</li>
        <li>Notificações geradas;</li>
        <li>Endereço IP e dados de acesso (logs do servidor);</li>
        <li>Informações sobre o dispositivo e navegador utilizado.</li>
    </ul>

    <p class="fw-semibold mb-1">3.4. Dados de pagamento:</p>
    <p>Os dados de cartão de crédito são processados e tokenizados exclusivamente pela plataforma <strong>Asaas</strong>. O Sítio Fácil armazena apenas o token, bandeira, últimos 4 dígitos e validade. <strong>Nenhum dado completo de cartão é armazenado pelo Sítio Fácil.</strong></p>

    <hr class="my-4">

    <!-- 4 -->
    <h2 class="h5 fw-bold mt-4 mb-3" id="p4">4. Finalidades e Base Legal do Tratamento</h2>
    <p>Tratamos seus dados pessoais com as seguintes finalidades e bases legais (LGPD, art. 7º):</p>

    <div class="table-responsive">
    <table class="table table-bordered table-sm small">
        <thead class="table-light">
            <tr>
                <th>Finalidade</th>
                <th>Base Legal (LGPD)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Criação e gestão de conta de usuário</td>
                <td>Execução de contrato (art. 7º, V)</td>
            </tr>
            <tr>
                <td>Intermediação de reservas entre Cliente e Locador</td>
                <td>Execução de contrato (art. 7º, V)</td>
            </tr>
            <tr>
                <td>Processamento de pagamentos e reembolsos</td>
                <td>Execução de contrato (art. 7º, V)</td>
            </tr>
            <tr>
                <td>Envio de notificações relacionadas à reserva</td>
                <td>Execução de contrato (art. 7º, V)</td>
            </tr>
            <tr>
                <td>Repasse de valores ao Locador</td>
                <td>Execução de contrato (art. 7º, V)</td>
            </tr>
            <tr>
                <td>Cumprimento de obrigações legais e fiscais</td>
                <td>Cumprimento de obrigação legal (art. 7º, II)</td>
            </tr>
            <tr>
                <td>Prevenção a fraudes e segurança da Plataforma</td>
                <td>Legítimo interesse (art. 7º, IX)</td>
            </tr>
            <tr>
                <td>Melhoria dos serviços e análise de uso da Plataforma</td>
                <td>Legítimo interesse (art. 7º, IX)</td>
            </tr>
            <tr>
                <td>Envio de comunicações de marketing (com opção de descadastro)</td>
                <td>Consentimento (art. 7º, I)</td>
            </tr>
            <tr>
                <td>Atendimento a solicitações, reclamações e suporte</td>
                <td>Legítimo interesse (art. 7º, IX)</td>
            </tr>
        </tbody>
    </table>
    </div>

    <hr class="my-4">

    <!-- 5 -->
    <h2 class="h5 fw-bold mt-4 mb-3" id="p5">5. Compartilhamento de Dados Pessoais</h2>
    <p>Seus dados podem ser compartilhados com:</p>
    <ul>
        <li><strong>Asaas Pagamentos S.A.:</strong> para processamento de pagamentos, tokenização de cartões e repasses. Os dados compartilhados são os necessários para a operação de cobrança (nome, CPF/CNPJ, e-mail). A Asaas possui sua própria Política de Privacidade.</li>
        <li><strong>Autoridades governamentais:</strong> quando exigido por lei, decisão judicial ou regulatória, incluindo a Receita Federal e a ANPD.</li>
        <li><strong>Locadores:</strong> os dados do Cliente necessários para a execução da reserva (nome, telefone) são compartilhados com o Locador da propriedade reservada.</li>
        <li><strong>Clientes:</strong> os dados públicos do Locador (nome, telefone, foto) são exibidos na página da propriedade.</li>
        <li><strong>Provedores de infraestrutura:</strong> serviços de hospedagem, banco de dados e armazenamento de arquivos, que atuam como operadores de dados sob nossas instruções.</li>
    </ul>
    <p><strong>Não vendemos dados pessoais a terceiros.</strong></p>

    <hr class="my-4">

    <!-- 6 -->
    <h2 class="h5 fw-bold mt-4 mb-3" id="p6">6. Retenção de Dados</h2>
    <p>Mantemos seus dados pessoais pelo tempo necessário para as finalidades descritas nesta Política, observando os seguintes critérios:</p>
    <ul>
        <li><strong>Durante a vigência do cadastro:</strong> todos os dados da conta;</li>
        <li><strong>Após encerramento da conta:</strong> dados de transações financeiras por <strong>5 (cinco) anos</strong>, conforme exigência fiscal e tributária (Lei 9.394/1996 e Código Tributário Nacional);</li>
        <li><strong>Logs de acesso:</strong> por <strong>6 (seis) meses</strong>, conforme o Marco Civil da Internet (Lei 12.965/2014, art. 15);</li>
        <li><strong>Dados de pagamento:</strong> conforme prazo exigido pela Asaas e pela legislação do Banco Central do Brasil.</li>
    </ul>
    <p>Findo o prazo de retenção, os dados são excluídos ou anonimizados de forma segura.</p>

    <hr class="my-4">

    <!-- 7 -->
    <h2 class="h5 fw-bold mt-4 mb-3" id="p7">7. Direitos do Titular de Dados</h2>
    <p>Nos termos da LGPD (art. 18), você tem os seguintes direitos:</p>
    <ul>
        <li><strong>Confirmação:</strong> saber se tratamos seus dados pessoais;</li>
        <li><strong>Acesso:</strong> obter cópia dos dados que mantemos sobre você;</li>
        <li><strong>Correção:</strong> solicitar a correção de dados incompletos, inexatos ou desatualizados;</li>
        <li><strong>Anonimização, bloqueio ou eliminação:</strong> de dados desnecessários, excessivos ou tratados em desconformidade com a LGPD;</li>
        <li><strong>Portabilidade:</strong> solicitar a transferência de seus dados a outro fornecedor;</li>
        <li><strong>Eliminação:</strong> solicitar a exclusão de dados tratados com base no seu consentimento;</li>
        <li><strong>Informação:</strong> ser informado sobre as entidades com as quais compartilhamos seus dados;</li>
        <li><strong>Revogação do consentimento:</strong> revogar consentimentos fornecidos, sem prejuízo da legalidade do tratamento anterior;</li>
        <li><strong>Oposição:</strong> opor-se a tratamentos realizados com fundamento em outras bases legais, em caso de descumprimento.</li>
    </ul>
    <p>Para exercer seus direitos, envie solicitação para <strong>privacidade@sitiofacil.com.br</strong> com identificação do titular. Responderemos no prazo de <strong>15 (quinze) dias</strong> corridos, conforme a ANPD. Você também pode apresentar reclamação à <strong>Autoridade Nacional de Proteção de Dados (ANPD)</strong> pelo site <strong>www.gov.br/anpd</strong>.</p>

    <hr class="my-4">

    <!-- 8 -->
    <h2 class="h5 fw-bold mt-4 mb-3" id="p8">8. Segurança dos Dados</h2>
    <p><strong>8.1.</strong> Adotamos medidas técnicas e organizacionais adequadas para proteger dados pessoais contra acesso não autorizado, perda, alteração ou divulgação indevida, incluindo:</p>
    <ul>
        <li>Criptografia de senhas com algoritmo bcrypt;</li>
        <li>Transmissão de dados via protocolo HTTPS/TLS;</li>
        <li>Acesso ao banco de dados restrito a usuários dedicados com permissões mínimas;</li>
        <li>Tokenização de dados de cartão de crédito via Asaas;</li>
        <li>Regeneração de identificadores de sessão após autenticação.</li>
    </ul>
    <p><strong>8.2.</strong> Em caso de incidente de segurança que possa acarretar risco ou dano relevante aos titulares, comunicaremos o fato à ANPD e aos titulares afetados no prazo previsto na LGPD (art. 48).</p>

    <hr class="my-4">

    <!-- 9 -->
    <h2 class="h5 fw-bold mt-4 mb-3" id="p9">9. Transferência Internacional de Dados</h2>
    <p>Os dados pessoais podem ser transferidos ou processados fora do Brasil pelos provedores de infraestrutura e pela Asaas, em conformidade com o art. 33 da LGPD. Garantimos que tais transferências observam as salvaguardas previstas na lei, incluindo cláusulas contratuais específicas.</p>

    <hr class="my-4">

    <!-- 10 -->
    <h2 class="h5 fw-bold mt-4 mb-3" id="p10">10. Cookies e Tecnologias Similares</h2>
    <p><strong>10.1.</strong> Utilizamos <em>cookies de sessão</em> exclusivamente para manter o estado de autenticação do Usuário durante a navegação. Esses cookies são temporários e excluídos ao encerrar o navegador.</p>
    <p><strong>10.2.</strong> Também utilizamos o <em>localStorage</em> do navegador para armazenar preferências de tema (claro/escuro) do Usuário, sem coleta de dados pessoais.</p>
    <p><strong>10.3.</strong> Não utilizamos cookies de rastreamento, publicidade comportamental ou cookies de terceiros para fins de marketing.</p>
    <p><strong>10.4.</strong> Você pode configurar seu navegador para recusar cookies. No entanto, a recusa de cookies de sessão pode impedir o acesso a áreas autenticadas da Plataforma.</p>

    <hr class="my-4">

    <!-- 11 -->
    <h2 class="h5 fw-bold mt-4 mb-3" id="p11">11. Menores de Idade</h2>
    <p>A Plataforma não é destinada a menores de 18 anos. Não coletamos intencionalmente dados pessoais de menores. Caso identificarmos que coletamos dados de um menor sem o consentimento dos responsáveis legais, excluiremos tais dados imediatamente.</p>

    <hr class="my-4">

    <!-- 12 -->
    <h2 class="h5 fw-bold mt-4 mb-3" id="p12">12. Alterações desta Política</h2>
    <p>Esta Política pode ser atualizada periodicamente. A versão vigente estará sempre disponível nesta página com a data de última atualização. Alterações relevantes serão comunicadas por e-mail com antecedência mínima de <strong>15 dias corridos</strong>.</p>

    <hr class="my-4">

    <div class="mt-4 p-3 rounded-3" style="background:var(--sf-surface-2);font-size:.8rem;color:var(--sf-text-muted);">
        <strong>Contato do Encarregado (DPO):</strong> <strong>privacidade@sitiofacil.com.br</strong><br>
        <strong>Contato geral:</strong> <strong>contato@sitiofacil.com.br</strong><br>
        <strong>ANPD:</strong> <a href="https://www.gov.br/anpd" target="_blank" rel="noopener" class="text-muted">www.gov.br/anpd</a>
    </div>

</div><!-- sf-section-block -->

<div class="d-flex flex-wrap gap-3 justify-content-center mt-4 mb-2 small">
    <a href="<?= BASE_URL ?>/termos" class="text-muted text-decoration-none">Termos de Uso</a>
    <span class="text-muted">·</span>
    <a href="<?= BASE_URL ?>/cancelamento" class="text-muted text-decoration-none">Política de Cancelamento</a>
</div>

</div><!-- row -->
