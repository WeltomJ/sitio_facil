<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Avaliacao extends Model
{
    protected string $table = 'avaliacoes';

    public function findByChacaraComUsuario(int $chacaraId): array
    {
        $stmt = $this->db->prepare("
            SELECT a.*, u.nome AS cliente_nome, u.foto_url AS cliente_foto
            FROM avaliacoes a
            INNER JOIN usuarios u ON u.id = a.cliente_id
            WHERE a.chacara_id = ?
            ORDER BY a.criado_em DESC
        ");
        $stmt->execute([$chacaraId]);
        return $stmt->fetchAll();
    }

    public function jaAvaliou(int $reservaId): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM avaliacoes WHERE reserva_id = ?");
        $stmt->execute([$reservaId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function mediaPorChacara(int $chacaraId): float
    {
        $stmt = $this->db->prepare("SELECT AVG(nota) FROM avaliacoes WHERE chacara_id = ?");
        $stmt->execute([$chacaraId]);
        return round((float) $stmt->fetchColumn(), 1);
    }
}
