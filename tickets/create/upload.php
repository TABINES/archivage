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
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["uploadedFile"]) && $_FILES["uploadedFile"]["error"] == UPLOAD_ERR_OK) {
    $userCuid = $_SESSION["user_cuid"];
    $file = $_FILES['uploadedFile']['tmp_name'];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file);

    if (in_array($mime, $allowedFileTypes)) {

        $tempfile = TEMP_DIRECTORY.uniqid('pdf') . '.' . pathinfo($_FILES["uploadedFile"]["name"], PATHINFO_EXTENSION);
        move_uploaded_file($file, $tempfile);

        foreach ($searchValues as $searchedString) {
            $searchResult[] = whereIsValue($tempfile, $searchedString);
        }
        
        if ($searchResult[0] != "") {
            $idTicket = str_replace(' ', '', $searchResult[0]);
            $client = getClientInfos($searchResult[1]);
            $desciption = trim($searchResult[2]);
            $app = $searchResult[3];
            $cte = $searchResult[4];
            $keyWords = [$app, $cte];
            $formattedDate = formatDate($searchResult[5]);
            $pdfPath = CONTENT_DIRECTORY.$idTicket.".pdf";
            

            $ticketBindParams = [
                ":id" => $idTicket,
                ":description" => $desciption,
                ":client_cuid" => $client["cuid"],
                ":date" => $formattedDate,
                ":keyWords" => json_encode($keyWords)
            ];
            $clientBindParams = [
                ":lastname" => $client["lastname"],
                ":firstname" => $client["firstname"],
                ":cuid" => $client["cuid"]
            ];

            if (sendTicketData($conn, $ticketBindParams, $userCuid)) {
                if (!file_exists($pdfPath)) {
                    rename($tempfile, "../view/".$pdfPath);
                }
                sendClientData($conn, $clientBindParams);
                echo json_encode(array("status" => "success", "ticket_id"=>$idTicket));
                exit;
            } else {
                echo json_encode(array("status" => "alredyExist"));
                unlink($tempfile);
                exit;
            }
        } else {
            echo json_encode(array("status" => "noMatch"));   
            unlink($tempfile);
            exit;         
        }
    } else {
        echo json_encode(array("status" => "badType"));
        exit;
    }
}
?>