USE `saintga251_bd`;

-- Insertion des services
INSERT INTO `Service` (`nom_service`, `description_service`) VALUES
('Informatique', 'Problèmes liés au matériel informatique, logiciels, réseau.'),
('Maintenance des locaux', 'Interventions pour l\'électricité, la plomberie, le mobilier.'),
('Support bureautique', 'Aide à l\'utilisation des logiciels de bureautique et des périphériques d\'impression.');

-- Insertion d\'un administrateur (mot de passe: admin)
INSERT INTO `Utilisateur` (`nom_utilisateur`, `prenom_utilisateur`, `email_utilisateur`, `mot_de_passe`, `role`) VALUES
('Admin', 'Super', 'admin@parcinfo.com', '$2y$10$fDFOm5BdnAyY7nwnJ6H8reusHI2yTiqT2zby1y8vxyMH7Zgvk3vfq', 'administrateur');

-- Insertion d\'un utilisateur (sans mot de passe)
INSERT INTO `Utilisateur` (`nom_utilisateur`, `prenom_utilisateur`, `email_utilisateur`, `role`) VALUES
('Dupont', 'Jean', 'jean.dupont@parcinfo.com', 'utilisateur');
-- Exemple de ticket
INSERT INTO `Ticket` (`uid_ticket`, `id_utilisateur`, `id_service`, `sujet_ticket`, `description_ticket`, `statut_ticket`, `priorite_ticket`)
VALUES
('TKT0000001', 2, 1, 'Problème de connexion réseau', 'Mon ordinateur ne parvient pas à se connecter au réseau filaire.', 'ouvert', 'haute');
