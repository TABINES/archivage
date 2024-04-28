<?php
require 'composer/vendor/autoload.php';

use Smalot\PdfParser\Parser;

function search($conn, array $tickets, array $searchFilters)
{
    $filteredTickets = array();

    $searchText = explode(" ", $searchFilters["searchText"]);
    $idTicket = $searchFilters["idTicket"];
    $startPeriode = $searchFilters["startPeriode"];
    $endPeriode = $searchFilters["endPeriode"];
    $idClient = $searchFilters["idClient"];
    $clientFirstname = $searchFilters["clientFirstname"];
    $clientLastname = $searchFilters["clientLastname"];
    echo var_dump($endPeriode);
    echo var_dump($startPeriode);
    foreach ($tickets as $ticket) {
        $client = getClient($conn, $ticket["client_cuid"]);

        $dbClientLastname = $client["lastname"];
        $dbClientFirstname = $client["firstname"];

        $ticketDesc = $ticket["description"];
        $dbIdClient = $ticket["client_cuid"];
        $ticketDate = new DateTime($ticket["date"]);
        $dbIdTicket = $ticket["id"];
        $keyWords = json_decode($ticket["keyWords"]);

        foreach ($keyWords as $keyword) {
            if (matchesCriteria($dbIdTicket, $searchText, $idTicket, $ticketDate, $ticketDesc, $startPeriode, $endPeriode, $dbIdClient, $dbClientFirstname, $dbClientLastname, $idClient, $clientFirstname, $clientLastname, $keyword)) {
                $filteredTickets[] = $ticket;
                break;
            }
        }
    }
    return $filteredTickets;
}

function getClient($conn, $clientCuid)
{
    $clientquery = "SELECT * FROM clients WHERE cuid = :cuid";
    $clientbindparams = array(":cuid" => $clientCuid);
    return prepareQuery($conn, $clientquery, $clientbindparams)[0];
}


function matchesCriteria($dbIdTicket, $searchText, $idTicket, $ticketDate, $ticketDesc, $startPeriode, $endPeriode, $dbIdClient, $dbClientFirstname, $dbClientLastname, $idClient, $clientFirstname, $clientLastname, $keyword)
{
    $searchResult = false;

    foreach ($searchText as $search) {
        if ($search != "") {
            if (stripos($dbIdTicket, $search) !== false || 
                stripos($dbClientFirstname, $search) !== false || 
                stripos($dbClientLastname, $search) !== false || 
                stripos($dbIdClient, $search) !== false || 
                stripos($keyword, $search) !== false || 
                stripos($ticketDesc, $search) !== false) {
                
                $searchResult = true;
                break;
            }
        } else {
            $searchResult = true;
        }
    }

    return ($searchResult && 
           ($idTicket === "" || $idTicket == $dbIdTicket) &&
           ($startPeriode === null || $ticketDate >= $startPeriode) &&
           ($endPeriode === null || $ticketDate <= $endPeriode) &&
           ($idClient === "" || $idClient == $dbIdClient) &&
           ($clientFirstname === "" || $clientFirstname == $dbClientFirstname) &&
           ($clientLastname === "" || $clientLastname == $dbClientLastname));
}

function whereIsValue(string $pdfPath, string $searchString)
{

    $parser = new Parser();
    $pdfText = $parser->parseFile($pdfPath)->getText();

    $startPosition = strpos($pdfText, $searchString);

    if ($startPosition !== false) {

        $textRight = substr($pdfText, $startPosition + strlen($searchString));

        if (preg_match('/.*?(?=\s*=>|$)/s', $textRight, $matches)) {
            return $matches[0];
        } else {
            return null;
        }
    } else {
        return null;
    }
}


function getClientInfos(string $clientInfosString)
{
    $clientParts = explode(" ", $clientInfosString);
    $pattern = '/\((.*?)\)/';
    if (preg_match($pattern, $clientParts[3], $matches)) {
        return ["cuid" => $matches[1], "lastname" => $clientParts[1], "firstname" => $clientParts[2]];
    }
}

