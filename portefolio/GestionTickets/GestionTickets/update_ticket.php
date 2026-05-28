<?php
session_start();

// Vérifier si l'utilisateur est connecté et est un administrateur
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'administrateur'){
    header("location: login.php");
    exit;
}

include_once 'includes/config.php';
include_once 'includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_ticket = $_POST["id_ticket"];
    $statut = $_POST["statut"];
    $priorite = $_POST["priorite"];
    $commentaire_admin = $_POST["commentaire_admin"];

    if (updateTicket($pdo, $statut, $priorite, $commentaire_admin, $id_ticket)) {
        header("location: admin_dashboard.php?success=true");
        exit();
    } else {
        echo "Erreur lors de la mise à jour du ticket.";
    }
}
?>
