<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class LegalController extends Controller
{
    public function termos(): void
    {
        $this->view('legal/termos', ['pageTitle' => 'Termos de Uso — Sítio Fácil']);
    }

    public function privacidade(): void
    {
        $this->view('legal/privacidade', ['pageTitle' => 'Política de Privacidade — Sítio Fácil']);
    }

    public function cancelamento(): void
    {
        $this->view('legal/cancelamento', ['pageTitle' => 'Política de Cancelamento — Sítio Fácil']);
    }
}
