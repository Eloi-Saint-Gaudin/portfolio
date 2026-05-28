CREATE DATABASE IF NOT EXISTS `saintga251_bd`;
USE `saintga251_bd`;

-- Table Utilisateur
CREATE TABLE `Utilisateur` (
    `id_utilisateur` INT AUTO_INCREMENT PRIMARY KEY,
    `nom_utilisateur` VARCHAR(50) NOT NULL,
    `prenom_utilisateur` VARCHAR(50) NOT NULL,
    `email_utilisateur` VARCHAR(100) NOT NULL UNIQUE,
    `mot_de_passe` VARCHAR(255) NULL, -- Optionnel pour les utilisateurs simples
    `role` ENUM('utilisateur', 'administrateur') NOT NULL DEFAULT 'utilisateur'
);

-- Table Service
CREATE TABLE `Service` (
    `id_service` INT AUTO_INCREMENT PRIMARY KEY,
    `nom_service` VARCHAR(100) NOT NULL UNIQUE,
    `description_service` TEXT
);

-- Table Ticket
CREATE TABLE `Ticket` (
    `id_ticket` INT AUTO_INCREMENT PRIMARY KEY,
    `uid_ticket`  VARCHAR(10) NOT NULL UNIQUE,
    `id_utilisateur` INT NOT NULL,
    `id_service` INT NOT NULL,
    `sujet_ticket` VARCHAR(255) NOT NULL,
    `description_ticket` TEXT NOT NULL,
    `date_creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `statut_ticket` ENUM('ouvert', 'en_cours', 'ferme') NOT NULL DEFAULT 'ouvert',
    `priorite_ticket` ENUM('basse', 'moyenne', 'haute') NOT NULL DEFAULT 'moyenne',
    `date_derniere_maj` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `commentaire_admin` TEXT,
    FOREIGN KEY (`id_utilisateur`) REFERENCES `Utilisateur`(`id_utilisateur`) ON DELETE CASCADE,
    FOREIGN KEY (`id_service`) REFERENCES `Service`(`id_service`) ON DELETE CASCADE
);
