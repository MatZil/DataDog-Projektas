-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 17, 2019 at 05:26 PM
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
-- Database: `myusers`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created` date NOT NULL,
  `price` double(11,2) NOT NULL,
  `address` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `fk_users_username` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_users_username` (`fk_users_username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `name`, `description`, `created`, `price`, `address`, `event_date`, `fk_users_username`) VALUES
(1, 'Namu darbu pristatymas', 'Matas rodys savo nesamones', '2019-03-17', 1000.00, 'Studentu 69', '2019-03-17', 'admin'),
(2, 'Naujas', 'tik testuoju', '2019-03-17', 124.00, 'Studentu 69', '2019-03-17', 'admin'),
(3, 'test', 'testtt', '2019-03-17', 123.00, 'Studentu 69', '2019-03-17', 'testuojunu');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `real_name` varchar(255) NOT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `username` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `joined` date NOT NULL,
  `hash` varchar(255) NOT NULL,
  PRIMARY KEY (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`real_name`, `surname`, `username`, `email`, `joined`, `hash`) VALUES
('Matas', 'Zilinskas', 'admin', 'zilinskas.matas1999@gmail.com', '2019-03-17', '$2y$10$bIxTIb1heohmoEbdr8p9uO5A.HISKwa50D3LpvDeh5oYvrDLq6zEO'),
('Lukas', 'Kezevicius', 'lukkez', 'lukkez@a', '2019-03-17', '$2y$10$9kaQkHvqpTu4UvqhPa/tpuAeXnc.1KCvgT.nX5wg5vpcrplHmNKFK'),
('asd', 'asd', 'Testing', 'asd@as', '2019-03-17', '$2y$10$ZdJorVHo0z8K/y6XigwUhuwe.YPi2PXcYr7eiNNLkaTWUINXvqKhi'),
('Mantas', 'Zilinskas', 'testuojunu', 'lukaskez@afs', '2019-03-17', '$2y$10$5qF.DIvIxkbRPcRizy1/keTAhStfslqauvzAtbRZI.JRCFLDJWXUq');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`fk_users_username`) REFERENCES `users` (`username`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
