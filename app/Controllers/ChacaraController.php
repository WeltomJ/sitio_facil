<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Avaliacao;
use App\Models\Chacara;

class ChacaraController extends Controller
{
    public function index(): void
    {
        $filtros = [
            'cidade'      => trim($_GET['cidade'] ?? ''),
            'capacidade'  => (int) ($_GET['capacidade'] ?? 0),
            'data_inicio' => $_GET['data_inicio'] ?? '',
            'data_fim'    => $_GET['data_fim'] ?? '',
        ];

        $model    = new Chacara();
        $chacaras = $model->buscar(array_filter($filtros));

        $this->view('chacaras.index', [
            'pageTitle' => 'Buscar Chácaras — Sítio Fácil',
            'chacaras'  => $chacaras,
            'filtros'   => $filtros,
        ]);
    }

    public function show(string $id): void
    {
        $model   = new Chacara();
        $chacara = $model->findComDetalhes((int) $id);

        if (!$chacara) {
            http_response_code(404);
            require ROOT_PATH . '/app/Views/errors/404.php';
            return;
        }

        $fotos       = $model->getFotos((int) $id);
        $comodidades = $model->getComodidades((int) $id);

        $avalModel  = new Avaliacao();
        $avaliacoes = $avalModel->findByChacaraComUsuario((int) $id);
        $notaMedia  = $avalModel->mediaPorChacara((int) $id);

        $this->view('chacaras.show', [
            'pageTitle'   => $chacara['nome'] . ' — Sítio Fácil',
            'chacara'     => $chacara,
            'fotos'       => $fotos,
            'comodidades' => $comodidades,
            'avaliacoes'  => $avaliacoes,
            'notaMedia'   => $notaMedia,
        ]);
    }

    public function minhasChacaras(): void
    {
        $this->requirePerfil('LOCADOR');
        $model    = new Chacara();
        $chacaras = $model->findByLocador((int) $_SESSION['usuario_id']);
        $this->view('chacaras.minhas', [
            'pageTitle' => 'Minhas Chácaras',
            'chacaras'  => $chacaras,
        ]);
    }

    public function create(): void
    {
        $this->requirePerfil('LOCADOR');
        $comodidades = Database::getInstance()
            ->query("SELECT * FROM comodidades ORDER BY nome")
            ->fetchAll();

        $this->view('chacaras.form', [
            'pageTitle'   => 'Cadastrar Chácara',
            'comodidades' => $comodidades,
        ]);
    }

    public function store(): void
    {
        $this->requirePerfil('LOCADOR');

        $model = new Chacara();
        $db    = Database::getInstance();

        $id = $model->insert([
            'locador_id'        => (int) $_SESSION['usuario_id'],
            'nome'              => trim($_POST['nome'] ?? ''),
            'descricao'         => trim($_POST['descricao'] ?? '') ?: null,
            'capacidade_maxima' => (int) ($_POST['capacidade_maxima'] ?? 1),
            'preco_diaria'      => (float) ($_POST['preco_diaria'] ?? 0),
            'tipo_cobranca'     => $_POST['tipo_cobranca'] ?? 'DIARIA',
            'horario_checkin'   => $_POST['horario_checkin'] ?? '14:00:00',
            'horario_checkout'  => $_POST['horario_checkout'] ?? '10:00:00',
        ]);

        $db->prepare("
            INSERT INTO chacara_enderecos
                (chacara_id, logradouro, numero, complemento, bairro, cidade, estado, cep, latitude, longitude)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ")->execute([
            $id,
            trim($_POST['logradouro'] ?? ''),
            trim($_POST['numero'] ?? '') ?: null,
            trim($_POST['complemento'] ?? '') ?: null,
            trim($_POST['bairro'] ?? '') ?: null,
            trim($_POST['cidade'] ?? ''),
            strtoupper(trim($_POST['estado'] ?? '')),
            preg_replace('/\D/', '', $_POST['cep'] ?? '') ?: null,
            $_POST['latitude'] ?? null,
            $_POST['longitude'] ?? null,
        ]);

        $model->sincronizarComodidades($id, $_POST['comodidades'] ?? []);

        $this->flashSuccess('Chácara cadastrada com sucesso!');
        $this->redirect(BASE_URL . '/locador/chacaras');
    }

    public function edit(string $id): void
    {
        $this->requirePerfil('LOCADOR');

        $model   = new Chacara();
        $chacara = $model->findComDetalhes((int) $id);

        if (!$chacara || (int) $chacara['locador_id'] !== (int) $_SESSION['usuario_id']) {
            http_response_code(403);
            require ROOT_PATH . '/app/Views/errors/403.php';
            return;
        }

        $comodidades         = Database::getInstance()->query("SELECT * FROM comodidades ORDER BY nome")->fetchAll();
        $comodosSelecionados = array_column($model->getComodidades((int) $id), 'id');

        $this->view('chacaras.form', [
            'pageTitle'           => 'Editar Chácara',
            'chacara'             => $chacara,
            'comodidades'         => $comodidades,
            'comodosSelecionados' => $comodosSelecionados,
        ]);
    }

    public function update(string $id): void
    {
        $this->requirePerfil('LOCADOR');

        $model   = new Chacara();
        $chacara = $model->find((int) $id);

        if (!$chacara || (int) $chacara['locador_id'] !== (int) $_SESSION['usuario_id']) {
            http_response_code(403);
            return;
        }

        $model->update((int) $id, [
            'nome'              => trim($_POST['nome'] ?? ''),
            'descricao'         => trim($_POST['descricao'] ?? '') ?: null,
            'capacidade_maxima' => (int) ($_POST['capacidade_maxima'] ?? 1),
            'preco_diaria'      => (float) ($_POST['preco_diaria'] ?? 0),
            'tipo_cobranca'     => $_POST['tipo_cobranca'] ?? 'DIARIA',
            'horario_checkin'   => $_POST['horario_checkin'] ?? '14:00:00',
            'horario_checkout'  => $_POST['horario_checkout'] ?? '10:00:00',
        ]);

        Database::getInstance()->prepare("
            UPDATE chacara_enderecos
            SET logradouro=?, numero=?, complemento=?, bairro=?, cidade=?, estado=?, cep=?, latitude=?, longitude=?
            WHERE chacara_id=?
        ")->execute([
            trim($_POST['logradouro'] ?? ''),
            trim($_POST['numero'] ?? '') ?: null,
            trim($_POST['complemento'] ?? '') ?: null,
            trim($_POST['bairro'] ?? '') ?: null,
            trim($_POST['cidade'] ?? ''),
            strtoupper(trim($_POST['estado'] ?? '')),
            preg_replace('/\D/', '', $_POST['cep'] ?? '') ?: null,
            $_POST['latitude'] ?? null,
            $_POST['longitude'] ?? null,
            $id,
        ]);

        $model->sincronizarComodidades((int) $id, $_POST['comodidades'] ?? []);

        $this->flashSuccess('Chácara atualizada com sucesso!');
        $this->redirect(BASE_URL . '/locador/chacaras');
    }
}
