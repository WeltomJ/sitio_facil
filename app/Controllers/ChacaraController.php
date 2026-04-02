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
            'preco_diaria'      => $this->parseMoney($_POST['preco_diaria'] ?? '0'),
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

        $this->salvarFotos($model, $id);

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
        $fotos               = $model->getFotos((int) $id);

        $this->view('chacaras.form', [
            'pageTitle'           => 'Editar Chácara',
            'chacara'             => $chacara,
            'comodidades'         => $comodidades,
            'comodosSelecionados' => $comodosSelecionados,
            'fotos'               => $fotos,
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
            'preco_diaria'      => $this->parseMoney($_POST['preco_diaria'] ?? '0'),
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

        $this->salvarFotos($model, (int) $id);

        $this->flashSuccess('Chácara atualizada com sucesso!');
        $this->redirect(BASE_URL . '/locador/chacaras');
    }

    public function excluirFoto(string $id, string $fotoId): void
    {
        $this->requirePerfil('LOCADOR');
        header('Content-Type: application/json');

        $model   = new Chacara();
        $chacara = $model->find((int) $id);

        if (!$chacara || (int) $chacara['locador_id'] !== (int) $_SESSION['usuario_id']) {
            echo json_encode(['ok' => false, 'msg' => 'Sem permissão']);
            return;
        }

        $url = $model->deleteFoto((int) $fotoId, (int) $id);
        if ($url === false) {
            echo json_encode(['ok' => false, 'msg' => 'Foto não encontrada']);
            return;
        }

        $arquivo = ROOT_PATH . '/public' . $url;
        if (file_exists($arquivo)) {
            unlink($arquivo);
        }

        echo json_encode(['ok' => true]);
    }

    public function definirFotoPrincipal(string $id, string $fotoId): void
    {
        $this->requirePerfil('LOCADOR');
        header('Content-Type: application/json');

        $model   = new Chacara();
        $chacara = $model->find((int) $id);

        if (!$chacara || (int) $chacara['locador_id'] !== (int) $_SESSION['usuario_id']) {
            echo json_encode(['ok' => false, 'msg' => 'Sem permissão']);
            return;
        }

        $ok = $model->setPrincipal((int) $id, (int) $fotoId);
        echo json_encode(['ok' => $ok]);
    }

    /** Converte valor monetário BR (1.500,00) para float */
    private function parseMoney(string $value): float
    {
        $clean = str_replace(['.', ' ', 'R$'], '', $value);
        $clean = str_replace(',', '.', $clean);
        return (float) $clean;
    }

    /** Salva novos uploads de fotos vinculados à chácara */
    private function salvarFotos(Chacara $model, int $chacaraId): void
    {
        if (empty($_FILES['fotos']['name'][0])) return;

        $principalIdx = (int) ($_POST['foto_principal_index'] ?? 0);
        $uploadDir    = ROOT_PATH . '/public/uploads/chacaras/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Ordena: principal vem primeiro (ordem = 0)
        $fotosExistentes = $model->getFotos($chacaraId);
        $proximaOrdem    = empty($fotosExistentes) ? 0 : (max(array_column($fotosExistentes, 'ordem')) + 1);

        $total = count($_FILES['fotos']['name']);
        for ($i = 0; $i < $total; $i++) {
            if ($_FILES['fotos']['error'][$i] !== UPLOAD_ERR_OK) continue;

            $tmp      = $_FILES['fotos']['tmp_name'][$i];
            $mime     = mime_content_type($tmp);
            $allowed  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];

            if (!array_key_exists($mime, $allowed)) continue;

            $ext      = $allowed[$mime];
            $nome     = uniqid('foto_', true) . '.' . $ext;
            $destino  = $uploadDir . $nome;

            if (!move_uploaded_file($tmp, $destino)) continue;

            $url = '/uploads/chacaras/' . $nome;

            if ($i === $principalIdx) {
                // Insere como principal e rebaixa as demais existentes
                $model->insertFoto($chacaraId, $url, 0);
                $this->rebaixarFotos($model, $chacaraId);
            } else {
                $model->insertFoto($chacaraId, $url, $proximaOrdem++);
            }
        }
    }

    /** Garante que apenas a foto de ordem 0 seja a principal, reordenando as demais */
    private function rebaixarFotos(Chacara $model, int $chacaraId): void
    {
        $fotos = $model->getFotos($chacaraId);
        // Conta quantas têm ordem 0 (pode ter mais de uma se recém inserida)
        $principais = array_filter($fotos, fn($f) => (int) $f['ordem'] === 0);
        if (count($principais) <= 1) return;

        // Mantém apenas a última inserida como principal, reordena as outras
        $ids = array_column($principais, 'id');
        $keepId = max($ids); // A recém-inserida tem id maior
        $ordem = 1;
        foreach ($fotos as $f) {
            if ((int) $f['id'] === $keepId) continue;
            if (in_array($f['id'], $ids)) {
                Database::getInstance()
                    ->prepare("UPDATE chacara_fotos SET ordem = ? WHERE id = ?")
                    ->execute([$ordem++, $f['id']]);
            }
        }
    }
}

