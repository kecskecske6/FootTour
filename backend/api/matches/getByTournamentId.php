<?php
include_once "../connect.php";
include_once "../../controllers/matchController.php";
include_once "../../controllers/header.php";

$mc = new MatchController();
$db = new DB();
$conn = $db->getConnection();
    echo json_encode($mc->getByTournamentId($conn, $_GET['tournamentId']));
?>