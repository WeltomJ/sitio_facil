-- ============================================================
-- SITIO FACIL - Estrutura do Banco de Dados (MySQL)
-- Marketplace de aluguel de chácaras
-- ============================================================

CREATE DATABASE IF NOT EXISTS sitio_facil
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE sitio_facil;

-- ============================================================
-- 1. USUARIOS
-- Regra 1: PF (CPF) ou PJ (CNPJ)
-- Regra 2: pode ser cliente, locador ou ambos
-- Regra 3: CPF/CNPJ único
-- ============================================================
CREATE TABLE usuarios (
  id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome          VARCHAR(150)    NOT NULL,
  email         VARCHAR(255)    NOT NULL,
  senha_hash    VARCHAR(255)    NOT NULL,
  telefone      VARCHAR(20)     NULL,
  tipo_pessoa   ENUM('PF','PJ') NOT NULL,
  cpf_cnpj      VARCHAR(18)     NOT NULL,
  perfil        SET('CLIENTE','LOCADOR') NOT NULL DEFAULT 'CLIENTE',
  foto_url      VARCHAR(500)    NULL,
  ativo         BOOLEAN         NOT NULL DEFAULT TRUE,
  criado_em     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  UNIQUE KEY uk_email    (email),
  UNIQUE KEY uk_cpf_cnpj (cpf_cnpj)
) ENGINE=InnoDB;

