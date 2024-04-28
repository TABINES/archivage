<?php
if (!$conn = connectDB($dsn, $dbusername, $dbpassword)) {
    echo "Une erreur s'est produite avec la base de données. Veuillez contacter un administrateur";
    exit;
};
function connectDB(string $dsn, string $dbuser, string $dbpassword)
{
    try {
        $conn = new PDO($dsn, $dbuser, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        error_log($e->getMessage(), 3, "pdo_errors.log");
        return false;
    }
}

function prepareQuery(object $conn, string $query, array $bindparams)
{
    try {
        $stmt = $conn->prepare($query);
        foreach ($bindparams as $dbvar => &$bindparam) {
            $stmt->bindParam($dbvar, $bindparam);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log($e->getMessage(), 3, "pdo_errors.log");
        return false;
    }
}
