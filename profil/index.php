<?php
include "../conf.php";
include "../functions.php";
include "../database.php";

session_start();
if (!isset($_SESSION['user_cuid'], $_SESSION['is_admin'], $_SESSION['user_firstname'], $_SESSION['user_email']) || $_SESSION["is_admin"] != 1) {
    header("Location: ../../login");
    exit();
} elseif ($_SESSION["change_pass"] == "1") {
    header("location: /profil/password/");
    exit();
} else {
    $get_profil_query = "SELECT * FROM users WHERE cuid = :cudi";
    $get_profil_bind[':cudi'] = $_SESSION['modify_cuid'];
    $profil = prepareQuery($conn, $get_profil_query, $get_profil_bind);
    if (count($profil) === 0) {
        header("Location: ../adminPanel");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["modifySub"])) {
        $change_profil_query = "UPDATE users SET cuid=:cuid, firstname=:firstname, lastname=:lastname, email=:email, is_admin=:is_admin WHERE cuid=:actual_cuid";
        $change_profil_bind = [
            ":cuid" => $_SESSION['modify_cuid'],
            ":firstname" => ucfirst(strtolower($_POST['firstname'])),
            ":lastname" => ucfirst(strtolower($_POST['name'])),
            ":email" => $_POST['email'],
            ":is_admin" => $_POST['isadmin'] == "on" ? 1 : 0,
            ":actual_cuid" => $profil[0]['cuid']
        ];
        prepareQuery($conn, $change_profil_query, $change_profil_bind);

        $profil = prepareQuery($conn, $get_profil_query, $get_profil_bind);
    }
    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header("location: /login");
        exit;
    } elseif (isset($_POST['change_pass'])) {
        $_SESSION["modify_cuid"] = $_SESSION["user_cuid"];
        header("location: /profil");
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
    <title>Profil</title>
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
    <h2>Modifier le profil :</h2>
    <form action="" method="post" id="modifyProfil">
        <label for="cuid">CUID : </label>
        <input type="text" value="<?= $profil[0]['cuid'] ?>" name="cuid" id="cuid"><br>
        <label for="name">Nom : </label>
        <input type="text" value="<?= $profil[0]['lastname'] ?>" name="name" id="name"><br>
        <label for="firstname">Prénom : </label>
        <input type="text" value="<?= $profil[0]['firstname'] ?>" name="firstname" id="firstname"><br>
        <label for="email">Email : </label>
        <input type="text" value="<?= $profil[0]['email'] ?>" name="email" id="email"><br>
        <label for="isadmin">Admin : </label>
        <input type="checkbox" name="isadmin" id="isadmin" <?php echo ($profil[0]['is_admin'] == 1) ? "checked" : "" ?>><br>
        <input type="submit" name="modifySub" value="Modifier">
    </form>
</body>

</html>