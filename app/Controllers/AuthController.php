<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect(BASE_URL . '/dashboard');
        }
        $this->view('auth.login', ['pageTitle' => 'Entrar — Sítio Fácil']);
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        $model   = new Usuario();
        $usuario = $model->findByEmail($email);

        if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
            $this->flashError('E-mail ou senha inválidos.');
            $this->redirect(BASE_URL . '/login');
        }

        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nome']       = $usuario['nome'];
        $_SESSION['perfil']     = $usuario['perfil'];

        $this->redirect(BASE_URL . '/dashboard');
    }

    public function showCadastro(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect(BASE_URL . '/dashboard');
        }
        $this->view('auth.cadastro', ['pageTitle' => 'Cadastro — Sítio Fácil']);
    }

    public function cadastro(): void
    {
        $nome       = trim($_POST['nome'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $senha      = $_POST['senha'] ?? '';
        $telefone   = preg_replace('/\D/', '', $_POST['telefone'] ?? '') ?: null;
        $tipoPessoa = $_POST['tipo_pessoa'] ?? 'PF';
        $cpfCnpj    = preg_replace('/\D/', '', $_POST['cpf_cnpj'] ?? '');

        // Perfil pode vir como array (checkboxes) ou string
        $perfisRaw = $_POST['perfil'] ?? ['CLIENTE'];
        $perfil    = is_array($perfisRaw) ? implode(',', $perfisRaw) : $perfisRaw;

        // Validação de campos obrigatórios
        if ($nome === '') {
            $this->flashError('O nome é obrigatório.');
            $this->redirect(BASE_URL . '/cadastro');
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flashError('Informe um e-mail válido.');
            $this->redirect(BASE_URL . '/cadastro');
        }

        if (strlen($senha) < 6) {
            $this->flashError('A senha deve ter no mínimo 6 caracteres.');
            $this->redirect(BASE_URL . '/cadastro');
        }

        if ($cpfCnpj === '') {
            $this->flashError('O CPF/CNPJ é obrigatório.');
            $this->redirect(BASE_URL . '/cadastro');
        }

        $model = new Usuario();

        if ($model->existeEmail($email)) {
            $this->flashError('E-mail já cadastrado.');
            $this->redirect(BASE_URL . '/cadastro');
        }

        if ($model->existeCpfCnpj($cpfCnpj)) {
            $this->flashError('CPF/CNPJ já cadastrado.');
            $this->redirect(BASE_URL . '/cadastro');
        }

        $id = $model->insert([
            'nome'        => $nome,
            'email'       => $email,
            'senha_hash'  => password_hash($senha, PASSWORD_DEFAULT),
            'telefone'    => $telefone ?: null,
            'tipo_pessoa' => $tipoPessoa,
            'cpf_cnpj'    => $cpfCnpj,
            'perfil'      => $perfil,
        ]);

        session_regenerate_id(true);
        $usuario                = $model->find($id);
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nome']       = $usuario['nome'];
        $_SESSION['perfil']     = $usuario['perfil'];

        $this->flashSuccess('Bem-vindo ao Sítio Fácil, ' . $usuario['nome'] . '!');
        $this->redirect(BASE_URL . '/dashboard');
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect(BASE_URL . '/login');
    }
}
