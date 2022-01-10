<?php
    include_once "header.php";
    include_once "../api/connect.php";
    include_once "../classes/tournament.php";
    include_once "auth.php";

    $auth = new Auth();

    if($auth->authorize() != null){
        $decoded = $auth->authorize();
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            if(isset($_REQUEST['id']))
                getByOrganizerId($decoded, $conn);
            elseif(isset($_REQUEST['name']))
                getTournamentByName($decoded, $conn);
        }
        elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
            createTournament($decoded, $conn);
        }
        elseif($_SERVER['REQUEST_METHOD'] == 'PUT'){
            modifyTournament($decoded, $conn);
        }
    }
    else{
        http_response_code(401);
    }
    
    function getByOrganizerId($decoded, $conn){
        $postdata = $_GET['id'];
        if($postdata == $decoded->data->id){
            $tournaments = array();
            $sql = "SELECT * from foottour.tournaments WHERE organizer_Id = '$postdata'";
            $result = $conn->query($sql);
            $count = mysqli_num_rows($result);
            if ($count > 0){
                $i = 0;
                while ($row = $result->fetch_assoc()) {
                    $tournaments[$i]["id"] = $row["id"];
                    $tournaments[$i]["organizerId"] = $row["organizer_id"];
                    $tournaments[$i]["startDate"] = $row["start_date"];
                    $tournaments[$i]["endDate"] = $row["end_date"];
                    $tournaments[$i]["name"] = $row["name"];
                    $tournaments[$i]["location"] = $row["location"];
                    $tournaments[$i]["bestPlayer"] = $row["best_player"];
                    $tournaments[$i]["topScorer"] = $row["top_scorer"];
                    $tournaments[$i]["bestGoalkeeper"] = $row["best_goalkeeper"];
                    $tournaments[$i]["entryFee"] = $row["entry_fee"];
                    $tournaments[$i]["teamsCount"] = $row["teams_count"];
                    $tournaments[$i]["description"] = $row["description"];
                    $i++;
                }
                http_response_code(200);
                echo json_encode($tournaments);
            }else{
                http_response_code(404);
            }
        }
        else{
        http_response_code(401);
        }
    }

    function createTournament($decoded, $conn){
        $postdata = json_decode(file_get_contents("php://input"));
        $id = $decoded->data->id;
        if($_GET['id'] == $id){
        $sql = "INSERT INTO foottour.tournaments (organizer_id, start_date, end_date, name,
        location, entry_fee, description, teams_count) VALUES ('$id', '$postdata->startDate',
        '$postdata->endDate', '$postdata->name', '$postdata->location', '$postdata->entryFee',
        '$postdata->description', '$postdata->teamsCount')";
            if($conn->query($sql)){
                
                http_response_code(200);
                echo json_encode(array("Sikeresen létrehozta a tornát"));
            }
            else{
                http_response_code(400);
            }
        }
        else{
            http_response_code(401);
        }
    }

    function modifyTournament($decoded, $conn){
        $postdata = json_decode(file_get_contents("php://input"));
        $id = $_REQUEST['id'];
        $organizerId = $decoded->data->id;
        $sql = "UPDATE foottour.tournaments SET start_date='$postdata->startDate',
        end_date = '$postdata->endDate', name='$postdata->name', location='$postdata->location',
        best_player = '$postdata->bestPlayer', best_goalkeeper='$postdata->bestGoalkeeper',
        entry_fee='$postdata->entryFee', description = '$postdata->description', teams_count='$postdata->teamsCount' WHERE organizer_id = $organizerId AND id = $id";
        if($conn->query($sql)){
                
            http_response_code(200);
            echo json_encode(array("Sikeresen módosította a tornát"));
        }
        else{
            http_response_code(400);
        }
    }

    function getTournamentByName($decoded, $conn){
        $name = $_REQUEST['name'];
        $tournaments = array();
        $sql = "SELECT * from foottour.tournaments WHERE name = '$name'";
        $result = $conn->query($sql);
        $count = mysqli_num_rows($result);
        if($count > 0){
            $i = 0;
                while ($row = $result->fetch_assoc()) {
                    $tournaments[$i]["id"] = $row["id"];
                    $tournaments[$i]["organizerId"] = $row["organizer_id"];
                    $tournaments[$i]["startDate"] = $row["start_date"];
                    $tournaments[$i]["endDate"] = $row["end_date"];
                    $tournaments[$i]["name"] = $row["name"];
                    $tournaments[$i]["location"] = $row["location"];
                    $tournaments[$i]["bestPlayer"] = $row["best_player"];
                    $tournaments[$i]["topScorer"] = $row["top_scorer"];
                    $tournaments[$i]["bestGoalkeeper"] = $row["best_goalkeeper"];
                    $tournaments[$i]["entryFee"] = $row["entry_fee"];
                    $tournaments[$i]["teamsCount"] = $row["teams_count"];
                    $tournaments[$i]["description"] = $row["description"];
                    $i++;
                }
                http_response_code(200);
                echo json_encode($tournaments);
        }
        else{
            http_response_code(404);
        }
    }
    $conn->close();
?>