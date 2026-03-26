class UserModel {
  final int id;
  final String nome;
  final String email;
  final String perfil;
  final String tipoPessoa;

  UserModel({
    required this.id,
    required this.nome,
    required this.email,
    required this.perfil,
    required this.tipoPessoa,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] as int,
      nome: json['nome'] as String,
      email: json['email'] as String,
      perfil: json['perfil'] as String,
      tipoPessoa: json['tipo_pessoa'] as String,
    );
  }

  bool get isLocador => perfil.contains('LOCADOR');
  bool get isCliente => perfil.contains('CLIENTE');
}
