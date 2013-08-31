

CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `path` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `author` varchar(50) NOT NULL,
  `version` varchar(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `remember_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usersId` int(10) unsigned NOT NULL,
  `token` char(32) NOT NULL,
  `userAgent` varchar(120) NOT NULL,
  `createdAt` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `token` (`token`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;



CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role` varchar(32) NOT NULL DEFAULT 'users',
  `firstname` varchar(32) NOT NULL DEFAULT ' ',
  `lastname` varchar(32) NOT NULL DEFAULT ' ',
  `email` varchar(62) NOT NULL DEFAULT ' ',
  `birthday` varchar(32) NOT NULL DEFAULT ' ',
  `sex` varchar(32) NOT NULL DEFAULT ' ',
  `username` varchar(32) NOT NULL DEFAULT ' ',
  `password` char(150) NOT NULL,
  `logins` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

