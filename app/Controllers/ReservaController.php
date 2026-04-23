<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Chacara;
use App\Models\Notificacao;
use App\Models\Pagamento;
use App\Models\Reserva;
use App\Services\AsaasService;

class ReservaController extends Controller
{
    /** Exibe a tela de checkout (resumo + pagamento) */
    public function checkoutForm(string $chacaraId): void
    {
        $this->requirePerfil('CLIENTE');

        $chacaraModel = new Chacara();
        $chacara      = $chacaraModel->findComDetalhes((int) $chacaraId);

        if (!$chacara || !$chacara['ativa']) {
            http_response_code(404);
            return;
        }

        $dataInicio  = trim($_GET['data_inicio'] ?? '');
        $dataFim     = trim($_GET['data_fim']    ?? '');
        $qtdHospedes = max(1, (int) ($_GET['qtd_hospedes'] ?? 1));

        if ($dataInicio < date('Y-m-d') || $dataFim < $dataInicio) {
            $this->flashError('Datas inválidas. Verifique o período selecionado.');
            $this->redirect(BASE_URL . '/chacaras/' . $chacaraId);
        }

        $dias       = (int) ((strtotime($dataFim) - strtotime($dataInicio)) / 86400) + 1;
        $valorTotal = $dias * (float) $chacara['preco_diaria'];

        $fotos = $chacaraModel->getFotos((int) $chacaraId);
        $foto  = $fotos[0] ?? null;

        // Busca cartões salvos do cliente
        $db    = Database::getInstance();
        $stmt  = $db->prepare('SELECT * FROM cliente_cartoes WHERE cliente_id = ? ORDER BY criado_em DESC');
        $stmt->execute([(int) $_SESSION['usuario_id']]);
        $cartoesSalvos = $stmt->fetchAll();

        $this->view('reservas.finalizar', [
            'pageTitle'     => 'Confirmar reserva — Sítio Fácil',
            'chacara'       => $chacara,
            'foto'          => $foto,
            'dataInicio'    => $dataInicio,
            'dataFim'       => $dataFim,
            'qtdHospedes'   => $qtdHospedes,
            'dias'          => $dias,
            'valorTotal'    => $valorTotal,
            'cartoesSalvos' => $cartoesSalvos,
        ]);
    }

