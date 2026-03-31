<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Pagamento extends Model
{
    protected string $table = 'pagamentos';

    public function findByReserva(int $reservaId): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM pagamentos WHERE reserva_id = ? ORDER BY criado_em DESC LIMIT 1"
        );
        $stmt->execute([$reservaId]);
        return $stmt->fetch();
    }

    public function confirmar(int $id): bool
    {
        return $this->update($id, [
            'status'  => 'PAGO',
            'pago_em' => date('Y-m-d H:i:s'),
        ]);
    }

    public function cancelar(int $id): bool
    {
        return $this->update($id, ['status' => 'CANCELADO']);
    }
}
