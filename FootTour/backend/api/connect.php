<?php
    header('Access-Control-Allow-Origin: *');
    $conn = new mysqli('localhost', 'root', '');

    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $sql = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'foottour'";
    $result = $conn->query($sql);
    if ($result->num_rows != 1) {
        include_once "createDb.php";
    }
?>