<?php
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $service_id = $_POST["service_id"];
    $sujet = $_POST["sujet"];
    $description = $_POST["description"];

    // Vérifier si l'utilisateur existe, sinon le créer
    $user = getUserByEmail($pdo, $email);
    $user_id = null;

    if ($user) {
        $user_id = $user['id_utilisateur'];
    } else {
        // Créer un nouvel utilisateur 
        $default_password_hash = ""; // pas de mot de passe utilisateur pour le moment
        $nom_default = explode('@', $email)[0];
        $prenom_default = ucfirst($nom_default);
        $user_id = createUser($pdo, $nom_default, $prenom_default, $email, $default_password_hash, 'utilisateur');
    }

    if ($user_id) {
        // Insérer le nouveau ticket
        $new_ticket_id = createTicket($pdo, $user_id, $service_id, $sujet, $description);
        header("location: track_ticket.php?ticket_id=" . $new_ticket_id . "&success=true");
        exit();
    } else {
        echo "Erreur: Impossible de déterminer ou créer l'utilisateur.";
    }
}
?>
