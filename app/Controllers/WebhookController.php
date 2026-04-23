<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Notificacao;
use App\Models\Pagamento;
use App\Models\Reserva;

/**
 * Recebe notificações do Asaas via webhook.
 *
 * URL configurada em: sandbox.asaas.com → Configurações → Notificações
 * Exemplo: https://seu-ngrok.ngrok.io/sitio_facil/webhook/asaas
 */
class WebhookController extends Controller
{
    public function handle(): void
    {
        // Fecha a sessão imediatamente — o webhook não precisa dela
        // e evita conflito de headers com a resposta JSON
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        // Limpa qualquer output que possa ter sido gerado antes
        if (ob_get_level()) {
            ob_end_clean();
        }

        http_response_code(200);
        header('Content-Type: application/json');

        try {
            $body    = file_get_contents('php://input');
            $payload = json_decode($body, true);

            // Payload inválido — retorna 200 mesmo assim para não ser penalizado
            if (!is_array($payload) || empty($payload['event'])) {
                echo json_encode(['ok' => false, 'reason' => 'invalid_payload']);
                exit;
            }

            $evento  = $payload['event'];
            $asaasId = $payload['payment']['id'] ?? '';

            // Ignora eventos que não são de pagamento recebido/confirmado
            $eventosAceitos = ['PAYMENT_RECEIVED', 'PAYMENT_CONFIRMED'];
            if (!in_array($evento, $eventosAceitos, true) || !$asaasId) {
                echo json_encode(['ok' => true, 'ignored' => true, 'event' => $evento]);
                exit;
            }

            $pagamentoModel = new Pagamento();
            $pagamento      = $pagamentoModel->findByAsaasId($asaasId);

            if (!$pagamento) {
                echo json_encode(['ok' => true, 'not_found' => true]);
                exit;
            }

            // Idempotência — já processado
            if ($pagamento['status'] === 'PAGO') {
                echo json_encode(['ok' => true, 'already_paid' => true]);
                exit;
            }

            // Marca pagamento como PAGO
            $pagamentoModel->marcarPago((int) $pagamento['id'], $asaasId);

            // Confirma a reserva e notifica o locador
            $reservaModel = new Reserva();
            $reserva      = $reservaModel->findComDetalhes((int) $pagamento['reserva_id']);

            if ($reserva) {
                $reservaModel->registrarHistorico(
                    (int) $reserva['id'],
                    (int) $reserva['cliente_id'],
                    'PAGAMENTO_REALIZADO',
                    'Pagamento PIX confirmado via webhook Asaas (ID: ' . $asaasId . ')'
                );

                // Confirma automaticamente
                $reservaModel->confirmar((int) $reserva['id'], (int) $reserva['cliente_id']);

                (new Notificacao())->enviar(
                    (int) $reserva['locador_id'],
                    'Nova reserva confirmada!',
                    "Uma reserva para \"{$reserva['chacara_nome']}\" de {$reserva['data_inicio']} a {$reserva['data_fim']} foi confirmada automaticamente após pagamento PIX."
                );

                (new Notificacao())->enviar(
                    (int) $reserva['cliente_id'],
                    'Reserva confirmada! ✓',
                    "Seu pagamento PIX foi recebido e sua reserva em \"{$reserva['chacara_nome']}\" está confirmada."
                );
            }

            echo json_encode(['ok' => true]);

        } catch (\Throwable $e) {
            // Mesmo em caso de erro interno, retorna 200 ao Asaas
            // para não ser penalizado — o polling vai compensar
            echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        }

        exit;
    }
}
