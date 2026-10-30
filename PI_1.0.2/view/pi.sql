SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

DELIMITER $$
CREATE DEFINER=`root`@`localhost` FUNCTION `calcular_custo_ingredientes` (`receita_id` INT) RETURNS DECIMAL(10,2) READS SQL DATA BEGIN
    DECLARE total DECIMAL(10,2);

    SELECT IFNULL(SUM(ri.quantidade * i.preco), 0)
    INTO total
    FROM receita_ingrediente ri
    JOIN ingrediente i ON i.id = ri.id_ingrediente
    WHERE ri.id_receita = receita_id;

    RETURN total;
END$$

DELIMITER ;

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categoria` (`id`, `nome`) VALUES
(5, 'Bebidas'),
(1, 'Massas'),
(3, 'Molhos e Bases'),
(4, 'Panificação'),
(2, 'Sobremesas');

CREATE TABLE `cliente` (
  `id_usuario` int(11) NOT NULL,
  `id_culin` int(11) DEFAULT NULL,
  `segmento` varchar(100) DEFAULT NULL,
  `localidade` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `cliente` (`id_usuario`, `id_culin`, `segmento`, `localidade`) VALUES
(5, 2, 'Panificação', 'Marília - SP'),
(6, 3, 'Alimentação', 'Marília - SP'),
(7, 2, 'Confeitaria', 'Garça - SP'),
(8, 3, 'Gastronomia', 'Marília - SP'),
(9, 3, 'Cafeteria', 'Vera Cruz - SP'),
(10, 4, 'Eventos', 'Pompeia - SP');

CREATE TABLE `culinarista` (
  `id_culin` int(11) NOT NULL,
  `setor` varchar(150) DEFAULT NULL,
  `segmento` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `culinarista` (`id_culin`, `setor`, `segmento`) VALUES
(2, 'Sul de SP', 'Panificação e Confeitaria'),
(3, 'Centro-Oeste', 'Restaurantes e Bistrôs'),
(4, 'Norte de SP', 'Catering e Buffets');

CREATE TABLE `ingrediente` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `unidade` varchar(20) NOT NULL,
  `preco` decimal(10,5) NOT NULL,
  `atualizado_em` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ingrediente` (`id`, `nome`, `unidade`, `preco`, `atualizado_em`) VALUES
(1, 'Farinha de Trigo', 'g', 0.00450, '2026-10-27 15:09:42'),
(2, 'Açúcar Refinado', 'g', 0.00380, '2026-10-27 15:09:42'),
(3, 'Ovos', 'unid', 0.75000, '2026-10-27 00:40:41'),
(4, 'Manteiga', 'g', 0.02800, '2026-10-27 15:09:42'),
(5, 'Leite Integral', 'ml', 0.00420, '2026-10-27 00:40:41'),
(6, 'Fermento Biológico', 'g', 0.08000, '2026-06-29 00:46:12'),
(7, 'Sal', 'g', 0.00200, '2026-10-27 15:09:42'),
(8, 'Chocolate em Pó', 'g', 0.02200, '2026-10-27 15:09:42');

