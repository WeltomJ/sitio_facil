import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../core/app_colors.dart';
import '../../core/app_constants.dart';
import '../../services/api_service.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final _api = ApiService();
  String _perfil = '';
  String _nome = '';

  @override
  void initState() {
    super.initState();
    _loadUser();
  }

  Future<void> _loadUser() async {
    final loggedIn = await _api.isLoggedIn();
    if (!loggedIn && mounted) {
      Navigator.of(context).pushReplacementNamed('/login');
      return;
    }

    final prefs = await SharedPreferences.getInstance();
    if (mounted) {
      setState(() {
        _perfil = prefs.getString(AppConstants.keyUserPerfil) ?? 'CLIENTE';
        _nome = prefs.getString(AppConstants.keyUserNome) ?? '';
      });
    }
  }

  Future<void> _logout() async {
    await _api.limparSessao();
    if (mounted) {
      Navigator.of(context).pushNamedAndRemoveUntil('/login', (_) => false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final isLocador = _perfil.contains('LOCADOR');

    return Scaffold(
      backgroundColor: AppColors.begeAreia,
      appBar: AppBar(
        title: const Text('Sítio Fácil'),
        actions: [
          IconButton(
            onPressed: _logout,
            icon: const Icon(Icons.logout_rounded),
            tooltip: 'Sair',
          ),
        ],
      ),
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                width: 90,
                height: 90,
                decoration: BoxDecoration(
                  color: AppColors.verdeClaro.withValues(alpha: 0.3),
                  borderRadius: BorderRadius.circular(24),
                ),
                child: Icon(
                  isLocador ? Icons.home_work_rounded : Icons.person_rounded,
                  size: 48,
                  color: AppColors.verdMusgo,
                ),
              ),
              const SizedBox(height: 24),
              Text(
                'Olá, $_nome!',
                style: Theme.of(context).textTheme.headlineMedium,
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 8),
              Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                decoration: BoxDecoration(
                  color: AppColors.verdMusgo,
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Text(
                  isLocador ? 'Locador' : 'Cliente',
                  style: const TextStyle(
                    color: AppColors.branco,
                    fontSize: 14,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
              const SizedBox(height: 32),
              Text(
                isLocador
                    ? 'Área do locador em construção.\nEm breve você poderá cadastrar suas chácaras!'
                    : 'Área do cliente em construção.\nEm breve você poderá buscar chácaras!',
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 15,
                  color: AppColors.cinzaMedio,
                  height: 1.5,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
