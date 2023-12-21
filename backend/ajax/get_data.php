<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/plan/backend/ajax/func/ajax_get_functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/plan/backend/db/connect.php';

$action = $_GET['action'] ?? null;
$username = $_GET['user'] ?? null;
$abteilung = $_GET['selectedAbteilung'] ?? null;
$bereich = $_GET['selectedBereich'] ?? null;
$startOfWeek = $_GET['KWbeginn']?? null;
$KWoffset = $_GET['KWoffset']?? null;



switch ($action) {
    case 'getAbteilungen':
        header('Content-Type: application/json');
        $abteilungen = getAbteilungenDropdown($pdo, $username);
        if ($abteilungen !== null) {
            echo json_encode($abteilungen);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Kann Abteilungen nicht abrufen"]);
        }
        break;
    case 'getBereiche':
        header('Content-Type: application/json');
        $bereiche = getBereicheDropdown($pdo, $abteilung);
        if ($bereiche !== null) {
            echo json_encode($bereiche);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Kann Abteilungen nicht abrufen"]);
        }
        break;
    case 'getTable':
        header('Content-Type: text/html');
        var_dump("KWoffset: " .$KWoffset);
        $tabelle = getTableForAbteilungenUndBereiche($pdo, $abteilung, $bereich, $startOfWeek, $KWoffset, $username);
        if ($tabelle !== null) {
            echo $tabelle;
        } else {
            http_response_code(500);
            echo "Kann Abteilungen nicht abrufen";
        }
        break;
    default:
        http_response_code(400);
        echo json_encode(["error" => "Unbekannte Aktion"]);
        break;
}