-- ============================================================
-- 2. CHACARAS
-- Regra 4: só locador cadastra
-- Regra 5: nome, endereço, descrição, capacidade, preço
-- Regra 6: pertence a um único locador
-- Regra 7: check-in / check-out definidos pelo locador
-- ============================================================
CREATE TABLE chacaras (
  id                 BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  locador_id         BIGINT UNSIGNED  NOT NULL,
  nome               VARCHAR(200)     NOT NULL,
  descricao          TEXT             NULL,
  capacidade_maxima  INT UNSIGNED     NOT NULL,
  preco_diaria       DECIMAL(10,2)    NOT NULL,
  tipo_cobranca      ENUM('DIARIA','PERIODO') NOT NULL DEFAULT 'DIARIA',
  horario_checkin    TIME             NOT NULL DEFAULT '14:00:00',
  horario_checkout   TIME             NOT NULL DEFAULT '10:00:00',
  ativa              BOOLEAN          NOT NULL DEFAULT TRUE,
  criado_em          DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT fk_chacara_locador
    FOREIGN KEY (locador_id) REFERENCES usuarios(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================================
-- 3. ENDERECOS DAS CHACARAS
-- Separado para manter normalização e suportar geolocalização
-- Regra 11: busca por localização / raio
-- ============================================================
CREATE TABLE chacara_enderecos (
  id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  chacara_id    BIGINT UNSIGNED  NOT NULL,
  logradouro    VARCHAR(255)     NOT NULL,
  numero        VARCHAR(20)      NULL,
  complemento   VARCHAR(100)     NULL,
  bairro        VARCHAR(100)     NULL,
  cidade        VARCHAR(100)     NOT NULL,
  estado        CHAR(2)          NOT NULL,
  cep           VARCHAR(10)      NULL,
  latitude      DECIMAL(10,7)    NULL,
  longitude     DECIMAL(10,7)    NULL,

  UNIQUE KEY uk_endereco_chacara (chacara_id),

  CONSTRAINT fk_endereco_chacara
    FOREIGN KEY (chacara_id) REFERENCES chacaras(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

-- Índice espacial simplificado para busca por proximidade
CREATE INDEX idx_geo ON chacara_enderecos (latitude, longitude);

-- ============================================================
-- 4. COMODIDADES (tabela de domínio)
-- Regra 5: piscina, churrasqueira, etc.
-- ============================================================
CREATE TABLE comodidades (
  id   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,

  UNIQUE KEY uk_comodidade_nome (nome)
) ENGINE=InnoDB;

-- Relacionamento N:N entre chácara e comodidades
CREATE TABLE chacara_comodidades (
  chacara_id    BIGINT UNSIGNED NOT NULL,
  comodidade_id BIGINT UNSIGNED NOT NULL,

  PRIMARY KEY (chacara_id, comodidade_id),

  CONSTRAINT fk_cc_chacara
    FOREIGN KEY (chacara_id) REFERENCES chacaras(id)
    ON UPDATE CASCADE ON DELETE CASCADE,

  CONSTRAINT fk_cc_comodidade
    FOREIGN KEY (comodidade_id) REFERENCES comodidades(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 5. FOTOS DAS CHACARAS
-- ============================================================
CREATE TABLE chacara_fotos (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  chacara_id  BIGINT UNSIGNED NOT NULL,
  url         VARCHAR(500)    NOT NULL,
  descricao   VARCHAR(200)    NULL,
  ordem       SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  criado_em   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_foto_chacara
    FOREIGN KEY (chacara_id) REFERENCES chacaras(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 6. RESERVAS
-- Regra 8:  intervalo data_inicio+checkin → data_fim+checkout
-- Regra 9:  sem sobreposição de reservas CONFIRMADAS
-- Regra 10: pendentes NÃO bloqueiam
-- Regra 13: status inicial = PENDENTE
-- Regra 14: só válida quando pagamento confirmado
-- Regra 17: primeira confirmada vence (concorrência)
-- ============================================================
CREATE TABLE reservas (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  chacara_id      BIGINT UNSIGNED  NOT NULL,
  cliente_id      BIGINT UNSIGNED  NOT NULL,
  data_inicio     DATE             NOT NULL,
  data_fim        DATE             NOT NULL,
  qtd_hospedes    INT UNSIGNED     NULL,
  valor_total     DECIMAL(10,2)    NOT NULL,
  status          ENUM('PENDENTE','CONFIRMADA','RECUSADA','CANCELADA','CONCLUIDA')
                    NOT NULL DEFAULT 'PENDENTE',
  criado_em       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT fk_reserva_chacara
    FOREIGN KEY (chacara_id) REFERENCES chacaras(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,

  CONSTRAINT fk_reserva_cliente
    FOREIGN KEY (cliente_id) REFERENCES usuarios(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,

  -- Impede reservas no passado (validar também no backend)
  CONSTRAINT chk_datas CHECK (data_fim >= data_inicio)
) ENGINE=InnoDB;

-- Índice para verificação de sobreposição de forma eficiente
CREATE INDEX idx_reserva_chacara_datas
  ON reservas (chacara_id, status, data_inicio, data_fim);

-- ============================================================
-- 7. PAGAMENTOS (simulado)
-- Regra 18: pagamento simulado ou confirmação manual
-- Regra 19: locador aprova / recusa
-- ============================================================
CREATE TABLE pagamentos (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  reserva_id      BIGINT UNSIGNED NOT NULL,
  valor           DECIMAL(10,2)   NOT NULL,
  metodo          ENUM('SIMULADO','PIX','CARTAO','MANUAL') NOT NULL DEFAULT 'SIMULADO',
  status          ENUM('PENDENTE','PAGO','CANCELADO','REEMBOLSADO')
                    NOT NULL DEFAULT 'PENDENTE',
  pago_em         DATETIME        NULL,
  criado_em       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_pagamento_reserva
    FOREIGN KEY (reserva_id) REFERENCES reservas(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================================
-- 8. HISTORICO / LOG DE ACOES
-- Regra 20: registrar criação, confirmação, cancelamento
-- Regra 21: usuário visualiza histórico
-- ============================================================
CREATE TABLE reserva_historico (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  reserva_id  BIGINT UNSIGNED NOT NULL,
  usuario_id  BIGINT UNSIGNED NOT NULL,
  acao        ENUM('CRIADA','CONFIRMADA','RECUSADA','CANCELADA','CONCLUIDA','PAGAMENTO_REALIZADO')
                NOT NULL,
  observacao  TEXT             NULL,
  criado_em   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_hist_reserva
    FOREIGN KEY (reserva_id) REFERENCES reservas(id)
    ON UPDATE CASCADE ON DELETE CASCADE,

  CONSTRAINT fk_hist_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE INDEX idx_hist_reserva ON reserva_historico (reserva_id, criado_em);

-- ============================================================
-- 9. AVALIACOES
-- Regra extra: cliente avalia após uso
-- ============================================================
CREATE TABLE avaliacoes (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  reserva_id  BIGINT UNSIGNED NOT NULL,
  cliente_id  BIGINT UNSIGNED NOT NULL,
  chacara_id  BIGINT UNSIGNED NOT NULL,
  nota        TINYINT UNSIGNED NOT NULL,
  comentario  TEXT             NULL,
  criado_em   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,

  -- Um cliente só avalia uma vez por reserva
  UNIQUE KEY uk_avaliacao_reserva (reserva_id),

  CONSTRAINT fk_aval_reserva
    FOREIGN KEY (reserva_id) REFERENCES reservas(id)
    ON UPDATE CASCADE ON DELETE CASCADE,

  CONSTRAINT fk_aval_cliente
    FOREIGN KEY (cliente_id) REFERENCES usuarios(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,

  CONSTRAINT fk_aval_chacara
    FOREIGN KEY (chacara_id) REFERENCES chacaras(id)
    ON UPDATE CASCADE ON DELETE CASCADE,

  -- Nota de 1 a 5
  CONSTRAINT chk_nota CHECK (nota BETWEEN 1 AND 5)
) ENGINE=InnoDB;

CREATE INDEX idx_aval_chacara ON avaliacoes (chacara_id);

-- ============================================================
-- 10. NOTIFICACOES
-- ============================================================
CREATE TABLE notificacoes (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id  BIGINT UNSIGNED NOT NULL,
  titulo      VARCHAR(200)    NOT NULL,
  mensagem    TEXT             NOT NULL,
  lida        BOOLEAN          NOT NULL DEFAULT FALSE,
  criado_em   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_notif_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_notif_usuario ON notificacoes (usuario_id, lida, criado_em DESC);

-- ============================================================
-- DADOS INICIAIS - Comodidades comuns
-- ============================================================
INSERT INTO comodidades (nome) VALUES
  ('Piscina'),
  ('Churrasqueira'),
  ('Wi-Fi'),
  ('Estacionamento'),
  ('Campo de futebol'),
  ('Playground'),
  ('Sauna'),
  ('Salão de festas'),
  ('Cozinha equipada'),
  ('Ar condicionado'),
  ('Lago'),
  ('Quadra esportiva');
