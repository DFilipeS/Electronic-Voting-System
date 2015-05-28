-- phpMyAdmin SQL Dump
-- version 4.4.1.1
-- http://www.phpmyadmin.net
--
-- Host: localhost:8889
-- Generation Time: May 15, 2015 at 12:31 AM
-- Server version: 5.5.42
-- PHP Version: 5.6.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `evs`
--

-- --------------------------------------------------------

--
-- Table structure for table `authentication`
--

CREATE TABLE `authentication` (
  `id` int(11) NOT NULL,
  `public_key` text NOT NULL,
  `token` text
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `authentication`
--

INSERT INTO `authentication` (`id`, `public_key`, `token`) VALUES
(1, '-----BEGIN PUBLIC KEY-----\r\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAz6+Nw118k4NeVkEdwPho\r\nuj0P0I/rBEvMvsuQFsRdUjhiFaKnT2Z98p2cOLtkujBBt6v3HDwffeiOMJHokmMT\r\nrZ9PQuMGuYdUn6L/P8jYmR4kJkz+lz7N8HS/Id4CMxKCprjWkSSwKB1asls1X3lv\r\nQNDKHHKudacDwLtUaUNq54gOGnQCoIcecURvOnXJBjNlebxeJDPpdhhUY0B9WpT0\r\naafG+rpaAkA0UGD/FhmOAQ22oJy2JceZfoQ2hnEAki0FUdg9F6fEh3IUCbecXU/G\r\n39MlXywOQTaVO1jnU9GBs34IDeT50iVyOQOydmAine7zflvYupxQocmqjFO4HEbM\r\nKwIDAQAB\r\n-----END PUBLIC KEY-----', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `authentication`
--
ALTER TABLE `authentication`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `authentication`
--
ALTER TABLE `authentication`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
