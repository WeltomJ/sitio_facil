# Sitio Facil

Marketplace de aluguel de chacaras. O app conecta **locadores** (donos de chacaras) com **clientes** que buscam lugares para lazer.

---

## Visao Geral

O sistema resolve 3 problemas:

1. **Descoberta** - Cliente busca chacaras por localizacao, datas e capacidade
2. **Disponibilidade** - Controle de agenda com check-in/check-out
3. **Reserva** - Com controle de concorrencia (primeira reserva confirmada vence)

### Perfis de usuario

| Perfil | O que faz |
|--------|-----------|
| **Cliente** | Busca, reserva e avalia chacaras |
| **Locador** | Cadastra chacaras, define precos, aprova/recusa reservas |
| **Ambos** | Um usuario pode ter os dois perfis |

---

## Tecnologias

| Camada | Tecnologia |
|--------|-----------|
| **App** | Flutter (Dart) - Android/iOS/Web/Desktop |
| **Backend** | Dart com `shelf` + `shelf_router` |
| **Banco** | MySQL 8+ |
| **Auth** | JWT (`dart_jsonwebtoken`) + SHA-256 |
| **E-mail** | `mailer` (SMTP) |

---

## Estrutura do Projeto

```
sitio_facil/
|
|-- lib/                          # App Flutter
|   |-- main.dart                 # Ponto de entrada + Splash + Rotas
|   |-- core/
|   |   |-- app_colors.dart       # Paleta de cores (verde musgo, bege areia...)
|   |   |-- app_theme.dart        # Tema Material3 com Google Fonts (Poppins)
|   |   |-- app_constants.dart    # URL da API, keys do SharedPreferences
|   |   |-- validators.dart       # Validadores (CPF, CNPJ, e-mail, telefone, senha)
|   |-- models/
|   |   |-- usuario.dart          # Model Usuario com toJson/fromJson
|   |-- services/
|   |   |-- api_service.dart      # Singleton HTTP (cadastro, login, sessao)
|   |-- features/
|       |-- onboarding/
|       |   |-- onboarding_screen.dart    # 4 telas de apresentacao (so no 1o acesso)
|       |-- auth/screens/
|       |   |-- perfil_escolha_screen.dart # Escolha: "Sou Cliente" / "Sou Locador"
|       |   |-- cadastro_screen.dart       # Formulario com mascaras CPF/CNPJ/telefone
|       |   |-- login_screen.dart          # Login e-mail + senha
|       |-- home/
|           |-- home_screen.dart           # Tela inicial pos-login (placeholder)
|
|-- backend/                      # API REST em Dart
|   |-- bin/
|   |   |-- server.dart           # Servidor Shelf na porta 3001
|   |-- lib/
|       |-- routes/
|       |   |-- auth_routes.dart  # POST /api/auth/cadastro e /api/auth/login
|       |-- services/
|           |-- db_service.dart   # Conexao MySQL
|           |-- auth_service.dart # Hash de senha + JWT
|           |-- email_service.dart# E-mail HTML de boas-vindas
|
|-- database/
|   |-- sitio_facil.sql           # Schema completo MySQL (11 tabelas)
|
|-- regra_negocio.txt             # Documento com as 21 regras de negocio
```

---

## Banco de Dados

### Tabelas (11)

| Tabela | Descricao |
|--------|-----------|
| `usuarios` | Clientes e locadores (PF/PJ, CPF/CNPJ unico) |
| `chacaras` | Imoveis com preco, capacidade, horarios check-in/out |
| `chacara_enderecos` | Endereco + latitude/longitude para busca geografica |
| `comodidades` | Tabela de dominio (Piscina, Churrasqueira, Wi-Fi...) |
| `chacara_comodidades` | Relacao N:N chacara <-> comodidades |
| `chacara_fotos` | Fotos dos imoveis com ordem de exibicao |
| `reservas` | Status: PENDENTE -> CONFIRMADA/RECUSADA/CANCELADA/CONCLUIDA |
| `pagamentos` | Pagamento simulado (PIX, cartao, manual) |
| `reserva_historico` | Log/auditoria de acoes nas reservas |
| `avaliacoes` | Nota 1-5 + comentario (uma por reserva) |
| `notificacoes` | Notificacoes in-app por usuario |

### Regras importantes do banco

- CPF/CNPJ e e-mail sao **unicos** (UNIQUE KEY)
- `perfil` usa `SET('CLIENTE','LOCADOR')` - permite ambos
- Reservas tem CHECK: `data_fim >= data_inicio`
- Avaliacoes tem CHECK: `nota BETWEEN 1 AND 5`
- Indice espacial em `latitude, longitude` para busca por proximidade
- Indice em `reservas(chacara_id, status, data_inicio, data_fim)` para verificacao de sobreposicao

---

## Fluxo do App

```
[1o acesso] -> Onboarding (4 telas) -> Escolha de Perfil -> Cadastro -> Home
[Ja tem conta]                      -> Login                         -> Home
[Ja logado]                                                          -> Home
```

O controle e feito via `SharedPreferences`:
- `onboarding_done` - Se ja viu a apresentacao
- `auth_token` - Token JWT da sessao
- `user_id`, `user_perfil`, `user_nome` - Dados do usuario logado

---

## Como Rodar

### Pre-requisitos

- Flutter SDK (3.10+)
- Dart SDK (3.0+)
- MySQL 8+ rodando localmente

