<?php

include("../../conf.php");
include("../../database.php");
include("../../functions.php"); 

session_start();
if (!isset($_SESSION['user_cuid'])) {
    header( "Location: ../../login" );
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['old_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        if (password_verify($_POST['old_password'], $_SESSION['user_pass'])) {
            if ($_POST['new_password'] === $_POST['confirm_password']) {
                if (verifyPassword(trim($_POST['new_password']))) {
                    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                    $changePassQuery = "UPDATE users SET password_hash = :password, change_pass = :change_pass WHERE cuid = :cuid";
                    $changePassParams = [
                        ':password' => $new_password,
                        ':cuid' => $_SESSION['user_cuid'],
                        ':change_pass' => 0
                    ];
                    prepareQuery($conn, $changePassQuery, $changePassParams);
                    $_SESSION["change_pass"] = 0;
                    header( "Location: ../../tickets/view/" );
                } else {
                    echo "<script>alert('Le mot de passe ne respecte pas les règles de sécurité !')</script>";
                }
            } else {
                echo "Les mots de passe ne correspondents pas";
            }
        } else {
            echo "Mauvais mot de passe";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
</head>

<body>
    <h2>Modification de votre mot de passe</h2>
    <form action="" method="post">
        <label for="old_password">Ancien mot de passe: </label><br />
        <input type="password" id="old_password" name="old_password" required /><br />

        <label for="new_password">Nouveau mot de passe: </label><br />
        <input type="password" id="new_password" name="new_password" required /><br />

        <label for="confirm_password">Confirmer le mot de passe: </label><br />
        <input type="password" id="confirm_password" name="confirm_password" onkeyup="checkPassword()" required /><span id="message"></span><br />

        <input type="submit" value="Submit" />
    </form>

    <script>
        function checkPassword() {
            var password = document.getElementById("new_password").value;
            var confirmPassword = document.getElementById("confirm_password").value;

            if (password != confirmPassword)
                document.getElementById("message").innerHTML = "Les mots de passes ne correspondent pas.";
            else
                document.getElementById("message").innerHTML = "";
        }
    </script>
</body>

</html>