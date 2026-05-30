-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 29/05/2026 às 15:22
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

CREATE DATABASE IF NOT EXISTS PI
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE PI;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE categoria (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE usuario (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('ADMIN','CULINARISTA','CLIENTE') NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE culinarista (
    id_culin INT(11) NOT NULL,
    setor VARCHAR(150),
    segmento VARCHAR(100),
    PRIMARY KEY (id_culin),
    CONSTRAINT fk_culinarista_usuario
    FOREIGN KEY (id_culin)
    REFERENCES usuario(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE cliente (
    id_usuario INT(11) NOT NULL,
    id_culin INT(11) NULL,
    segmento VARCHAR(100),
    localidade VARCHAR(100),
    PRIMARY KEY (id_usuario),

    CONSTRAINT fk_cliente_usuario
    FOREIGN KEY (id_usuario)
    REFERENCES usuario(id),

    CONSTRAINT fk_cliente_culinarista
    FOREIGN KEY (id_culin)
    REFERENCES culinarista(id_culin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE ingrediente (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    unidade VARCHAR(20) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE produto_distrib (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    unidade VARCHAR(20) NOT NULL,
    estoque DECIMAL(10,3) NOT NULL DEFAULT 0.000,
    preco DECIMAL(10,2) NOT NULL,
    atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE receita (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    custo DECIMAL(10,2),
    rendimento VARCHAR(50),
    tempo_preparo INT(11),
    visibilidade ENUM('PUBLICA','PRIVADA') NOT NULL DEFAULT 'PRIVADA',
    id_categoria INT(11),
    id_culin INT(11),
    id_cliente INT(11),
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    CONSTRAINT fk_receita_categoria
    FOREIGN KEY (id_categoria)
    REFERENCES categoria(id),

    CONSTRAINT fk_receita_culin
    FOREIGN KEY (id_culin)
    REFERENCES culinarista(id_culin),

    CONSTRAINT fk_receita_cliente
    FOREIGN KEY (id_cliente)
    REFERENCES cliente(id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE receita_ingrediente (
    id_receita INT(11) NOT NULL,
    id_ingrediente INT(11) NOT NULL,
    quantidade DECIMAL(10,3) NOT NULL,

    PRIMARY KEY (id_receita, id_ingrediente),

    CONSTRAINT fk_receitaingred_receita
    FOREIGN KEY (id_receita)
    REFERENCES receita(id),

    CONSTRAINT fk_receitaingred_ingrediente
    FOREIGN KEY (id_ingrediente)
    REFERENCES ingrediente(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE receita_prod_distrib (
    id_receita INT(11) NOT NULL,
    id_produto INT(11) NOT NULL,
    quantidade DECIMAL(10,3) NOT NULL,

    PRIMARY KEY (id_receita, id_produto),

    CONSTRAINT fk_receitaprod_receita
    FOREIGN KEY (id_receita)
    REFERENCES receita(id),

    CONSTRAINT fk_receitaprod_produto
    FOREIGN KEY (id_produto)
    REFERENCES produto_distrib(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE pedido (
    id INT(11) NOT NULL AUTO_INCREMENT,
    id_cliente INT(11) NOT NULL,
    id_culin INT(11) NOT NULL,
    data DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('PENDENTE','CONFIRMADO','ENVIADO','ENTREGUE','CANCELADO')
    NOT NULL DEFAULT 'PENDENTE',

    PRIMARY KEY (id),

    CONSTRAINT fk_pedido_cliente
    FOREIGN KEY (id_cliente)
    REFERENCES cliente(id_usuario),

    CONSTRAINT fk_pedido_culin
    FOREIGN KEY (id_culin)
    REFERENCES culinarista(id_culin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE item_pedido (
    id_pedido INT(11) NOT NULL,
    id_produto INT(11) NOT NULL,
    quantidade DECIMAL(10,3) NOT NULL,
    preco_unit DECIMAL(10,2) NOT NULL,

    PRIMARY KEY (id_pedido, id_produto),

    CONSTRAINT fk_itempedido_pedido
    FOREIGN KEY (id_pedido)
    REFERENCES pedido(id),

    CONSTRAINT fk_itempedido_produto
    FOREIGN KEY (id_produto)
    REFERENCES produto_distrib(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE credito_cliente (
    id_cliente INT(11) NOT NULL,
    id_culin INT(11) NOT NULL,
    limite DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    utilizado DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id_cliente),

    CONSTRAINT fk_credito_cliente
    FOREIGN KEY (id_cliente)
    REFERENCES cliente(id_usuario),

    CONSTRAINT fk_credito_culin
    FOREIGN KEY (id_culin)
    REFERENCES culinarista(id_culin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE visita (
    id INT(11) NOT NULL AUTO_INCREMENT,
    id_cliente INT(11) NOT NULL,
    id_culin INT(11) NOT NULL,
    data DATETIME NOT NULL,
    status ENUM('AGENDADA','REALIZADA','CANCELADA')
    NOT NULL DEFAULT 'AGENDADA',
    observacao TEXT,

    PRIMARY KEY (id),

    CONSTRAINT fk_visita_cliente
    FOREIGN KEY (id_cliente)
    REFERENCES cliente(id_usuario),

    CONSTRAINT fk_visita_culin
    FOREIGN KEY (id_culin)
    REFERENCES culinarista(id_culin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE relatorio (
    id INT(11) NOT NULL AUTO_INCREMENT,
    id_culin INT(11) NOT NULL,
    descricao TEXT NOT NULL,
    data DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    CONSTRAINT fk_relatorio_culin
    FOREIGN KEY (id_culin)
    REFERENCES culinarista(id_culin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DELIMITER $$

CREATE FUNCTION calcular_custo_receita(receita_id INT)
RETURNS DECIMAL(10,2)
DETERMINISTIC
BEGIN

    DECLARE total DECIMAL(10,2);

    SELECT
        IFNULL(SUM(ri.quantidade * i.preco),0)
    INTO total
    FROM receita_ingrediente ri
    JOIN ingrediente i
    ON i.id = ri.id_ingrediente
    WHERE ri.id_receita = receita_id;

    SET total = total + IFNULL((
        SELECT SUM(rp.quantidade * pd.preco)
        FROM receita_prod_distrib rp
        JOIN produto_distrib pd
        ON pd.id = rp.id_produto
        WHERE rp.id_receita = receita_id
    ),0);

    RETURN total;

END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER trg_custo_ingrediente
AFTER INSERT ON receita_ingrediente
FOR EACH ROW
BEGIN

    UPDATE receita
    SET custo = calcular_custo_receita(NEW.id_receita)
    WHERE id = NEW.id_receita;

END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER trg_custo_produto
AFTER INSERT ON receita_prod_distrib
FOR EACH ROW
BEGIN

    UPDATE receita
    SET custo = calcular_custo_receita(NEW.id_receita)
    WHERE id = NEW.id_receita;

END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER trg_credito_confirmar
AFTER UPDATE ON pedido
FOR EACH ROW
BEGIN

    DECLARE total DECIMAL(10,2);

    IF NEW.status = 'CONFIRMADO'
    AND OLD.status != 'CONFIRMADO' THEN

        SELECT SUM(quantidade * preco_unit)
        INTO total
        FROM item_pedido
        WHERE id_pedido = NEW.id;

        UPDATE credito_cliente
        SET utilizado = utilizado + total
        WHERE id_cliente = NEW.id_cliente;

    END IF;

END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER trg_credito_cancelar
AFTER UPDATE ON pedido
FOR EACH ROW
BEGIN

    DECLARE total DECIMAL(10,2);

    IF NEW.status = 'CANCELADO'
    AND OLD.status = 'CONFIRMADO' THEN

        SELECT SUM(quantidade * preco_unit)
        INTO total
        FROM item_pedido
        WHERE id_pedido = NEW.id;

        UPDATE credito_cliente
        SET utilizado = utilizado - total
        WHERE id_cliente = NEW.id_cliente;

    END IF;

END$$

DELIMITER ;

COMMIT;

-- Ajustes para bancos ja importados antes desta versao:
-- permite cliente sem culinarista vinculado no momento do cadastro
-- e cria os registros de perfil para usuarios existentes.
ALTER TABLE cliente
MODIFY id_culin INT(11) NULL;

INSERT IGNORE INTO culinarista (id_culin)
SELECT id
FROM usuario
WHERE tipo = 'CULINARISTA';

INSERT IGNORE INTO cliente (id_usuario, id_culin)
SELECT id, NULL
FROM usuario
WHERE tipo = 'CLIENTE';

-- Usuario inicial para teste do login.
-- Email: admin@pi.com
-- Senha: 12345678
INSERT IGNORE INTO usuario (nome, email, senha, tipo)
VALUES ('Administrador', 'admin@pi.com', '12345678', 'CULINARISTA');

INSERT IGNORE INTO culinarista (id_culin)
SELECT id
FROM usuario
WHERE email = 'admin@pi.com';
