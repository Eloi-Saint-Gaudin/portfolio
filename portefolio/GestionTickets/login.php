<?php
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

$pageTitle = "Connexion Administrateur";

$email = $password = "";
$email_err = $password_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Valider l'email
    if(empty(trim($_POST["email"]))){
        $email_err = "Veuillez entrer votre email.";
    } else{
        $email = trim($_POST["email"]);
    }

    // Valider le mot de passe
    if(empty(trim($_POST["password"]))){
        $password_err = "Veuillez entrer votre mot de passe.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Valider les identifiants
    if(empty($email_err) && empty($password_err)){
        $user = getUserByEmail($pdo, $email);

        if($user){
            if(password_verify($password, $user['mot_de_passe'])){
                // Mot de passe correct, démarrer une nouvelle session
                
                // Stocker les données dans les variables de session
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $user['id_utilisateur'];
                $_SESSION["email"] = $user['email_utilisateur'];
                $_SESSION["role"] = $user['role'];
                
                // Rediriger l'utilisateur vers la page d'administration si admin
                if($user['role'] == 'administrateur') {
                    header("location: admin_dashboard.php");
                    exit();
                } else {
                    $login_err = "Vous n'avez pas les droits d'administrateur.";
                }
            } else{
                $login_err = "Email ou mot de passe invalide.";
            }
        } else{
            $login_err = "Email ou mot de passe invalide.";
        }
    }
}

include_once 'template/header.php';
?>

    <div class="wrapper">
        <h2>Connexion Administrateur</h2>
        <p>Veuillez remplir vos identifiants pour vous connecter.</p>

        <?php 
        if(!empty($login_err)){
            echo "<div class=\"alert alert-danger\">" . $login_err . "</div>";
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>    
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="mb-3">
                <input type="submit" class="btn btn-primary" value="Se connecter">
            </div>
        </form>
    </div>

<?php include_once 'template/footer.php'; ?>
