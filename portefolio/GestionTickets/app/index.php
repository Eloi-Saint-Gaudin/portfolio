<?php
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

$pageTitle = "Accueil";

// Récupérer les services pour le formulaire
$services = getServices($pdo);

include_once 'template/header.php';
?>

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Ouvrir un nouveau ticket</h4>
                    </div>
                    <div class="card-body">
                        <form action="submit_ticket.php" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Votre Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="service" class="form-label">Service concerné</label>
                                <select class="form-select" id="service" name="service_id" required>
                                    <option value="">Choisir un service...</option>
                                    <?php foreach ($services as $service): ?>
                                        <option value="<?php echo htmlspecialchars($service['id_service']); ?>"><?php echo htmlspecialchars($service['nom_service']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="sujet" class="form-label">Sujet du ticket</label>
                                <input type="text" class="form-control" id="sujet" name="sujet" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description du problème</label>
                                <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Ouvrir le ticket</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-8 offset-md-2">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0">Suivre un ticket existant</h4>
                    </div>
                    <div class="card-body">
                        <form action="track_ticket.php" method="GET">
                            <div class="mb-3">
                                <label for="ticket_id_track" class="form-label">Numéro de Ticket</label>
                                <input type="text" class="form-control" id="ticket_id_track" name="ticket_id" required>
                            </div>
                            <button type="submit" class="btn btn-info">Suivre le ticket</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

<?php include_once 'template/footer.php'; ?>
