-- phpMyAdmin SQL Dump
-- version 3.3.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 13, 2014 at 06:15 AM
-- Server version: 5.5.35
-- PHP Version: 5.5.3-1ubuntu2.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `playbypost`
--

-- --------------------------------------------------------

--
-- Table structure for table `characters`
--

CREATE TABLE IF NOT EXISTS `characters` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` smallint(6) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'not really sure what I''m going to use this for yet',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='super class for all characters (including gms)' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `characters`
--


-- --------------------------------------------------------

--
-- Table structure for table `characters_gm`
--

CREATE TABLE IF NOT EXISTS `characters_gm` (
  `id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `characters_gm`
--


-- --------------------------------------------------------

--
-- Table structure for table `character_game_map`
--

CREATE TABLE IF NOT EXISTS `character_game_map` (
  `character_id` mediumint(8) unsigned NOT NULL,
  `game_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`character_id`),
  KEY `game_id` (`game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Fixes a problem with circular references and allows gameless characters';

--
-- Dumping data for table `character_game_map`
--


-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE IF NOT EXISTS `games` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `gm_id` mediumint(8) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_activity` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gm_id` (`gm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `games`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `username_hash` binary(4) NOT NULL,
  `password_hash` varbinary(255) NOT NULL,
  `joined` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_hash` (`username_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Email and last seen may be added (email maybe in a separate table)' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `users`
--


--
-- Constraints for dumped tables
--

--
-- Constraints for table `characters`
--
ALTER TABLE `characters`
  ADD CONSTRAINT `characters_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `characters_gm`
--
ALTER TABLE `characters_gm`
  ADD CONSTRAINT `characters_gm_ibfk_1` FOREIGN KEY (`id`) REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `character_game_map`
--
ALTER TABLE `character_game_map`
  ADD CONSTRAINT `character_game_map_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `character_game_map_ibfk_1` FOREIGN KEY (`character_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `games_ibfk_1` FOREIGN KEY (`gm_id`) REFERENCES `characters_gm` (`id`) ON UPDATE CASCADE;
