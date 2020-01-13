CREATE TABLE `user` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `username` varchar(180) NOT NULL,
 `roles` varchar(255) NOT NULL CHECK (json_valid(`roles`)),
 `password` varchar(255) NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `api_token` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) NOT NULL,
 `token` varchar(255) NOT NULL,
 `expiry` datetime NOT NULL,
 `created_on` datetime NOT NULL,
 PRIMARY KEY (`id`),
 INDEX (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `asset` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) NOT NULL,
 `currency_id` int(11) NOT NULL,
 `label` varchar(255) NOT NULL,
 `amount` float(12,8) NOT NULL,
 `last_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`),
 INDEX (`user_id`, `currency_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `currency` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(255) NOT NULL,
 `slug` varchar(10) NOT NULL,
 `converter` varchar(255) NOT NULL DEFAULT "default",
 PRIMARY KEY (`id`),
 INDEX (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;