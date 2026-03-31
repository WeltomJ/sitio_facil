<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Chacara;
use App\Models\Notificacao;
use App\Models\Pagamento;
use App\Models\Reserva;

class ReservaController extends Controller
{
    /** Cliente solicita reserva (status inicial = PENDENTE) */
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
        $dataFim    = $_POST['data_fim'] ?? '';

        if ($dataInicio < date('Y-m-d') || $dataFim < $dataInicio) {
            $this->flashError('Datas inválidas. Verifique o período selecionado.');
            $this->redirect(BASE_URL . '/chacaras/' . $chacaraId);
        }

        $dias       = (int) ((strtotime($dataFim) - strtotime($dataInicio)) / 86400) + 1;
        $valorTotal = $dias * (float) $chacara['preco_diaria'];

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

        // Cria registro de pagamento simulado
        (new Pagamento())->insert([
            'reserva_id' => $id,
            'valor'      => $valorTotal,
            'metodo'     => 'SIMULADO',
            'status'     => 'PENDENTE',
        ]);

        // Notifica o locador
        (new Notificacao())->enviar(
            (int) $chacara['locador_id'],
            'Nova solicitação de reserva',
            "Você recebeu uma nova solicitação para \"{$chacara['nome']}\" de {$dataInicio} a {$dataFim}."
        );

        $this->flashSuccess('Reserva solicitada com sucesso! Aguarde a confirmação do locador.');
        $this->redirect(BASE_URL . '/minhas-reservas');
    }

    /** Histórico de reservas do cliente */
    public function historico(): void
    {
        $this->requireAuth();
        $reservas = (new Reserva())->findByCliente((int) $_SESSION['usuario_id']);
        $this->view('reservas.historico', [
            'pageTitle' => 'Minhas Reservas',
            'reservas'  => $reservas,
        ]);
    }

    /** Lista de reservas recebidas pelo locador */
    public function indexLocador(): void
    {
        $this->requirePerfil('LOCADOR');
        $reservas = (new Reserva())->findByLocador((int) $_SESSION['usuario_id']);
        $this->view('reservas.index_locador', [
            'pageTitle' => 'Reservas Recebidas',
            'reservas'  => $reservas,
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

        // Confirma o pagamento simulado
        $pagamento = (new Pagamento())->findByReserva((int) $id);
        if ($pagamento) {
            (new Pagamento())->confirmar((int) $pagamento['id']);
        }

        $reservaModel = new Reserva();
        $reservaModel->registrarHistorico((int) $id, (int) $_SESSION['usuario_id'], 'PAGAMENTO_REALIZADO');

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

        $this->flashSuccess('Reserva cancelada.');
        $this->redirect(BASE_URL . '/minhas-reservas');
    }
}
