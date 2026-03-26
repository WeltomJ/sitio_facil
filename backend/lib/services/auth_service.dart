import 'dart:convert';
import 'package:crypto/crypto.dart';
import 'package:dart_jsonwebtoken/dart_jsonwebtoken.dart';

// =============================================
// CONFIGURE AQUI SUA CHAVE SECRETA JWT
// =============================================
const String jwtSecret = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWUsImlhdCI6MTc3NDY1MTYxOH0.W_XV58ESbGLkq4rLbC29j-ZTza-R5QSGNIPBLbb087M';

String hashPassword(String password) {
  final bytes = utf8.encode(password);
  final digest = sha256.convert(bytes);
  return digest.toString();
}

bool verifyPassword(String password, String hash) {
  return hashPassword(password) == hash;
}

String generateToken(int userId, String email, String perfil) {
  final jwt = JWT({
    'id': userId,
    'email': email,
    'perfil': perfil,
  });
  return jwt.sign(SecretKey(jwtSecret), expiresIn: const Duration(days: 7));
}

Map<String, dynamic>? verifyToken(String token) {
  try {
    final jwt = JWT.verify(token, SecretKey(jwtSecret));
    return jwt.payload as Map<String, dynamic>;
  } catch (_) {
    return null;
  }
}
