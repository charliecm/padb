-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 03, 2018 at 10:30 PM
-- Server version: 10.1.28-MariaDB
-- PHP Version: 7.1.11

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `padb`
--
CREATE DATABASE IF NOT EXISTS `padb` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `padb`;

-- --------------------------------------------------------

--
-- Table structure for table `artistArtworks`
--

DROP TABLE IF EXISTS `artistArtworks`;
CREATE TABLE `artistArtworks` (
  `artistID` int(11) NOT NULL,
  `artworkID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `artists`
--

DROP TABLE IF EXISTS `artists`;
CREATE TABLE `artists` (
  `artistID` int(11) NOT NULL,
  `firstName` text,
  `lastName` text NOT NULL,
  `websiteURL` text,
  `biography` text,
  `biographyURL` text,
  `photoURL` text,
  `countryID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `artworks`
--

DROP TABLE IF EXISTS `artworks`;
CREATE TABLE `artworks` (
  `artworkID` int(11) NOT NULL,
  `title` text NOT NULL,
  `status` enum('In Place','Removed','','') NOT NULL,
  `yearInstalled` date NOT NULL,
  `siteName` text,
  `siteAddress` text,
  `description` text,
  `statement` text,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `material` text,
  `photoURL` text,
  `websiteURL` text,
  `neighborhoodID` int(11) NOT NULL,
  `ownerID` int(11) NOT NULL,
  `typeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
  `countryID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `favoriteArtists`
--

DROP TABLE IF EXISTS `favoriteArtists`;
CREATE TABLE `favoriteArtists` (
  `memberID` int(11) NOT NULL,
  `artistID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `marks`
--

DROP TABLE IF EXISTS `marks`;
CREATE TABLE `marks` (
  `memberID` int(11) NOT NULL,
  `artworkID` int(11) NOT NULL,
  `status` enum('To See','Seen') NOT NULL DEFAULT 'To See'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
CREATE TABLE `members` (
  `memberID` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` text NOT NULL,
  `password` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `neighborhoods`
--

DROP TABLE IF EXISTS `neighborhoods`;
CREATE TABLE `neighborhoods` (
  `neighborhoodID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `owners`
--

DROP TABLE IF EXISTS `owners`;
CREATE TABLE `owners` (
  `ownerID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `types`
--

DROP TABLE IF EXISTS `types`;
CREATE TABLE `types` (
  `typeID` int(11) NOT NULL,
  `type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `artistArtworks`
--
ALTER TABLE `artistArtworks`
  ADD UNIQUE KEY `composite` (`artistID`,`artworkID`),
  ADD KEY `artistArtworks_artwork` (`artworkID`) USING BTREE;

--
-- Indexes for table `artists`
--
ALTER TABLE `artists`
  ADD PRIMARY KEY (`artistID`),
  ADD KEY `artists_country` (`countryID`) USING BTREE;

--
-- Indexes for table `artworks`
--
ALTER TABLE `artworks`
  ADD PRIMARY KEY (`artworkID`),
  ADD KEY `artworks_neighborhood` (`neighborhoodID`),
  ADD KEY `artworks_type` (`typeID`),
  ADD KEY `artworks_owner` (`ownerID`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`countryID`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `favoriteArtists`
--
ALTER TABLE `favoriteArtists`
  ADD UNIQUE KEY `composite` (`memberID`,`artistID`),
  ADD KEY `favoriteArtists_artist` (`artistID`);

--
-- Indexes for table `marks`
--
ALTER TABLE `marks`
  ADD UNIQUE KEY `composite` (`memberID`,`artworkID`),
  ADD KEY `toSee_artwork` (`artworkID`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`memberID`),
  ADD UNIQUE KEY `email_unique` (`email`) USING BTREE;

--
-- Indexes for table `neighborhoods`
--
ALTER TABLE `neighborhoods`
  ADD PRIMARY KEY (`neighborhoodID`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `owners`
--
ALTER TABLE `owners`
  ADD PRIMARY KEY (`ownerID`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`typeID`),
  ADD UNIQUE KEY `name` (`type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `artists`
--
ALTER TABLE `artists`
  MODIFY `artistID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=562;

--
-- AUTO_INCREMENT for table `artworks`
--
ALTER TABLE `artworks`
  MODIFY `artworkID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=635;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `countryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `memberID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `neighborhoods`
--
ALTER TABLE `neighborhoods`
  MODIFY `neighborhoodID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `owners`
--
ALTER TABLE `owners`
  MODIFY `ownerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `types`
--
ALTER TABLE `types`
  MODIFY `typeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `artistArtworks`
--
ALTER TABLE `artistArtworks`
  ADD CONSTRAINT `artistArtworks_artist` FOREIGN KEY (`artistID`) REFERENCES `artists` (`artistID`),
  ADD CONSTRAINT `artistArtworks_artwork` FOREIGN KEY (`artworkID`) REFERENCES `artworks` (`artworkID`);

--
-- Constraints for table `artists`
--
ALTER TABLE `artists`
  ADD CONSTRAINT `artists_country` FOREIGN KEY (`countryID`) REFERENCES `countries` (`countryID`);

--
-- Constraints for table `artworks`
--
ALTER TABLE `artworks`
  ADD CONSTRAINT `artworks_neighborhood` FOREIGN KEY (`neighborhoodID`) REFERENCES `neighborhoods` (`neighborhoodID`),
  ADD CONSTRAINT `artworks_owner` FOREIGN KEY (`ownerID`) REFERENCES `owners` (`ownerID`),
  ADD CONSTRAINT `artworks_type` FOREIGN KEY (`typeID`) REFERENCES `types` (`typeID`);

--
-- Constraints for table `favoriteArtists`
--
ALTER TABLE `favoriteArtists`
  ADD CONSTRAINT `favoriteArtists_artist` FOREIGN KEY (`artistID`) REFERENCES `artists` (`artistID`),
  ADD CONSTRAINT `favoriteArtists_member` FOREIGN KEY (`memberID`) REFERENCES `members` (`memberID`);

--
-- Constraints for table `marks`
--
ALTER TABLE `marks`
  ADD CONSTRAINT `toSee_artwork` FOREIGN KEY (`artworkID`) REFERENCES `artworks` (`artworkID`),
  ADD CONSTRAINT `toSee_member` FOREIGN KEY (`memberID`) REFERENCES `members` (`memberID`);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
