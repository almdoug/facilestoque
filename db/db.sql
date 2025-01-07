
CREATE TABLE IF NOT EXISTS `categorias` (
  `id_categoria` int NOT NULL AUTO_INCREMENT,
  `id_comercio` int DEFAULT NULL,
  `nome` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_categoria`),
  KEY `FK_categorias_comercios` (`id_comercio`),
  CONSTRAINT `FK_categorias_comercios` FOREIGN KEY (`id_comercio`) REFERENCES `comercios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela facilestoque.categorias: ~4 rows (aproximadamente)
INSERT INTO `categorias` (`id_categoria`, `id_comercio`, `nome`) VALUES
	(1, 1, 'Alimentos'),
	(2, 1, 'Produtos de limpeza'),
	(3, 1, 'Ferramentas'),
	(4, 1, 'Tintas');

-- Copiando estrutura para tabela facilestoque.comercios
CREATE TABLE IF NOT EXISTS `comercios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `endereco` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela facilestoque.comercios: ~1 rows (aproximadamente)
INSERT INTO `comercios` (`id`, `nome`, `endereco`, `telefone`, `email`, `data_criacao`) VALUES
	(1, 'comercio1', 'rua A', '8802', 'comercio1@gmail.com', '2024-10-11 14:57:40');

-- Copiando estrutura para tabela facilestoque.movimentacoes
CREATE TABLE IF NOT EXISTS `movimentacoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `comercio_id` int DEFAULT NULL,
  `produto_id` int DEFAULT NULL,
  `tipo` enum('entrada','saida') COLLATE utf8mb4_general_ci NOT NULL,
  `quantidade` int NOT NULL,
  `data_movimentacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `comercio_id` (`comercio_id`),
  KEY `produto_id` (`produto_id`),
  CONSTRAINT `movimentacoes_ibfk_1` FOREIGN KEY (`comercio_id`) REFERENCES `comercios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `movimentacoes_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela facilestoque.movimentacoes: ~16 rows (aproximadamente)
INSERT INTO `movimentacoes` (`id`, `comercio_id`, `produto_id`, `tipo`, `quantidade`, `data_movimentacao`) VALUES
	(1, 1, 6, 'saida', 20, '2025-01-03 17:46:47'),
	(2, 1, 6, 'saida', 20, '2025-01-03 17:47:08'),
	(3, 1, 6, 'entrada', 20, '2025-01-03 17:48:15'),
	(4, 1, 6, 'saida', 100, '2025-01-07 15:23:44'),
	(5, 1, 7, 'saida', 100, '2025-01-07 15:56:22'),
	(6, 1, 8, 'saida', 100, '2025-01-07 15:56:25'),
	(7, 1, 9, 'saida', 100, '2025-01-07 15:56:28'),
	(8, 1, 10, 'saida', 100, '2025-01-07 15:56:32'),
	(9, 1, 11, 'saida', 100, '2025-01-07 15:56:39'),
	(10, 1, 12, 'saida', 100, '2025-01-07 15:56:43'),
	(11, 1, 13, 'saida', 100, '2025-01-07 15:56:47'),
	(12, 1, 14, 'saida', 100, '2025-01-07 15:56:51'),
	(13, 1, 15, 'saida', 100, '2025-01-07 15:56:55'),
	(14, 1, 6, 'saida', 100, '2025-01-07 17:25:34'),
	(15, 1, 6, 'saida', 100, '2025-01-07 18:39:04'),
	(16, 1, 6, 'saida', 100, '2025-01-07 18:44:30');

-- Copiando estrutura para tabela facilestoque.produtos
CREATE TABLE IF NOT EXISTS `produtos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `comercio_id` int DEFAULT NULL,
  `id_categoria` int DEFAULT NULL,
  `nome` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `quantidade` int DEFAULT '0',
  `preco` decimal(10,2) DEFAULT NULL,
  `validade` date DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `imagem` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comercio_id` (`comercio_id`),
  KEY `FK_produtos_categorias` (`id_categoria`),
  CONSTRAINT `FK_produtos_categorias` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`),
  CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`comercio_id`) REFERENCES `comercios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela facilestoque.produtos: ~10 rows (aproximadamente)
INSERT INTO `produtos` (`id`, `comercio_id`, `id_categoria`, `nome`, `quantidade`, `preco`, `validade`, `data_criacao`, `imagem`) VALUES
	(6, 1, 3, 'Mini Ventilador Portátil', 100, 100.00, NULL, '2025-01-02 02:54:57', '../../public/assets/image/produtos/1/Mini_Ventilador_Port__til.webp'),
	(7, 1, 3, 'Ventilador de Mesa Mondial', 100, 100.00, NULL, '2025-01-02 03:06:22', '../../public/assets/image/produtos/1/Ventilador_de_Mesa_Mondial.webp'),
	(8, 1, 3, 'Computador PC Gamer Completo', 100, 100.00, NULL, '2025-01-02 03:07:50', '../../public/assets/image/produtos/1/Computador_PC_Gamer_Completo.webp'),
	(9, 1, 3, 'Samsung Galaxy Tab S9 FE+', 100, 100.00, NULL, '2025-01-02 03:08:56', '../../public/assets/image/produtos/1/Samsung_Galaxy_Tab_S9_FE_.webp'),
	(10, 1, 3, 'Motorola Moto G24 4G 128GB 4GB', 100, 100.00, NULL, '2025-01-02 03:10:01', '../../public/assets/image/produtos/1/Motorola_Moto_G24_4G_128GB_4GB.webp'),
	(11, 1, 3, 'Samsung Galaxy S23 FE 5G', 100, 100.00, NULL, '2025-01-02 03:10:59', '../../public/assets/image/produtos/1/Samsung_Galaxy_S23_FE_5G.webp'),
	(12, 1, 3, 'Ar Condicionado Split Hi Wall', 100, 100.00, NULL, '2025-01-02 03:11:36', '../../public/assets/image/produtos/1/Ar_Condicionado_Split_Hi_Wall.webp'),
	(13, 1, 3, 'Rack com Painel TV 65"', 100, 100.00, NULL, '2025-01-02 03:12:18', '../../public/assets/image/produtos/1/Rack_com_Painel_TV_65_.webp'),
	(14, 1, 3, 'Cervejeira Consul Preta - CZE12BE', 100, 100.00, NULL, '2025-01-02 03:13:01', '../../public/assets/image/image.png'),
	(15, 1, 3, 'Relógio Feminino Condor Analógico Dourado', 100, 100.00, NULL, '2025-01-02 03:14:17', '../../public/assets/image/produtos/1/Rel__gio_Feminino_Condor_Anal__gico_Dourado.webp');

-- Copiando estrutura para tabela facilestoque.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `comercio_id` int DEFAULT NULL,
  `nome` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `comercio_id` (`comercio_id`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`comercio_id`) REFERENCES `comercios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela facilestoque.usuarios: ~1 rows (aproximadamente)
INSERT INTO `usuarios` (`id`, `comercio_id`, `nome`, `email`, `senha`, `data_criacao`) VALUES
	(1, 1, 'Admin', 'admin@email.com', '$2y$10$CQ80l6Vj3Z9vJ3xwogLqH.3VDJeoqK2AI2hAEXuKuwvgO1WrRLtPq', '2024-10-11 14:58:01');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
