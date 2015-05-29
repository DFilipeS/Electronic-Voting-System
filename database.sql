-- phpMyAdmin SQL Dump
-- version 4.4.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 29, 2015 at 09:35 PM
-- Server version: 5.5.43
-- PHP Version: 5.4.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

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
  `token` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `authentication`
--

INSERT INTO `authentication` (`id`, `public_key`, `token`) VALUES
(1, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAz6+Nw118k4NeVkEdwPho\nuj0P0I/rBEvMvsuQFsRdUjhiFaKnT2Z98p2cOLtkujBBt6v3HDwffeiOMJHokmMT\nrZ9PQuMGuYdUn6L/P8jYmR4kJkz+lz7N8HS/Id4CMxKCprjWkSSwKB1asls1X3lv\nQNDKHHKudacDwLtUaUNq54gOGnQCoIcecURvOnXJBjNlebxeJDPpdhhUY0B9WpT0\naafG+rpaAkA0UGD/FhmOAQ22oJy2JceZfoQ2hnEAki0FUdg9F6fEh3IUCbecXU/G\n39MlXywOQTaVO1jnU9GBs34IDeT50iVyOQOydmAine7zflvYupxQocmqjFO4HEbM\nKwIDAQAB\n-----END PUBLIC KEY-----', 0);

-- --------------------------------------------------------

--
-- Table structure for table `parties`
--

CREATE TABLE `parties` (
  `id` int(11) NOT NULL,
  `hash_id` text NOT NULL,
  `name` text NOT NULL,
  `hashed_name` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `parties`
--

INSERT INTO `parties` (`id`, `hash_id`, `name`, `hashed_name`) VALUES
(4, '91a69e5fac4da1093a38ef00622562f0', 'Partido 0', 'partido0_ID'),
(5, '526a9b5c6f52952a30da6554c7fb628f', 'Partido 1', 'partido1_ID'),
(6, '07156a48113d6031b605ee56a96accdf', 'Partido 2', 'partido2_ID'),
(7, 'f79cac13581077ed5364737486c5cbb1', 'Partido 3', 'partido3_ID');

-- --------------------------------------------------------

--
-- Table structure for table `sets`
--

CREATE TABLE `sets` (
  `id` int(11) NOT NULL,
  `token` text NOT NULL,
  `data` text,
  `chosen` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `token` text NOT NULL,
  `vote_id` text NOT NULL,
  `party_hash` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

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
-- Indexes for table `parties`
--
ALTER TABLE `parties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sets`
--
ALTER TABLE `sets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `authentication`
--
ALTER TABLE `authentication`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `parties`
--
ALTER TABLE `parties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `sets`
--
ALTER TABLE `sets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=132;
--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