CREATE TABLE `produto_distrib` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `unidade` varchar(20) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `criado` datetime NOT NULL DEFAULT current_timestamp(),
  `quantidade` decimal(10,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `produto_distrib` (`id`, `nome`, `unidade`, `preco`, `criado`, `quantidade`) VALUES
(1, 'Nestlé Creme de Leite 200g', 'g', 4.90, '2026-10-23 16:16:59', 0.200),
(2, 'Nestlé Leite Condensado 395g', 'g', 6.50, '2026-10-23 16:16:59', 0.395),
(3, 'Nestlé Chocolate Meio Amargo 1kg', 'g', 38.00, '2026-10-23 16:16:59', 1.000),
(4, 'Nestlé Farinha Láctea 400g', 'g', 7.20, '2026-10-23 16:16:59', 0.400),
(5, 'Nestlé Nescau 400g', 'g', 9.80, '2026-10-23 16:16:59', 0.400),
(6, 'Nestlé Baunilha Extrato 30ml', 'l', 3.50, '2026-10-23 16:16:59', 0.030),
(7, 'Nestlé Achocolatado 200ml', 'l', 2.80, '2026-10-23 16:16:59', 0.200);

CREATE TABLE `receita` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `rendimento` varchar(50) DEFAULT NULL,
  `tempo_preparo` int(11) DEFAULT NULL,
  `dificuldade` enum('facil','medio','dificil') DEFAULT NULL,
  `visibilidade` enum('PUBLICA','PRIVADA') NOT NULL DEFAULT 'PRIVADA',
  `id_categoria` int(11) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `receita` (`id`, `nome`, `descricao`, `imagem`, `rendimento`, `tempo_preparo`, `dificuldade`, `visibilidade`, `id_categoria`, `id_usuario`, `criado_em`) VALUES
(1, 'Pão Francês Artesanal', 'Pão crocante por fora e macio por dentro.', NULL, '20 unidades', 90, NULL, 'PUBLICA', 4, 2, '2026-06-29 00:46:12'),
(2, 'Bolo de Chocolate Intenso', 'Bolo úmido com cobertura de ganache.', NULL, '12 fatias', 60, NULL, 'PUBLICA', 2, 3, '2026-06-29 00:46:12'),
(3, 'Molho Bechamel Clássico', 'Base para lasanha e massas gratinadas.', NULL, '500ml', 20, NULL, 'PUBLICA', 3, 3, '2026-06-29 00:46:12'),
(4, 'Croissant Amanteigado', 'Massa folhada com camadas bem definidas.', NULL, '10 unidades', 180, NULL, 'PRIVADA', 4, 2, '2026-06-29 00:46:12'),
(5, 'Brownie Nestlé', 'Brownie denso usando chocolate meio amargo.', NULL, '16 pedaços', 45, NULL, 'PUBLICA', 2, 4, '2026-06-29 00:46:12'),
(6, 'Lasanha Bolonhesa', 'Massa fresca com molho de carne e bechamel.', NULL, '8 porções', 120, NULL, 'PUBLICA', 1, 3, '2026-06-29 00:46:12'),
(7, 'Cappuccino Cremoso', 'Receita interna de cappuccino com achocolatado.', NULL, '1 porção', 10, NULL, 'PRIVADA', 5, 5, '2026-06-29 00:46:12'),
(8, 'Pão de Mel', 'Pão de mel recheado com doce de leite condensado.', NULL, '24 unidades', 50, NULL, 'PUBLICA', 2, 7, '2026-06-29 00:46:12'),
(11, 'bolo', 'ddsdd', NULL, '3', 34, NULL, 'PRIVADA', 2, 2, '2026-10-05 01:04:49'),
(12, 'bolo de ovo', 'É um bolo de ovo', NULL, '5', 20, NULL, 'PRIVADA', 5, 2, '2026-10-09 16:07:18'),
(13, 'bolo', 'dad', NULL, '7', 43, NULL, 'PRIVADA', 2, 2, '2026-10-10 01:24:13'),
(14, 'bolo', 'SDFS', 'uploads/receitas/00f1c221255d15263831002026d0e601.jpg', '4', 54, 'dificil', 'PUBLICA', 5, 2, '2026-10-10 01:44:12'),
(15, 'Teste', 'Gravando uma receita', NULL, '1', 15, 'facil', 'PRIVADA', 3, 2, '2026-10-26 22:44:31'),
(16, 'Teste valor', 'valor', NULL, '1', 10, 'facil', 'PRIVADA', 5, 2, '2026-10-26 22:46:26');

CREATE TABLE `receita_ingrediente` (
  `id_receita` int(11) NOT NULL,
  `id_ingrediente` int(11) NOT NULL,
  `quantidade` decimal(10,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `receita_ingrediente` (`id_receita`, `id_ingrediente`, `quantidade`) VALUES
(1, 1, 500.000),
(1, 6, 10.000),
(1, 7, 10.000),
(2, 1, 300.000),
(2, 2, 250.000),
(2, 3, 3.000),
(2, 4, 100.000),
(2, 8, 100.000),
(3, 1, 50.000),
(3, 4, 50.000),
(3, 5, 500.000),
(4, 1, 500.000),
(4, 2, 50.000),
(4, 4, 250.000),
(4, 6, 5.000),
(5, 2, 200.000),
(5, 3, 2.000),
(5, 4, 100.000),
(6, 1, 200.000),
(6, 3, 2.000),
(6, 7, 5.000),
(7, 5, 150.000),
(8, 1, 300.000),
(8, 2, 150.000),
(8, 3, 2.000),
(8, 8, 50.000),
(13, 2, 200.000),
(15, 6, 0.020);

CREATE TABLE `receita_prod_distrib` (
  `id_receita` int(11) NOT NULL,
  `id_produto` char(24) NOT NULL,
  `quantidade` decimal(10,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `receita_prod_distrib` (`id_receita`, `id_produto`, `quantidade`) VALUES
(2, '3', 0.200),
(5, '3', 0.300),
(7, '5', 0.020),
(7, '6', 0.005),
(8, '2', 0.395),
(12, '2', 0.400),
(12, '4', 150.000),
(12, '7', 200.000),
(13, '6', 149.998),
(14, '3', 10000.000),
(14, '4', 100.000),
(15, '6add781fddc331553c650ae2', 0.200),
(16, '6add781fddc331553c650ae2', 3.000);

CREATE TABLE `relatorio` (
  `id` int(11) NOT NULL,
  `id_culin` int(11) NOT NULL,
  `descricao` text NOT NULL,
  `data` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `relatorio` (`id`, `id_culin`, `descricao`, `data`) VALUES
(1, 2, 'Região Sul de SP: 2 clientes visitados em maio. Padaria Estrela e Confeitaria Doce demonstraram interesse em ampliar pedidos de chocolates. Próximas visitas agendadas para junho.', '2026-05-30 08:00:00'),
(2, 3, 'Centro-Oeste: bom desempenho no mês. Restaurante Sabor e Bistrô Central realizaram pedidos expressivos. Café do Porto ainda em fase de prospecção.', '2026-05-30 09:00:00'),
(3, 4, 'Norte de SP: Buffet Alegria cancelou pedido de grande volume. Necessário retomar contato e entender objeções antes de nova proposta.', '2026-05-30 10:00:00');

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('ADMIN','CULINARISTA','CLIENTE') NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `usuario` (`id`, `nome`, `email`, `senha`, `tipo`, `ativo`, `criado_em`) VALUES
(1, 'Administrador', 'admin@pi.com', '$2y$10$xm0wZBB1VY8hQ7KrTR2NAumHx.MfcqmYm/dC7U/adaLgFOihlAmbm', 'ADMIN', 1, '2026-06-29 00:46:12'),
(2, 'Carlos Mendes', 'carlos@pi.com', '$2y$10$RDfA.KsSn0iFH7/Z8/TqJOIGCAh6LN5eHij4/cBGi07KTQLlq1GK6', 'CULINARISTA', 1, '2026-06-29 00:46:12'),
(3, 'Fernanda Lima', 'fernanda@pi.com', '$2b$10$j9vZgwl.V9MNMFiZ/FwnxuYyxmMJ7xk3JsHfxZ0ODduI5gzTUI60G', 'CULINARISTA', 1, '2026-06-29 00:46:12'),
(4, 'Roberto Souza', 'roberto@pi.com', '$2b$10$j9vZgwl.V9MNMFiZ/FwnxuYyxmMJ7xk3JsHfxZ0ODduI5gzTUI60G', 'CULINARISTA', 1, '2026-06-29 00:46:12'),
(5, 'Padaria Estrela', 'estrela@pi.com', '$2y$10$V3Poa6ACLxdZKEIVuS9VCusmmClgv8x4ZTY/5nhgXBnDdWm8qIkAu', 'CLIENTE', 1, '2026-06-29 00:46:12'),
(6, 'Restaurante Sabor', 'sabor@pi.com', '$2b$10$j9vZgwl.V9MNMFiZ/FwnxuYyxmMJ7xk3JsHfxZ0ODduI5gzTUI60G', 'CLIENTE', 1, '2026-06-29 00:46:12'),
(7, 'Confeitaria Doce', 'doce@pi.com', '$2b$10$j9vZgwl.V9MNMFiZ/FwnxuYyxmMJ7xk3JsHfxZ0ODduI5gzTUI60G', 'CLIENTE', 1, '2026-06-29 00:46:12'),
(8, 'Bistrô Central', 'bistro@pi.com', '$2b$10$j9vZgwl.V9MNMFiZ/FwnxuYyxmMJ7xk3JsHfxZ0ODduI5gzTUI60G', 'CLIENTE', 1, '2026-06-29 00:46:12'),
(9, 'Café do Porto', 'porto@pi.com', '$2b$10$j9vZgwl.V9MNMFiZ/FwnxuYyxmMJ7xk3JsHfxZ0ODduI5gzTUI60G', 'CLIENTE', 1, '2026-06-29 00:46:12'),
(10, 'Buffet Alegria', 'alegria@pi.com', '$2b$10$j9vZgwl.V9MNMFiZ/FwnxuYyxmMJ7xk3JsHfxZ0ODduI5gzTUI60G', 'CLIENTE', 1, '2026-06-29 00:46:12');

CREATE TABLE `visita` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_culin` int(11) NOT NULL,
  `data` datetime NOT NULL,
  `status` enum('AGENDADA','REALIZADA','CANCELADA') NOT NULL DEFAULT 'AGENDADA',
  `observacao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `visita` (`id`, `id_cliente`, `id_culin`, `data`, `status`, `observacao`) VALUES
(1, 5, 2, '2026-05-08 10:00:00', 'REALIZADA', 'Apresentação do catálogo de produtos Nestlé.'),
(2, 6, 3, '2026-05-11 14:00:00', 'REALIZADA', 'Cliente interessado em linha de chocolates.'),
(3, 7, 2, '2026-05-14 09:30:00', 'REALIZADA', 'Degustação de produtos realizada com sucesso.'),
(4, 8, 3, '2026-05-19 11:00:00', 'AGENDADA', 'Visita de acompanhamento pós-pedido.'),
(5, 9, 3, '2026-05-21 15:00:00', 'AGENDADA', 'Apresentação inicial ao cliente.'),
(6, 10, 4, '2026-05-23 10:00:00', 'CANCELADA', 'Cliente cancelou por indisponibilidade.');
CREATE TABLE `vw_receitas_custo` (
`id` int(11)
,`nome` varchar(100)
,`descricao` text
,`rendimento` varchar(50)
,`tempo_preparo` int(11)
,`visibilidade` enum('PUBLICA','PRIVADA')
,`id_categoria` int(11)
,`id_usuario` int(11)
,`criado_em` datetime
,`custo_ingredientes` decimal(10,2)
);
DROP TABLE IF EXISTS `vw_receitas_custo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_receitas_custo`  AS SELECT `r`.`id` AS `id`, `r`.`nome` AS `nome`, `r`.`descricao` AS `descricao`, `r`.`rendimento` AS `rendimento`, `r`.`tempo_preparo` AS `tempo_preparo`, `r`.`visibilidade` AS `visibilidade`, `r`.`id_categoria` AS `id_categoria`, `r`.`id_usuario` AS `id_usuario`, `r`.`criado_em` AS `criado_em`, `calcular_custo_ingredientes`(`r`.`id`) AS `custo_ingredientes` FROM `receita` AS `r` ;


ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_categoria_nome` (`nome`);

ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `fk_cliente_culinarista` (`id_culin`);

ALTER TABLE `culinarista`
  ADD PRIMARY KEY (`id_culin`);

ALTER TABLE `ingrediente`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `produto_distrib`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `receita`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_receita_categoria` (`id_categoria`),
  ADD KEY `fk_receita_usuario` (`id_usuario`);

ALTER TABLE `receita_ingrediente`
  ADD PRIMARY KEY (`id_receita`,`id_ingrediente`),
  ADD KEY `fk_receitaingred_ingrediente` (`id_ingrediente`);

ALTER TABLE `receita_prod_distrib`
  ADD PRIMARY KEY (`id_receita`,`id_produto`),
  ADD KEY `fk_receitaprod_produto` (`id_produto`);

ALTER TABLE `relatorio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_relatorio_culin` (`id_culin`);

ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_usuario_email` (`email`);

ALTER TABLE `visita`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_visita_cliente` (`id_cliente`),
  ADD KEY `fk_visita_culin` (`id_culin`);


ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `ingrediente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `produto_distrib`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `receita`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

ALTER TABLE `relatorio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `visita`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;


ALTER TABLE `cliente`
  ADD CONSTRAINT `fk_cliente_culinarista` FOREIGN KEY (`id_culin`) REFERENCES `culinarista` (`id_culin`),
  ADD CONSTRAINT `fk_cliente_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

ALTER TABLE `culinarista`
  ADD CONSTRAINT `fk_culinarista_usuario` FOREIGN KEY (`id_culin`) REFERENCES `usuario` (`id`);

ALTER TABLE `receita`
  ADD CONSTRAINT `fk_receita_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id`),
  ADD CONSTRAINT `fk_receita_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

ALTER TABLE `receita_ingrediente`
  ADD CONSTRAINT `fk_receitaingred_ingrediente` FOREIGN KEY (`id_ingrediente`) REFERENCES `ingrediente` (`id`),
  ADD CONSTRAINT `fk_receitaingred_receita` FOREIGN KEY (`id_receita`) REFERENCES `receita` (`id`);

ALTER TABLE `receita_prod_distrib`
  ADD CONSTRAINT `fk_receitaprod_receita` FOREIGN KEY (`id_receita`) REFERENCES `receita` (`id`);

ALTER TABLE `relatorio`
  ADD CONSTRAINT `fk_relatorio_culin` FOREIGN KEY (`id_culin`) REFERENCES `culinarista` (`id_culin`);

ALTER TABLE `visita`
  ADD CONSTRAINT `fk_visita_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_usuario`),
  ADD CONSTRAINT `fk_visita_culin` FOREIGN KEY (`id_culin`) REFERENCES `culinarista` (`id_culin`);
COMMIT;


