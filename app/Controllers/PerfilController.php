<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Services\AsaasService;

class PerfilController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // PERFIL GERAL — qualquer usuário logado
    // ──────────────────────────────────────────────────────────────────────────

    public function usuario(): void
    {
        $this->requireAuth();

        $db   = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM usuarios WHERE id = ? LIMIT 1');
        $stmt->execute([(int) $_SESSION['usuario_id']]);
        $usuario = $stmt->fetch();

        $this->view('perfil.usuario', [
            'pageTitle' => 'Meu Perfil — Sítio Fácil',
            'usuario'   => $usuario,
        ]);
    }

    public function salvarPerfil(): void
    {
        $this->requireAuth();

        $nome     = trim($_POST['nome']     ?? '');
        $telefone = preg_replace('/\D/', '', $_POST['telefone'] ?? '') ?: null;

        if (strlen($nome) < 2) {
            $this->flashError('O nome deve ter ao menos 2 caracteres.');
            $this->redirect(BASE_URL . '/perfil');
        }

        $db = Database::getInstance();
        $db->prepare('UPDATE usuarios SET nome = ?, telefone = ? WHERE id = ?')
           ->execute([$nome, $telefone, (int) $_SESSION['usuario_id']]);

        // Atualiza nome na sessão
        $_SESSION['nome'] = $nome;

        $this->flashSuccess('Perfil atualizado com sucesso!');
        $this->redirect(BASE_URL . '/perfil');
    }

    public function uploadFoto(): void
    {
        $this->requireAuth();

        if (empty($_FILES['foto_url']) || $_FILES['foto_url']['error'] !== UPLOAD_ERR_OK) {
            $this->flashError('Nenhuma foto enviada ou erro no upload.');
            $this->redirect(BASE_URL . '/perfil');
        }

        $tmp  = $_FILES['foto_url']['tmp_name'];
        $mime = mime_content_type($tmp);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

        if (!array_key_exists($mime, $allowed)) {
            $this->flashError('Formato não suportado. Use JPG, PNG ou WEBP.');
            $this->redirect(BASE_URL . '/perfil');
        }

        if (!@getimagesize($tmp)) {
            $this->flashError('O arquivo enviado não é uma imagem válida.');
            $this->redirect(BASE_URL . '/perfil');
        }

        if ($_FILES['foto_url']['size'] > 3 * 1024 * 1024) {
            $this->flashError('A foto deve ter no máximo 3 MB.');
            $this->redirect(BASE_URL . '/perfil');
        }

        $uploadDir = ROOT_PATH . '/public/uploads/perfil/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext      = $allowed[$mime];
        $arquivo  = 'u' . (int) $_SESSION['usuario_id'] . '_' . time() . '.' . $ext;
        $destino  = $uploadDir . $arquivo;

        if (!move_uploaded_file($tmp, $destino)) {
            $this->flashError('Não foi possível salvar a foto. Verifique as permissões.');
            $this->redirect(BASE_URL . '/perfil');
        }

        $novaUrl = '/uploads/perfil/' . $arquivo;

        $db = Database::getInstance();

        // Remove foto antiga do disco
        $stmt = $db->prepare('SELECT foto_url FROM usuarios WHERE id = ? LIMIT 1');
        $stmt->execute([(int) $_SESSION['usuario_id']]);
        $atual = $stmt->fetchColumn();
        if ($atual) {
            $caminhoAntigo = ROOT_PATH . '/public' . $atual;
            if (file_exists($caminhoAntigo)) {
                @unlink($caminhoAntigo);
            }
        }

        $db->prepare('UPDATE usuarios SET foto_url = ? WHERE id = ?')
           ->execute([$novaUrl, (int) $_SESSION['usuario_id']]);

        // Atualiza sessão
        $_SESSION['foto_url'] = $novaUrl;

        $this->flashSuccess('Foto de perfil atualizada!');
        $this->redirect(BASE_URL . '/perfil');
    }

    public function removerFoto(): void
    {
        $this->requireAuth();

        $db   = Database::getInstance();
        $stmt = $db->prepare('SELECT foto_url FROM usuarios WHERE id = ? LIMIT 1');
        $stmt->execute([(int) $_SESSION['usuario_id']]);
        $url = $stmt->fetchColumn();

        if ($url) {
            $caminho = ROOT_PATH . '/public' . $url;
            if (file_exists($caminho)) {
                @unlink($caminho);
            }
            $db->prepare('UPDATE usuarios SET foto_url = NULL WHERE id = ?')
               ->execute([(int) $_SESSION['usuario_id']]);
            unset($_SESSION['foto_url']);
        }

        $this->flashSuccess('Foto removida.');
        $this->redirect(BASE_URL . '/perfil');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // LOCADOR — Dados Bancários
    // ──────────────────────────────────────────────────────────────────────────

    public function locador(): void
    {
        $this->requirePerfil('LOCADOR');

        $db    = Database::getInstance();
        $stmt  = $db->prepare('SELECT * FROM locador_dados_bancarios WHERE usuario_id = ? LIMIT 1');
        $stmt->execute([(int) $_SESSION['usuario_id']]);
        $dados = $stmt->fetch() ?: [];

        $this->view('perfil.locador', [
            'pageTitle' => 'Dados de Recebimento — Sítio Fácil',
            'dados'     => $dados,
        ]);
    }

    public function salvarLocador(): void
    {
        $this->requirePerfil('LOCADOR');

        $banco       = trim($_POST['banco']        ?? '');
        $agencia     = trim($_POST['agencia']      ?? '');
        $conta       = trim($_POST['conta']        ?? '');
        $tipoConta   = $_POST['tipo_conta'] === 'POUPANCA' ? 'POUPANCA' : 'CORRENTE';
        $nomeTitular = trim($_POST['nome_titular'] ?? '');
        $cpfCnpj     = trim($_POST['cpf_cnpj']    ?? '');

        if (!$banco || !$agencia || !$conta || !$nomeTitular || !$cpfCnpj) {
            $this->flashError('Preencha todos os campos obrigatórios.');
            $this->redirect(BASE_URL . '/locador/perfil');
        }

        $db   = Database::getInstance();
        $stmt = $db->prepare('SELECT id FROM locador_dados_bancarios WHERE usuario_id = ? LIMIT 1');
        $stmt->execute([(int) $_SESSION['usuario_id']]);
        $existente = $stmt->fetch();

        if ($existente) {
            $db->prepare('
                UPDATE locador_dados_bancarios
                SET banco = ?, agencia = ?, conta = ?, tipo_conta = ?, nome_titular = ?, cpf_cnpj = ?
                WHERE usuario_id = ?
            ')->execute([$banco, $agencia, $conta, $tipoConta, $nomeTitular, $cpfCnpj, (int) $_SESSION['usuario_id']]);
        } else {
            $db->prepare('
                INSERT INTO locador_dados_bancarios (usuario_id, banco, agencia, conta, tipo_conta, nome_titular, cpf_cnpj)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ')->execute([(int) $_SESSION['usuario_id'], $banco, $agencia, $conta, $tipoConta, $nomeTitular, $cpfCnpj]);
        }

        $this->flashSuccess('Dados bancários salvos com sucesso!');
        $this->redirect(BASE_URL . '/locador/perfil');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // CLIENTE — Cartões Salvos
    // ──────────────────────────────────────────────────────────────────────────

    public function cartoes(): void
    {
        $this->requirePerfil('CLIENTE');

        $db   = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM cliente_cartoes WHERE cliente_id = ? ORDER BY criado_em DESC');
        $stmt->execute([(int) $_SESSION['usuario_id']]);
        $cartoes = $stmt->fetchAll();

        $this->view('perfil.cartoes', [
            'pageTitle' => 'Meus Cartões — Sítio Fácil',
            'cartoes'   => $cartoes,
        ]);
    }

    public function adicionarCartao(): void
    {
        $this->requirePerfil('CLIENTE');

        $numero   = trim($_POST['numero_cartao']   ?? '');
        $nome     = trim($_POST['nome_cartao']     ?? '');
        $validade = trim($_POST['validade_cartao'] ?? '');
        $cvv      = trim($_POST['cvv_cartao']      ?? '');

        if (!$numero || !$nome || !$validade || !$cvv) {
            $this->flashError('Preencha todos os dados do cartão.');
            $this->redirect(BASE_URL . '/cliente/cartoes');
        }

        // Busca dados do usuário para o Asaas
        $db   = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM usuarios WHERE id = ? LIMIT 1');
        $stmt->execute([(int) $_SESSION['usuario_id']]);
        $usuario = $stmt->fetch();

        try {
            $asaas      = new AsaasService();
            $customerId = $usuario['asaas_customer_id'] ?? null;

            if (!$customerId) {
                $customerId = $asaas->buscarOuCriarCliente($usuario);
                // Salva o customer ID no usuário
                $db->prepare('UPDATE usuarios SET asaas_customer_id = ? WHERE id = ?')
                   ->execute([$customerId, (int) $_SESSION['usuario_id']]);
            }

            $cep = preg_replace('/\D/', '', trim($_POST['cep_titular'] ?? ''));

            $tokenData = $asaas->tokenizarCartao($customerId, [
                'numero'           => $numero,
                'nome'             => $nome,
                'validade'         => $validade,
                'cvv'              => $cvv,
                'email'            => $usuario['email'],
                'cpf_cnpj'         => $usuario['cpf_cnpj'],
                'telefone'         => $usuario['telefone'] ?? '',
                'cep'              => $cep,
                'numero_endereco'  => 'S/N',
            ]);

            $db->prepare('
                INSERT INTO cliente_cartoes
                    (cliente_id, asaas_customer_id, token, bandeira, final_cartao, nome_titular, expiry_month, expiry_year)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ')->execute([
                (int) $_SESSION['usuario_id'],
                $customerId,
                $tokenData['token'],
                $tokenData['bandeira'],
                $tokenData['final_cartao'],
                strtoupper($nome),
                $tokenData['expiry_month'],
                $tokenData['expiry_year'],
            ]);

            $this->flashSuccess('Cartão adicionado com sucesso!');
        } catch (\RuntimeException $e) {
            $this->flashError('Erro ao adicionar cartão: ' . $e->getMessage());
        }

        $this->redirect(BASE_URL . '/cliente/cartoes');
    }

    public function removerCartao(string $id): void
    {
        $this->requirePerfil('CLIENTE');

        $db   = Database::getInstance();
        $stmt = $db->prepare('SELECT id FROM cliente_cartoes WHERE id = ? AND cliente_id = ? LIMIT 1');
        $stmt->execute([(int) $id, (int) $_SESSION['usuario_id']]);

        if (!$stmt->fetch()) {
            $this->flashError('Cartão não encontrado.');
            $this->redirect(BASE_URL . '/cliente/cartoes');
        }

        $db->prepare('DELETE FROM cliente_cartoes WHERE id = ?')->execute([(int) $id]);

        $this->flashSuccess('Cartão removido.');
        $this->redirect(BASE_URL . '/cliente/cartoes');
    }
}
