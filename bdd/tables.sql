-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 07 sep. 2023 à 22:30
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `lorcadex`
--

-- --------------------------------------------------------

--
-- Structure de la table `cartes`
--

CREATE TABLE `cartes` (
  `id_carte` int(11) NOT NULL,
  `numero_carte` int(11) NOT NULL,
  `nom_carte` varchar(255) NOT NULL,
  `categorie_carte` text NOT NULL,
  `cout_carte` int(11) NOT NULL,
  `force_carte` int(11) DEFAULT NULL,
  `volonte_carte` int(11) DEFAULT NULL,
  `lore_carte` int(11) DEFAULT NULL,
  `type_carte` int(11) NOT NULL,
  `encre` int(11) NOT NULL,
  `rarete` int(11) NOT NULL DEFAULT 1,
  `chapitre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `liens_activation`
--

CREATE TABLE `liens_activation` (
  `id_lien` varchar(250) NOT NULL,
  `date_fin` datetime NOT NULL,
  `mail` varchar(250) NOT NULL,
  `data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `pseudo_user` varchar(255) DEFAULT NULL,
  `mdp_user` varchar(255) NOT NULL,
  `email_user` varchar(255) NOT NULL,
  `admin` int(11) NOT NULL DEFAULT 0,
  `nom_user` varchar(255) DEFAULT NULL,
  `prenom_user` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_as_carte`
--

CREATE TABLE `user_as_carte` (
  `id_user` int(11) NOT NULL,
  `id_carte` int(11) NOT NULL,
  `quantite` int(11) NOT NULL DEFAULT 1,
  `foil` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Structure de la table `user_tokens`
--
CREATE TABLE user_tokens
(
    id               INT AUTO_INCREMENT PRIMARY KEY,
	selector         VARCHAR(255) NOT NULL,
    hashed_validator VARCHAR(255) NOT NULL,
    token            VARCHAR(255) NOT NULL,
    expiry           DATETIME NOT NULL,
    user_id          INT      NOT NULL,
    CONSTRAINT fk_user_id
        FOREIGN KEY (user_id)
            REFERENCES users (id_user) ON DELETE CASCADE
);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `cartes`
--
ALTER TABLE `cartes`
  ADD PRIMARY KEY (`id_carte`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `cartes`
--
ALTER TABLE `cartes`
  MODIFY `id_carte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