    /** Cliente solicita reserva com integração Asaas */
    public function store(string $chacaraId): void
    {
        $this->requirePerfil('CLIENTE');

        $chacaraModel = new Chacara();
        $chacara      = $chacaraModel->find((int) $chacaraId);

        if (!$chacara || !$chacara['ativa']) {
            http_response_code(404);
            return;
        }

        $dataInicio = $_POST['data_inicio'] ?? '';
        $dataFim    = $_POST['data_fim']    ?? '';

        if ($dataInicio < date('Y-m-d') || $dataFim < $dataInicio) {
            $this->flashError('Datas inválidas. Verifique o período selecionado.');
            $this->redirect(BASE_URL . '/chacaras/' . $chacaraId);
        }

        $dias       = (int) ((strtotime($dataFim) - strtotime($dataInicio)) / 86400) + 1;
        $valorTotal = $dias * (float) $chacara['preco_diaria'];
        $parcelas   = max(1, (int) ($_POST['parcelas'] ?? 1));

        $metodoBruto    = strtoupper(trim($_POST['metodo_pagamento'] ?? 'SIMULADO'));
        $metodosValidos = ['PIX', 'CARTAO', 'SIMULADO'];
        $metodo         = in_array($metodoBruto, $metodosValidos, true) ? $metodoBruto : 'SIMULADO';

        // Busca dados do usuário logado
        $db   = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM usuarios WHERE id = ? LIMIT 1');
        $stmt->execute([(int) $_SESSION['usuario_id']]);
        $usuario = $stmt->fetch();

        // ── SIMULADO (fallback sem Asaas) ───────────────────────────────────
        if ($metodo === 'SIMULADO') {
            $reservaModel = new Reserva();
            $id = $reservaModel->insert([
                'chacara_id'   => (int) $chacaraId,
                'cliente_id'   => (int) $_SESSION['usuario_id'],
                'data_inicio'  => $dataInicio,
                'data_fim'     => $dataFim,
                'qtd_hospedes' => (int) ($_POST['qtd_hospedes'] ?? 1),
                'valor_total'  => $valorTotal,
                'status'       => 'PENDENTE',
            ]);

            $reservaModel->registrarHistorico($id, (int) $_SESSION['usuario_id'], 'CRIADA');

            (new Pagamento())->insert([
                'reserva_id' => $id,
                'valor'      => $valorTotal,
                'metodo'     => 'SIMULADO',
                'status'     => 'PAGO',
                'pago_em'    => date('Y-m-d H:i:s'),
            ]);

            $reservaModel->registrarHistorico($id, (int) $_SESSION['usuario_id'], 'PAGAMENTO_REALIZADO');

            // Confirma automaticamente após pagamento
            $reservaModel->confirmar($id, (int) $_SESSION['usuario_id']);

            (new Notificacao())->enviar(
                (int) $chacara['locador_id'],
                'Nova reserva confirmada!',
                "Uma reserva para \"{$chacara['nome']}\" de {$dataInicio} a {$dataFim} foi confirmada automaticamente após pagamento."
            );

            $this->redirect(BASE_URL . '/reservas/' . $id . '/confirmacao');
        }

        // ── ASAAS: Garante customer_id ──────────────────────────────────────
        try {
            $asaas      = new AsaasService();
            $customerId = $usuario['asaas_customer_id'] ?? null;

            if (!$customerId) {
                $customerId = $asaas->buscarOuCriarCliente($usuario);
                $db->prepare('UPDATE usuarios SET asaas_customer_id = ? WHERE id = ?')
                   ->execute([$customerId, (int) $_SESSION['usuario_id']]);
            }

            // ── PIX ──────────────────────────────────────────────────────────
            if ($metodo === 'PIX') {
                $descricao = "Reserva #{reserva} — {$chacara['nome']} ({$dataInicio} a {$dataFim})";

                // Cria reserva primeiro para ter o ID na descrição
                $reservaModel = new Reserva();
                $reservaId = $reservaModel->insert([
                    'chacara_id'   => (int) $chacaraId,
                    'cliente_id'   => (int) $_SESSION['usuario_id'],
                    'data_inicio'  => $dataInicio,
                    'data_fim'     => $dataFim,
                    'qtd_hospedes' => (int) ($_POST['qtd_hospedes'] ?? 1),
                    'valor_total'  => $valorTotal,
                    'status'       => 'PENDENTE',
                ]);

                $reservaModel->registrarHistorico($reservaId, (int) $_SESSION['usuario_id'], 'CRIADA');

                $descricao = "Reserva #{$reservaId} — {$chacara['nome']} ({$dataInicio} a {$dataFim})";
                $pixData   = $asaas->criarCobrancaPix($customerId, $valorTotal, $descricao, $reservaId);

                (new Pagamento())->insert([
                    'reserva_id'   => $reservaId,
                    'valor'        => $valorTotal,
                    'metodo'       => 'PIX',
                    'status'       => 'PENDENTE',
                    'asaas_id'     => $pixData['asaas_id'],
                    'pix_codigo'   => $pixData['pix_codigo'],
                    'pix_expira_em'=> $pixData['pix_expira_em'],
                    'parcelas'     => 1,
                ]);

                // Armazena QR base64 temporariamente na sessão para a view
                $_SESSION['_pix_qr'][$reservaId] = $pixData['pix_qr_base64'];

                $this->redirect(BASE_URL . '/reservas/' . $reservaId . '/confirmacao');
            }

            // ── CARTÃO ───────────────────────────────────────────────────────
            if ($metodo === 'CARTAO') {
                $cartaoSalvoId = (int) ($_POST['cartao_salvo_id'] ?? 0);
                $token         = '';
                $bandeira      = '';
                $finalCartao   = '';

                if ($cartaoSalvoId > 0) {
                    // Usa token salvo
                    $stmtC = $db->prepare('SELECT * FROM cliente_cartoes WHERE id = ? AND cliente_id = ? LIMIT 1');
                    $stmtC->execute([$cartaoSalvoId, (int) $_SESSION['usuario_id']]);
                    $cartaoSalvo = $stmtC->fetch();

                    if (!$cartaoSalvo) {
                        $this->flashError('Cartão selecionado não encontrado.');
                        $this->redirect(BASE_URL . '/chacaras/' . $chacaraId . '/checkout?' . http_build_query([
                            'data_inicio'  => $dataInicio,
                            'data_fim'     => $dataFim,
                            'qtd_hospedes' => $_POST['qtd_hospedes'] ?? 1,
                        ]));
                    }

                    $token       = $cartaoSalvo['token'];
                    $bandeira    = $cartaoSalvo['bandeira'];
                    $finalCartao = $cartaoSalvo['final_cartao'];
                } else {
                    // Novo cartão — tokeniza
                    $numero   = trim($_POST['numero_cartao']   ?? '');
                    $nome     = trim($_POST['nome_cartao']     ?? '');
                    $validade = trim($_POST['validade_cartao'] ?? '');
                    $cvv      = trim($_POST['cvv_cartao']      ?? '');

                    if (!$numero || !$nome || !$validade || !$cvv) {
                        $this->flashError('Preencha todos os dados do cartão.');
                        $this->redirect(BASE_URL . '/chacaras/' . $chacaraId . '/checkout?' . http_build_query([
                            'data_inicio'  => $dataInicio,
                            'data_fim'     => $dataFim,
                            'qtd_hospedes' => $_POST['qtd_hospedes'] ?? 1,
                        ]));
                    }

                    $cep = preg_replace('/\D/', '', trim($_POST['cep_titular'] ?? ''));

                    $tokenData = $asaas->tokenizarCartao($customerId, [
                        'numero'           => $numero,
                        'nome'             => $nome,
                        'validade'         => $validade,
                        'cvv'              => $cvv,
                        'email'            => $usuario['email'],
                        'cpf_cnpj'         => $usuario['cpf_cnpj'],
                        'telefone'         => $usuario['telefone'] ?? '',
                        'cep'              => $cep,
                        'numero_endereco'  => 'S/N',
                    ]);

                    $token       = $tokenData['token'];
                    $bandeira    = $tokenData['bandeira'];
                    $finalCartao = $tokenData['final_cartao'];

                    // Salva se o cliente marcou "salvar cartão"
                    if (!empty($_POST['salvar_cartao'])) {
                        $db->prepare('
                            INSERT INTO cliente_cartoes
                                (cliente_id, asaas_customer_id, token, bandeira, final_cartao, nome_titular, expiry_month, expiry_year)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                        ')->execute([
                            (int) $_SESSION['usuario_id'],
                            $customerId,
                            $token,
                            $bandeira,
                            $finalCartao,
                            strtoupper($nome),
                            $tokenData['expiry_month'],
                            $tokenData['expiry_year'],
                        ]);
                    }
                }

                // Cria reserva
                $reservaModel = new Reserva();
                $reservaId = $reservaModel->insert([
                    'chacara_id'   => (int) $chacaraId,
                    'cliente_id'   => (int) $_SESSION['usuario_id'],
                    'data_inicio'  => $dataInicio,
                    'data_fim'     => $dataFim,
                    'qtd_hospedes' => (int) ($_POST['qtd_hospedes'] ?? 1),
                    'valor_total'  => $valorTotal,
                    'status'       => 'PENDENTE',
                ]);

                $reservaModel->registrarHistorico($reservaId, (int) $_SESSION['usuario_id'], 'CRIADA');

                // Cobra no Asaas
                $descricao  = "Reserva #{$reservaId} — {$chacara['nome']} ({$dataInicio} a {$dataFim})";
                $cobrancaRet = $asaas->criarCobrancaCartao($customerId, $valorTotal, $token, $parcelas, $descricao, $reservaId);

                // Verifica se aprovado
                $statusAsaas = $cobrancaRet['status'];
                $pagamentoStatus = in_array($statusAsaas, ['CONFIRMED', 'RECEIVED'], true) ? 'PAGO' : 'PENDENTE';
                $pagoEm = $pagamentoStatus === 'PAGO' ? date('Y-m-d H:i:s') : null;

                (new Pagamento())->insert([
                    'reserva_id' => $reservaId,
                    'valor'      => $valorTotal,
                    'metodo'     => 'CARTAO',
                    'status'     => $pagamentoStatus,
                    'asaas_id'   => $cobrancaRet['asaas_id'],
                    'parcelas'   => $parcelas,
                    'pago_em'    => $pagoEm,
                ]);

                if ($pagamentoStatus === 'PAGO') {
                    $reservaModel->registrarHistorico($reservaId, (int) $_SESSION['usuario_id'], 'PAGAMENTO_REALIZADO');

                    // Confirma automaticamente após pagamento aprovado
                    $reservaModel->confirmar($reservaId, (int) $_SESSION['usuario_id']);

                    (new Notificacao())->enviar(
                        (int) $chacara['locador_id'],
                        'Nova reserva confirmada!',
                        "Uma reserva para \"{$chacara['nome']}\" de {$dataInicio} a {$dataFim} foi confirmada automaticamente após pagamento."
                    );
                }

                $this->redirect(BASE_URL . '/reservas/' . $reservaId . '/confirmacao');
            }
        } catch (\RuntimeException $e) {
            $this->flashError('Erro no pagamento: ' . $e->getMessage());
            $this->redirect(BASE_URL . '/chacaras/' . $chacaraId . '/checkout?' . http_build_query([
                'data_inicio'  => $dataInicio,
                'data_fim'     => $dataFim,
                'qtd_hospedes' => $_POST['qtd_hospedes'] ?? 1,
            ]));
        }
    }

    /** Exibe a tela de confirmação pós-pagamento */
    public function confirmacao(string $id): void
    {
        $this->requireAuth();

        $reserva = (new Reserva())->findComDetalhes((int) $id);

        if (!$reserva || (int) $reserva['cliente_id'] !== (int) $_SESSION['usuario_id']) {
            http_response_code(403);
            return;
        }

        $pagamento = (new Pagamento())->findByReserva((int) $id);
        $dias = (int) ((strtotime($reserva['data_fim']) - strtotime($reserva['data_inicio'])) / 86400) + 1;

        // QR PIX armazenado temporariamente na sessão pelo store()
        $pixQrBase64 = $_SESSION['_pix_qr'][$id] ?? null;
        unset($_SESSION['_pix_qr'][$id]);

        $this->view('reservas.confirmacao', [
            'pageTitle'   => 'Confirmação de reserva — Sítio Fácil',
            'reserva'     => $reserva,
            'pagamento'   => $pagamento ?: ['metodo' => 'SIMULADO', 'status' => 'PAGO'],
            'dias'        => $dias,
            'pixQrBase64' => $pixQrBase64,
        ]);
    }

    /** Polling: retorna status atual do pagamento (JSON) — usado pela UI do PIX */
    public function statusPagamento(string $id): void
    {
        $this->requireAuth();

        $reserva = (new Reserva())->find((int) $id);

        if (!$reserva || (int) $reserva['cliente_id'] !== (int) $_SESSION['usuario_id']) {
            $this->json(['error' => 'forbidden'], 403);
        }

        $pagamentoModel = new Pagamento();
        $pagamento      = $pagamentoModel->findByReserva((int) $id);

        if (!$pagamento) {
            $this->json(['status' => 'NOT_FOUND']);
        }

        // Se ainda PENDENTE, consulta o Asaas
        if ($pagamento['status'] === 'PENDENTE' && !empty($pagamento['asaas_id'])) {
            try {
                $asaas  = new AsaasService();
                $remote = $asaas->consultarPagamento($pagamento['asaas_id']);
                $remoteStatus = $remote['status'] ?? '';

                if (in_array($remoteStatus, ['RECEIVED', 'CONFIRMED'], true)) {
                    $pagamentoModel->marcarPago((int) $pagamento['id'], $pagamento['asaas_id']);
                    $pagamento['status'] = 'PAGO';

                    $reservaModel = new Reserva();
                    $reservaModel->registrarHistorico(
                        (int) $id,
                        (int) $_SESSION['usuario_id'],
                        'PAGAMENTO_REALIZADO',
                        'Confirmado via polling (Asaas status: ' . $remoteStatus . ')'
                    );

                    // Confirma automaticamente
                    $reservaModel->confirmar((int) $id, (int) $_SESSION['usuario_id']);

                    (new Notificacao())->enviar(
                        (int) ($reserva['locador_id'] ?? 0),
                        'Nova reserva confirmada!',
                        "Uma reserva foi confirmada automaticamente após pagamento PIX."
                    );
                }
            } catch (\RuntimeException) {
                // Silencia erros de polling — tenta na próxima chamada
            }
        }

        $this->json([
            'status'   => $pagamento['status'],
            'pago'     => $pagamento['status'] === 'PAGO',
        ]);
    }

    /** Histórico de reservas do cliente */
    public function historico(): void
    {
        $this->requireAuth();

        $perPage    = 15;
        $page       = max(1, (int) ($_GET['page'] ?? 1));
        $model      = new Reserva();
        $total      = $model->countByCliente((int) $_SESSION['usuario_id']);
        $reservas   = $model->findByCliente((int) $_SESSION['usuario_id'], $page, $perPage);
        $totalPages = (int) ceil($total / $perPage) ?: 1;

        $this->view('reservas.historico', [
            'pageTitle'   => 'Minhas Reservas',
            'reservas'    => $reservas,
            'currentPage' => min($page, $totalPages),
            'totalPages'  => $totalPages,
            'totalItens'  => $total,
        ]);
    }

    /** Lista de reservas recebidas pelo locador */
    public function indexLocador(): void
    {
        $this->requirePerfil('LOCADOR');

        $perPage    = 15;
        $page       = max(1, (int) ($_GET['page'] ?? 1));
        $model      = new Reserva();
        $total      = $model->countByLocador((int) $_SESSION['usuario_id']);
        $reservas   = $model->findByLocador((int) $_SESSION['usuario_id'], $page, $perPage);
        $totalPages = (int) ceil($total / $perPage) ?: 1;

        $this->view('reservas.index_locador', [
            'pageTitle'   => 'Reservas Recebidas',
            'reservas'    => $reservas,
            'currentPage' => min($page, $totalPages),
            'totalPages'  => $totalPages,
            'totalItens'  => $total,
        ]);
    }

    /** Locador confirma a reserva — verifica concorrência antes (Regra 17) */
    public function confirmar(string $id): void
    {
        $this->requirePerfil('LOCADOR');

        $model   = new Reserva();
        $reserva = $model->findComDetalhes((int) $id);

        if (!$reserva || (int) $reserva['locador_id'] !== (int) $_SESSION['usuario_id']) {
            http_response_code(403);
            return;
        }

        if ($model->periodoOcupado(
            (int) $reserva['chacara_id'],
            $reserva['data_inicio'],
            $reserva['data_fim'],
            (int) $id
        )) {
            $this->flashError('Período já bloqueado por outra reserva confirmada. Esta reserva não pode ser aprovada.');
            $this->redirect(BASE_URL . '/locador/reservas');
        }

        $model->confirmar((int) $id, (int) $_SESSION['usuario_id']);

        // Confirma o pagamento se ainda não estiver PAGO
        $pagamento = (new Pagamento())->findByReserva((int) $id);
        if ($pagamento && $pagamento['status'] !== 'PAGO') {
            (new Pagamento())->confirmar((int) $pagamento['id']);
        }

        // Notifica o cliente
        (new Notificacao())->enviar(
            (int) $reserva['cliente_id'],
            'Reserva confirmada! ✓',
            "Sua reserva em \"{$reserva['chacara_nome']}\" de {$reserva['data_inicio']} a {$reserva['data_fim']} foi confirmada."
        );

        $this->flashSuccess('Reserva confirmada com sucesso!');
        $this->redirect(BASE_URL . '/locador/reservas');
    }

    /** Locador recusa a reserva */
    public function recusar(string $id): void
    {
        $this->requirePerfil('LOCADOR');

        $model   = new Reserva();
        $reserva = $model->findComDetalhes((int) $id);

        if (!$reserva || (int) $reserva['locador_id'] !== (int) $_SESSION['usuario_id']) {
            http_response_code(403);
            return;
        }

        $model->recusar((int) $id, (int) $_SESSION['usuario_id'], trim($_POST['motivo'] ?? ''));

        // Notifica o cliente
        (new Notificacao())->enviar(
            (int) $reserva['cliente_id'],
            'Reserva recusada',
            "Sua reserva em \"{$reserva['chacara_nome']}\" foi recusada pelo locador."
        );

        $this->flashSuccess('Reserva recusada.');
        $this->redirect(BASE_URL . '/locador/reservas');
    }

    /** Cliente cancela sua própria reserva */
    public function cancelar(string $id): void
    {
        $this->requireAuth();

        $model   = new Reserva();
        $reserva = $model->find((int) $id);

        if (!$reserva || (int) $reserva['cliente_id'] !== (int) $_SESSION['usuario_id']) {
            http_response_code(403);
            return;
        }

        if (!in_array($reserva['status'], ['PENDENTE', 'CONFIRMADA'], true)) {
            $this->flashError('Esta reserva não pode ser cancelada.');
            $this->redirect(BASE_URL . '/minhas-reservas');
        }

        $model->cancelar((int) $id, (int) $_SESSION['usuario_id']);

        // Cancela pagamento se existir
        $pagamento = (new Pagamento())->findByReserva((int) $id);
        if ($pagamento && $pagamento['status'] !== 'PAGO') {
            (new Pagamento())->cancelar((int) $pagamento['id']);
        }

        $this->flashSuccess('Reserva cancelada.');
        $this->redirect(BASE_URL . '/minhas-reservas');
    }

    /** Retorna os intervalos de datas ocupadas de uma chácara (JSON público) */
    public function datasOcupadas(string $chacaraId): void
    {
        header('Content-Type: application/json');
        $model  = new Reserva();
        $ranges = $model->getDatasOcupadas((int) $chacaraId);
        echo json_encode(
            array_map(fn($r) => ['from' => $r['data_inicio'], 'to' => $r['data_fim']], $ranges)
        );
    }
}
