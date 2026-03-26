class Validators {
  Validators._();

  static String? nome(String? value) {
    if (value == null || value.trim().isEmpty) return 'Informe seu nome';
    if (value.trim().length < 3) return 'Nome muito curto';
    return null;
  }

  static String? email(String? value) {
    if (value == null || value.trim().isEmpty) return 'Informe seu e-mail';
    final regex = RegExp(r'^[\w\-.]+@([\w\-]+\.)+[\w\-]{2,4}$');
    if (!regex.hasMatch(value.trim())) return 'E-mail inválido';
    return null;
  }

  static String? senha(String? value) {
    if (value == null || value.isEmpty) return 'Informe sua senha';
    if (value.length < 6) return 'Mínimo de 6 caracteres';
    return null;
  }

  static String? confirmarSenha(String? value, String senha) {
    if (value == null || value.isEmpty) return 'Confirme sua senha';
    if (value != senha) return 'As senhas não coincidem';
    return null;
  }

  static String? telefone(String? value) {
    if (value == null || value.trim().isEmpty) return 'Informe seu telefone';
    final digits = value.replaceAll(RegExp(r'\D'), '');
    if (digits.length < 10 || digits.length > 11) return 'Telefone inválido';
    return null;
  }

  static String? cpf(String? value) {
    if (value == null || value.trim().isEmpty) return 'Informe seu CPF';
    final digits = value.replaceAll(RegExp(r'\D'), '');
    if (digits.length != 11) return 'CPF inválido';
    return null;
  }

  static String? cnpj(String? value) {
    if (value == null || value.trim().isEmpty) return 'Informe seu CNPJ';
    final digits = value.replaceAll(RegExp(r'\D'), '');
    if (digits.length != 14) return 'CNPJ inválido';
    return null;
  }

  static String? cpfCnpj(String? value, String tipoPessoa) {
    if (tipoPessoa == 'PF') return cpf(value);
    return cnpj(value);
  }
}
