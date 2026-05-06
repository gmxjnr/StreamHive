<?php

$host = "";
$dbname = "";
$username = "";
$password = "";

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
