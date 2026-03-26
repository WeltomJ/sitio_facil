import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import '../../../core/app_colors.dart';

class PerfilEscolhaScreen extends StatelessWidget {
  const PerfilEscolhaScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        width: double.infinity,
        decoration: const BoxDecoration(gradient: AppColors.gradientOnboarding),
        child: SafeArea(
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 28),
            child: Column(
              children: [
                const Spacer(flex: 2),
                const Icon(
                  Icons.landscape_rounded,
                  size: 80,
                  color: AppColors.branco,
                ).animate().fadeIn(duration: 500.ms).scale(
                      begin: const Offset(0.5, 0.5),
                      duration: 500.ms,
                    ),
                const SizedBox(height: 24),
                const Text(
                  'Como deseja usar\no Sítio Fácil?',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    fontSize: 26,
                    fontWeight: FontWeight.w700,
                    color: AppColors.branco,
                  ),
                ).animate().fadeIn(delay: 200.ms, duration: 400.ms),
                const SizedBox(height: 8),
                Text(
                  'Você pode alterar isso depois',
                  style: TextStyle(
                    fontSize: 14,
                    color: AppColors.branco.withValues(alpha: 0.8),
                  ),
                ).animate().fadeIn(delay: 300.ms, duration: 400.ms),
                const Spacer(flex: 2),

                // Card Cliente
                _PerfilCard(
                  icon: Icons.person_rounded,
                  titulo: 'Sou Cliente',
                  descricao: 'Quero encontrar e reservar chácaras para meu lazer.',
                  onTap: () => Navigator.of(context).pushNamed(
                    '/cadastro',
                    arguments: 'CLIENTE',
                  ),
                )
                    .animate()
                    .fadeIn(delay: 400.ms, duration: 400.ms)
                    .slideX(begin: -0.1, duration: 400.ms),

                const SizedBox(height: 16),

                // Card Locador
                _PerfilCard(
                  icon: Icons.home_work_rounded,
                  titulo: 'Sou Locador',
                  descricao: 'Quero anunciar minhas chácaras e receber reservas.',
                  onTap: () => Navigator.of(context).pushNamed(
                    '/cadastro',
                    arguments: 'LOCADOR',
                  ),
                )
                    .animate()
                    .fadeIn(delay: 500.ms, duration: 400.ms)
                    .slideX(begin: 0.1, duration: 400.ms),

                const Spacer(),

                // Link login
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Text(
                      'Já tem conta? ',
                      style: TextStyle(
                        color: AppColors.branco.withValues(alpha: 0.8),
                        fontSize: 14,
                      ),
                    ),
                    GestureDetector(
                      onTap: () =>
                          Navigator.of(context).pushNamed('/login'),
                      child: const Text(
                        'Entrar',
                        style: TextStyle(
                          color: AppColors.branco,
                          fontSize: 14,
                          fontWeight: FontWeight.w700,
                          decoration: TextDecoration.underline,
                          decorationColor: AppColors.branco,
                        ),
                      ),
                    ),
                  ],
                ).animate().fadeIn(delay: 600.ms, duration: 400.ms),

                const SizedBox(height: 32),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _PerfilCard extends StatelessWidget {
  final IconData icon;
  final String titulo;
  final String descricao;
  final VoidCallback onTap;

  const _PerfilCard({
    required this.icon,
    required this.titulo,
    required this.descricao,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(16),
        child: Container(
          width: double.infinity,
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            color: AppColors.branco,
            borderRadius: BorderRadius.circular(16),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: 0.15),
                blurRadius: 12,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: Row(
            children: [
              Container(
                width: 56,
                height: 56,
                decoration: BoxDecoration(
                  color: AppColors.verdeClaro.withValues(alpha: 0.3),
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Icon(icon, size: 30, color: AppColors.verdMusgo),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      titulo,
                      style: const TextStyle(
                        fontSize: 17,
                        fontWeight: FontWeight.w600,
                        color: AppColors.cinzaGrafite,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      descricao,
                      style: const TextStyle(
                        fontSize: 13,
                        color: AppColors.cinzaMedio,
                      ),
                    ),
                  ],
                ),
              ),
              const Icon(
                Icons.arrow_forward_ios_rounded,
                size: 18,
                color: AppColors.cinzaMedio,
              ),
            ],
          ),
        ),
      ),
    );
  }
}
