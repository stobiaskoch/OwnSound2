-- phpMyAdmin SQL Dump
-- version 4.2.12deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 03. Jan 2015 um 21:08
-- Server Version: 5.5.40-1
-- PHP-Version: 5.6.4-1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `musikdatenbank`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `album`
--

CREATE TABLE IF NOT EXISTS `album` (
`id` int(11) NOT NULL,
  `artist` varchar(100) DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `imgdata` longblob NOT NULL,
  `imgdata_big` longblob NOT NULL,
  `imgdata_small` longblob NOT NULL,
  `cover` varchar(10) NOT NULL,
  `coverbig` varchar(20) NOT NULL,
  `imgtype` varchar(100) NOT NULL DEFAULT 'image/jpeg',
  `genre` varchar(100) DEFAULT NULL,
  `label` varchar(100) DEFAULT NULL,
  `plays` int(11) NOT NULL DEFAULT '0',
  `downloads` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1686 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `artist`
--

CREATE TABLE IF NOT EXISTS `artist` (
`id` int(11) NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `navname` varchar(500) CHARACTER SET utf8 NOT NULL,
  `fav` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `scanner`
--

CREATE TABLE IF NOT EXISTS `scanner` (
`id` int(11) NOT NULL,
  `path` varchar(500) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=142 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `scanner_log`
--

CREATE TABLE IF NOT EXISTS `scanner_log` (
  `id` int(11) NOT NULL,
  `starttime` datetime NOT NULL,
  `endtime` datetime NOT NULL,
  `artist` varchar(500) NOT NULL DEFAULT '0',
  `album` varchar(500) NOT NULL DEFAULT '0',
  `error` varchar(500) NOT NULL,
  `folderscanned` int(11) NOT NULL,
  `foldertoscan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL,
  `path` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `title`
--

CREATE TABLE IF NOT EXISTS `title` (
`id` int(11) NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `artist` varchar(100) DEFAULT NULL,
  `album` varchar(100) DEFAULT NULL,
  `duration` varchar(100) NOT NULL,
  `path` varchar(700) NOT NULL,
  `track` int(11) NOT NULL,
  `plays` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=16476 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE IF NOT EXISTS `user` (
`id` int(11) NOT NULL,
  `name` varchar(100) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `fullname` varchar(200) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `role` varchar(50) NOT NULL,
  `active` set('0','1') NOT NULL DEFAULT '0',
  `loginfails` int(11) NOT NULL,
  `actlink` varchar(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `album`
--
ALTER TABLE `album`
 ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `artist`
--
ALTER TABLE `artist`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`);

--
-- Indizes für die Tabelle `scanner`
--
ALTER TABLE `scanner`
 ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `scanner_log`
--
ALTER TABLE `scanner_log`
 ADD UNIQUE KEY `id` (`id`);

--
-- Indizes für die Tabelle `settings`
--
ALTER TABLE `settings`
 ADD UNIQUE KEY `id` (`id`);

--
-- Indizes für die Tabelle `title`
--
ALTER TABLE `title`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `path` (`path`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `album`
--
ALTER TABLE `album`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1686;
--
-- AUTO_INCREMENT für Tabelle `artist`
--
ALTER TABLE `artist`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=124;
--
-- AUTO_INCREMENT für Tabelle `scanner`
--
ALTER TABLE `scanner`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=142;
--
-- AUTO_INCREMENT für Tabelle `title`
--
ALTER TABLE `title`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16476;
--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
