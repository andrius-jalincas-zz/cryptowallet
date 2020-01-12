INSERT INTO `user` (`username`, `roles`, `password`) VALUES
('test', '[\"ROLE_USER\"]', '$argon2id$v=19$m=65536,t=4,p=1$TGxlWjc2QlBYWGlaWElpSg$GY9Dq6GEvBXiTGG3GT9l/WOVOoXri4TjbJEiNZOGVQQ');

INSERT INTO `currency` (`name`, `slug`) VALUES
('BitCoin', 'BTC'),
('Ethereum', 'ETH'),
('IOTA', 'IOTA');