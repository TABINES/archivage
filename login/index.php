<?php

include "../conf.php";
include "../database.php";

$incomplete_form = false;
$login_failed = false;

session_set_cookie_params($cookie_lifetime, '/', '', true, true);

session_start();

if (isset($_SESSION['user_cuid'], $_SESSION['is_admin'], $_SESSION['user_firstname'], $_SESSION['user_email'])) {
    header("Location: /tickets/view");
    exit();
} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST["cuid"]) || empty($_POST["pass"])) {
            $incomplete_form = true;
        } else {
            $cuid = $_POST["cuid"];
            $pass = $_POST["pass"];
            
            $query = "SELECT * FROM users WHERE cuid = :cuid";
            $bindparams = [":cuid"=>$cuid];

            $results = prepareQuery($conn, $query, $bindparams);

            if (count($results) < 2) {
                if (count($results) > 0 && password_verify($pass, $results[0]["password_hash"])) {
                    session_regenerate_id(true);
                    $_SESSION['user_cuid'] = strtoupper($cuid);
                    $_SESSION['is_admin'] = $results[0]["is_admin"];
                    $_SESSION['user_firstname'] = $results[0]["firstname"];
                    $_SESSION['user_lastname'] = $results[0]["lastname"];
                    $_SESSION['user_email'] = $results[0]["email"];
                    $_SESSION['change_pass'] = $results[0]["change_pass"];

                    if (isset($_SESSION['user_cuid'], $_SESSION['is_admin'], $_SESSION['user_firstname'], $_SESSION['user_email'])) {

                        if ($_SESSION['change_pass'] == 0) {
                            header("Location: /tickets/view/");
                            exit();
                        } else {
                            $_SESSION['user_pass'] = $results[0]["password_hash"];
                            header("Location: /profil/password/");
                            exit();
                        }
                    }
                    
                } else {
                    $login_failed = true;
                }
            } else {
                echo "La base de données semble corrompue, veuillez contacter un administrateur";
                exit;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/login/login.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Login</title>
</head>
<body>
    <form id="form" action="" method="post">
        <h1>Se connecter</h1>
        <?php if ($incomplete_form): ?>
            <div class="error">
                <span class="error-content">Champs incomplets</span>
            </div>
        <?php elseif ($login_failed): ?>
            <div class="error">
                <span class="error-content">Informations incorrectes</span>
            </div>
        <?php else: ?>
            <div class="info">
                <h4 id="info-title">Votre première connexion ?</h4>
                <span class="info-content">Contactez un administrateur pour créer votre compte.</span>
            </div>
        <?php endif; ?>
        
        <div class="input-group">
            <label for="cuid">Nom d'utilisateur</label>
            <input type="text" name="cuid" id="cuid">
        </div>
        <div class="input-group">
            <label for="pass">Mot de passe</label>
            <input type="password" name="pass" id="pass">
        </div>
        <input type="submit" value="VALIDER">
    </form>
</body>
</html>