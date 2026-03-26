import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:smooth_page_indicator/smooth_page_indicator.dart';
import 'package:flutter_animate/flutter_animate.dart';
import '../../core/app_colors.dart';
import '../../core/app_constants.dart';

class OnboardingScreen extends StatefulWidget {
  const OnboardingScreen({super.key});

  @override
  State<OnboardingScreen> createState() => _OnboardingScreenState();
}

class _OnboardingScreenState extends State<OnboardingScreen> {
  final _controller = PageController();
  int _currentPage = 0;

  final _pages = const [
    _OnboardingPage(
      icon: Icons.landscape_rounded,
      title: 'Bem-vindo ao Sítio Fácil',
      description:
          'O marketplace perfeito para encontrar ou alugar chácaras incríveis para seu lazer.',
      gradient: AppColors.gradientOnboarding,
    ),
    _OnboardingPage(
      icon: Icons.search_rounded,
      title: 'Encontre o lugar ideal',
      description:
          'Busque chácaras por localização, datas e capacidade. Veja fotos, comodidades e avaliações.',
      gradient: AppColors.gradientPrimario,
    ),
    _OnboardingPage(
      icon: Icons.calendar_month_rounded,
      title: 'Reserve com facilidade',
      description:
          'Escolha as datas, faça a reserva e aguarde a confirmação do locador. Simples assim!',
      gradient: AppColors.gradientOnboarding,
    ),
    _OnboardingPage(
      icon: Icons.home_work_rounded,
      title: 'É locador? Anuncie!',
      description:
          'Cadastre sua chácara, defina preços e horários, e receba reservas diretamente no app.',
      gradient: AppColors.gradientPrimario,
    ),
  ];

  bool get _isLastPage => _currentPage == _pages.length - 1;

  Future<void> _finishOnboarding() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool(AppConstants.keyOnboardingDone, true);
    if (mounted) {
      Navigator.of(context).pushReplacementNamed('/perfil-escolha');
    }
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Stack(
        children: [
          // Pages
          PageView.builder(
            controller: _controller,
            itemCount: _pages.length,
            onPageChanged: (i) => setState(() => _currentPage = i),
            itemBuilder: (_, i) => _pages[i],
          ),

          // Skip button
          if (!_isLastPage)
            Positioned(
              top: MediaQuery.of(context).padding.top + 12,
              right: 16,
              child: TextButton(
                onPressed: _finishOnboarding,
                child: const Text(
                  'Pular',
                  style: TextStyle(color: AppColors.branco, fontSize: 15),
                ),
              ),
            ),

          // Bottom area
          Positioned(
            bottom: 48,
            left: 0,
            right: 0,
            child: Column(
              children: [
                SmoothPageIndicator(
                  controller: _controller,
                  count: _pages.length,
                  effect: const ExpandingDotsEffect(
                    dotColor: AppColors.verdeClaro,
                    activeDotColor: AppColors.branco,
                    dotHeight: 8,
                    dotWidth: 8,
                    expansionFactor: 3,
                  ),
                ),
                const SizedBox(height: 32),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 32),
                  child: SizedBox(
                    width: double.infinity,
                    height: 52,
                    child: ElevatedButton(
                      onPressed: () {
                        if (_isLastPage) {
                          _finishOnboarding();
                        } else {
                          _controller.nextPage(
                            duration: 400.ms,
                            curve: Curves.easeInOut,
                          );
                        }
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppColors.branco,
                        foregroundColor: AppColors.verdMusgo,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(14),
                        ),
                      ),
                      child: Text(_isLastPage ? 'Começar' : 'Próximo'),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _OnboardingPage extends StatelessWidget {
  final IconData icon;
  final String title;
  final String description;
  final LinearGradient gradient;

  const _OnboardingPage({
    required this.icon,
    required this.title,
    required this.description,
    required this.gradient,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(gradient: gradient),
      padding: const EdgeInsets.symmetric(horizontal: 32),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(icon, size: 120, color: AppColors.branco.withValues(alpha: 0.9))
              .animate()
              .fadeIn(duration: 500.ms)
              .scale(begin: const Offset(0.5, 0.5), duration: 500.ms),
          const SizedBox(height: 40),
          Text(
            title,
            textAlign: TextAlign.center,
            style: const TextStyle(
              fontSize: 26,
              fontWeight: FontWeight.w700,
              color: AppColors.branco,
            ),
          )
              .animate()
              .fadeIn(delay: 200.ms, duration: 400.ms)
              .slideY(begin: 0.2, duration: 400.ms),
          const SizedBox(height: 16),
          Text(
            description,
            textAlign: TextAlign.center,
            style: TextStyle(
              fontSize: 16,
              color: AppColors.branco.withValues(alpha: 0.9),
              height: 1.5,
            ),
          )
              .animate()
              .fadeIn(delay: 400.ms, duration: 400.ms)
              .slideY(begin: 0.2, duration: 400.ms),
          const SizedBox(height: 100),
        ],
      ),
    );
  }
}
