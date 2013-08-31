
-- --------------------------------------------------------

--
-- Структура таблицы `pnews`
--

CREATE TABLE IF NOT EXISTS `pnews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `author` varchar(100) NOT NULL,
  `body` text NOT NULL,
  `views` int(11) NOT NULL DEFAULT '0',
  `date_news` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `pnews_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `pnews_category`
--

INSERT INTO `pnews_category` (`id`, `name`) VALUES
(1, 'Новости'),
(2, 'Новости Phalcon S');
