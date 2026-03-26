import 'package:flutter/material.dart';

class AppColors {
  AppColors._();

  // Paleta principal — Natureza Moderna
  static const Color verdMusgo = Color(0xFF2E7D32);
  static const Color verdeClaro = Color(0xFFA5D6A7);
  static const Color begeAreia = Color(0xFFF5F5DC);
  static const Color cinzaGrafite = Color(0xFF333333);

  // Variações úteis
  static const Color verdeMusgoClaro = Color(0xFF4CAF50);
  static const Color verdeMusgoEscuro = Color(0xFF1B5E20);
  static const Color branco = Color(0xFFFFFFFF);
  static const Color cinzaClaro = Color(0xFFF5F5F5);
  static const Color cinzaMedio = Color(0xFF9E9E9E);
  static const Color vermelho = Color(0xFFD32F2F);

  // Gradientes
  static const LinearGradient gradientPrimario = LinearGradient(
    colors: [verdMusgo, verdeMusgoClaro],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient gradientOnboarding = LinearGradient(
    colors: [verdeMusgoEscuro, verdMusgo],
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
  );
}
