-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 16 mars 2024 à 13:32
-- Version du serveur : 8.0.31
-- Version de PHP : 8.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `archivage`
--

-- --------------------------------------------------------

--
-- Structure de la table `archives`
--

DROP TABLE IF EXISTS `archives`;
CREATE TABLE IF NOT EXISTS `archives` (
  `archive_id` int NOT NULL AUTO_INCREMENT,
  `ticket_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_cuid` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`archive_id`),
  KEY `user_id` (`user_cuid`),
  KEY `idTicket` (`ticket_id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `archives`
--

INSERT INTO `archives` (`archive_id`, `ticket_id`, `user_cuid`, `date`) VALUES
(25, 'INC001009662394', 'TMKF5302', '2024-03-12 15:35:35'),
(23, 'INC001009742801', 'TMKF5302', '2024-03-12 15:27:53'),
(24, 'INC001009610898', 'TMKF5302', '2024-03-12 15:30:15');

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `cuid` varchar(8) NOT NULL,
  `lastname` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `firstname` varchar(100) NOT NULL,
  PRIMARY KEY (`cuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`cuid`, `lastname`, `firstname`) VALUES
('PAOL6621', 'LORIC', 'PHILIPPE'),
('MPBK0025', 'TATRY', 'LIONEL'),
('ADLE6374', 'LEROUGE', 'DANIEL'),
('CAYF7292', 'FLOUVAT', 'CATHERINE');

-- --------------------------------------------------------

--
-- Structure de la table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `client_cuid` varchar(8) NOT NULL,
  `date` date NOT NULL,
  `keywords` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_cuid` (`client_cuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `tickets`
--

INSERT INTO `tickets` (`id`, `description`, `client_cuid`, `date`, `keywords`) VALUES
('INC001009742801', ' RDV100 12/03 15:30 - TEAMS : je n`arrive\npas a acceder a certains environnements Teams ext', 'CAYF7292', '2024-03-12', 'bientot, à, venir'),
('INC001009610898', ' Ascenseur monte/descend tout seul par\nintermittence', 'PAOL6621', '2024-01-15', 'bientot, à, venir'),
('INC001009662394', ' erreur au lancement du logiciel calculatrice', 'MPBK0025', '2024-02-05', 'bientot, à, venir');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `cuid` varchar(8) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `change_pass` tinyint(1) NOT NULL DEFAULT '1',
  `password_hash` varchar(60) NOT NULL,
  PRIMARY KEY (`cuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`cuid`, `firstname`, `lastname`, `email`, `is_admin`, `change_pass`, `password_hash`) VALUES
('tmkf5302', 'Bastien', 'Chicherie', 'bastien.chicherie@gmail.com', 1, 1, '$2y$10$HlzJ500DrEr4K/i8KjUAH.w5rvfp8jVeEN826JuBmbjNcNCH3b0Xm');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
