-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 03/10/2026 às 16:35
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `pi`
--
CREATE DATABASE IF NOT EXISTS `pi` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `pi`;

DELIMITER $$
--
-- Funções
--
DROP FUNCTION IF EXISTS `calcular_custo_receita`$$
CREATE DEFINER=`php`@`localhost` FUNCTION `calcular_custo_receita` (`receita_id` INT) RETURNS DECIMAL(10,2) DETERMINISTIC BEGIN
    DECLARE total DECIMAL(10,2);

    -- Soma dos ingredientes genéricos
    SELECT IFNULL(SUM(ri.quantidade * i.preco), 0)
    INTO total
    FROM receita_ingrediente ri
    JOIN ingrediente i ON i.id = ri.id_ingrediente
    WHERE ri.id_receita = receita_id;

    -- Adiciona soma dos produtos do distribuidor
    SET total = total + IFNULL((
        SELECT SUM(rp.quantidade * pd.preco)
        FROM receita_prod_distrib rp
        JOIN produto_distrib pd ON pd.id = rp.id_produto
        WHERE rp.id_receita = receita_id
    ), 0);

    RETURN total;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria`
--

DROP TABLE IF EXISTS `categoria`;
CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categoria`
--

INSERT INTO `categoria` (`id`, `nome`) VALUES
(5, 'Bebidas'),
(1, 'Massas'),
(3, 'Molhos e Bases'),
(4, 'Panificação'),
(2, 'Sobremesas');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

DROP TABLE IF EXISTS `cliente`;
CREATE TABLE `cliente` (
  `id_usuario` int(11) NOT NULL,
  `id_culin` int(11) DEFAULT NULL,
  `segmento` varchar(100) DEFAULT NULL,
  `localidade` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`id_usuario`, `id_culin`, `segmento`, `localidade`) VALUES
(5, 2, 'Panificação', 'Marília - SP'),
(6, 3, 'Alimentação', 'Marília - SP'),
(7, 2, 'Confeitaria', 'Garça - SP'),
(8, 3, 'Gastronomia', 'Marília - SP'),
(9, 3, 'Cafeteria', 'Vera Cruz - SP'),
(10, 4, 'Eventos', 'Pompeia - SP');

-- --------------------------------------------------------

--
-- Estrutura para tabela `culinarista`
--

