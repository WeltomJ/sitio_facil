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
        $model = new Chacara();

        // Coleta todos os filtros da requisição
        $filtros = [
            'cidade'         => trim($_GET['cidade'] ?? ''),
            'estado'         => trim($_GET['estado'] ?? ''),
            'capacidade'     => (int) ($_GET['capacidade'] ?? 0),
            'capacidade_max' => (int) ($_GET['capacidade_max'] ?? 0),
            'preco_min'      => (float) ($_GET['preco_min'] ?? 0),
            'preco_max'      => (float) ($_GET['preco_max'] ?? 0),
            'data_inicio'    => $_GET['data_inicio'] ?? '',
            'data_fim'       => $_GET['data_fim'] ?? '',
            'tipo_cobranca'  => $_GET['tipo_cobranca'] ?? '',
            'comodidades'    => $_GET['comodidades'] ?? [],
            'ordenar'        => $_GET['ordenar'] ?? 'relevancia',
        ];

        // Validação de datas
        $erros = [];
        if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
            if ($filtros['data_fim'] < $filtros['data_inicio']) {
                $erros[] = 'A data de término deve ser posterior à data de início';
            }
        }

        // Busca com filtros aplicados
        $chacaras = empty($erros) ? $model->buscar(array_filter($filtros)) : [];

        // Carrega comodidades para os filtros
        $comodidades = $model->getTodasComodidades();

        // Verifica disponibilidade para cada chácara se datas foram informadas
        if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim']) && empty($erros)) {
            foreach ($chacaras as &$chacara) {
                $disp = $model->verificarDisponibilidade(
                    (int) $chacara['id'],
                    $filtros['data_inicio'],
                    $filtros['data_fim']
                );
                $chacara['disponibilidade'] = $disp;
            }
        }

        // Dados para UI de filtros
        $faixaPrecos = $this->getFaixaPrecos();
        $opcoesOrdenacao = [
            'relevancia'   => 'Relevância',
            'preco_asc'    => 'Menor preço',
            'preco_desc'   => 'Maior preço',
            'nota'         => 'Melhor avaliação',
            'capacidade'   => 'Maior capacidade',
            'novos'        => 'Mais recentes',
        ];

        $this->view('chacaras.index', [
            'pageTitle'       => 'Buscar Chácaras — Sítio Fácil',
            'chacaras'        => $chacaras,
            'filtros'         => $filtros,
            'erros'           => $erros,
            'comodidades'     => $comodidades,
            'faixaPrecos'     => $faixaPrecos,
            'opcoesOrdenacao' => $opcoesOrdenacao,
        ]);
    }

    /**
     * Endpoint AJAX para verificar disponibilidade em tempo real
     */
    public function verificarDisponibilidadeAjax(string $id): void
    {
        $dataInicio = $_GET['data_inicio'] ?? '';
        $dataFim = $_GET['data_fim'] ?? '';

        if (empty($dataInicio) || empty($dataFim)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Datas não informadas']);
            return;
        }

        $model = new Chacara();
        $resultado = $model->verificarDisponibilidade((int) $id, $dataInicio, $dataFim);

        header('Content-Type: application/json');
        echo json_encode($resultado);
    }

    /**
     * Retorna a faixa de preços para filtros
     */
    private function getFaixaPrecos(): array
    {
        $stmt = $this->db->query("
            SELECT MIN(preco_diaria) as minimo, MAX(preco_diaria) as maximo
            FROM chacaras WHERE ativa = 1
        ");
        $result = $stmt->fetch();
        return [
            'min' => (float) ($result['minimo'] ?? 0),
            'max' => (float) ($result['maximo'] ?? 1000),
        ];
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
