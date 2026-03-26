import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:mask_text_input_formatter/mask_text_input_formatter.dart';
import '../../../core/app_colors.dart';
import '../../../core/validators.dart';
import '../../../models/usuario.dart';
import '../../../services/api_service.dart';

class CadastroScreen extends StatefulWidget {
  const CadastroScreen({super.key});

  @override
  State<CadastroScreen> createState() => _CadastroScreenState();
}

class _CadastroScreenState extends State<CadastroScreen> {
  final _formKey = GlobalKey<FormState>();
  final _api = ApiService();

  final _nomeCtrl = TextEditingController();
  final _emailCtrl = TextEditingController();
  final _telefoneCtrl = TextEditingController();
  final _cpfCnpjCtrl = TextEditingController();
  final _senhaCtrl = TextEditingController();
  final _confirmarSenhaCtrl = TextEditingController();

  String _tipoPessoa = 'PF';
  bool _senhaVisivel = false;
  bool _isLoading = false;

  late String _perfil;

  final _cpfFormatter = MaskTextInputFormatter(
    mask: '###.###.###-##',
    filter: {'#': RegExp(r'[0-9]')},
  );

  final _cnpjFormatter = MaskTextInputFormatter(
    mask: '##.###.###/####-##',
    filter: {'#': RegExp(r'[0-9]')},
  );

