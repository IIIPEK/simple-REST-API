-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Окт 28 2021 г., 21:35
-- Версия сервера: 10.4.17-MariaDB
-- Версия PHP: 8.0.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `api_db`
--
CREATE DATABASE IF NOT EXISTS `api_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `api_db`;

-- --------------------------------------------------------

--
-- Структура таблицы `daughters`
--

CREATE TABLE `daughters` (
  `id_rel` int(11) NOT NULL,
  `id_org` int(11) NOT NULL,
  `daughters_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orgs`
--

CREATE TABLE `orgs` (
  `id` int(11) NOT NULL,
  `organisation` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='List of organisation';

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `daughters`
--
ALTER TABLE `daughters`
  ADD PRIMARY KEY (`id_rel`,`id_org`),
  ADD UNIQUE KEY `unique_rels` (`id_org`,`daughters_id`);

--
-- Индексы таблицы `orgs`
--
ALTER TABLE `orgs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `organisation` (`organisation`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `daughters`
--
ALTER TABLE `daughters`
  MODIFY `id_rel` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orgs`
--
ALTER TABLE `orgs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `daughters`
--
ALTER TABLE `daughters`
  ADD CONSTRAINT `daughters_ibfk_1` FOREIGN KEY (`daughters_id`) REFERENCES `orgs` (`id`),
  ADD CONSTRAINT `orgs_id` FOREIGN KEY (`id_org`) REFERENCES `orgs` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
