<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Reserva extends Model
{
    protected string $table = 'reservas';

    public function findComDetalhes(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT r.*,
                   c.nome AS chacara_nome, c.horario_checkin, c.horario_checkout, c.locador_id,
                   u.nome AS cliente_nome, u.telefone AS cliente_telefone
            FROM reservas r
            INNER JOIN chacaras c ON c.id = r.chacara_id
            INNER JOIN usuarios u ON u.id = r.cliente_id
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByCliente(int $clienteId): array
    {
        $stmt = $this->db->prepare("
            SELECT r.*, c.nome AS chacara_nome, e.cidade, e.estado
            FROM reservas r
            INNER JOIN chacaras c ON c.id = r.chacara_id
            LEFT JOIN chacara_enderecos e ON e.chacara_id = c.id
            WHERE r.cliente_id = ?
            ORDER BY r.criado_em DESC
        ");
        $stmt->execute([$clienteId]);
        return $stmt->fetchAll();
    }

    public function findByLocador(int $locadorId): array
    {
        $stmt = $this->db->prepare("
            SELECT r.*, c.nome AS chacara_nome,
                   u.nome AS cliente_nome, u.telefone AS cliente_telefone
            FROM reservas r
            INNER JOIN chacaras c ON c.id = r.chacara_id
            INNER JOIN usuarios u ON u.id = r.cliente_id
            WHERE c.locador_id = ?
            ORDER BY r.criado_em DESC
        ");
        $stmt->execute([$locadorId]);
        return $stmt->fetchAll();
    }

    /**
     * Verifica sobreposição com reservas CONFIRMADAS (Regra 9).
     * Retorna true se o período solicitado já está bloqueado.
     */
    public function periodoOcupado(int $chacaraId, string $dataInicio, string $dataFim, ?int $exceptId = null): bool
    {
        $sql    = "
            SELECT COUNT(*) FROM reservas
            WHERE chacara_id = ?
              AND status = 'CONFIRMADA'
              AND data_inicio <= ? AND data_fim >= ?
        ";
        $params = [$chacaraId, $dataFim, $dataInicio];

        if ($exceptId !== null) {
            $sql    .= " AND id != ?";
            $params[] = $exceptId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function registrarHistorico(int $reservaId, int $usuarioId, string $acao, ?string $observacao = null): void
    {
        $this->db->prepare("
            INSERT INTO reserva_historico (reserva_id, usuario_id, acao, observacao)
            VALUES (?, ?, ?, ?)
        ")->execute([$reservaId, $usuarioId, $acao, $observacao]);
    }

    public function confirmar(int $id, int $usuarioId): bool
    {
        if ($this->update($id, ['status' => 'CONFIRMADA'])) {
            $this->registrarHistorico($id, $usuarioId, 'CONFIRMADA');
            return true;
        }
        return false;
    }

    public function recusar(int $id, int $usuarioId, ?string $motivo = null): bool
    {
        if ($this->update($id, ['status' => 'RECUSADA'])) {
            $this->registrarHistorico($id, $usuarioId, 'RECUSADA', $motivo);
            return true;
        }
        return false;
    }

    public function cancelar(int $id, int $usuarioId): bool
    {
        if ($this->update($id, ['status' => 'CANCELADA'])) {
            $this->registrarHistorico($id, $usuarioId, 'CANCELADA');
            return true;
        }
        return false;
    }

    /**
     * Busca estatísticas de reservas para o locador
     */
    public function getEstatisticasLocador(int $locadorId): array
    {
        // Total por status
        $stmt = $this->db->prepare("
            SELECT r.status, COUNT(*) as total, SUM(r.valor_total) as valor
            FROM reservas r
            INNER JOIN chacaras c ON c.id = r.chacara_id
            WHERE c.locador_id = ?
            GROUP BY r.status
        ");
        $stmt->execute([$locadorId]);
        $porStatus = $stmt->fetchAll();

        // Reservas por mês (últimos 6 meses)
        $stmt = $this->db->prepare("
            SELECT DATE_FORMAT(r.criado_em, '%Y-%m') as mes,
                   COUNT(*) as total,
                   SUM(CASE WHEN r.status = 'CONFIRMADA' THEN r.valor_total ELSE 0 END) as receita
            FROM reservas r
            INNER JOIN chacaras c ON c.id = r.chacara_id
            WHERE c.locador_id = ?
              AND r.criado_em >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(r.criado_em, '%Y-%m')
            ORDER BY mes DESC
        ");
        $stmt->execute([$locadorId]);
        $porMes = $stmt->fetchAll();

        // Reservas por chácara
        $stmt = $this->db->prepare("
            SELECT c.nome, COUNT(*) as total,
                   SUM(CASE WHEN r.status = 'CONFIRMADA' THEN r.valor_total ELSE 0 END) as receita
            FROM reservas r
            INNER JOIN chacaras c ON c.id = r.chacara_id
            WHERE c.locador_id = ?
            GROUP BY c.id
            ORDER BY total DESC
            LIMIT 5
        ");
        $stmt->execute([$locadorId]);
        $porChacara = $stmt->fetchAll();

        // Reservas próximas (check-in em até 7 dias)
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total
            FROM reservas r
            INNER JOIN chacaras c ON c.id = r.chacara_id
            WHERE c.locador_id = ?
              AND r.status = 'CONFIRMADA'
              AND r.data_inicio BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        ");
        $stmt->execute([$locadorId]);
        $proximas = $stmt->fetchColumn();

        // Taxa de ocupação (reservas confirmadas / total)
        $stmt = $this->db->prepare("
            SELECT
                COUNT(CASE WHEN r.status = 'CONFIRMADA' THEN 1 END) as confirmadas,
                COUNT(*) as total
            FROM reservas r
            INNER JOIN chacaras c ON c.id = r.chacara_id
            WHERE c.locador_id = ?
        ");
        $stmt->execute([$locadorId]);
        $taxa = $stmt->fetch();

        return [
            'por_status' => $porStatus,
            'por_mes' => $porMes,
            'por_chacara' => $porChacara,
            'proximas' => (int) $proximas,
            'taxa_ocupacao' => $taxa['total'] > 0 ? round(($taxa['confirmadas'] / $taxa['total']) * 100, 1) : 0,
        ];
    }

    /**
     * Busca histórico completo de reservas com filtros
     */
    public function buscarHistoricoLocador(int $locadorId, array $filtros = []): array
    {
        $sql = "
            SELECT r.*,
                   c.nome as chacara_nome, c.capacidade_maxima,
                   u.nome as cliente_nome, u.telefone as cliente_telefone,
                   TIMESTAMPDIFF(DAY, r.data_inicio, r.data_fim) + 1 as diarias
            FROM reservas r
            INNER JOIN chacaras c ON c.id = r.chacara_id
            INNER JOIN usuarios u ON u.id = r.cliente_id
            WHERE c.locador_id = ?
        ";
        $params = [$locadorId];

        if (!empty($filtros['status'])) {
            $sql .= " AND r.status = ?";
            $params[] = $filtros['status'];
        }

        if (!empty($filtros['chacara_id'])) {
            $sql .= " AND r.chacara_id = ?";
            $params[] = (int) $filtros['chacara_id'];
        }

        if (!empty($filtros['data_inicio'])) {
            $sql .= " AND r.data_inicio >= ?";
            $params[] = $filtros['data_inicio'];
        }

        if (!empty($filtros['data_fim'])) {
            $sql .= " AND r.data_fim <= ?";
            $params[] = $filtros['data_fim'];
        }

        if (!empty($filtros['busca'])) {
            $sql .= " AND (u.nome LIKE ? OR c.nome LIKE ?)";
            $like = '%' . $filtros['busca'] . '%';
            $params[] = $like;
            $params[] = $like;
        }

        // Ordenação
        $ordenar = $filtros['ordenar'] ?? 'recentes';
        $sql .= match($ordenar) {
            'recentes' => " ORDER BY r.criado_em DESC",
            'antigas' => " ORDER BY r.criado_em ASC",
            'data_inicio' => " ORDER BY r.data_inicio DESC",
            'valor_desc' => " ORDER BY r.valor_total DESC",
            'valor_asc' => " ORDER BY r.valor_total ASC",
            default => " ORDER BY r.criado_em DESC"
        };

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
