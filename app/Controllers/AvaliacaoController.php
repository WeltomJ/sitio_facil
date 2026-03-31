<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Avaliacao;
use App\Models\Reserva;

class AvaliacaoController extends Controller
{
    public function store(string $reservaId): void
    {
        $this->requirePerfil('CLIENTE');

        $reservaModel = new Reserva();
        $reserva      = $reservaModel->find((int) $reservaId);

        // Só permite avaliação de reservas concluídas do próprio cliente
        if (
            !$reserva
            || (int) $reserva['cliente_id'] !== (int) $_SESSION['usuario_id']
            || $reserva['status'] !== 'CONCLUIDA'
        ) {
            http_response_code(403);
            return;
        }

        $model = new Avaliacao();

        if ($model->jaAvaliou((int) $reservaId)) {
            $this->flashError('Você já avaliou esta reserva.');
            $this->redirect(BASE_URL . '/minhas-reservas');
        }

        $nota = (int) ($_POST['nota'] ?? 0);
        if ($nota < 1 || $nota > 5) {
            $this->flashError('Nota inválida. Escolha entre 1 e 5 estrelas.');
            $this->redirect(BASE_URL . '/minhas-reservas');
        }

        $model->insert([
            'reserva_id'  => (int) $reservaId,
            'cliente_id'  => (int) $_SESSION['usuario_id'],
            'chacara_id'  => (int) $reserva['chacara_id'],
            'nota'        => $nota,
            'comentario'  => trim($_POST['comentario'] ?? '') ?: null,
        ]);

        $this->flashSuccess('Avaliação enviada! Obrigado pelo feedback.');
        $this->redirect(BASE_URL . '/minhas-reservas');
    }
}
