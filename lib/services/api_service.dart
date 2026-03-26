import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../core/app_constants.dart';
import '../models/usuario.dart';

class ApiService {
  static final ApiService _instance = ApiService._internal();
  factory ApiService() => _instance;
  ApiService._internal();

  String get _baseUrl => AppConstants.apiBaseUrl;

  Future<Map<String, String>> _headers() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString(AppConstants.keyAuthToken);
    return {
      'Content-Type': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  // ======================== AUTH ========================

  Future<Map<String, dynamic>> cadastrar(Usuario usuario) async {
    final response = await http.post(
      Uri.parse('$_baseUrl/auth/cadastro'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode(usuario.toJson()),
    );
    final data = jsonDecode(response.body) as Map<String, dynamic>;
    if (response.statusCode != 201) {
      throw Exception(data['error'] ?? 'Erro ao cadastrar');
    }
    return data;
  }

  Future<Map<String, dynamic>> login(String email, String senha) async {
    final response = await http.post(
      Uri.parse('$_baseUrl/auth/login'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({'email': email, 'senha': senha}),
    );
    final data = jsonDecode(response.body) as Map<String, dynamic>;
    if (response.statusCode != 200) {
      throw Exception(data['error'] ?? 'Credenciais inválidas');
    }
    return data;
  }

  // Salvar dados de sessão
  Future<void> salvarSessao(Map<String, dynamic> data) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(AppConstants.keyAuthToken, data['token'] as String);
    await prefs.setInt(AppConstants.keyUserId, data['usuario']['id'] as int);
    await prefs.setString(
      AppConstants.keyUserPerfil,
      data['usuario']['perfil'] as String,
    );
    await prefs.setString(
      AppConstants.keyUserNome,
      data['usuario']['nome'] as String,
    );
  }

  Future<void> limparSessao() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(AppConstants.keyAuthToken);
    await prefs.remove(AppConstants.keyUserId);
    await prefs.remove(AppConstants.keyUserPerfil);
    await prefs.remove(AppConstants.keyUserNome);
  }

  Future<bool> isLoggedIn() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.containsKey(AppConstants.keyAuthToken);
  }

  Future<String?> getUserPerfil() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(AppConstants.keyUserPerfil);
  }

  // ======================== GENÉRICOS ========================

  Future<Map<String, dynamic>> get(String endpoint) async {
    final response = await http.get(
      Uri.parse('$_baseUrl$endpoint'),
      headers: await _headers(),
    );
    return jsonDecode(response.body) as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> post(
    String endpoint,
    Map<String, dynamic> body,
  ) async {
    final response = await http.post(
      Uri.parse('$_baseUrl$endpoint'),
      headers: await _headers(),
      body: jsonEncode(body),
    );
    return jsonDecode(response.body) as Map<String, dynamic>;
  }
}
