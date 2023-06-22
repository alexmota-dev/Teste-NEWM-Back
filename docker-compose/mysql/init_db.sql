DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `id` BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` VARCHAR(50) UNIQUE COLLATE utf8mb4_unicode_ci NOT NULL,
  `observation` VARCHAR(300) COLLATE utf8mb4_unicode_ci,
  `address` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpf` VARCHAR(11) NOT NULL,
    -- O date tem a data em um formato diferente do usado no BR -> "YYYY-MM-DD"
  `birth` DATE NOT NULL,
  `phone` VARCHAR(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