  final _telefoneFormatter = MaskTextInputFormatter(
    mask: '(##) #####-####',
    filter: {'#': RegExp(r'[0-9]')},
  );

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    _perfil = ModalRoute.of(context)?.settings.arguments as String? ?? 'CLIENTE';
  }

  @override
  void dispose() {
    _nomeCtrl.dispose();
    _emailCtrl.dispose();
    _telefoneCtrl.dispose();
    _cpfCnpjCtrl.dispose();
    _senhaCtrl.dispose();
    _confirmarSenhaCtrl.dispose();
    super.dispose();
  }

  Future<void> _cadastrar() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isLoading = true);

    try {
      final usuario = Usuario(
        nome: _nomeCtrl.text.trim(),
        email: _emailCtrl.text.trim(),
        senha: _senhaCtrl.text,
        telefone: _telefoneCtrl.text.trim(),
        tipoPessoa: _tipoPessoa,
        cpfCnpj: _cpfCnpjCtrl.text.trim(),
        perfil: _perfil,
      );

      final data = await _api.cadastrar(usuario);
      await _api.salvarSessao(data);

      if (mounted) {
        Navigator.of(context).pushNamedAndRemoveUntil('/home', (_) => false);
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(e.toString().replaceFirst('Exception: ', '')),
            backgroundColor: AppColors.vermelho,
          ),
        );
      }
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final isLocador = _perfil == 'LOCADOR';

    return Scaffold(
      backgroundColor: AppColors.begeAreia,
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Header
                Row(
                  children: [
                    IconButton(
                      onPressed: () => Navigator.of(context).pop(),
                      icon: const Icon(Icons.arrow_back_rounded),
                      color: AppColors.cinzaGrafite,
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                Center(
                  child: Container(
                    width: 70,
                    height: 70,
                    decoration: BoxDecoration(
                      color: AppColors.verdeClaro.withValues(alpha: 0.3),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Icon(
                      isLocador ? Icons.home_work_rounded : Icons.person_rounded,
                      size: 36,
                      color: AppColors.verdMusgo,
                    ),
                  ),
                ).animate().fadeIn(duration: 400.ms).scale(
                      begin: const Offset(0.8, 0.8),
                      duration: 400.ms,
                    ),
                const SizedBox(height: 16),
                Center(
                  child: Text(
                    isLocador ? 'Cadastro de Locador' : 'Cadastro de Cliente',
                    style: Theme.of(context).textTheme.headlineMedium,
                  ),
                ).animate().fadeIn(delay: 100.ms, duration: 400.ms),
                Center(
                  child: Text(
                    'Preencha seus dados para começar',
                    style: TextStyle(color: AppColors.cinzaMedio, fontSize: 14),
                  ),
                ).animate().fadeIn(delay: 200.ms, duration: 400.ms),
                const SizedBox(height: 28),

                // Nome
                _buildLabel('Nome completo'),
                TextFormField(
                  controller: _nomeCtrl,
                  textCapitalization: TextCapitalization.words,
                  decoration: const InputDecoration(
                    hintText: 'Seu nome completo',
                    prefixIcon: Icon(Icons.person_outline_rounded),
                  ),
                  validator: Validators.nome,
                ),
                const SizedBox(height: 16),

                // E-mail
                _buildLabel('E-mail'),
                TextFormField(
                  controller: _emailCtrl,
                  keyboardType: TextInputType.emailAddress,
                  decoration: const InputDecoration(
                    hintText: 'seuemail@exemplo.com',
                    prefixIcon: Icon(Icons.email_outlined),
                  ),
                  validator: Validators.email,
                ),
                const SizedBox(height: 16),

                // Telefone
                _buildLabel('Telefone'),
                TextFormField(
                  controller: _telefoneCtrl,
                  keyboardType: TextInputType.phone,
                  inputFormatters: [_telefoneFormatter],
                  decoration: const InputDecoration(
                    hintText: '(00) 00000-0000',
                    prefixIcon: Icon(Icons.phone_outlined),
                  ),
                  validator: Validators.telefone,
                ),
                const SizedBox(height: 16),

                // Tipo Pessoa
                _buildLabel('Tipo de pessoa'),
                Row(
                  children: [
                    Expanded(
                      child: _TipoPessoaChip(
                        label: 'Pessoa Física',
                        isSelected: _tipoPessoa == 'PF',
                        onTap: () {
                          setState(() {
                            _tipoPessoa = 'PF';
                            _cpfCnpjCtrl.clear();
                          });
                        },
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _TipoPessoaChip(
                        label: 'Pessoa Jurídica',
                        isSelected: _tipoPessoa == 'PJ',
                        onTap: () {
                          setState(() {
                            _tipoPessoa = 'PJ';
                            _cpfCnpjCtrl.clear();
                          });
                        },
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),

                // CPF ou CNPJ
                _buildLabel(_tipoPessoa == 'PF' ? 'CPF' : 'CNPJ'),
                TextFormField(
                  controller: _cpfCnpjCtrl,
                  keyboardType: TextInputType.number,
                  inputFormatters: [
                    _tipoPessoa == 'PF' ? _cpfFormatter : _cnpjFormatter,
                  ],
                  decoration: InputDecoration(
                    hintText: _tipoPessoa == 'PF'
                        ? '000.000.000-00'
                        : '00.000.000/0000-00',
                    prefixIcon: const Icon(Icons.badge_outlined),
                  ),
                  validator: (v) => Validators.cpfCnpj(v, _tipoPessoa),
                ),
                const SizedBox(height: 16),

                // Senha
                _buildLabel('Senha'),
                TextFormField(
                  controller: _senhaCtrl,
                  obscureText: !_senhaVisivel,
                  decoration: InputDecoration(
                    hintText: 'Mínimo 6 caracteres',
                    prefixIcon: const Icon(Icons.lock_outline_rounded),
                    suffixIcon: IconButton(
                      onPressed: () =>
                          setState(() => _senhaVisivel = !_senhaVisivel),
                      icon: Icon(
                        _senhaVisivel
                            ? Icons.visibility_off_rounded
                            : Icons.visibility_rounded,
                      ),
                    ),
                  ),
                  validator: Validators.senha,
                ),
                const SizedBox(height: 16),

                // Confirmar senha
                _buildLabel('Confirmar senha'),
                TextFormField(
                  controller: _confirmarSenhaCtrl,
                  obscureText: !_senhaVisivel,
                  decoration: const InputDecoration(
                    hintText: 'Repita sua senha',
                    prefixIcon: Icon(Icons.lock_outline_rounded),
                  ),
                  validator: (v) =>
                      Validators.confirmarSenha(v, _senhaCtrl.text),
                ),
                const SizedBox(height: 32),

                // Botão cadastrar
                SizedBox(
                  width: double.infinity,
                  height: 52,
                  child: ElevatedButton(
                    onPressed: _isLoading ? null : _cadastrar,
                    child: _isLoading
                        ? const SizedBox(
                            width: 24,
                            height: 24,
                            child: CircularProgressIndicator(
                              color: AppColors.branco,
                              strokeWidth: 2.5,
                            ),
                          )
                        : const Text('Criar conta'),
                  ),
                ),
                const SizedBox(height: 16),

                // Link login
                Center(
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const Text(
                        'Já tem conta? ',
                        style: TextStyle(fontSize: 14),
                      ),
                      GestureDetector(
                        onTap: () =>
                            Navigator.of(context).pushReplacementNamed('/login'),
                        child: const Text(
                          'Entrar',
                          style: TextStyle(
                            color: AppColors.verdMusgo,
                            fontWeight: FontWeight.w600,
                            fontSize: 14,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 24),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildLabel(String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 6, left: 4),
      child: Text(
        text,
        style: const TextStyle(
          fontSize: 14,
          fontWeight: FontWeight.w500,
          color: AppColors.cinzaGrafite,
        ),
      ),
    );
  }
}

class _TipoPessoaChip extends StatelessWidget {
  final String label;
  final bool isSelected;
  final VoidCallback onTap;

  const _TipoPessoaChip({
    required this.label,
    required this.isSelected,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 250),
        padding: const EdgeInsets.symmetric(vertical: 12),
        decoration: BoxDecoration(
          color: isSelected ? AppColors.verdMusgo : AppColors.branco,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: isSelected ? AppColors.verdMusgo : AppColors.cinzaMedio.withValues(alpha: 0.3),
            width: 1.5,
          ),
        ),
        child: Center(
          child: Text(
            label,
            style: TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w600,
              color: isSelected ? AppColors.branco : AppColors.cinzaGrafite,
            ),
          ),
        ),
      ),
    );
  }
}
