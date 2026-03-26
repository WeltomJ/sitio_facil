import 'dart:convert';
import 'package:shelf/shelf.dart';
import 'package:shelf_router/shelf_router.dart';
import '../services/db_service.dart';
import '../services/auth_service.dart';
import '../services/email_service.dart';

class AuthRoutes {
  final DbService db;

  AuthRoutes(this.db);

  Router get router {
    final r = Router();

    // POST /api/auth/cadastro
    r.post('/cadastro', _register);

    // POST /api/auth/login
    r.post('/login', _login);

    return r;
  }

  // ==================== CADASTRO ====================
  Future<Response> _register(Request request) async {
    try {
      final body = jsonDecode(await request.readAsString());

      final nome = body['nome'] as String?;
      final email = body['email'] as String?;
      final senha = body['senha'] as String?;
      final telefone = body['telefone'] as String?;
      final tipoPessoa = body['tipo_pessoa'] as String?;
      final cpfCnpj = body['cpf_cnpj'] as String?;
      final perfil = body['perfil'] as String?; // CLIENTE, LOCADOR, CLIENTE,LOCADOR

      if (nome == null ||
          email == null ||
          senha == null ||
          tipoPessoa == null ||
          cpfCnpj == null ||
          perfil == null) {
        return _jsonResponse(400, {'error': 'Campos obrigatórios faltando'});
      }

      // Verifica duplicidade de e-mail
      final emailCheck = await db.query(
        'SELECT id FROM usuarios WHERE email = :email',
        {'email': email},
      );
      if (emailCheck.rows.isNotEmpty) {
        return _jsonResponse(409, {'error': 'E-mail já cadastrado'});
      }

      // Verifica duplicidade de CPF/CNPJ
      final docCheck = await db.query(
        'SELECT id FROM usuarios WHERE cpf_cnpj = :doc',
        {'doc': cpfCnpj},
      );
      if (docCheck.rows.isNotEmpty) {
        return _jsonResponse(409, {'error': 'CPF/CNPJ já cadastrado'});
      }

      // Insere o usuário
      final senhaHash = hashPassword(senha);
      await db.query(
        '''INSERT INTO usuarios (nome, email, senha_hash, telefone, tipo_pessoa, cpf_cnpj, perfil)
           VALUES (:nome, :email, :senha, :telefone, :tipo, :doc, :perfil)''',
        {
          'nome': nome,
          'email': email,
          'senha': senhaHash,
          'telefone': telefone ?? '',
          'tipo': tipoPessoa,
          'doc': cpfCnpj,
          'perfil': perfil,
        },
      );

      // Busca o ID do usuário criado
      final result = await db.query(
        'SELECT id FROM usuarios WHERE email = :email',
        {'email': email},
      );
      final userId =
          int.parse(result.rows.first.assoc()['id']!);

      final token = generateToken(userId, email, perfil);

      // Envia e-mail de boas-vindas (assíncrono, não bloqueia)
      EmailService.enviarBoasVindas(
        destinatario: email,
        nomeUsuario: nome,
        perfil: perfil,
      );

      return _jsonResponse(201, {
        'message': 'Cadastro realizado com sucesso',
        'token': token,
        'usuario': {
          'id': userId,
          'nome': nome,
          'email': email,
          'perfil': perfil,
          'tipo_pessoa': tipoPessoa,
        },
      });
    } catch (e) {
      print('Erro no registro: $e');
      return _jsonResponse(500, {'error': 'Erro interno do servidor'});
    }
  }

  // ==================== LOGIN ====================
  Future<Response> _login(Request request) async {
    try {
      final body = jsonDecode(await request.readAsString());

      final email = body['email'] as String?;
      final senha = body['senha'] as String?;

      if (email == null || senha == null) {
        return _jsonResponse(400, {'error': 'E-mail e senha são obrigatórios'});
      }

      final result = await db.query(
        'SELECT id, nome, email, senha_hash, perfil, tipo_pessoa, ativo FROM usuarios WHERE email = :email',
        {'email': email},
      );

      if (result.rows.isEmpty) {
        return _jsonResponse(401, {'error': 'E-mail ou senha incorretos'});
      }

      final user = result.rows.first.assoc();

      if (user['ativo'] != '1') {
        return _jsonResponse(403, {'error': 'Conta desativada'});
      }

      if (!verifyPassword(senha, user['senha_hash']!)) {
        return _jsonResponse(401, {'error': 'E-mail ou senha incorretos'});
      }

      final token = generateToken(
        int.parse(user['id']!),
        user['email']!,
        user['perfil']!,
      );

      return _jsonResponse(200, {
        'message': 'Login realizado com sucesso',
        'token': token,
        'usuario': {
          'id': int.parse(user['id']!),
          'nome': user['nome'],
          'email': user['email'],
          'perfil': user['perfil'],
          'tipo_pessoa': user['tipo_pessoa'],
        },
      });
    } catch (e) {
      print('Erro no login: $e');
      return _jsonResponse(500, {'error': 'Erro interno do servidor'});
    }
  }

  Response _jsonResponse(int statusCode, Map<String, dynamic> body) {
    return Response(statusCode,
        body: jsonEncode(body),
        headers: {'Content-Type': 'application/json'});
  }
}
