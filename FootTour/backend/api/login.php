<?php
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

include_once("connect.php");
include_once("../classes/user.php");
require_once("../vendor/autoload.php");
use \Firebase\JWT\JWT;
header('Access-Control-Allow-Origin: *');
header("Access-Control-Request-Method: POST");
header("Access-Control-Allow-Headers: *");

$headers = apache_request_headers();
define('SECRET_KEY', 'FootTourSecret');
define('ALGORITHM', 'HS256');

$postdata = file_get_contents("php://input");


if(isset($postdata) && !empty($postdata))
{
    $request = json_decode($postdata);
    $email = $request->email;
    $password = $request->password;
    $sql = "SELECT * from foottour.users WHERE email = '$email' and password = '$password'";
    $result = $conn->query($sql);
    $count = mysqli_num_rows($result);
    if ($count == 1) {
        $user = new User();
        $iat = time();
        $nbf = $iat + 10;
        $exp = $iat + 600000;
        $row = mysqli_fetch_row($result);
        $token = array(
            "iss" => "localhost",
            "aud" => "www.FootTour.com",
            "iat" => $iat,
            "nbf" => $nbf,
            "exp" => $exp,
            "data" => array(
                "id" => $row[0],
                "name" => $row[1],
                "email" => $row[2]
            )
        );
        http_response_code(200);
        $jwt = JWT::encode($token, SECRET_KEY);

        $data_insert = array(
            'access_token' => $jwt,
            'time' => time(),
            'status' => "success",
            'id' => $row[0]
        );
        echo json_encode($data_insert);
    }
    if($count == 0){
        http_response_code(401);
        echo json_encode(array("message" =>"Hibás felhasználó név vagy jelszó!"));
    }
}
$conn->close();
?>