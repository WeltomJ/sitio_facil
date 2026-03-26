import 'package:mailer/mailer.dart';
import 'package:mailer/smtp_server.dart';

class EmailService {
  // =============================================
  // CONFIGURE AQUI SUAS CREDENCIAIS DE E-MAIL
  // =============================================
  static const String _smtpHost = 'smtp.gmail.com'; // ou outro SMTP
  static const int _smtpPort = 587;
  static const String _username = 'weltom.jhon23@gmail.com';
  static const String _password = 'qvvt vtfk wdqo njvd';
  static const String _fromName = 'Sítio Fácil';

  static Future<void> enviarBoasVindas({
    required String destinatario,
    required String nomeUsuario,
    required String perfil,
  }) async {
    final smtpServer = SmtpServer(
      _smtpHost,
      port: _smtpPort,
      username: _username,
      password: _password,
    );

    final perfilTexto = perfil.contains('LOCADOR')
        ? 'Locador de Chácaras'
        : 'Cliente';

    final message = Message()
      ..from = Address(_username, _fromName)
      ..recipients.add(destinatario)
      ..subject = 'Bem-vindo ao Sítio Fácil! 🏡'
      ..html = '''
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
</head>
<body style="margin:0;padding:0;background:#f5f5dc;font-family:'Segoe UI',Arial,sans-serif;">
  <div style="max-width:600px;margin:0 auto;background:#ffffff;">

    <!-- Header -->
    <div style="background:linear-gradient(135deg,#2E7D32,#43A047);padding:40px 30px;text-align:center;">
      <h1 style="color:#ffffff;margin:0;font-size:28px;">🏡 Sítio Fácil</h1>
      <p style="color:#A5D6A7;margin:8px 0 0;font-size:14px;">Seu paraíso a um clique de distância</p>
    </div>

    <!-- Body -->
    <div style="padding:35px 30px;">
      <h2 style="color:#2E7D32;margin:0 0 15px;">Olá, $nomeUsuario!</h2>
      <p style="color:#333333;font-size:16px;line-height:1.6;">
        Estamos muito felizes em ter você conosco! 🎉
      </p>
      <p style="color:#333333;font-size:16px;line-height:1.6;">
        Sua conta como <strong style="color:#2E7D32;">$perfilTexto</strong>
        foi criada com sucesso.
      </p>

      <div style="background:#f5f5dc;border-left:4px solid #2E7D32;padding:15px 20px;margin:25px 0;border-radius:0 8px 8px 0;">
        <p style="color:#333333;margin:0;font-size:14px;">
          ${perfil.contains('LOCADOR') ? '📌 Agora você pode cadastrar suas chácaras e começar a receber reservas!' : '📌 Agora você pode buscar e reservar as melhores chácaras da região!'}
        </p>
      </div>

      <div style="text-align:center;margin:30px 0;">
        <a href="#" style="background:#2E7D32;color:#ffffff;padding:14px 35px;text-decoration:none;border-radius:25px;font-weight:bold;font-size:16px;display:inline-block;">
          Acessar o App
        </a>
      </div>
    </div>

    <!-- Footer -->
    <div style="background:#333333;padding:20px 30px;text-align:center;">
      <p style="color:#A5D6A7;margin:0;font-size:12px;">
        © 2026 Sítio Fácil - Todos os direitos reservados
      </p>
      <p style="color:#888888;margin:8px 0 0;font-size:11px;">
        Você recebeu este e-mail porque se cadastrou no Sítio Fácil.
      </p>
    </div>

  </div>
</body>
</html>
''';

    try {
      await send(message, smtpServer);
      print('E-mail de boas-vindas enviado para $destinatario');
    } catch (e) {
      print('Erro ao enviar e-mail: $e');
      // Não lança exceção para não bloquear o cadastro
    }
  }
}
