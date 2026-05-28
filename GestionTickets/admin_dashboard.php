<?php
session_start();

// Vérifier si l'utilisateur est connecté et est un administrateur
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'administrateur'){
    header("location: login.php");
    exit;
}

include_once 'includes/config.php';
include_once 'includes/functions.php';

$pageTitle = "Tableau de Bord Admin";

$tickets = getAllTickets($pdo);

include_once 'template/header.php';
?>

        <h2 class="mb-4">Gestion des Tickets</h2>

        <?php if (empty($tickets)): ?>
            <div class="alert alert-info" role="alert">
                Aucun ticket à afficher pour le moment.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Sujet</th>
                            <th>Utilisateur</th>
                            <th>Service</th>
                            <th>Statut</th>
                            <th>Priorité</th>
                            <th>Créé le</th>
                            <th>Dernière MAJ</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ticket['id_ticket']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['sujet_ticket']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['email_utilisateur']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['nom_service']); ?></td>
                                <td><span class="badge 
                                    <?php 
                                        switch($ticket['statut_ticket']) {
                                            case 'ouvert': echo 'bg-danger'; break;
                                            case 'en_cours': echo 'bg-warning text-dark'; break;
                                            case 'ferme': echo 'bg-success'; break;
                                            default: echo 'bg-secondary'; break;
                                        }
                                    ?>">
                                    <?php echo htmlspecialchars($ticket['statut_ticket']); ?>
                                </span></td>
                                <td><span class="badge 
                                    <?php 
                                        switch($ticket['priorite_ticket']) {
                                            case 'basse': echo 'bg-info'; break;
                                            case 'moyenne': echo 'bg-primary'; break;
                                            case 'haute': echo 'bg-danger'; break;
                                            default: echo 'bg-secondary'; break;
                                        }
                                    ?>">
                                    <?php echo htmlspecialchars($ticket['priorite_ticket']); ?>
                                </span></td>
                                <td><?php echo htmlspecialchars($ticket['date_creation']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['date_derniere_maj']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editTicketModal" 
                                            data-id="<?php echo $ticket['id_ticket']; ?>"
                                            data-sujet="<?php echo htmlspecialchars($ticket['sujet_ticket']); ?>"
                                            data-description="<?php echo htmlspecialchars($ticket['description_ticket']); ?>"
                                            data-statut="<?php echo htmlspecialchars($ticket['statut_ticket']); ?>"
                                            data-priorite="<?php echo htmlspecialchars($ticket['priorite_ticket']); ?>"
                                            data-commentaire="<?php echo htmlspecialchars($ticket['commentaire_admin']); ?>">
                                        Modifier
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    <!-- Modal pour la modification de ticket -->
    <div class="modal fade" id="editTicketModal" tabindex="-1" aria-labelledby="editTicketModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTicketModalLabel">Modifier le Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="update_ticket.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id_ticket" id="modal_id_ticket">
                        <div class="mb-3">
                            <label for="modal_sujet" class="form-label">Sujet</label>
                            <input type="text" class="form-control" id="modal_sujet" name="sujet" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="modal_description" class="form-label">Description</label>
                            <textarea class="form-control" id="modal_description" name="description" rows="3" readonly></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="modal_statut" class="form-label">Statut</label>
                            <select class="form-select" id="modal_statut" name="statut">
                                <option value="ouvert">Ouvert</option>
                                <option value="en_cours">En cours</option>
                                <option value="ferme">Fermé</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="modal_priorite" class="form-label">Priorité</label>
                            <select class="form-select" id="modal_priorite" name="priorite">
                                <option value="basse">Basse</option>
                                <option value="moyenne">Moyenne</option>
                                <option value="haute">Haute</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="modal_commentaire_admin" class="form-label">Commentaire Admin</label>
                            <textarea class="form-control" id="modal_commentaire_admin" name="commentaire_admin" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php include_once 'template/footer.php'; ?>

    <script>
        const editTicketModal = document.getElementById('editTicketModal')
        editTicketModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget
            const id_ticket = button.getAttribute('data-id')
            const sujet = button.getAttribute('data-sujet')
            const description = button.getAttribute('data-description')
            const statut = button.getAttribute('data-statut')
            const priorite = button.getAttribute('data-priorite')
            const commentaire = button.getAttribute('data-commentaire')

            const modal_id_ticket = editTicketModal.querySelector('#modal_id_ticket')
            const modal_sujet = editTicketModal.querySelector('#modal_sujet')
            const modal_description = editTicketModal.querySelector('#modal_description')
            const modal_statut = editTicketModal.querySelector('#modal_statut')
            const modal_priorite = editTicketModal.querySelector('#modal_priorite')
            const modal_commentaire_admin = editTicketModal.querySelector('#modal_commentaire_admin')

            modal_id_ticket.value = id_ticket
            modal_sujet.value = sujet
            modal_description.value = description
            modal_statut.value = statut
            modal_priorite.value = priorite
            modal_commentaire_admin.value = commentaire
        })
    </script>
