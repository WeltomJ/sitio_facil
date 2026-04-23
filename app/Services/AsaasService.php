<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Integração com a API do Asaas (Sandbox).
 *
 * Documentação: https://docs.asaas.com
 */
class AsaasService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $config        = require __DIR__ . '/../../config/asaas.php';
        $this->apiKey  = $config['api_key'];
        $this->baseUrl = rtrim($config['base_url'], '/');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // CLIENTES
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Retorna o customer_id do Asaas para o usuário.
     * Se ainda não existe, cria e retorna o novo ID.
     */
    public function buscarOuCriarCliente(array $usuario): string
    {
        // Remove pontuação do CPF/CNPJ
        $cpfCnpj = preg_replace('/\D/', '', $usuario['cpf_cnpj']);

        // Tenta encontrar cliente já existente
        $resp = $this->request('GET', '/customers?cpfCnpj=' . urlencode($cpfCnpj));

        if (!empty($resp['data'][0]['id'])) {
            return $resp['data'][0]['id'];
        }

        // Cria novo cliente
        $payload = [
            'name'     => $usuario['nome'],
            'email'    => $usuario['email'],
            'cpfCnpj'  => $cpfCnpj,
            'phone'    => preg_replace('/\D/', '', $usuario['telefone'] ?? ''),
        ];

        $novo = $this->request('POST', '/customers', $payload);

        if (empty($novo['id'])) {
            throw new \RuntimeException('Asaas: falha ao criar cliente — ' . ($novo['errors'][0]['description'] ?? 'erro desconhecido'));
        }

        return $novo['id'];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // TOKENIZAÇÃO DE CARTÃO
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Tokeniza um cartão de crédito no Asaas e retorna os dados do token.
     *
     * @param string $customerId  ID do customer no Asaas
     * @param array  $dadosCartao ['numero', 'nome', 'validade' (MM/AA), 'cvv']
     * @return array ['token', 'bandeira', 'final_cartao', 'expiry_month', 'expiry_year']
     */
    public function tokenizarCartao(string $customerId, array $dadosCartao): array
    {
        [$mes, $ano] = explode('/', $dadosCartao['validade']);
        $anoCompleto = strlen(trim($ano)) === 2 ? '20' . trim($ano) : trim($ano);

        $payload = [
            'customer'   => $customerId,
            'creditCard' => [
                'holderName'  => strtoupper(trim($dadosCartao['nome'])),
                'number'      => preg_replace('/\D/', '', $dadosCartao['numero']),
                'expiryMonth' => trim($mes),
                'expiryYear'  => $anoCompleto,
                'ccv'         => trim($dadosCartao['cvv']),
            ],
            'creditCardHolderInfo' => [
                'name'     => strtoupper(trim($dadosCartao['nome'])),
                'email'    => $dadosCartao['email']    ?? '',
                'cpfCnpj'  => preg_replace('/\D/', '', $dadosCartao['cpf_cnpj'] ?? ''),
                'phone'    => preg_replace('/\D/', '', $dadosCartao['telefone'] ?? ''),
                'postalCode' => preg_replace('/\D/', '', $dadosCartao['cep'] ?? '00000000'),
                'addressNumber' => $dadosCartao['numero_endereco'] ?? 'S/N',
            ],
            'remoteIp' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        ];

        $resp = $this->request('POST', '/creditCard/tokenizeCreditCard', $payload);

        if (empty($resp['creditCardToken'])) {
            throw new \RuntimeException('Asaas: falha ao tokenizar cartão — ' . ($resp['errors'][0]['description'] ?? 'erro desconhecido'));
        }

        return [
            'token'        => $resp['creditCardToken'],
            'bandeira'     => strtoupper($resp['creditCardBrand'] ?? 'DESCONHECIDA'),
            'final_cartao' => substr(preg_replace('/\D/', '', $dadosCartao['numero']), -4),
            'expiry_month' => trim($mes),
            'expiry_year'  => $anoCompleto,
        ];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // COBRANÇAS PIX
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Cria uma cobrança PIX e retorna asaas_id + código copia-e-cola.
     *
     * @return array ['asaas_id', 'pix_codigo', 'pix_expira_em', 'pix_qr_base64']
     */
    public function criarCobrancaPix(string $customerId, float $valor, string $descricao, int $reservaId): array
    {
        $payload = [
            'customer'    => $customerId,
            'billingType' => 'PIX',
            'value'       => round($valor, 2),
            'dueDate'     => date('Y-m-d', strtotime('+1 day')),
            'description' => $descricao,
            'externalReference' => (string) $reservaId,
        ];

        $resp = $this->request('POST', '/payments', $payload);

        if (empty($resp['id'])) {
            throw new \RuntimeException('Asaas: falha ao criar cobrança PIX — ' . ($resp['errors'][0]['description'] ?? 'erro desconhecido'));
        }

        $asaasId = $resp['id'];

        // Busca QR Code
        $qr = $this->buscarQrCodePix($asaasId);

        return [
            'asaas_id'    => $asaasId,
            'pix_codigo'  => $qr['payload']      ?? '',
            'pix_expira_em' => date('Y-m-d H:i:s', strtotime('+30 minutes')),
            'pix_qr_base64' => $qr['encodedImage'] ?? '',
        ];
    }

    /**
     * Busca o QR Code PIX de uma cobrança existente.
     *
     * @return array ['encodedImage' (base64), 'payload' (copia-cola), 'expirationDate']
     */
    public function buscarQrCodePix(string $asaasId): array
    {
        $resp = $this->request('GET', '/payments/' . $asaasId . '/pixQrCode');
        return $resp;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // COBRANÇAS CARTÃO
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Cria uma cobrança de cartão de crédito usando token salvo.
     *
     * @return array ['asaas_id', 'status']  status pode ser 'CONFIRMED', 'PENDING', etc.
     */
    public function criarCobrancaCartao(
        string $customerId,
        float  $valor,
        string $token,
        int    $parcelas,
        string $descricao,
        int    $reservaId
    ): array {
        $payload = [
            'customer'              => $customerId,
            'billingType'           => 'CREDIT_CARD',
            'value'                 => round($valor, 2),
            'dueDate'               => date('Y-m-d'),
            'description'           => $descricao,
            'externalReference'     => (string) $reservaId,
            'installmentCount'      => $parcelas,
            'creditCardToken'       => $token,
            'remoteIp'              => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        ];

        $resp = $this->request('POST', '/payments', $payload);

        if (empty($resp['id'])) {
            $msg = $resp['errors'][0]['description'] ?? ($resp['message'] ?? 'erro desconhecido');
            throw new \RuntimeException('Asaas: pagamento recusado — ' . $msg);
        }

        return [
            'asaas_id' => $resp['id'],
            'status'   => $resp['status'] ?? 'PENDING',
        ];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // CONSULTAR STATUS
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Consulta o status de uma cobrança no Asaas.
     * Possíveis retornos: PENDING, CONFIRMED, RECEIVED, OVERDUE, REFUNDED, etc.
     */
    public function consultarPagamento(string $asaasId): array
    {
        return $this->request('GET', '/payments/' . $asaasId);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HTTP HELPER
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Faz uma requisição HTTP para a API do Asaas usando cURL.
     */
    private function request(string $method, string $endpoint, array $body = []): array
    {
        $url = $this->baseUrl . $endpoint;

        $headers = [
            'accept: application/json',
            'content-type: application/json',
            'access_token: ' . $this->apiKey,
            'User-Agent: SitioFacil/1.0',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \RuntimeException('Asaas cURL error: ' . $error);
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : [];
    }
}
