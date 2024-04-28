<?php
/* Base de données */
$dsn = "mysql:host=localhost;dbname=archivage";
$dbusername = "root";
$dbpassword = "";

/* Cookie */
$cookie_lifetime = 0;

/*Gestion Fichier excel*/

define('TEMP_DIRECTORY', 'filetmp/');
define('CONTENT_DIRECTORY', 'content/');
$allowedFileTypes = ['application/pdf'];

$searchValues = [
    '=> Référence :',
    '=> Demandeur :',
    '=> Titre de la demande :',
    '=> Application ou équipement  :',
    '=> Objet de la demande :',
    'Création d\'une demande d\'assistance le '
];
