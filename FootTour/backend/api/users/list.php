<?php
    include_once "../connect.php";
    include_once "../../controllers/header.php";
    include_once "../../controllers/auth.php";
    include_once "../../controllers/userController.php";
    include_once "../../classes/user.php";

    $auth = new Auth();
    $uc = new UserController();
    $user = new User();
    $db = new DB();
    $conn = $db->getConnection();


    if($auth->authorize() != null){
        if(isset($_GET)){
        if (isset($_GET["id"])) {
            echo json_encode($uc->getNameById($conn, $_GET["id"]));
        }
        elseif (isset($_GET["type"])) {
            echo json_encode($uc->getByType($conn, $_GET["type"]));
        }
    }else{
        http_response_code(405);
    }
    }
    else{
        http_response_code(401);
    }
?>