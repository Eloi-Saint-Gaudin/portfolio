<?php
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

$pageTitle = "Suivi de Ticket";
$ticket_id = null;
$ticket = null;
$error_message = '';

if (isset($_GET['ticket_id']) && !empty(trim($_GET['ticket_id']))) {
    $ticket_id = trim($_GET['ticket_id']);

    $ticket = getTicketDetails($pdo, $ticket_id);

    if (!$ticket) {
        $error_message = "Aucun ticket trouvé avec cet ID.";
    }
} else {
    $error_message = "Veuillez fournir un numéro de ticket.";
}

include_once 'template/header.php';
?>

        <h2 class="mb-4">Détails du Ticket</h2>

        <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
            <div class="alert alert-success" role="alert">
                Votre ticket a été créé avec succès ! Votre numéro de ticket est <strong><?php echo htmlspecialchars($ticket_id); ?></strong>. Veuillez le conserver pour le suivi.
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php elseif ($ticket): ?>
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    Ticket #<?php echo htmlspecialchars($ticket['id_ticket']); ?> - <?php echo htmlspecialchars($ticket['sujet_ticket']); ?>
                </div>
                <div class="card-body">
                    <p><strong>Service:</strong> <?php echo htmlspecialchars($ticket['nom_service']); ?></p>
                    <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($ticket['description_ticket'])); ?></p>
                    <p><strong>Créé le:</strong> <?php echo htmlspecialchars($ticket['date_creation']); ?></p>
                    <p><strong>Statut:</strong> <span class="badge 
                        <?php 
                            switch($ticket['statut_ticket']) {
                                case 'ouvert': echo 'bg-danger'; break;
                                case 'en_cours': echo 'bg-warning text-dark'; break;
                                case 'ferme': echo 'bg-success'; break;
                                default: echo 'bg-secondary'; break;
                            }
                        ?>">
                        <?php echo htmlspecialchars($ticket['statut_ticket']); ?>
                    </span></p>
                    <p><strong>Priorité:</strong> <span class="badge 
                        <?php 
                            switch($ticket['priorite_ticket']) {
                                case 'basse': echo 'bg-info'; break;
                                case 'moyenne': echo 'bg-primary'; break;
                                case 'haute': echo 'bg-danger'; break;
                                default: echo 'bg-secondary'; break;
                            }
                        ?>">
                        <?php echo htmlspecialchars($ticket['priorite_ticket']); ?>
                    </span></p>
                    <p><strong>Dernière mise à jour:</strong> <?php echo htmlspecialchars($ticket['date_derniere_maj']); ?></p>
                    <?php if (!empty($ticket['commentaire_admin'])): ?>
                        <div class="mt-3 p-3 bg-light border rounded">
                            <strong>Commentaire de l'administrateur:</strong><br>
                            <?php echo nl2br(htmlspecialchars($ticket['commentaire_admin'])); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="mt-4 text-center">
            <a href="index.php" class="btn btn-secondary">Retour à l'accueil</a>
        </div>

<?php include_once 'template/footer.php'; ?>
