-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Värd: localhost
-- Tid vid skapande: 27 sep 2016 kl 23:06
-- Serverversion: 5.5.50-0+deb8u1
-- PHP-version: 5.6.24-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databas: `visits`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `daytable`
--

CREATE TABLE IF NOT EXISTS `daytable` (
  `date` date NOT NULL,
  `visits` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `minutetable`
--

CREATE TABLE IF NOT EXISTS `minutetable` (
  `intervalStart` datetime NOT NULL,
  `intervalStop` datetime NOT NULL,
  `doorA` int(11) NOT NULL,
  `doorB` int(11) NOT NULL,
  `doorC` int(11) NOT NULL,
  `doorD` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Index för dumpade tabeller
--

--
-- Index för tabell `daytable`
--
ALTER TABLE `daytable`
 ADD PRIMARY KEY (`date`);

--
-- Index för tabell `minutetable`
--
ALTER TABLE `minutetable`
 ADD PRIMARY KEY (`intervalStop`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