DROP TABLE IF EXISTS `culinarista`;
CREATE TABLE `culinarista` (
  `id_culin` int(11) NOT NULL,
  `setor` varchar(150) DEFAULT NULL,
  `segmento` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `culinarista`
--

INSERT INTO `culinarista` (`id_culin`, `setor`, `segmento`) VALUES
(2, 'Sul de SP', 'Panificação e Confeitaria'),
(3, 'Centro-Oeste', 'Restaurantes e Bistrôs'),
(4, 'Norte de SP', 'Catering e Buffets');

-- --------------------------------------------------------

--
-- Estrutura para tabela `ingrediente`
--

DROP TABLE IF EXISTS `ingrediente`;
CREATE TABLE `ingrediente` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `unidade` varchar(20) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `atualizado_em` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `ingrediente`
--

INSERT INTO `ingrediente` (`id`, `nome`, `unidade`, `preco`, `atualizado_em`) VALUES
(1, 'Farinha de Trigo', 'kg', 4.50, '2026-06-29 00:46:12'),
(2, 'Açúcar Refinado', 'kg', 3.80, '2026-06-29 00:46:12'),
(3, 'Ovos', 'un', 0.75, '2026-06-29 00:46:12'),
(4, 'Manteiga', 'kg', 28.00, '2026-06-29 00:46:12'),
(5, 'Leite Integral', 'L', 4.20, '2026-06-29 00:46:12'),
(6, 'Fermento Biológico', 'g', 0.08, '2026-06-29 00:46:12'),
(7, 'Sal', 'kg', 2.00, '2026-06-29 00:46:12'),
(8, 'Chocolate em Pó', 'kg', 22.00, '2026-06-29 00:46:12');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produto_distrib`
--

DROP TABLE IF EXISTS `produto_distrib`;
CREATE TABLE `produto_distrib` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `unidade` varchar(20) NOT NULL,
  `estoque` decimal(10,3) NOT NULL DEFAULT 0.000,
  `preco` decimal(10,2) NOT NULL,
  `atualizado_em` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produto_distrib`
--

INSERT INTO `produto_distrib` (`id`, `nome`, `unidade`, `estoque`, `preco`, `atualizado_em`) VALUES
(1, 'Nestlé Creme de Leite 200g', 'un', 120.000, 4.90, '2026-06-29 00:46:12'),
(2, 'Nestlé Leite Condensado 395g', 'un', 85.000, 6.50, '2026-06-29 00:46:12'),
(3, 'Nestlé Chocolate Meio Amargo 1kg', 'kg', 40.000, 38.00, '2026-06-29 00:46:12'),
(4, 'Nestlé Farinha Láctea 400g', 'un', 60.000, 7.20, '2026-06-29 00:46:12'),
(5, 'Nestlé Nescau 400g', 'un', 55.000, 9.80, '2026-06-29 00:46:12'),
(6, 'Nestlé Baunilha Extrato 30ml', 'un', 90.000, 3.50, '2026-06-29 00:46:12'),
(7, 'Nestlé Achocolatado 200ml', 'un', 200.000, 2.80, '2026-06-29 00:46:12');

-- --------------------------------------------------------

--
-- Estrutura para tabela `receita`
--

DROP TABLE IF EXISTS `receita`;
CREATE TABLE `receita` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `rendimento` varchar(50) DEFAULT NULL,
  `tempo_preparo` int(11) DEFAULT NULL,
  `visibilidade` enum('PUBLICA','PRIVADA') NOT NULL DEFAULT 'PRIVADA',
  `id_categoria` int(11) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `receita`
--

INSERT INTO `receita` (`id`, `nome`, `descricao`, `rendimento`, `tempo_preparo`, `visibilidade`, `id_categoria`, `id_usuario`, `criado_em`) VALUES
(1, 'Pão Francês Artesanal', 'Pão crocante por fora e macio por dentro.', '20 unidades', 90, 'PUBLICA', 4, 2, '2026-06-29 00:46:12'),
(2, 'Bolo de Chocolate Intenso', 'Bolo úmido com cobertura de ganache.', '12 fatias', 60, 'PUBLICA', 2, 3, '2026-06-29 00:46:12'),
(3, 'Molho Bechamel Clássico', 'Base para lasanha e massas gratinadas.', '500ml', 20, 'PUBLICA', 3, 3, '2026-06-29 00:46:12'),
(4, 'Croissant Amanteigado', 'Massa folhada com camadas bem definidas.', '10 unidades', 180, 'PRIVADA', 4, 2, '2026-06-29 00:46:12'),
(5, 'Brownie Nestlé', 'Brownie denso usando chocolate meio amargo.', '16 pedaços', 45, 'PUBLICA', 2, 4, '2026-06-29 00:46:12'),
(6, 'Lasanha Bolonhesa', 'Massa fresca com molho de carne e bechamel.', '8 porções', 120, 'PUBLICA', 1, 3, '2026-06-29 00:46:12'),
(7, 'Cappuccino Cremoso', 'Receita interna de cappuccino com achocolatado.', '1 porção', 10, 'PRIVADA', 5, 5, '2026-06-29 00:46:12'),
(8, 'Pão de Mel', 'Pão de mel recheado com doce de leite condensado.', '24 unidades', 50, 'PUBLICA', 2, 7, '2026-06-29 00:46:12');

-- --------------------------------------------------------

--
-- Estrutura para tabela `receita_ingrediente`
--

DROP TABLE IF EXISTS `receita_ingrediente`;
CREATE TABLE `receita_ingrediente` (
  `id_receita` int(11) NOT NULL,
  `id_ingrediente` int(11) NOT NULL,
  `quantidade` decimal(10,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `receita_ingrediente`
--

INSERT INTO `receita_ingrediente` (`id_receita`, `id_ingrediente`, `quantidade`) VALUES
(1, 1, 0.500),
(1, 6, 10.000),
(1, 7, 0.010),
(2, 1, 0.300),
(2, 2, 0.250),
(2, 3, 3.000),
(2, 4, 0.100),
(2, 8, 0.100),
(3, 1, 0.050),
(3, 4, 0.050),
(3, 5, 0.500),
(4, 1, 0.500),
(4, 2, 0.050),
(4, 4, 0.250),
(4, 6, 5.000),
(5, 2, 0.200),
(5, 3, 2.000),
(5, 4, 0.100),
(6, 1, 0.200),
(6, 3, 2.000),
(6, 7, 0.005),
(7, 5, 0.150),
(8, 1, 0.300),
(8, 2, 0.150),
(8, 3, 2.000),
(8, 8, 0.050);

-- --------------------------------------------------------

--
-- Estrutura para tabela `receita_prod_distrib`
--

DROP TABLE IF EXISTS `receita_prod_distrib`;
CREATE TABLE `receita_prod_distrib` (
  `id_receita` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `quantidade` decimal(10,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `receita_prod_distrib`
--

INSERT INTO `receita_prod_distrib` (`id_receita`, `id_produto`, `quantidade`) VALUES
(2, 3, 0.200),
(5, 3, 0.300),
(7, 5, 0.020),
(7, 6, 0.005),
(8, 2, 0.395);

-- --------------------------------------------------------

--
-- Estrutura para tabela `relatorio`
--

DROP TABLE IF EXISTS `relatorio`;
CREATE TABLE `relatorio` (
  `id` int(11) NOT NULL,
  `id_culin` int(11) NOT NULL,
  `descricao` text NOT NULL,
  `data` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `relatorio`
--

INSERT INTO `relatorio` (`id`, `id_culin`, `descricao`, `data`) VALUES
(1, 2, 'Região Sul de SP: 2 clientes visitados em maio. Padaria Estrela e Confeitaria Doce demonstraram interesse em ampliar pedidos de chocolates. Próximas visitas agendadas para junho.', '2026-05-30 08:00:00'),
(2, 3, 'Centro-Oeste: bom desempenho no mês. Restaurante Sabor e Bistrô Central realizaram pedidos expressivos. Café do Porto ainda em fase de prospecção.', '2026-05-30 09:00:00'),
(3, 4, 'Norte de SP: Buffet Alegria cancelou pedido de grande volume. Necessário retomar contato e entender objeções antes de nova proposta.', '2026-05-30 10:00:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('ADMIN','CULINARISTA','CLIENTE') NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nome`, `email`, `senha`, `tipo`, `ativo`, `criado_em`) VALUES
(1, 'Administrador', 'admin@pi.com', '$2y$10$xm0wZBB1VY8hQ7KrTR2NAumHx.MfcqmYm/dC7U/adaLgFOihlAmbm', 'ADMIN', 1, '2026-06-29 00:46:12'),
(2, 'Carlos Mendes', 'carlos@pi.com', '$2y$10$nl7lK70iB/zR0ugFbcbxG.8jkLYlXLLpzfm1K1GvlOV7NRAr1f5QW', 'CULINARISTA', 1, '2026-06-29 00:46:12'),
(3, 'Fernanda Lima', 'fernanda@pi.com', '$2b$10$j9vZgwl.V9MNMFiZ/FwnxuYyxmMJ7xk3JsHfxZ0ODduI5gzTUI60G', 'CULINARISTA', 1, '2026-06-29 00:46:12'),
(4, 'Roberto Souza', 'roberto@pi.com', '$2b$10$j9vZgwl.V9MNMFiZ/FwnxuYyxmMJ7xk3JsHfxZ0ODduI5gzTUI60G', 'CULINARISTA', 1, '2026-06-29 00:46:12'),
(5, 'Padaria Estrela', 'estrela@pi.com', '$2y$10$V3Poa6ACLxdZKEIVuS9VCusmmClgv8x4ZTY/5nhgXBnDdWm8qIkAu', 'CLIENTE', 1, '2026-06-29 00:46:12'),
(6, 'Restaurante Sabor', 'sabor@pi.com', '$2b$10$j9vZgwl.V9MNMFiZ/FwnxuYyxmMJ7xk3JsHfxZ0ODduI5gzTUI60G', 'CLIENTE', 1, '2026-06-29 00:46:12'),
(7, 'Confeitaria Doce', 'doce@pi.com', '$2b$10$j9vZgwl.V9MNMFiZ/FwnxuYyxmMJ7xk3JsHfxZ0ODduI5gzTUI60G', 'CLIENTE', 1, '2026-06-29 00:46:12'),
(8, 'Bistrô Central', 'bistro@pi.com', '$2b$10$j9vZgwl.V9MNMFiZ/FwnxuYyxmMJ7xk3JsHfxZ0ODduI5gzTUI60G', 'CLIENTE', 1, '2026-06-29 00:46:12'),
(9, 'Café do Porto', 'porto@pi.com', '$2b$10$j9vZgwl.V9MNMFiZ/FwnxuYyxmMJ7xk3JsHfxZ0ODduI5gzTUI60G', 'CLIENTE', 1, '2026-06-29 00:46:12'),
(10, 'Buffet Alegria', 'alegria@pi.com', '$2b$10$j9vZgwl.V9MNMFiZ/FwnxuYyxmMJ7xk3JsHfxZ0ODduI5gzTUI60G', 'CLIENTE', 1, '2026-06-29 00:46:12');

-- --------------------------------------------------------

--
-- Estrutura para tabela `visita`
--

DROP TABLE IF EXISTS `visita`;
CREATE TABLE `visita` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_culin` int(11) NOT NULL,
  `data` datetime NOT NULL,
  `status` enum('AGENDADA','REALIZADA','CANCELADA') NOT NULL DEFAULT 'AGENDADA',
  `observacao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `visita`
--

INSERT INTO `visita` (`id`, `id_cliente`, `id_culin`, `data`, `status`, `observacao`) VALUES
(1, 5, 2, '2026-05-08 10:00:00', 'REALIZADA', 'Apresentação do catálogo de produtos Nestlé.'),
(2, 6, 3, '2026-05-11 14:00:00', 'REALIZADA', 'Cliente interessado em linha de chocolates.'),
(3, 7, 2, '2026-05-14 09:30:00', 'REALIZADA', 'Degustação de produtos realizada com sucesso.'),
(4, 8, 3, '2026-05-19 11:00:00', 'AGENDADA', 'Visita de acompanhamento pós-pedido.'),
(5, 9, 3, '2026-05-21 15:00:00', 'AGENDADA', 'Apresentação inicial ao cliente.'),
(6, 10, 4, '2026-05-23 10:00:00', 'CANCELADA', 'Cliente cancelou por indisponibilidade.');

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_receitas_custo`
-- (Veja abaixo para a visão atual)
--
DROP VIEW IF EXISTS `vw_receitas_custo`;
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
,`custo_total` decimal(10,2)
);

-- --------------------------------------------------------

--
-- Estrutura para view `vw_receitas_custo`
--
DROP TABLE IF EXISTS `vw_receitas_custo`;

DROP VIEW IF EXISTS `vw_receitas_custo`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_receitas_custo`  AS SELECT `r`.`id` AS `id`, `r`.`nome` AS `nome`, `r`.`descricao` AS `descricao`, `r`.`rendimento` AS `rendimento`, `r`.`tempo_preparo` AS `tempo_preparo`, `r`.`visibilidade` AS `visibilidade`, `r`.`id_categoria` AS `id_categoria`, `r`.`id_usuario` AS `id_usuario`, `r`.`criado_em` AS `criado_em`, `calcular_custo_receita`(`r`.`id`) AS `custo_total` FROM `receita` AS `r` ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_categoria_nome` (`nome`);

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `fk_cliente_culinarista` (`id_culin`);

--
-- Índices de tabela `culinarista`
--
ALTER TABLE `culinarista`
  ADD PRIMARY KEY (`id_culin`);

--
-- Índices de tabela `ingrediente`
--
ALTER TABLE `ingrediente`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `produto_distrib`
--
ALTER TABLE `produto_distrib`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `receita`
--
ALTER TABLE `receita`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_receita_categoria` (`id_categoria`),
  ADD KEY `fk_receita_usuario` (`id_usuario`);

--
-- Índices de tabela `receita_ingrediente`
--
ALTER TABLE `receita_ingrediente`
  ADD PRIMARY KEY (`id_receita`,`id_ingrediente`),
  ADD KEY `fk_receitaingred_ingrediente` (`id_ingrediente`);

--
-- Índices de tabela `receita_prod_distrib`
--
ALTER TABLE `receita_prod_distrib`
  ADD PRIMARY KEY (`id_receita`,`id_produto`),
  ADD KEY `fk_receitaprod_produto` (`id_produto`);

--
-- Índices de tabela `relatorio`
--
ALTER TABLE `relatorio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_relatorio_culin` (`id_culin`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_usuario_email` (`email`);

--
-- Índices de tabela `visita`
--
ALTER TABLE `visita`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_visita_cliente` (`id_cliente`),
  ADD KEY `fk_visita_culin` (`id_culin`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `ingrediente`
--
ALTER TABLE `ingrediente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `produto_distrib`
--
ALTER TABLE `produto_distrib`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `receita`
--
ALTER TABLE `receita`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `relatorio`
--
ALTER TABLE `relatorio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `visita`
--
ALTER TABLE `visita`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `fk_cliente_culinarista` FOREIGN KEY (`id_culin`) REFERENCES `culinarista` (`id_culin`),
  ADD CONSTRAINT `fk_cliente_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

--
-- Restrições para tabelas `culinarista`
--
ALTER TABLE `culinarista`
  ADD CONSTRAINT `fk_culinarista_usuario` FOREIGN KEY (`id_culin`) REFERENCES `usuario` (`id`);

--
-- Restrições para tabelas `receita`
--
ALTER TABLE `receita`
  ADD CONSTRAINT `fk_receita_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id`),
  ADD CONSTRAINT `fk_receita_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

--
-- Restrições para tabelas `receita_ingrediente`
--
ALTER TABLE `receita_ingrediente`
  ADD CONSTRAINT `fk_receitaingred_ingrediente` FOREIGN KEY (`id_ingrediente`) REFERENCES `ingrediente` (`id`),
  ADD CONSTRAINT `fk_receitaingred_receita` FOREIGN KEY (`id_receita`) REFERENCES `receita` (`id`);

--
-- Restrições para tabelas `receita_prod_distrib`
--
ALTER TABLE `receita_prod_distrib`
  ADD CONSTRAINT `fk_receitaprod_produto` FOREIGN KEY (`id_produto`) REFERENCES `produto_distrib` (`id`),
  ADD CONSTRAINT `fk_receitaprod_receita` FOREIGN KEY (`id_receita`) REFERENCES `receita` (`id`);

--
-- Restrições para tabelas `relatorio`
--
ALTER TABLE `relatorio`
  ADD CONSTRAINT `fk_relatorio_culin` FOREIGN KEY (`id_culin`) REFERENCES `culinarista` (`id_culin`);

--
-- Restrições para tabelas `visita`
--
ALTER TABLE `visita`
  ADD CONSTRAINT `fk_visita_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_usuario`),
  ADD CONSTRAINT `fk_visita_culin` FOREIGN KEY (`id_culin`) REFERENCES `culinarista` (`id_culin`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
