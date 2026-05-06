<?php

$host = "217.154.164.56";
$dbname = "s27_StreamHiveMilan";
$username = "u27_FWbFmsXe9H";
$password = "79hKgc4EcA5!H6@W14@NzGK9";

try {

    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password
    );

    // PDO error mode
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected successfully";

} catch(PDOException $e) {

    die("Connection failed: " . $e->getMessage());

}

?>