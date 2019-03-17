-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: 2019 m. Kov 17 d. 11:48
-- Server version: 5.7.24
-- PHP Version: 7.2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `homeworkdb`
--

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `date` datetime NOT NULL,
  `users_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Sukurta duomenų kopija lentelei `events`
--

INSERT INTO `events` (`id`, `name`, `date`, `users_id`) VALUES
(1, 'Pirma paskaita', '2019-02-28 13:30:00', 1),
(2, 'Darbu perziura', '2019-03-08 11:00:00', 1),
(3, 'Pirma paskaita', '2019-02-28 13:30:00', 2);

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Sukurta duomenų kopija lentelei `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`) VALUES
(1, 'oof@ktu.lt', '$2y$10$j2gOL4.PIWbPTj8ZzG.hguOulFk8ngJ/mR2V0PxH6k/CW5cH9etf2'),
(2, 'biggeroof@ktu.lt', '$2y$10$9uKV7WZbWNDsTRGNayNCTuvvg4IbbijMALHh3ZPqduegylODjPCw.'),
(3, 'whatisthis@yikes.lt', '$2y$10$ygLsbi2DwES824XH1Sm6Oe8WbPmYuZWdlF8dcL2LFUPMyityhFLYm');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
