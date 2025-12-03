CREATE TABLE IF NOT EXISTS usuarios (
  id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO usuarios (nome, email, senha) VALUES
('Admin Teste', 'admin@teste.com', 'testeadm123');

CREATE TABLE IF NOT EXISTS produtos (
  id INT() NOT NULL PRIMARY KEY AUTO_INCREMENT,
  nome VARCHAR(255) NOT NULL,
  categoria VARCHAR(20) NOT NULL,
  preco_atual DECIMAL(10,2) NOT NULL,
  imagem VARCHAR(255), NOT NULL,
  link_afiliado TEXT()
)
