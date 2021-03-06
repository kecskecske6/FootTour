<?php
    include_once "groupController.php";
    include_once "teamstoGroupsController.php";
    include_once "userController.php";
    include_once "matchController.php";
    class DrawController{
        function makeDraw($conn, $data) {
            $gc = new GroupController();
            $ttgc = new TeamstoGroupsController();
            $uc = new UserController();
            $mc = new MatchController();
            $matches = array();
            if ($data->tournament->type == "Csoportkör és kieséses") {
                $groups = array();
                for ($i = 0; $i < $data->tournament->teamsCount / 4; $i++) array_push($groups, array());
                for ($i = 0; $i < count($data->teams); $i++) {
                    $team = $data->teams[0];
                    $doAgain = false;
                    do {
                        $team = $data->teams[rand(0, count($data->teams) - 1)];
                        $doAgain = false;
                        for ($j = 0; $j < count($groups); $j++) if (in_array($team, $groups[$j])) $doAgain = true;
                    } while ($doAgain);
                    array_push($groups[floor($i / 4)], $team);
                }
                $i = 0;
                $letters = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');
                foreach ($groups as $key => $g) {
                    $name = "GS" . $letters[$i];
                    $group = $gc->createGroup($conn, (object)array("tournamentId" => $data->tournament->id, "name" => $name));
                    foreach ($g as $key => $t) {
                        $ttgc->createGroup($conn, (object)array("teamId" => $t->id, "groupId" => $group->id));
                    }
                    ++$i;
                }
                $groupsGenerated = $gc->getByTournamentId($conn, $data->tournament->id);
                foreach ($groupsGenerated as $key => $g) {
                    $orderedTeams = array();
                    $sql = "SELECT teamId from foottour.teams_to_groups where groupId = ?;";
                    $stmt = $conn->prepare($sql);
                    if (!$stmt) return false;
                    $groupId = htmlspecialchars(strip_tags($g->id));
                    $stmt->bind_param("i", $groupId);
                    if (!$stmt->execute()) return false;
                    $result = $stmt->get_result();
                    $teamIds = array();
                    while ($row = $result->fetch_object()) array_push($teamIds, $row);
                    for ($i = 0; $i < 4; $i++) {
                        $team = 1;
                        $doAgain = false;
                        do {
                            $team = $teamIds[rand(0, count($teamIds) - 1)]->teamId;
                            $doAgain = false;
                            for ($j = 0; $j < count($orderedTeams); $j++) {
                                if ($orderedTeams[$j] == $team) $doAgain = true;
                            }
                        } while ($doAgain);
                        array_push($orderedTeams, $team);
                    }
                    $referees = $data->referees;
                    $match = $mc->createMatch($conn, (object)array("refereeId" => $referees[rand(0, count($referees) - 1)]->id, "team1Id" => $orderedTeams[0], "team2Id" => $orderedTeams[1], "code" => $g->name . "1-1", "groupId" => $g->id));
                    array_push($matches, $match);
                    $match = $mc->createMatch($conn, (object)array("refereeId" => $referees[rand(0, count($referees) - 1)]->id, "team1Id" => $orderedTeams[3], "team2Id" => $orderedTeams[2], "code" => $g->name . "1-2", "groupId" => $g->id));
                    array_push($matches, $match);
                    $match = $mc->createMatch($conn, (object)array("refereeId" => $referees[rand(0, count($referees) - 1)]->id, "team1Id" => $orderedTeams[0], "team2Id" => $orderedTeams[3], "code" => $g->name . "2-1", "groupId" => $g->id));
                    array_push($matches, $match);
                    $match = $mc->createMatch($conn, (object)array("refereeId" => $referees[rand(0, count($referees) - 1)]->id, "team1Id" => $orderedTeams[1], "team2Id" => $orderedTeams[2], "code" => $g->name . "2-2", "groupId" => $g->id));
                    array_push($matches, $match);
                    $match = $mc->createMatch($conn, (object)array("refereeId" => $referees[rand(0, count($referees) - 1)]->id, "team1Id" => $orderedTeams[2], "team2Id" => $orderedTeams[0], "code" => $g->name . "3-1", "groupId" => $g->id));
                    array_push($matches, $match);
                    $match = $mc->createMatch($conn, (object)array("refereeId" => $referees[rand(0, count($referees) - 1)]->id, "team1Id" => $orderedTeams[1], "team2Id" => $orderedTeams[3], "code" => $g->name . "3-2", "groupId" => $g->id));
                    array_push($matches, $match);
                    if ($data->tournament->groupMatches == 2)
                    {
                        $match = $mc->createMatch($conn, (object)array("refereeId" => $referees[rand(0, count($referees) - 1)]->id, "team1Id" => $orderedTeams[1], "team2Id" => $orderedTeams[0], "code" => $g->name . "4-1", "groupId" => $g->id));
                        array_push($matches, $match);
                        $match = $mc->createMatch($conn, (object)array("refereeId" => $referees[rand(0, count($referees) - 1)]->id, "team1Id" => $orderedTeams[2], "team2Id" => $orderedTeams[3], "code" => $g->name . "4-2", "groupId" => $g->id));
                        array_push($matches, $match);
                        $match = $mc->createMatch($conn, (object)array("refereeId" => $referees[rand(0, count($referees) - 1)]->id, "team1Id" => $orderedTeams[3], "team2Id" => $orderedTeams[0], "code" => $g->name . "5-1", "groupId" => $g->id));
                        array_push($matches, $match);
                        $match = $mc->createMatch($conn, (object)array("refereeId" => $referees[rand(0, count($referees) - 1)]->id, "team1Id" => $orderedTeams[2], "team2Id" => $orderedTeams[1], "code" => $g->name . "5-2", "groupId" => $g->id));
                        array_push($matches, $match);
                        $match = $mc->createMatch($conn, (object)array("refereeId" => $referees[rand(0, count($referees) - 1)]->id, "team1Id" => $orderedTeams[0], "team2Id" => $orderedTeams[2], "code" => $g->name . "6-1", "groupId" => $g->id));
                        array_push($matches, $match);
                        $match = $mc->createMatch($conn, (object)array("refereeId" => $referees[rand(0, count($referees) - 1)]->id, "team1Id" => $orderedTeams[3], "team2Id" => $orderedTeams[1], "code" => $g->name . "6-2", "groupId" => $g->id));
                        array_push($matches, $match);
                    }
                }
            }
            else {
                $groups = array();
                for ($i = 0; $i < $data->tournament->teamsCount / 2; $i++) array_push($groups, array());
                for ($i=0; $i < count($data->teams); $i++) { 
                    $team = $data->teams[0];
                    $doAgain = false;
                    do {
                        $team = $data->teams[rand(0, count($data->teams) - 1)];
                        $doAgain = false;
                        for ($j = 0; $j < count($groups); $j++) if (in_array($team, $groups[$j])) $doAgain = true;
                    } while ($doAgain);
                    array_push($groups[floor($i / 2)], $team);
                }
                $i = 1;
                foreach ($groups as $key => $g) {
                    $name = "";
                    if ($data->tournament->teamsCount == 8) $name = "QF" . $i;
                    else if ($data->tournament->teamsCount == 16) $name = "R16" . $i;
                    else $name = "R32" . $i;
                    $group = $gc->createGroup($conn, (object)array("tournamentId" => $data->tournament->id, "name" => $name));
                    foreach ($g as $key => $t) {
                        $ttgc->createGroup($conn, (object)array("teamId" => $t->id, "groupId" => $group->id));
                    }
                    ++$i;
                }
                $groupsGenerated = $gc->getByTournamentId($conn, $data->tournament->id);
                foreach ($groupsGenerated as $key => $g) {
                    $orderedTeams = array();
                    $sql = "SELECT teamId FROM foottour.teams_to_groups WHERE groupId = ?;";
                    $stmt = $conn->prepare($sql);
                    if ($stmt == false) return false;
                    $groupId = htmlspecialchars(strip_tags($g->id));
                    $stmt->bind_param("i", $groupId);
                    if ($stmt->execute() == false) return false;
                    $result = $stmt->get_result();
                    $teamIds = array();
                    while ($row = $result->fetch_object()) array_push($teamIds, $row);
                    for ($i=0; $i < 2; $i++) { 
                        $team = 1;
                        $doAgain = false;
                        do {
                            $team = $teamIds[rand(0, count($teamIds) - 1)]->teamId;
                            $doAgain = false;
                            for ($j=0; $j < count($orderedTeams); $j++) { 
                                if ($orderedTeams[$j] == $team) $doAgain = true;
                            }
                        } while ($doAgain);
                        array_push($orderedTeams, $team);
                    }
                    $referees = $data->referees;
                    $match = $mc->createMatch($conn, (object)array("refereeId" => $referees[rand(0, count($referees) - 1)]->id, "team1Id" => $orderedTeams[0], "team2Id" => $orderedTeams[1], "code" => $g->name . "-1", "groupId" => $g->id));
                    array_push($matches, $match);
                    if ($data->tournament->knockoutMatches == 2)
                    {
                        $match = $mc->createMatch($conn, (object)array("refereeId" => $referees[rand(0, count($referees) - 1)]->id, "team1Id" => $orderedTeams[1], "team2Id" => $orderedTeams[0], "code" => $g->name . "-2", "groupId" => $g->id));
                        array_push($matches, $match);
                    }
                }
            }
            return $matches;
        }
    }
?>