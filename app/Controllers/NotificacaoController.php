<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Notificacao;

class NotificacaoController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $model        = new Notificacao();
        $notificacoes = $model->findByUsuario((int) $_SESSION['usuario_id']);

        $model->marcarTodasLidas((int) $_SESSION['usuario_id']);

        $this->view('notificacoes.index', [
            'pageTitle'    => 'Notificações',
            'notificacoes' => $notificacoes,
        ]);
    }

    public function marcarLida(string $id): void
    {
        $this->requireAuth();
        $model = new Notificacao();
        $model->marcarLida((int) $id, (int) $_SESSION['usuario_id']);
        $this->json(['ok' => true]);
    }
}