function formatDate($fullDateString)
{
    $dateString = explode(" ", $fullDateString);
    $dateObj = DateTime::createFromFormat('d/m/Y', $dateString[0]);
    return  $dateObj->format('Y-m-d');
}

function sendTicketData($conn, $bindParams, $userCuid)
{
    // Requêtes SQL
    $sendTicketQuery = "INSERT INTO tickets (id, description, client_cuid, date, keyWords) VALUES (:id, :description, :client_cuid, :date, :keyWords)";
    $sendArchiveQuery = "INSERT INTO archives (ticket_id, user_cuid) VALUES (:ticket_id, :user_cuid)";
    $getArchiveQuery = "SELECT * FROM archives WHERE ticket_id = :id";
    $getTicketQuery = "SELECT id FROM tickets WHERE id = :id";

    // Paramètres pour les requêtes
    $getArchiveParams = [":id" => $bindParams[":id"]];
    $getTicketParams = [":id" => $bindParams[":id"]];

    // Préparation et exécution des requêtes
    $ticket = prepareQuery($conn, $getTicketQuery, $getTicketParams);
    $archives = prepareQuery($conn, $getArchiveQuery, $getArchiveParams);

    // Vérification de l'existence de l'archive
    $archiveExist = false;
    foreach ($archives as $archive) {
        if ($archive["user_cuid"] == $userCuid) {
            $archiveExist = true;
            break;
        }
    }

    // Insertion des données si l'archive n'existe pas
    if (!$archiveExist) {
        if (!$ticket) {
            prepareQuery($conn, $sendTicketQuery, $bindParams);
        }
        prepareQuery($conn, $sendArchiveQuery, [":ticket_id" => $bindParams[":id"], ":user_cuid" => $userCuid]);
        return true;
    }
    return false;
}

function sendClientData($conn, $clientBindParams)
{
    $getClientQuery = "SELECT cuid FROM clients WHERE cuid = :cuid";
    $getClientParams = [":cuid" => $clientBindParams[":cuid"]];

    $client = prepareQuery($conn, $getClientQuery, $getClientParams);

    if ($client) {
        return false;
    } else {
        $insertClientQuery = "INSERT INTO clients (cuid, lastname, firstname) VALUES (:cuid, :lastname, :firstname)";
        prepareQuery($conn, $insertClientQuery, $clientBindParams);
        return true;
    }
}

function deleteUser($conn, $userCuid)
{
    $deleteUserQuery = "DELETE FROM users WHERE cuid=:cuid";

    $params = array(":cuid" => $userCuid);
    $result = prepareQuery($conn, $deleteUserQuery, $params);

    // Vérification si la requête a échoué
    if ($result) {
        echo 'error';
    } else {
        echo 'success';
    }
}


function generatePassword()
{
    // Définir les ensembles de caractères pour chaque type
    $numbers = '0123456789';
    $lowercaseChars = 'abcdefghijklmnopqrstuvwxyz';
    $uppercaseChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $specialChars = '!@#$%^&*()-_+=<>?';

    // Initialiser le mot de passe
    $password = '';

    // Ajouter un caractère aléatoire de chaque type au mot de passe
    $password .= $numbers[rand(0, strlen($numbers) - 1)];
    $password .= $lowercaseChars[rand(0, strlen($lowercaseChars) - 1)];
    $password .= $uppercaseChars[rand(0, strlen($uppercaseChars) - 1)];
    $password .= $specialChars[rand(0, strlen($specialChars) - 1)];

    // Générer le reste du mot de passe avec un mélange équilibré de tous les types de caractères
    $remainingLength = 20 - 4; // 4 caractères ajoutés précédemment
    $allChars = $numbers . $lowercaseChars . $uppercaseChars . $specialChars;
    for ($i = 0; $i < $remainingLength; $i++) {
        $password .= $allChars[rand(0, strlen($allChars) - 1)];
    }

    // Mélanger le mot de passe pour plus de sécurité
    $password = str_shuffle($password);

    return $password;
}

function verifyPassword($password)
{

    if (
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)
    ) {
        return false;
    }

    return true;
}
