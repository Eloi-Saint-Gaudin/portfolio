<?php
/**
 * Récupre tous les services disponibles
 * @param PDO $pdo la co pdo a la db
 * @return array un tab dàobjets représentant le sesrvices
 */
function getServices(PDO $pdo): array {
    $stmt = $pdo->query("SELECT id_service, nom_service FROM Service ORDER BY nom_service ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * recup un user par son mail
 * @param PDO $pdo la co PDO a la db
 * @param string $email mail du user
 * @return arrray|false un tab associatif représatant le user ou false si non trouvé
 */
function getUserByEmail(PDO $pdo, string $email) {
    $stmt = $pdo->prepare("SELECT id_utilisateur, email_utilisateur, mot_de_passe, role FROM Utilisateur WHERE email_utilisateur = ?");
    $stmt->execute([$email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * @param PDO $pdo la co pdo a la db
 * @param string $nom
 * @param string $prenom
 * @param string $email
 * @param string $password_hash
 * @param string $role
 * @return int
 */
function createUser(PDO $pdo, string $nom, string $prenom, string $email, string $password_hash, string $role = 'utilisateur'): int {
    $stmt = $pdo->prepare("INSERT INTO Utilisateur (nom_utilisateur, prenom_utilisateur, email_utilisateur, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $prenom, $email, $password_hash, $role]);
    return $pdo->lastInsertId();
}
/**
 * @param PDO $pdo la co pdo a la db
 * @param int $id_utilisateur
 * @param int $id_service
 * @param string $sujet
 * @param string $description
 * @return int
 */
function createTicket(PDO $pdo, int $id_utilisateur, int $id_service, string $sujet, string $description): int {
    $stmt = $pdo->prepare("INSERT INTO Ticket (id_utilisateur, id_service, sujet_ticket, description_ticket) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id_utilisateur, $id_service, $sujet, $description]);
    return $pdo->lastInsertId();
}
/**
 * @param PDO $pdo la co pdo a la db
 * @param int $id_ticket
 * @return array|false
 */
function getTicketDetails(PDO $pdo, int $id_ticket) {
    $sql = "SELECT t.id_ticket, t.sujet_ticket, t.description_ticket, t.date_creation, 
            t.statut_ticket, t.priorite_ticket, t.date_derniere_maj, 
            t.commentaire_admin, u.email_utilisateur, s.nom_service 
            FROM Ticket t 
            JOIN Utilisateur u ON t.id_utilisateur = u.id_utilisateur 
            JOIN Service s ON t.id_service = s.id_service 
            WHERE t.id_ticket = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_ticket]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
/**
 * @param PDO $pdo la co pdo a la db
 * @return array
 */
function getAllTickets(PDO $pdo): array {
    $sql = "SELECT t.id_ticket, t.sujet_ticket, t.description_ticket, t.date_creation, 
            t.statut_ticket, t.priorite_ticket, t.date_derniere_maj, 
            t.commentaire_admin, u.email_utilisateur, s.nom_service 
            FROM Ticket t 
            JOIN Utilisateur u ON t.id_utilisateur = u.id_utilisateur 
            JOIN Service s ON t.id_service = s.id_service 
            ORDER BY t.date_creation DESC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
/**
 * @param PDO $pdo la co pdo a la db
 * @param string $statut
 * @param string $priorite
 * @param string|null $commentaire_admin
 * @return int $id_ticket
 * @return bool vrai si maj reussi, else faux
 */
function updateTicket(PDO $pdo, string $statut, string $priorite, ?string $commentaire_admin, int $id_ticket): bool {
    $sql = "UPDATE Ticket SET statut_ticket = ?, priorite_ticket = ?, commentaire_admin = ? WHERE id_ticket = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$statut, $priorite, $commentaire_admin, $id_ticket]);
}
?>