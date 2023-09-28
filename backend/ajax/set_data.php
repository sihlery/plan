<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/plan/backend/ajax/func/ajax_set_functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/plan/backend/db/connect.php';


$action = $_POST['action'];
$userId = $_POST['userId'];
$datumAttribut = $_POST['datumAttribut'];
$comment = $_POST['comment'];
$time1 = $_POST['time1'];
$time2 = $_POST['time2'];
$dienstID = $_POST['dienstID'];
$sollk = $_POST['sollk'];

// Parametervorbereitung
$params = [
    ':userId' => $userId,
    ':datumAttribut' => $datumAttribut,
    ':comment' => $comment,
    ':time1' => $time1,
    ':time2' => $time2,
    ':dienstID' => $dienstID,
    ':sollk' => $sollk
];


switch ($action) {
    case 'setEinzelnenArbeitstag':
        header('Content-Type: application/json');
        $eintrag = setEinzelnenArbeitstag($pdo, $params);
        if ($eintrag !== null) {
            echo json_encode($eintrag);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Kann Arbeitstag nicht setzen"]);
        }
        break;
    default:
        http_response_code(400);
        echo json_encode(["error" => "Unbekannte Aktion"]);
        break;
}


?>
