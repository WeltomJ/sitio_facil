<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Chacara extends Model
{
    protected string $table = 'chacaras';

    /**
     * Busca chácaras disponíveis aplicando filtros completos.
     * Filtros: cidade, capacidade, datas, preço, comodidades, ordenação.
     * Só exclui períodos bloqueados por reservas CONFIRMADAS.
     */
    public function buscar(array $filtros): array
    {
        $sql = "
            SELECT c.*,
                   e.cidade, e.estado, e.latitude, e.longitude,
                   (SELECT url FROM chacara_fotos WHERE chacara_id = c.id ORDER BY ordem ASC LIMIT 1) AS capa,
                   (SELECT ROUND(AVG(nota), 1) FROM avaliacoes WHERE chacara_id = c.id) AS nota_media,
                   COUNT(DISTINCT cc.comodidade_id) as comodidades_match
            FROM chacaras c
            INNER JOIN chacara_enderecos e ON e.chacara_id = c.id
            LEFT JOIN chacara_comodidades cc ON cc.chacara_id = c.id
            WHERE c.ativa = 1
        ";
        $params = [];

        // Filtro por cidade/região
        if (!empty($filtros['cidade'])) {
            $sql     .= " AND (e.cidade LIKE ? OR e.bairro LIKE ?)";
            $like = '%' . $filtros['cidade'] . '%';
            $params[] = $like;
            $params[] = $like;
        }

        // Filtro por estado
        if (!empty($filtros['estado'])) {
            $sql     .= " AND e.estado = ?";
            $params[] = strtoupper($filtros['estado']);
        }

        // Filtro por capacidade mínima
        if (!empty($filtros['capacidade'])) {
            $sql     .= " AND c.capacidade_maxima >= ?";
            $params[] = (int) $filtros['capacidade'];
        }

        // Filtro por capacidade máxima
        if (!empty($filtros['capacidade_max'])) {
            $sql     .= " AND c.capacidade_maxima <= ?";
            $params[] = (int) $filtros['capacidade_max'];
        }

        // Filtro por preço mínimo
        if (!empty($filtros['preco_min'])) {
            $sql     .= " AND c.preco_diaria >= ?";
            $params[] = (float) $filtros['preco_min'];
        }

        // Filtro por preço máximo
        if (!empty($filtros['preco_max'])) {
            $sql     .= " AND c.preco_diaria <= ?";
            $params[] = (float) $filtros['preco_max'];
        }

        // Filtro por tipo de cobrança
        if (!empty($filtros['tipo_cobranca'])) {
            $sql     .= " AND c.tipo_cobranca = ?";
            $params[] = $filtros['tipo_cobranca'];
        }

        // Filtro por comodidades (todas devem estar presentes)
        if (!empty($filtros['comodidades']) && is_array($filtros['comodidades'])) {
            $comodidadeIds = array_map('intval', $filtros['comodidades']);
            $placeholders = implode(',', array_fill(0, count($comodidadeIds), '?'));
            $sql .= " AND cc.comodidade_id IN ($placeholders)";
            $params = array_merge($params, $comodidadeIds);
        }

        $sql .= " GROUP BY c.id";

        // Filtro por comodidades - garante que tenha TODAS as comodidades selecionadas
        if (!empty($filtros['comodidades']) && is_array($filtros['comodidades'])) {
            $sql .= " HAVING comodidades_match >= ?";
            $params[] = count($filtros['comodidades']);
        }

        // Exclui reservas CONFIRMADAS que conflitam com o período
        if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
            $sql = "
                SELECT * FROM ($sql) AS chacaras_filtradas
                WHERE id NOT IN (
                    SELECT chacara_id FROM reservas
                    WHERE status = 'CONFIRMADA'
                      AND data_inicio <= ? AND data_fim >= ?
                )
            ";
            $params[] = $filtros['data_fim'];
            $params[] = $filtros['data_inicio'];
        }

        // Ordenação
        $ordenacao = $filtros['ordenar'] ?? 'relevancia';
        $sql .= match($ordenacao) {
            'preco_asc'     => " ORDER BY preco_diaria ASC",
            'preco_desc'    => " ORDER BY preco_diaria DESC",
            'nota'          => " ORDER BY nota_media DESC NULLS LAST",
            'capacidade'    => " ORDER BY capacidade_maxima DESC",
            'novos'         => " ORDER BY c.criado_em DESC",
            default         => " ORDER BY nota_media DESC NULLS LAST, c.criado_em DESC"
        };

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Verifica se a chácara está disponível para o período especificado.
     * Considera apenas reservas CONFIRMADAS.
     */
    public function verificarDisponibilidade(int $chacaraId, string $dataInicio, string $dataFim): array
    {
        // Validar datas
        $erros = [];
        $hoje = date('Y-m-d');

        if ($dataInicio < $hoje) {
            $erros[] = 'Data de início não pode ser no passado';
        }

        if ($dataFim < $dataInicio) {
            $erros[] = 'Data de término deve ser posterior à data de início';
        }

        if (!empty($erros)) {
            return [
                'disponivel' => false,
                'erros' => $erros,
                'conflitos' => []
            ];
        }

        $stmt = $this->db->prepare("
            SELECT id, data_inicio, data_fim, cliente_id
            FROM reservas
            WHERE chacara_id = ?
              AND status = 'CONFIRMADA'
              AND data_inicio <= ? AND data_fim >= ?
            ORDER BY data_inicio
        ");
        $stmt->execute([$chacaraId, $dataFim, $dataInicio]);
        $conflitos = $stmt->fetchAll();

        return [
            'disponivel' => empty($conflitos),
            'erros' => [],
            'conflitos' => $conflitos,
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim,
            'noites' => (new \DateTime($dataFim))->diff(new \DateTime($dataInicio))->days
        ];
    }

    /**
     * Busca todas as comodidades disponíveis para filtros
     */
    public function getTodasComodidades(): array
    {
        $stmt = $this->db->query("
            SELECT * FROM comodidades
            ORDER BY nome ASC
        ");
        return $stmt->fetchAll();
    }

    /** Retorna chácara com endereço e dados do locador joinados */
    public function findComDetalhes(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT c.*,
                   e.logradouro, e.numero, e.complemento, e.bairro,
                   e.cidade, e.estado, e.cep, e.latitude, e.longitude,
                   u.nome AS locador_nome, u.telefone AS locador_telefone
            FROM chacaras c
            LEFT JOIN chacara_enderecos e ON e.chacara_id = c.id
            LEFT JOIN usuarios u ON u.id = c.locador_id
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByLocador(int $locadorId): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, e.cidade, e.estado
            FROM chacaras c
            LEFT JOIN chacara_enderecos e ON e.chacara_id = c.id
            WHERE c.locador_id = ?
            ORDER BY c.criado_em DESC
        ");
        $stmt->execute([$locadorId]);
        return $stmt->fetchAll();
    }

    public function getComodidades(int $chacaraId): array
    {
        $stmt = $this->db->prepare("
            SELECT co.id, co.nome
            FROM comodidades co
            INNER JOIN chacara_comodidades cc ON cc.comodidade_id = co.id
            WHERE cc.chacara_id = ?
            ORDER BY co.nome
        ");
        $stmt->execute([$chacaraId]);
        return $stmt->fetchAll();
    }

    public function getFotos(int $chacaraId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM chacara_fotos WHERE chacara_id = ? ORDER BY ordem ASC"
        );
        $stmt->execute([$chacaraId]);
        return $stmt->fetchAll();
    }

    /** Substitui todas as comodidades da chácara pelos IDs fornecidos */
    public function sincronizarComodidades(int $chacaraId, array $comodidadeIds): void
    {
        $this->db->prepare("DELETE FROM chacara_comodidades WHERE chacara_id = ?")->execute([$chacaraId]);

        if (empty($comodidadeIds)) return;

        $placeholders = implode(', ', array_fill(0, count($comodidadeIds), '(?, ?)'));
        $params       = [];
        foreach ($comodidadeIds as $comId) {
            $params[] = $chacaraId;
            $params[] = (int) $comId;
        }

        $this->db->prepare(
            "INSERT INTO chacara_comodidades (chacara_id, comodidade_id) VALUES {$placeholders}"
        )->execute($params);
    }
}
