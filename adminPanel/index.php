<?php
    include "../conf.php";
    include "../database.php";
    include "../functions.php";

    session_start();
    if (!isset($_SESSION['user_cuid'], $_SESSION['is_admin'], $_SESSION['user_firstname'], $_SESSION['user_email']) && ($_SESSION['is_admin'] != 1)) {
        header("Location: ../../login");
        exit();
    } elseif ($_SESSION["change_pass"] == "1") {
        header("location: /profil/password/");
        exit();
    } else {
        if ($_SESSION["is_admin"]) {
            $getUsersQuery = "SELECT * FROM users";
            $users = prepareQuery($conn, $getUsersQuery, []);

            if ($_SERVER[ 'REQUEST_METHOD' ] == 'POST') {
                if (isset($_POST['cuid'], $_POST['firstname'], $_POST['lastname'], $_POST['email'], $_POST['password'])) {
                    if ($_POST['password'] == "" || verifyPassword($_POST['password'])) {
                        $_SESSION['add_user'] = [
                            "cuid"=>strtoupper($_POST['cuid']), 
                            "email"=>$_POST['email'], 
                            "firstname"=>$_POST['firstname'], 
                            "lastname"=>$_POST['lastname'], 
                            "is_admin"=>$_POST['is_admin'], 
                            "password"=>$_POST['password'] != "" ? $_POST['password'] : generatePassword()
                        ]; 
                        $validPass = true;
                        header("Location: confirm.php");
                        exit;
                    } else {
                        echo "<script> alert('Le mot de passe ne respecte pas les règles de sécurité'); </script>";
                    }
                }
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
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
    <link rel="stylesheet" href="admin.css">
    <link rel="shortcut icon" href="/medias/orange_logo.svg" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Panel admin</title>
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

    <h2>Liste des utilisateurs</h2>
    <div class="add-user-btn-div">
        <button type="button" id="add-user-btn">Ajouter un utilisateur</button>
    </div>

    <div id="add-user" class="add-user">
        <div class="add-user-content">
            <span class="close">&times;</span>
            <h2>Ajouter un utilisateur</h2>
            <form action="" method="post">
                <div>
                    <label for="is_admin">Administrateur ?</label>
                    <input type="checkbox" name="is_admin" id="is_admin"><br/>
                </div>
                <input type="text" name="cuid" id="cuid"  placeholder="Identifiant de l'utilisateur"/>
                <input type="email" name="email" id="email" placeholder="Adresse e-mail de l'utilisateur"/><br/>
                <input type="text" name="firstname" id="firstname"  placeholder="Prénom de l'utilisateur"/>
                <input type="text" name="lastname" id="lastname" placeholder="Nom de l'utilisateur">
                
                <input type="password" name="password" id="password" placeholder="Mot de passe"> 
                <button type="submit">Ajouter</button>
            </form>
        </div>
    </div>

    <div>
        <table>
            <tr>
                <td>Actions</td>
                <td>Cuid</td>
                <td>Prénom</td>
                <td>Nom</td>
                <td>Email</td>
                <td>Administrateur</td>
            </tr>
            <?php 
            foreach ($users as $user) : ?>
                <tr>
                    <td class="actions"><button id="modify" onclick="modifyProfil('<?= $user['cuid']?>')"></button> <button id="delete" onclick="userDelete('<?=$user['cuid']?>', '<?=$_SESSION['user_cuid']?>')"></button></td>
                    <td><?= $user["cuid"]?></td>
                    <td><?= $user["firstname"]?></td>
                    <td><?= $user["lastname"]?></td>
                    <td><?= $user["email"]?></td>
                    <td><?= $user["is_admin"] ? "Oui" : "Non"?></td>
                </tr>
            <?php endforeach;?>
        </table>
    </div>
    <script src="admin.js"></script>
</body>
</html>