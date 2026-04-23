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

    public function findByAsaasId(string $asaasId): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM pagamentos WHERE asaas_id = ? LIMIT 1"
        );
        $stmt->execute([$asaasId]);
        return $stmt->fetch();
    }

    public function marcarPago(int $id, string $asaasId): bool
    {
        return $this->update($id, [
            'status'   => 'PAGO',
            'asaas_id' => $asaasId,
            'pago_em'  => date('Y-m-d H:i:s'),
        ]);
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
