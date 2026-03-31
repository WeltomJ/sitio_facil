<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Notificacao extends Model
{
    protected string $table = 'notificacoes';

    public function findByUsuario(int $usuarioId, bool $apenasNaoLidas = false): array
    {
        $sql = "SELECT * FROM notificacoes WHERE usuario_id = ?";
        if ($apenasNaoLidas) {
            $sql .= " AND lida = 0";
        }
        $sql .= " ORDER BY criado_em DESC LIMIT 50";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll();
    }

    public function contarNaoLidas(int $usuarioId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM notificacoes WHERE usuario_id = ? AND lida = 0"
        );
        $stmt->execute([$usuarioId]);
        return (int) $stmt->fetchColumn();
    }

    public function enviar(int $usuarioId, string $titulo, string $mensagem): void
    {
        $this->insert([
            'usuario_id' => $usuarioId,
            'titulo'     => $titulo,
            'mensagem'   => $mensagem,
        ]);
    }

    public function marcarLida(int $id, int $usuarioId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE notificacoes SET lida = 1 WHERE id = ? AND usuario_id = ?"
        );
        return $stmt->execute([$id, $usuarioId]);
    }

    public function marcarTodasLidas(int $usuarioId): void
    {
        $this->db->prepare(
            "UPDATE notificacoes SET lida = 1 WHERE usuario_id = ? AND lida = 0"
        )->execute([$usuarioId]);
    }
}
