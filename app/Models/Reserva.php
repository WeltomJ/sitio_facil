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

    public function findByCliente(int $clienteId, int $page = 1, int $perPage = 15): array
    {
        $stmt = $this->db->prepare("
            SELECT r.*, c.nome AS chacara_nome, e.cidade, e.estado
            FROM reservas r
            INNER JOIN chacaras c ON c.id = r.chacara_id
            LEFT JOIN chacara_enderecos e ON e.chacara_id = c.id
            WHERE r.cliente_id = ?
            ORDER BY r.criado_em DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$clienteId, $perPage, ($page - 1) * $perPage]);
        return $stmt->fetchAll();
    }

    public function countByCliente(int $clienteId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM reservas WHERE cliente_id = ?");
        $stmt->execute([$clienteId]);
        return (int) $stmt->fetchColumn();
    }

    public function findByLocador(int $locadorId, int $page = 1, int $perPage = 15): array
    {
        $stmt = $this->db->prepare("
            SELECT r.*, c.nome AS chacara_nome,
                   u.nome AS cliente_nome, u.telefone AS cliente_telefone
            FROM reservas r
            INNER JOIN chacaras c ON c.id = r.chacara_id
            INNER JOIN usuarios u ON u.id = r.cliente_id
            WHERE c.locador_id = ?
            ORDER BY r.criado_em DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$locadorId, $perPage, ($page - 1) * $perPage]);
        return $stmt->fetchAll();
    }

    public function countByLocador(int $locadorId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM reservas r
            INNER JOIN chacaras c ON c.id = r.chacara_id
            WHERE c.locador_id = ?
        ");
        $stmt->execute([$locadorId]);
        return (int) $stmt->fetchColumn();
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

    /**
     * Retorna os intervalos de datas bloqueados (reservas CONFIRMADAS futuras).
     * Usado para desabilitar datas no calendário de reservas.
     */
    public function getDatasOcupadas(int $chacaraId): array
    {
        $stmt = $this->db->prepare("
            SELECT data_inicio, data_fim
            FROM reservas
            WHERE chacara_id = ?
              AND status = 'CONFIRMADA'
              AND data_fim >= CURDATE()
            ORDER BY data_inicio
        ");
        $stmt->execute([$chacaraId]);
        return $stmt->fetchAll();
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
}
