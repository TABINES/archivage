<?php
include "../../conf.php";
include "../../database.php";
include "../../functions.php";

session_start();

if (!isset($_SESSION['user_cuid'], $_SESSION['is_admin'], $_SESSION['user_firstname'], $_SESSION['user_email'])) {
    header("Location: ../../login");
    exit();
} elseif ($_SESSION["change_pass"] == "1") {
    header("location: /profil/password/");
    exit();
} else {
    $archivequery = "SELECT ticket_id FROM archives WHERE user_cuid = :user_cuid";
    $archivebindparam = [":user_cuid" => $_SESSION['user_cuid']];

    $ticketquery = "SELECT * FROM tickets WHERE id = :ticket_id";

    $archives = prepareQuery($conn, $archivequery, $archivebindparam);
    $tickets = [];

    foreach ($archives as $archive) {
        $ticketbindparam = ["ticket_id" => $archive["ticket_id"]];
        $tickets[] = prepareQuery($conn, $ticketquery, $ticketbindparam)[0];
    }

    $filteredTickets = [];
    if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST["submit-search-text"]) || isset($_POST["submit-filtered-search"]))) {
        if (isset($_POST["submit-search-text"])) {
            $searchFilters = [
                "searchText" => isset($_POST["search_text"]) ? $_POST["search_text"] : "",
                "idTicket" => "",
                "startPeriode" => null,
                "endPeriode" => null,
                "idClient" => "",
                "clientFirstname" => "",
                "clientLastname" => "",
            ];
        } elseif (isset($_POST["submit-filtered-search"])) {
            $searchFilters = [
                "searchText" => isset($_POST["search_text"]) ? $_POST["search_text"] : "",
                "idTicket" => isset($_POST["id_ticket"]) ? $_POST["id_ticket"] : "",
                "startPeriode" => !empty($_POST["start_periode"]) ? new DateTime($_POST["start_periode"]) : null,
                "endPeriode" => !empty($_POST["end_periode"]) ? new DateTime($_POST["end_periode"]) : null,
                "idClient" => isset($_POST["client_cuid"]) ? $_POST["client_cuid"] : "",
                "clientFirstname" => isset($_POST["client_firstname"]) ? $_POST["client_firstname"] : "",
                "clientLastname" => isset($_POST["client_lastname"]) ? $_POST["client_lastname"] : "",
            ];
        }

        $filteredTickets = search($conn, $tickets, $searchFilters);
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
    <link rel="stylesheet" href="view.css">
    <link rel="shortcut icon" href="/medias/orange_logo.svg" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Archive personnelle</title>
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

    <?php if (!isset($_GET['ticket'])) : ?>
        <form action="" method="post" class="search-form">
            <div class="search">
                <div class="search-input">
                    <input type="search" id="search_text" name="search_text" placeholder="Rechercher">
                    <button type="submit" class="submit-search" name="submit-search-text"></button>
                </div>
                <button type="button" id="advanced-search-btn">Recherche Avancée</button>
            </div>
        </form>

        <div id="advenced-search" class="advenced-search">
            <div class="advenced-search-content">
                <span class="close">&times;</span>
                <h2>Recherche Avancée</h2>
                <form action="" method="post">
                    <h4>Ticket : </h4>
                    <div>
                        <input name="id_ticket" placeholder="ID du ticket">
                    </div>

                    <h4>Date de création :</h4>
                    <div>
                        <label for="start_periode">Du : </label><input type="date" name="start_periode">
                        <label for="end_periode">Au : </label><input type="date" name="end_periode">
                    </div>

                    <h4>Client :</h4>
                    <div>
                        <input name="client_cuid" placeholder="Cuid">
                        <input name="client_firstname" placeholder="Prénom">
                        <input name="client_lastname" placeholder="Nom">
                    </div>

                    <button type="submit" id="submit-search" name="submit-filtered-search"> Rechercher </button>
                </form>
            </div>
        </div>


        <table>
            <tr class="header-line">
                <td>Id ticket</td>
                <td>Description</td>
                <td>Cuid du client</td>
                <td>Prénom du client</td>
                <td>Nom du client</td>
                <td>Date de création</td>
            </tr>
            <?php
            if ($filteredTickets === []) :
                foreach ($tickets as $ticket) :
                    $client = getClient($conn, $ticket["client_cuid"]);
            ?>
                    <tr>
                        <td><a target="_blank" href="?ticket=<?= $ticket['id'] ?>"><?= $ticket["id"] ?></a></td>
                        <td><?= $ticket["description"] ?></td>
                        <td><?= $client["cuid"] ?></td>
                        <td><?= $client["firstname"] ?></td>
                        <td><?= $client["lastname"] ?></td>
                        <td><?= $ticket["date"] ?></td>
                    </tr>
                <?php endforeach;
            else :
                foreach ($filteredTickets as $filteredTicket) :
                    $client = getClient($conn, $filteredTicket["client_cuid"]);
                ?>
                    <tr>
                        <td><a href="?ticket=<?= $filteredTicket['id'] ?>"><?= $filteredTicket["id"] ?></a></td>
                        <td title="test"><?= $filteredTicket["description"] ?></td>
                        <td><?= $client["cuid"] ?></td>
                        <td><?= $client["firstname"] ?></td>
                        <td><?= $client["lastname"] ?></td>
                        <td><?= $filteredTicket["date"] ?></td>
                    </tr>
            <?php endforeach;
            endif; ?>
        </table>
        <?php else :
        if (file_exists("content/" . $_GET['ticket'] . ".pdf")) : ?>

            <embed src=<?= "/tickets/view/content/" . $_GET['ticket'] . ".pdf" ?> width="100%" height="800"></embed>

        <?php else :
            echo "Le fichier recherché n'est pas trouvé. Redirection en cours...";
            header("Location: view");
            exit();
        endif; ?>
    <?php endif ?>

    <script src="/tickets/view/view.js"></script>
</body>

</html>