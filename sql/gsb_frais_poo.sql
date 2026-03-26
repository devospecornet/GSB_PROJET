-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 26 mars 2026 à 12:31
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gsb_frais_poo`
--

-- --------------------------------------------------------

--
-- Structure de la table `api_jetons`
--

CREATE TABLE `api_jetons` (
  `id` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `jeton` varchar(128) NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `date_expiration` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `api_jetons`
--

INSERT INTO `api_jetons` (`id`, `id_utilisateur`, `jeton`, `date_creation`, `date_expiration`) VALUES
(10, 3, 'eebbd751b511486b618e3d2126804c80b0ba20790aedbd3ed6fbe1e8f8d00127', '2026-03-26 11:55:37', '2026-03-26 12:12:58');

-- --------------------------------------------------------

--
-- Structure de la table `fiches_frais`
--

CREATE TABLE `fiches_frais` (
  `id` int(11) NOT NULL,
  `numero_fiche` varchar(20) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `mois` varchar(20) NOT NULL,
  `montant_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `statut` enum('saisie','transmise','validee','refusee') NOT NULL DEFAULT 'saisie',
  `commentaire_visiteur` text DEFAULT NULL,
  `commentaire_comptable` text DEFAULT NULL,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modification` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `fiches_frais`
--

INSERT INTO `fiches_frais` (`id`, `numero_fiche`, `id_utilisateur`, `mois`, `montant_total`, `statut`, `commentaire_visiteur`, `commentaire_comptable`, `date_creation`, `date_modification`) VALUES
(1, 'FF-000001', 3, 'Janvier', 5.00, 'transmise', 'KM : 0 € | Essence : 0 € | Hôtel : 0 € | Resto : 5 €', NULL, '2026-03-23 15:16:06', '2026-03-23 15:42:35'),
(2, 'FF-000002', 3, '2026-02', 20.00, 'saisie', 'KM : 0 € | Essence : 15 € | Hôtel : 5 € | Resto : 0 €', 'no', '2026-03-23 15:16:22', '2026-03-23 15:42:25'),
(3, 'FF-000003', 3, '2026-03', 96.00, 'saisie', 'KM : 5 € | Essence : 23 € | Hôtel : 5 € | Resto : 63 €', NULL, '2026-03-23 16:23:16', '2026-03-23 16:23:16'),
(4, 'FF-000004', 3, '2026-01', 20.00, 'saisie', 'KM : 20 € | Essence : 0 € | Hôtel : 0 € | Resto : 0 €', NULL, '2026-03-24 10:28:06', '2026-03-24 10:28:06'),
(5, 'FF-000005', 3, '2026-04', 28.00, 'validee', 'KM : 0 € | Essence : 0 € | Hôtel : 0 € | Resto : 23 €', 'Accepté mais c\'est la dernière fois', '2026-03-24 10:28:43', '2026-03-24 10:44:12'),
(6, 'FF-000006', 3, '2026-08', 260.00, 'transmise', 'Essence : 60 € | Hôtel : 160 € | Resto : 25 €', NULL, '2026-03-24 12:54:18', '2026-03-24 12:57:50');

-- --------------------------------------------------------

--
-- Structure de la table `hors_forfaits`
--

CREATE TABLE `hors_forfaits` (
  `id` int(11) NOT NULL,
  `id_fiche` int(11) NOT NULL,
  `type_consommation` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `commentaire` text NOT NULL,
  `date_ajout` datetime NOT NULL DEFAULT current_timestamp(),
  `taux_tva` decimal(5,2) NOT NULL DEFAULT 20.00,
  `montant_ht` decimal(10,2) NOT NULL DEFAULT 0.00,
  `montant_tva` decimal(10,2) NOT NULL DEFAULT 0.00,
  `montant_ttc` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `hors_forfaits`
--

INSERT INTO `hors_forfaits` (`id`, `id_fiche`, `type_consommation`, `date`, `libelle`, `montant`, `commentaire`, `date_ajout`, `taux_tva`, `montant_ht`, `montant_tva`, `montant_ttc`) VALUES
(1, 5, 'repas_midi', '2026-03-24', 'Repas du midi || annonce erroné du restaurant', 5.00, 'annonce erroné du restaurant', '2026-03-24 10:31:34', 20.00, 4.17, 0.83, 5.00),
(2, 6, 'nuitee', '2026-03-24', 'Nuitée || oups', 10.00, 'oups', '2026-03-24 12:55:56', 20.00, 8.33, 1.67, 10.00),
(3, 6, 'repas_midi', '2026-03-24', 'Repas du midi || oups2', 5.00, 'oups2', '2026-03-24 12:56:40', 20.00, 4.17, 0.83, 5.00);

-- --------------------------------------------------------

--
-- Structure de la table `justificatifs`
--

CREATE TABLE `justificatifs` (
  `id` int(11) NOT NULL,
  `id_fiche` int(11) NOT NULL,
  `nom_reel` varchar(255) NOT NULL,
  `nom_serveur` varchar(255) NOT NULL,
  `extension` varchar(10) NOT NULL,
  `contient_tva` tinyint(1) NOT NULL DEFAULT 0,
  `date_envoi` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `justificatifs`
--

INSERT INTO `justificatifs` (`id`, `id_fiche`, `nom_reel`, `nom_serveur`, `extension`, `contient_tva`, `date_envoi`) VALUES
(1, 5, 'Capture d\'écran 2026-03-22 200836.png', 'justif_69c259bd39c102.09809699.png', 'png', 0, '2026-03-24 10:30:37'),
(2, 6, 'FOND ECRAN.jpg', 'justif_69c27bc0c3d4c6.07795166.jpg', 'jpg', 1, '2026-03-24 12:55:44');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(120) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `role` enum('visiteur','comptable','administrateur') NOT NULL DEFAULT 'visiteur',
  `est_approuve` tinyint(1) NOT NULL DEFAULT 0,
  `consentement_cookies` tinyint(1) NOT NULL DEFAULT 0,
  `date_consentement_cookies` datetime DEFAULT NULL,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `email`, `mdp`, `role`, `est_approuve`, `consentement_cookies`, `date_consentement_cookies`, `date_creation`) VALUES
(1, 'Admin', 'Super', 'admin@gsb.local', '$2y$10$FyMsbn/Tn05NQ.0VB/mFEOlka6Pz0v2AsRIMXB8JgrRNbhsdEGRba', 'administrateur', 1, 0, NULL, '2026-03-19 17:59:10'),
(2, 'Durand', 'Claire', 'comptable@gsb.local', '$2y$10$FyMsbn/Tn05NQ.0VB/mFEOlka6Pz0v2AsRIMXB8JgrRNbhsdEGRba', 'comptable', 1, 0, NULL, '2026-03-19 17:59:10'),
(3, 'Martin', 'Lucas', 'visiteur@gsb.local', '$2y$10$FyMsbn/Tn05NQ.0VB/mFEOlka6Pz0v2AsRIMXB8JgrRNbhsdEGRba', 'visiteur', 1, 0, NULL, '2026-03-19 17:59:10');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `api_jetons`
--
ALTER TABLE `api_jetons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jeton` (`jeton`),
  ADD KEY `fk_api_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `fiches_frais`