### 1. Banco de dados

```sql
-- No MySQL, execute o script:
source c:/Flutter/sitio_facil/database/sitio_facil.sql;
```

Isso cria o banco `sitio_facil`, todas as 11 tabelas e insere as comodidades iniciais.

### 2. Configurar o backend

Edite os arquivos em `backend/lib/services/`:

| Arquivo | O que configurar |
|---------|-----------------|
| `db_service.dart` | Host, porta, usuario e **senha do MySQL** |
| `auth_service.dart` | Chave secreta JWT (`jwtSecret`) |
| `email_service.dart` | Credenciais SMTP (e-mail e senha de app) |

### 3. Iniciar o backend

```bash
cd backend
dart pub get
dart run bin/server.dart
```

Voce deve ver:
```
MySQL conectado em localhost:3306/sitio_facil
Backend rodando em http://0.0.0.0:3001
```

**Mantenha esse terminal aberto.**

### 4. Configurar a URL da API no app

Edite `lib/core/app_constants.dart`:

```dart
// Emulador Android (AVD):
static const String apiBaseUrl = 'http://10.0.2.2:3001/api';

// Celular fisico (mesmo Wi-Fi):
static const String apiBaseUrl = 'http://SEU_IP_LOCAL:3001/api';

// Windows desktop ou web:
static const String apiBaseUrl = 'http://localhost:3001/api';
```

Para descobrir seu IP local: `ipconfig` (Windows) ou `ifconfig` (Mac/Linux).

**Se usar celular fisico**, libere a porta no firewall (como admin):
```
netsh advfirewall firewall add rule name="SitioFacil Backend 3001" dir=in action=allow protocol=TCP localport=3001
```

### 5. Rodar o app Flutter

```bash
cd ..
flutter pub get
flutter run
```

---

## API - Endpoints

### `GET /api/health`
Verifica se o backend esta rodando.

**Resposta:** `{"status":"ok"}`

---

### `POST /api/auth/cadastro`
Cria um novo usuario.

**Body (JSON):**
```json
{
  "nome": "Joao Silva",
  "email": "joao@email.com",
  "senha": "minhasenha123",
  "telefone": "(92) 99999-9999",
  "tipo_pessoa": "PF",
  "cpf_cnpj": "123.456.789-00",
  "perfil": "CLIENTE"
}
```

**Resposta (201):**
```json
{
  "message": "Cadastro realizado com sucesso",
  "token": "eyJhbG...",
  "usuario": {
    "id": 1,
    "nome": "Joao Silva",
    "email": "joao@email.com",
    "perfil": "CLIENTE",
    "tipo_pessoa": "PF"
  }
}
```

**Erros:** `409` (e-mail ou CPF/CNPJ duplicado), `400` (campos faltando)

---

### `POST /api/auth/login`
Autentica um usuario.

**Body (JSON):**
```json
{
  "email": "joao@email.com",
  "senha": "minhasenha123"
}
```

**Resposta (200):**
```json
{
  "message": "Login realizado com sucesso",
  "token": "eyJhbG...",
  "usuario": {
    "id": 1,
    "nome": "Joao Silva",
    "email": "joao@email.com",
    "perfil": "CLIENTE",
    "tipo_pessoa": "PF"
  }
}
```

**Erros:** `401` (credenciais invalidas), `403` (conta desativada)

---

## Paleta de Cores

| Nome | Hex | Uso |
|------|-----|-----|
| Verde musgo | `#2E7D32` | Cor primaria, botoes, AppBar |
| Verde claro | `#A5D6A7` | Acentos, icones, indicadores |
| Bege areia | `#F5F5DC` | Fundo das telas (scaffold) |
| Cinza grafite | `#333333` | Textos principais |
| Branco | `#FFFFFF` | Cards, inputs, superficies |

---

## Regras de Negocio Principais

1. **Cadastro**: PF (CPF) ou PJ (CNPJ), sem duplicidade
2. **Perfis**: Cliente, Locador ou ambos
3. **Chacaras**: So locador cadastra; cada chacara tem 1 dono
4. **Reservas**: Status inicial PENDENTE; pendentes NAO bloqueiam datas
5. **Concorrencia**: Primeira reserva confirmada vence
6. **Pagamento**: Simulado ou confirmacao manual pelo locador
7. **Historico**: Todas as acoes sao registradas (log/auditoria)

Documento completo: `regra_negocio.txt`

---

## O que esta implementado

- [x] Onboarding (apresentacao no 1o acesso)
- [x] Escolha de perfil (Cliente / Locador)
- [x] Cadastro com validacoes e mascaras
- [x] Login com JWT
- [x] Backend REST (cadastro + login)
- [x] E-mail de boas-vindas (HTML)
- [x] Banco de dados completo (11 tabelas)
- [x] Splash screen com roteamento inteligente

## O que falta implementar

- [ ] Cadastro de chacaras (CRUD)
- [ ] Upload de fotos
- [ ] Busca com filtros (localizacao, datas, capacidade)
- [ ] Integracao com mapas (Google Maps / OpenStreetMap)
- [ ] Sistema de reservas com controle de concorrencia
- [ ] Pagamento simulado
- [ ] Aprovacao/recusa de reservas pelo locador
- [ ] Avaliacoes e comentarios
- [ ] Notificacoes in-app
- [ ] Recuperacao de senha
- [ ] Tela de perfil do usuario
