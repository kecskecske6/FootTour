<?php
include_once("../../controllers/auth.php");
include_once("../../controllers/header.php");
include_once("../../controllers/groupController.php");
include_once("../connect.php");

$auth = new Auth();
$gc = new GroupController();
$db = new DB();
$conn = $db->getConnection();

if($auth->authorize() == "organizer"){
    $postdata = json_decode(file_get_contents("php://input"));
    echo json_encode($gc->createGroup($conn, $postdata));
}
else{
    http_response_code(401);
}

?>