--
ALTER TABLE `fiches_frais`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_fiche_utilisateur_mois` (`id_utilisateur`,`mois`),
  ADD UNIQUE KEY `numero_fiche` (`numero_fiche`);

--
-- Index pour la table `hors_forfaits`
--
ALTER TABLE `hors_forfaits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_hors_forfaits_fiche_date` (`id_fiche`,`date_ajout`);

--
-- Index pour la table `justificatifs`
--
ALTER TABLE `justificatifs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_justificatifs_fiche_tva` (`id_fiche`,`contient_tva`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `api_jetons`
--
ALTER TABLE `api_jetons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `fiches_frais`
--
ALTER TABLE `fiches_frais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `hors_forfaits`
--
ALTER TABLE `hors_forfaits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `justificatifs`
--
ALTER TABLE `justificatifs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `api_jetons`
--
ALTER TABLE `api_jetons`
  ADD CONSTRAINT `fk_api_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `fiches_frais`
--
ALTER TABLE `fiches_frais`
  ADD CONSTRAINT `fk_fiche_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `hors_forfaits`
--
ALTER TABLE `hors_forfaits`
  ADD CONSTRAINT `fk_hf_fiche` FOREIGN KEY (`id_fiche`) REFERENCES `fiches_frais` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `justificatifs`
--
ALTER TABLE `justificatifs`
  ADD CONSTRAINT `fk_justificatif_fiche` FOREIGN KEY (`id_fiche`) REFERENCES `fiches_frais` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
