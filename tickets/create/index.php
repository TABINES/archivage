<?php
include "../../conf.php";
include "../../functions.php";
include "../../database.php";
require '../../composer/vendor/autoload.php';

session_start();

if (!isset($_SESSION['user_cuid'], $_SESSION['is_admin'], $_SESSION['user_firstname'], $_SESSION['user_email'])) {
    header("Location: ../../login");
    exit();
} elseif ($_SESSION["change_pass"] == "1") {
    header("location: /profil/password/");
    exit();
} else {
    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header("location: /login");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/main.css">
    <link rel="stylesheet" href="/tickets/create/create.css">
    <link rel="shortcut icon" href="/medias/orange_logo.svg" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Ajouter</title>
</head>
<body>
<header class="nav-header">
        <div class="navbar">
            <a href="/tickets/view/" class="navbar-text">
                <img src="/medias/orange_logo.svg" alt="logo" class="logo_orange">
                Archives personnelle
            </a>
            <a href="/tickets/create/" class="navbar-text">Créer une archive</a>
            <?php if ($_SESSION['is_admin'] === 1) : ?>
                <a href="/adminPanel/" class="navbar-text">Panel Admin</a>
            <?php endif ?>
        </div>

        <div class="dropdown">
            <button id="dropbtn" class="navbar-text"><?= $_SESSION['user_firstname'] . " " . $_SESSION['user_lastname'] ?></button>
            <div class="dropdown-content">
                <a href="/profil/password/" class="navbar-text">Modifier le profil</a>
                <form action="" method="post">
                    <button type="submit" name="logout" id="logout" class="navbar-text">Se déconnecter</button>
                </form>
            </div>
        </div>
    </header>
    
    <div id="drop_zone">
        <label for="file-input" id="add-file">Ajouter un fichier</label>
        <input type="file" id="file-input" class="hidden" name="uploadedFile">
        
        <div id="drop-box-base-content">
            <p>Déposez votre fichier ici ou cliquez pour le sélectionner.</p>
        </div>

        <div id="pending" class="hidden">
            <p>Chargement....</p>
        </div>

        <div id="dropped-file" class="hidden">
            <p>Une archive à été créée à votre nom pour le ticket : <span id="ticket_id"></span></p>
        </div>

        <div id="already-exist-content" class="hidden">
            <p>Le ticket existe déjà, merci d'en déposer un nouveau</p>
        </div>

        <div id="wrong-file-content" class="hidden">
            <p>Impossible de récupérer les informations du ticket. Merci de vérifier qu'il s'agit bien d'un rapport de ticket complet</p>
        </div>

        <div id="too-many-content" class="hidden">
            <p>Merci de ne déposer qu'un fichier à la fois.</p>
        </div>

        <div id="invalid-type-content" class="hidden">
            <p>Le fichier doit être un PDF. Merci de déposer un fichier valide.</p>
        </div>
    </div>

    <script src="/tickets/create/create.js"></script>
</body>
</html>
