-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  mer. 01 mai 2019 à 14:23
-- Version du serveur :  5.7.24
-- Version de PHP :  7.2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `ece_amazon`
--

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`id`, `name`, `slug`) VALUES
(4, 'Véhicules', 'vehicules'),
(5, 'Livres', 'livres'),
(6, 'Musique', 'musique'),
(7, 'Vêtements', 'vetements'),
(8, 'Sport et Loisir', 'sport-et-loisir');

-- --------------------------------------------------------

--
-- Structure de la table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `price` text NOT NULL,
  `category` varchar(255) NOT NULL,
  `weight` varchar(255) NOT NULL,
  `shipping` varchar(255) NOT NULL,
  `tva` varchar(255) NOT NULL,
  `final_price` varchar(255) NOT NULL,
  `stock` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `products`
--

INSERT INTO `products` (`ID`, `title`, `slug`, `description`, `price`, `category`, `weight`, `shipping`, `tva`, `final_price`, `stock`) VALUES
(39, 'Velo', 'velo', 'Description du velo', '1000', 'Véhicules', '100', '10', '20', '1212', '90'),
(43, 'Test', 'test', 'desc', '1', 'Livres', '500', '20', '20', '25.2', '10'),
(44, 'a', 'a', 'a', '1', 'Livres', '', '5', '20', '7.2', '90');

-- --------------------------------------------------------

--
-- Structure de la table `products_transactions`
--

DROP TABLE IF EXISTS `products_transactions`;
CREATE TABLE IF NOT EXISTS `products_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `country` varchar(25) NOT NULL,
  `date` varchar(255) NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `currency_code` varchar(5) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `user`, `email`, `nom`, `prenom`, `adresse`, `mdp`) VALUES
(8, 'test', 'test@test.com', 'test', 'test', 'test', 'test');

-- --------------------------------------------------------

--
-- Structure de la table `weights`
--

DROP TABLE IF EXISTS `weights`;
CREATE TABLE IF NOT EXISTS `weights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `weights`
--

INSERT INTO `weights` (`id`, `name`, `price`) VALUES
(1, 0, 5),
(2, 100, 10),
(3, 300, 15),
(4, 500, 20),
(5, 1000, 40);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
