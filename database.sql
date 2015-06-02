-- phpMyAdmin SQL Dump
-- version 4.4.1.1
-- http://www.phpmyadmin.net
--
-- Host: localhost:8889
-- Generation Time: Jun 02, 2015 at 01:32 PM
-- Server version: 5.5.42
-- PHP Version: 5.6.7

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `authentication`
--

INSERT INTO `authentication` (`id`, `public_key`, `token`) VALUES
(1, '-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDOlNuxJnupgAS6SACWz//pHQdM\nzb9DaNX04++Xpw9dMXchuH2QH2F6xrH/HcAFbawNxYTsfxx9Vn6pllPlh1weKnF9\nACLCYajP3vry/Ek6YPRiyDkfUZ3a4d5ERPVGqKXG6yok14ZcdjrKxFyV2jTGhuY5\n/OiYfj3AwL3v0K8kwwIDAQAB\n-----END PUBLIC KEY-----', 0),
(2, '-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDKsnVD+qQxsJYWUv3GINuKOchV\nFGEIihfwO8LS+NtAvt8XIwGIAtoFWijy0bV08ePYltDz4H14os2yWOkeNv3rxdvM\nStzaIlO3oBJctvJO3ozFU3PGjGmTRUqDvZp7mTWQe3M7f3aKVLLb9ZKfbqeq4TpR\nsTX8ZfkVoSPzIMjnCQIDAQAB\n-----END PUBLIC KEY-----', 0),
(3, '-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDU1bo7m76imNo4UBaED6LLmXWz\nagP4TbaQYDnb5wwsveROYvUKvGuqvMf5aZafsDm7avC+W+9wN5xnEw7ioLxT75Jd\n+WbtVJqXDvFGRRdNAs17yzZ0lW917JMUu1GUYZQ1o8CHl4cuhbLmNIPL6otZi4SS\nceQVsz5FivEsN4J32wIDAQAB\n-----END PUBLIC KEY-----', 0),
(4, '-----BEGIN PUBLIC KEY-----\r\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDJT6ijvqD++qsqxBeRiF8hEJhj\r\njde485AOllPMzj72zT6yKCxXoiU0Go2Y8wiHfHJ0hy1QOXKyRQSbJdG/EBR+VsUN\r\nXJwONpx1T5HN0L/gk4Ru5cXo+LAM41ZsBzcf5tHvm1QVjuFTZKZrLFk9l0oeoCbb\r\nPbFHL7way7oOo4dNhwIDAQAB\r\n-----END PUBLIC KEY-----', 0),
(5, '-----BEGIN PUBLIC KEY-----\r\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCq2J/7a8uCPP+WCLN/3cyHMC0x\r\no63xL3C65bduDmvwg2mnrBgv0wnntDEIC+8zIBuXoDyHJeM9RmeCQbb3qo7eg7jD\r\n0O+94KFYTDyzjVCtoKKH/CXF3OEC7q4eWbcLNu4/o/bVI/dl/5yx/8xTNUMyjXUh\r\nqsOKd9CaqMLAxVJIzQIDAQAB\r\n-----END PUBLIC KEY-----', 0);

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
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `token` text NOT NULL,
  `vote_id` text NOT NULL,
  `party_hash` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `parties`
--
ALTER TABLE `parties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `sets`
--
ALTER TABLE `sets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
