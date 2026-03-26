import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'core/app_theme.dart';
import 'core/app_constants.dart';
import 'features/onboarding/onboarding_screen.dart';
import 'features/auth/screens/perfil_escolha_screen.dart';
import 'features/auth/screens/cadastro_screen.dart';
import 'features/auth/screens/login_screen.dart';
import 'features/home/home_screen.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(const SitioFacilApp());
}

class SitioFacilApp extends StatelessWidget {
  const SitioFacilApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Sítio Fácil',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.light,
      home: const SplashRouter(),
      routes: {
        '/onboarding': (_) => const OnboardingScreen(),
        '/perfil-escolha': (_) => const PerfilEscolhaScreen(),
        '/cadastro': (_) => const CadastroScreen(),
        '/login': (_) => const LoginScreen(),
        '/home': (_) => const HomeScreen(),
      },
    );
  }
}

/// Tela inicial que decide para onde o usuário vai:
/// 1. Primeiro acesso → Onboarding
/// 2. Não logado → Escolha de perfil
/// 3. Logado → Home
class SplashRouter extends StatefulWidget {
  const SplashRouter({super.key});

  @override
  State<SplashRouter> createState() => _SplashRouterState();
}

class _SplashRouterState extends State<SplashRouter> {
  @override
  void initState() {
    super.initState();
    _navigate();
  }

  Future<void> _navigate() async {
    await Future.delayed(const Duration(milliseconds: 800));

    final prefs = await SharedPreferences.getInstance();
    final onboardingDone =
        prefs.getBool(AppConstants.keyOnboardingDone) ?? false;
    final hasToken = prefs.containsKey(AppConstants.keyAuthToken);

    if (!mounted) return;

    String route;
    if (!onboardingDone) {
      route = '/onboarding';
    } else if (hasToken) {
      route = '/home';
    } else {
      route = '/perfil-escolha';
    }

    Navigator.of(context).pushReplacementNamed(route);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [Color(0xFF1B5E20), Color(0xFF2E7D32)],
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
          ),
        ),
        child: const Center(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Icon(Icons.landscape_rounded, size: 80, color: Colors.white),
              SizedBox(height: 16),
              Text(
                'Sítio Fácil',
                style: TextStyle(
                  fontSize: 30,
                  fontWeight: FontWeight.w700,
                  color: Colors.white,
                ),
              ),
              SizedBox(height: 24),
              CircularProgressIndicator(color: Colors.white),
            ],
          ),
        ),
      ),
    );
  }
}
