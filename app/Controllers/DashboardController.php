<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Chacara;
use App\Models\Notificacao;
use App\Models\Reserva;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $usuarioId  = (int) $_SESSION['usuario_id'];
        $perfil     = $_SESSION['perfil'] ?? 'CLIENTE';
        $naoLidas   = (new Notificacao())->contarNaoLidas($usuarioId);
        $reservas   = (new Reserva())->findByCliente($usuarioId);

        if (str_contains($perfil, 'LOCADOR')) {
            $chacaras        = (new Chacara())->findByLocador($usuarioId);
            $reservasLocador = (new Reserva())->findByLocador($usuarioId);

            $this->view('dashboard.locador', [
                'pageTitle'       => 'Dashboard — Sítio Fácil',
                'chacaras'        => $chacaras,
                'reservasLocador' => $reservasLocador,
                'reservas'        => $reservas,
                'naoLidas'        => $naoLidas,
            ]);
            return;
        }

        $this->view('dashboard.cliente', [
            'pageTitle' => 'Dashboard — Sítio Fácil',
            'reservas'  => $reservas,
            'naoLidas'  => $naoLidas,
        ]);
    }
}
