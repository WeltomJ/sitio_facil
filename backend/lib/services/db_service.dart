import 'package:mysql_client/mysql_client.dart';

class DbService {
  late MySQLConnection _conn;

  // =============================================
  // CONFIGURE AQUI SUAS CREDENCIAIS DO MYSQL
  // =============================================
  static const String _host = 'localhost';
  static const int _port = 3306;
  static const String _user = 'root';
  static const String _password = 'root123';
  static const String _database = 'sitio_facil';

  Future<void> connect() async {
    _conn = await MySQLConnection.createConnection(
      host: _host,
      port: _port,
      userName: _user,
      password: _password,
      databaseName: _database,
      secure: false,
    );
    await _conn.connect();
    print('MySQL conectado em $_host:$_port/$_database');
  }

  MySQLConnection get conn => _conn;

  Future<IResultSet> query(String sql,
      [Map<String, dynamic>? params]) async {
    if (params != null) {
      return await _conn.execute(sql, params);
    }
    return await _conn.execute(sql);
  }

  Future<void> close() async {
    await _conn.close();
  }
}
