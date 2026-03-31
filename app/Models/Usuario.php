<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Usuario extends Model
{
    protected string $table = 'usuarios';

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM usuarios WHERE email = ? AND ativo = 1 LIMIT 1"
        );
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function existeEmail(string $email, ?int $exceptId = null): bool
    {
        $sql    = "SELECT COUNT(*) FROM usuarios WHERE email = ?";
        $params = [$email];
        if ($exceptId !== null) {
            $sql    .= " AND id != ?";
            $params[] = $exceptId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function existeCpfCnpj(string $cpfCnpj, ?int $exceptId = null): bool
    {
        $sql    = "SELECT COUNT(*) FROM usuarios WHERE cpf_cnpj = ?";
        $params = [$cpfCnpj];
        if ($exceptId !== null) {
            $sql    .= " AND id != ?";
            $params[] = $exceptId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    /** Verifica se o usuário possui determinado perfil (CLIENTE ou LOCADOR) */
    public function temPerfil(int $id, string $perfil): bool
    {
        $stmt = $this->db->prepare("SELECT perfil FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) return false;
        return str_contains($row['perfil'], $perfil);
    }
}
