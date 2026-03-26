class Usuario {
  final int? id;
  final String nome;
  final String email;
  final String? senha;
  final String telefone;
  final String tipoPessoa; // PF ou PJ
  final String cpfCnpj;
  final String perfil; // CLIENTE, LOCADOR, CLIENTE,LOCADOR
  final String? fotoUrl;
  final bool ativo;
  final DateTime? criadoEm;

  Usuario({
    this.id,
    required this.nome,
    required this.email,
    this.senha,
    required this.telefone,
    required this.tipoPessoa,
    required this.cpfCnpj,
    required this.perfil,
    this.fotoUrl,
    this.ativo = true,
    this.criadoEm,
  });

  factory Usuario.fromJson(Map<String, dynamic> json) {
    return Usuario(
      id: json['id'] as int?,
      nome: json['nome'] as String,
      email: json['email'] as String,
      telefone: json['telefone'] as String? ?? '',
      tipoPessoa: json['tipo_pessoa'] as String,
      cpfCnpj: json['cpf_cnpj'] as String,
      perfil: json['perfil'] as String,
      fotoUrl: json['foto_url'] as String?,
      ativo: json['ativo'] == true || json['ativo'] == 1,
      criadoEm: json['criado_em'] != null
          ? DateTime.tryParse(json['criado_em'].toString())
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      if (id != null) 'id': id,
      'nome': nome,
      'email': email,
      if (senha != null) 'senha': senha,
      'telefone': telefone,
      'tipo_pessoa': tipoPessoa,
      'cpf_cnpj': cpfCnpj,
      'perfil': perfil,
      if (fotoUrl != null) 'foto_url': fotoUrl,
    };
  }

  bool get isLocador => perfil.contains('LOCADOR');
  bool get isCliente => perfil.contains('CLIENTE');
}
