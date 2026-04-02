<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Chacara extends Model
{
    protected string $table = 'chacaras';

    /**
     * Busca chácaras disponíveis aplicando filtros de cidade, capacidade e datas.
     * Só exclui períodos bloqueados por reservas CONFIRMADAS (Regra 9 + 12).
     */
    public function buscar(array $filtros): array
    {
        $sql = "
            SELECT c.*,
                   e.cidade, e.estado, e.latitude, e.longitude,
                   (SELECT url FROM chacara_fotos WHERE chacara_id = c.id ORDER BY ordem ASC LIMIT 1) AS capa,
                   (SELECT ROUND(AVG(nota), 1) FROM avaliacoes WHERE chacara_id = c.id) AS nota_media
            FROM chacaras c
            INNER JOIN chacara_enderecos e ON e.chacara_id = c.id
            WHERE c.ativa = 1
        ";
        $params = [];

        if (!empty($filtros['cidade'])) {
            $sql     .= " AND e.cidade LIKE ?";
            $params[] = '%' . $filtros['cidade'] . '%';
        }

        if (!empty($filtros['capacidade'])) {
            $sql     .= " AND c.capacidade_maxima >= ?";
            $params[] = (int) $filtros['capacidade'];
        }

        // Exclui apenas reservas CONFIRMADAS que conflitam com o período (Regra 9)
        if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
            $sql .= "
                AND c.id NOT IN (
                    SELECT chacara_id FROM reservas
                    WHERE status = 'CONFIRMADA'
                      AND data_inicio <= ? AND data_fim >= ?
                )
            ";
            $params[] = $filtros['data_fim'];
            $params[] = $filtros['data_inicio'];
        }

        $sql .= " ORDER BY nota_media DESC, c.criado_em DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
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

    public function insertFoto(int $chacaraId, string $url, int $ordem = 99): int
    {
        $this->db->prepare(
            "INSERT INTO chacara_fotos (chacara_id, url, ordem) VALUES (?, ?, ?)"
        )->execute([$chacaraId, $url, $ordem]);
        return (int) $this->db->lastInsertId();
    }

    /** Deleta foto verificando que pertence à chácara informada. Retorna a URL para exclusão do arquivo. */
    public function deleteFoto(int $fotoId, int $chacaraId): string|false
    {
        $stmt = $this->db->prepare("SELECT url FROM chacara_fotos WHERE id = ? AND chacara_id = ?");
        $stmt->execute([$fotoId, $chacaraId]);
        $foto = $stmt->fetch();
        if (!$foto) return false;
        $this->db->prepare("DELETE FROM chacara_fotos WHERE id = ?")->execute([$fotoId]);
        return $foto['url'];
    }

    /** Define a foto principal (ordem = 0) e reordena as demais. */
    public function setPrincipal(int $chacaraId, int $fotoId): bool
    {
        $fotos = $this->getFotos($chacaraId);
        $found = false;
        foreach ($fotos as $f) {
            if ((int) $f['id'] === $fotoId) { $found = true; break; }
        }
        if (!$found) return false;

        $ordem = 1;
        foreach ($fotos as $f) {
            if ((int) $f['id'] === $fotoId) {
                $this->db->prepare("UPDATE chacara_fotos SET ordem = 0 WHERE id = ?")->execute([$f['id']]);
            } else {
                $this->db->prepare("UPDATE chacara_fotos SET ordem = ? WHERE id = ?")->execute([$ordem++, $f['id']]);
            }
        }
        return true;
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
