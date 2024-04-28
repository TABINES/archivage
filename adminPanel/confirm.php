<?php
    include("../conf.php");
    include("../database.php");
    session_start();

    if (!isset($_SESSION['user_cuid'], $_SESSION['is_admin'], $_SESSION['user_firstname'], $_SESSION['user_email']) && ($_SESSION['is_admin'] != 1)) {
        header("Location: ../../login");
        exit();
    } elseif ($_SESSION["change_pass"] == "1") {
        header("location: /profil/password/");
        exit();
    } elseif (isset($_SESSION['add_user'])) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["valid"])) {
                $valid = $_POST["valid"];
                if ($valid === "Valider") {
                    $addUserQuery = "INSERT INTO users (cuid, firstname, lastname, email, is_admin, change_pass, password_hash) VALUES (:cuid, :firstname, :lastname, :email, :is_admin, :change_pass, :password_hash)";
                    $addUserBindParams = [
                        ":cuid"=>$_SESSION['add_user']['cuid'], 
                        ":firstname"=>$_SESSION['add_user']['firstname'], 
                        ":lastname"=>$_SESSION['add_user']['lastname'], 
                        ":email"=>$_SESSION['add_user']['email'],
                        ":is_admin"=>$_SESSION['add_user']['is_admin'] == "on" ? 1 : 0, 
                        ":change_pass"=> 1,
                        ":password_hash"=>password_hash($_SESSION['add_user']['password'], PASSWORD_DEFAULT) 
                    ];
                    prepareQuery($conn, $addUserQuery, $addUserBindParams);
                    $to = $_SESSION['add_user']['email'];

                    $subject = "Votre compte pour l'outil d'archivage des incidents";

                    $message = 
                    "Bonjour ".$_SESSION['add_user']['firstname']." ".$_SESSION['add_user']['lastname'].", 
                    
                    Votre compte à bien été créé avec le mot de passe : ".$_SESSION['add_user']['password'].".
                    
                    Merci de vous connecter avec votre CUID et de changer votre mot de passe rapidement.";

                    $headers = "From: archive@orange.com";
                    
                    mail($to, $subject, $message, $headers);
                    
                    header('Location: ./');
                    unset($_SESSION['add_user']);
                    exit;
                    
                } elseif ($valid === "Annuler") {
                    unset($_SESSION['add_user']);
                    header('Location: ./');
                    exit;
                }
            }
        }
    } else {
        header("Location: ./");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="confirm.css">
    <title>Confirmation</title>
</head>
<body>
    <div class="content">
        <?php
        if ($_SESSION['add_user']['is_admin'] == "on") {
            echo "<h2> Vous êtes sur le point d'ajouter un <span style='color: red;'>administrateur</span> </h2><br>";
        } else {
            echo "<h2> Vous êtes sur le point d'ajouter un utilisateur </h2><br>";
        }
            echo "Veuillez confirmer les informations apportés ou annuler la création : <br>";
            echo "CUID : ".$_SESSION['add_user']['cuid']."<br> Prénom : ".$_SESSION['add_user']['firstname']."<br> Nom : ".$_SESSION['add_user']['lastname']."<br> Mot de passe : ".$_SESSION['add_user']['password'];
        ?>
        <form action="" method="post">
            <input type="submit" name="valid" value="Valider">
            <input type="submit" name="valid" value="Annuler">
        </form>
    </div>
    
</body>
</html>