<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/plan/backend/db/connect.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/plan/backend/db/queries.php';


function setEinzelnenArbeitstag($pdo,$params) {
    $dienstID = $params[':dienstID'];
    switch ($dienstID) {
        //Einer Uhrzeit ------
        case 1:  // Normal
        case 2:  // Spätschicht
        case 9:  // Rufbereitschaft
        case 13: // Mittelsch.
        case 15: // Frühschi.
            $params[':time2'] = '';
            return handleDatabaseOperation($pdo, $params);
        //Einer Uhrzeit ------
        case 8:  // geteilter Dienst
        //Zwei uhrzeiten
            return handleDatabaseOperation($pdo, $params);

        //keine Uhrzeit ------
        case 3:  // FZA
        case 4:  // Urlaub
        case 5:  // AZV
        case 6:  // Kurs
        case 7:  // Krank
        case 10: // Feiert.
        case 11: // Extern
        case 12: // Frei
        case 14: // AFT
        case 16: // LZK
            $params[':time1'] = '';
            $params[':time2'] = '';
            return handleDatabaseOperation($pdo, $params);
        //keine Uhrzeit ----
        default:
            http_response_code(400);
            echo json_encode(["error" => "Unbekannte Dienstart: " . $dienstID]);
            exit;
    }
}

function handleDatabaseOperation($pdo, $params) {
    $userId= $params[':userId'];
    $datumAttribut= $params[':datumAttribut'];
    $count = checkDienstplanEntry($pdo, $userId, $datumAttribut);

    if ($count > 0) {
        return updateDienstplan($pdo, $params);
    } else {
        return insertDienstplan($pdo, $params);
    }
}
?>