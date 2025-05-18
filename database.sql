-- Create database
CREATE DATABASE IF NOT EXISTS `BD_inscription`;

USE `BD_inscription`;

-- Create tables
CREATE TABLE IF NOT EXISTS `Sondage` (
    `NumS` INT AUTO_INCREMENT PRIMARY KEY,
    `Theme` VARCHAR(50),
    `DateDebut` DATE
);

CREATE TABLE IF NOT EXISTS `Question` (
    `NumS` INT,
    `NumQ` INT,
    `Contenu` VARCHAR(150),
    PRIMARY KEY (`NumS`, `NumQ`),
    FOREIGN KEY (`NumS`) REFERENCES `Sondage`(`NumS`)
);

CREATE TABLE IF NOT EXISTS `Participant` (
    `IdParticipant` INT AUTO_INCREMENT PRIMARY KEY,
    `Mail` VARCHAR(50),
    `Mdp` VARCHAR(6),
    `Genre` CHAR(1)
);

CREATE TABLE IF NOT EXISTS `Reponse` (
    `NumQ` INT,
    `NumS` INT,
    `IdParticipant` INT,
    `Rep` CHAR(1),
    PRIMARY KEY (`NumQ`, `NumS`, `IdParticipant`),
    FOREIGN KEY (`NumQ`, `NumS`) REFERENCES `Question`(`NumQ`, `NumS`),
    FOREIGN KEY (`IdParticipant`) REFERENCES `Participant`(`IdParticipant`)
);

-- Insert initial data
INSERT INTO `Sondage` (`Theme`, `DateDebut`) VALUES
('Les réseaux sociaux', '2019-05-01'),
('Les jeux vidéo', '2019-06-01');

INSERT INTO `Question` (`NumQ`, `NumS`, `Contenu`) VALUES
(1, 1, 'Les informations partagées sur les réseaux sociaux sont fiables'),
(2, 1, 'L''usage des réseaux sociaux par les enfants doit être sous le contrôle parental'),
(3, 1, 'Les réseaux sociaux deviennent une nécessité pour les citoyens'),
(1, 2, 'Les jeux vidéo contribuent au développement de la pensée logique'); 