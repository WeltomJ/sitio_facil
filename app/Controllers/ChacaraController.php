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
            'cidade'      => trim($_GET['cidade']      ?? ''),
            'estado'      => trim($_GET['estado']      ?? ''),
            'capacidade'  => (int) ($_GET['capacidade'] ?? 0),
            'data_inicio' => trim($_GET['data_inicio'] ?? ''),
            'data_fim'    => trim($_GET['data_fim']    ?? ''),
            'preco_min'   => (float) ($_GET['preco_min'] ?? 0),
            'preco_max'   => (float) ($_GET['preco_max'] ?? 0),
            'nota_min'    => (float) ($_GET['nota_min']  ?? 0),
            'comodidades' => array_map('intval', (array) ($_GET['comodidades'] ?? [])),
            'ordenar'     => $_GET['ordenar'] ?? '',
        ];

        $perPage     = 12;
        $page        = max(1, (int) ($_GET['page'] ?? 1));
        $filtrosAtiv = array_filter($filtros, fn($v) => $v !== '' && $v !== 0 && $v !== 0.0 && $v !== []);

        $model      = new Chacara();
        $total      = $model->buscarTotal($filtrosAtiv);
        $chacaras   = $model->buscar($filtrosAtiv, $page, $perPage);
        $totalPages = (int) ceil($total / $perPage) ?: 1;
        $page       = min($page, $totalPages);

        $comodidades = Database::getInstance()
            ->query("SELECT id, nome FROM comodidades ORDER BY nome")
            ->fetchAll();

        $this->view('chacaras.index', [
            'pageTitle'   => 'Buscar Chácaras — Sítio Fácil',
            'chacaras'    => $chacaras,
            'filtros'     => $filtros,
            'comodidades' => $comodidades,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'totalItens'  => $total,
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

        $perPage    = 12;
        $page       = max(1, (int) ($_GET['page'] ?? 1));
        $model      = new Chacara();
        $total      = $model->countByLocador((int) $_SESSION['usuario_id']);
        $chacaras   = $model->findByLocador((int) $_SESSION['usuario_id'], $page, $perPage);
        $totalPages = (int) ceil($total / $perPage) ?: 1;

        $this->view('chacaras.minhas', [
            'pageTitle'   => 'Minhas Chácaras',
            'chacaras'    => $chacaras,
            'currentPage' => min($page, $totalPages),
            'totalPages'  => $totalPages,
            'totalItens'  => $total,
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

        $erro = $this->validarCampos();
        if ($erro) {
            $this->flashError($erro);
            $this->redirect(BASE_URL . '/locador/chacaras/nova');
        }

        $model = new Chacara();
        $db    = Database::getInstance();

        $id = $model->insert([
            'locador_id'        => (int) $_SESSION['usuario_id'],
            'nome'              => trim($_POST['nome']),
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
            trim($_POST['logradouro']),
            trim($_POST['numero'] ?? '') ?: null,
            trim($_POST['complemento'] ?? '') ?: null,
            trim($_POST['bairro'] ?? '') ?: null,
            trim($_POST['cidade']),
            strtoupper(trim($_POST['estado'])),
            preg_replace('/\D/', '', $_POST['cep'] ?? '') ?: null,
            $_POST['latitude'] ? (float) $_POST['latitude'] : null,
            $_POST['longitude'] ? (float) $_POST['longitude'] : null,
        ]);

        $model->sincronizarComodidades($id, $_POST['comodidades'] ?? []);

        $erroFoto = $this->salvarFotos($model, $id);
        if ($erroFoto) {
            $this->flashError('Chácara cadastrada, mas ' . $erroFoto);
            $this->redirect(BASE_URL . '/locador/chacaras');
        }

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

        $erro = $this->validarCampos();
        if ($erro) {
            $this->flashError($erro);
            $this->redirect(BASE_URL . '/locador/chacaras/' . $id . '/editar');
        }

        $model->update((int) $id, [
            'nome'              => trim($_POST['nome']),
            'descricao'         => trim($_POST['descricao'] ?? '') ?: null,
            'capacidade_maxima' => (int) ($_POST['capacidade_maxima'] ?? 1),
            'preco_diaria'      => $this->parseMoney($_POST['preco_diaria'] ?? '0'),
            'tipo_cobranca'     => $_POST['tipo_cobranca'] ?? 'DIARIA',
            'horario_checkin'   => $_POST['horario_checkin'] ?? '14:00:00',
            'horario_checkout'  => $_POST['horario_checkout'] ?? '10:00:00',
        ]);

        $this->upsertEndereco((int) $id);

        $model->sincronizarComodidades((int) $id, $_POST['comodidades'] ?? []);

        $erroFoto = $this->salvarFotos($model, (int) $id);
        if ($erroFoto) {
            $this->flashError('Dados salvos, mas ' . $erroFoto);
            $this->redirect(BASE_URL . '/locador/chacaras/' . $id . '/editar');
        }

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

    // ─────────────────────────────────────────────────────────────────────────

    /** Valida campos obrigatórios comuns a store e update. Retorna mensagem de erro ou null. */
    private function validarCampos(): ?string
    {
        $nome       = trim($_POST['nome']       ?? '');
        $logradouro = trim($_POST['logradouro'] ?? '');
        $cidade     = trim($_POST['cidade']     ?? '');
        $estado     = trim($_POST['estado']     ?? '');

        if (strlen($nome) < 3) {
            return 'O nome da chácara deve ter ao menos 3 caracteres.';
        }
        if (!$logradouro) {
            return 'Informe o logradouro.';
        }
        if (!$cidade) {
            return 'Informe a cidade.';
        }
        if (strlen($estado) !== 2) {
            return 'Informe o estado com a sigla de 2 letras (ex: SP).';
        }

        $capacidade = (int) ($_POST['capacidade_maxima'] ?? 0);
        if ($capacidade < 1) {
            return 'A capacidade máxima deve ser ao menos 1 pessoa.';
        }

        $preco = $this->parseMoney($_POST['preco_diaria'] ?? '0');
        if ($preco <= 0) {
            return 'Informe um preço por diária maior que zero.';
        }

        return null;
    }

    /**
     * INSERT ou UPDATE em chacara_enderecos garantindo que sempre exista uma linha.
     * Usa UPDATE + fallback para INSERT caso a linha não exista ainda.
     */
    private function upsertEndereco(int $chacaraId): void
    {
        $db     = Database::getInstance();
        $params = [
            trim($_POST['logradouro']),
            trim($_POST['numero'] ?? '') ?: null,
            trim($_POST['complemento'] ?? '') ?: null,
            trim($_POST['bairro'] ?? '') ?: null,
            trim($_POST['cidade']),
            strtoupper(trim($_POST['estado'])),
            preg_replace('/\D/', '', $_POST['cep'] ?? '') ?: null,
            $_POST['latitude']  ? (float) $_POST['latitude']  : null,
            $_POST['longitude'] ? (float) $_POST['longitude'] : null,
        ];

        $stmt = $db->prepare("SELECT id FROM chacara_enderecos WHERE chacara_id = ? LIMIT 1");
        $stmt->execute([$chacaraId]);

        if ($stmt->fetch()) {
            $db->prepare("
                UPDATE chacara_enderecos
                SET logradouro=?, numero=?, complemento=?, bairro=?, cidade=?, estado=?, cep=?, latitude=?, longitude=?
                WHERE chacara_id=?
            ")->execute([...$params, $chacaraId]);
        } else {
            $db->prepare("
                INSERT INTO chacara_enderecos
                    (chacara_id, logradouro, numero, complemento, bairro, cidade, estado, cep, latitude, longitude)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ")->execute([$chacaraId, ...$params]);
        }
    }

    /** Converte valor monetário BR (1.500,00) para float */
    private function parseMoney(string $value): float
    {
        $clean = str_replace(['.', ' ', 'R$'], '', $value);
        $clean = str_replace(',', '.', $clean);
        return (float) $clean;
    }

    /**
     * Salva novos uploads de fotos vinculados à chácara.
     * Retorna null em sucesso ou uma string de erro amigável.
     */
    private function salvarFotos(Chacara $model, int $chacaraId): ?string
    {
        // Detecta estouro do post_max_size do PHP (FILES fica vazio mas CONTENT_LENGTH > 0)
        $contentLen = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
        if ($contentLen > 0 && empty($_FILES)) {
            return 'os arquivos enviados excedem o limite do servidor. Reduza o tamanho ou envie menos fotos de uma vez.';
        }

        if (empty($_FILES['fotos']['name'][0])) {
            return null; // nenhum arquivo enviado — OK
        }

        $principalIdx = (int) ($_POST['foto_principal_index'] ?? 0);
        $uploadDir    = ROOT_PATH . '/public/uploads/chacaras/';

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            return 'não foi possível criar o diretório de uploads.';
        }

        $fotosExistentes = $model->getFotos($chacaraId);
        $proximaOrdem    = empty($fotosExistentes)
            ? 1
            : (max(array_column($fotosExistentes, 'ordem')) + 1);

        // Se não há fotos ainda, a primeira vira principal (ordem 0)
        $temPrincipal = !empty($fotosExistentes);

        $allowed  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        $erros    = [];
        $total    = count($_FILES['fotos']['name']);

        for ($i = 0; $i < $total; $i++) {
            $errCode = $_FILES['fotos']['error'][$i];

            if ($errCode === UPLOAD_ERR_NO_FILE) continue;

            if ($errCode === UPLOAD_ERR_INI_SIZE || $errCode === UPLOAD_ERR_FORM_SIZE) {
                $erros[] = '"' . htmlspecialchars($_FILES['fotos']['name'][$i]) . '" excede o tamanho máximo permitido.';
                continue;
            }

            if ($errCode !== UPLOAD_ERR_OK) {
                $erros[] = '"' . htmlspecialchars($_FILES['fotos']['name'][$i]) . '" falhou no upload (código ' . $errCode . ').';
                continue;
            }

            $tmp  = $_FILES['fotos']['tmp_name'][$i];
            $nome = $_FILES['fotos']['name'][$i];

            // Verifica MIME real via conteúdo do arquivo (não confiar na extensão)
            $mime = mime_content_type($tmp);
            if (!array_key_exists($mime, $allowed)) {
                $erros[] = '"' . htmlspecialchars($nome) . '" não é uma imagem válida.';
                continue;
            }

            // Verifica que o arquivo é realmente uma imagem (previne uploads disfarçados)
            if (!@getimagesize($tmp)) {
                $erros[] = '"' . htmlspecialchars($nome) . '" não pôde ser lido como imagem.';
                continue;
            }

            $ext     = $allowed[$mime];
            $arquivo = uniqid('foto_', true) . '.' . $ext;
            $destino = $uploadDir . $arquivo;

            if (!move_uploaded_file($tmp, $destino)) {
                $erros[] = '"' . htmlspecialchars($nome) . '" não pôde ser salva. Verifique as permissões do servidor.';
                continue;
            }

            $url          = '/uploads/chacaras/' . $arquivo;
            $devePrincipal = (!$temPrincipal && $i === 0) || ($temPrincipal && $i === $principalIdx);

            if ($devePrincipal) {
                $fotoId = $model->insertFoto($chacaraId, $url, 0);
                // setPrincipal reordena TODAS as fotos: destino=0, demais=1,2,3...
                $model->setPrincipal($chacaraId, $fotoId);
                $temPrincipal = true;
                // Recalcula próxima ordem pois setPrincipal reordenou tudo
                $fotosAtuais  = $model->getFotos($chacaraId);
                $proximaOrdem = empty($fotosAtuais) ? 1 : (max(array_column($fotosAtuais, 'ordem')) + 1);
            } else {
                $model->insertFoto($chacaraId, $url, $proximaOrdem++);
            }
        }

        if (!empty($erros)) {
            return 'algumas fotos não foram salvas: ' . implode(' ', $erros);
        }

        return null;
    }
}
