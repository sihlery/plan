<?php

function getAbteilungUndSeheAbteilung($pdo, $mitarbeiterName) {
    $stmt = $pdo->prepare("SELECT Abteilung, SeheAbteilung FROM stammdaten WHERE Login = :mitarbeiterName");

    $stmt->bindParam(':mitarbeiterName', $mitarbeiterName, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getBereicheVonAbteilung($pdo, $abteilung) {
    $stmt = $pdo->prepare("SELECT DISTINCT  Bereich FROM stammdaten WHERE Abteilung = :abteilung");

    $stmt->bindParam(':abteilung', $abteilung, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMitarbeiterEinerAbteilungUndBereich($pdo, $abteilung, $bereich) {
    $sql = "SELECT userid, Arbeitnehmer, Bereich, Mo_DoVonBis, FrVonBis , Login
            FROM stammdaten 
            WHERE Abteilung = :abteilung AND Login != '*' AND Eintragsart != 'X' AND Eintragsart != 'D'";
    
    if ($bereich !== 'Alle') {
        $sql .= " AND Bereich = :bereich";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':abteilung', $abteilung, PDO::PARAM_STR);
    
    // Binden den Bereich nur, wenn er nicht 'Alle' ist
    if ($bereich !== 'Alle') {
        $stmt->bindParam(':bereich', $bereich, PDO::PARAM_STR);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getArbeitsstundenEinesMitarbeiters($pdo, $userid, $KWbeginn) {
    $startOfWeekDate = new DateTime($KWbeginn);
    $endOfWeekDate = clone $startOfWeekDate;
    $endOfWeekDate->modify('+6 days');
    $KWende = $endOfWeekDate->format('Y-m-d');

    $sql = "SELECT stammdaten.*, dienstarten.*, dienstplan.*
            FROM `stammdaten`
            JOIN dienstplan ON stammdaten.userid = dienstplan.userid
            JOIN dienstarten ON dienstplan.`Dienstart-ID` = dienstarten.`Dienstart-ID`
            WHERE stammdaten.userid = :userid
            AND dienstplan.Datum BETWEEN :KWbeginn AND :KWende
            ORDER BY dienstplan.Datum ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
    $stmt->bindParam(':KWbeginn', $KWbeginn, PDO::PARAM_STR);
    $stmt->bindParam(':KWende', $KWende, PDO::PARAM_STR);

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function checkUserBerechtigung($pdo, $username) {
    try {
        $stmt = $pdo->prepare("SELECT  Chef 
        FROM stammdaten 
        WHERE Login = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return (int) $result['Chef'];
        }

        return 0; // Standardwert falls kein Ergebnis gefunden

    } catch (PDOException $e) {
        error_log("Datenbankfehler: " . $e->getMessage());
        return 0; // Fehlerbehandlung: Wenn ein Fehler auftritt, geben Sie den Standardwert zurück
    }
}



function checkDienstplanEntry($pdo, $userId, $datumAttribut) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM dienstplan WHERE userid = :userId AND Datum = :datumAttribut");
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':datumAttribut', $datumAttribut, PDO::PARAM_STR);
    $stmt->execute();
    
    return $stmt->fetchColumn();
}

function updateDienstplan($pdo, $params) {
    $sql = "UPDATE dienstplan
            SET Kommentar = :comment,
                VonBis1 = :time1,
                VonBis2 = :time2,
                `Dienstart-ID` = :dienstID,
                Sollk = :sollk
            WHERE userid = :userId AND Datum = :datumAttribut";

    $stmt = $pdo->prepare($sql);
    if ($stmt->execute($params)) {
        return ["success" => true];
    } else {
        return ["success" => false, "error" => "Failed to update dienstplan."];
    }
}

function insertDienstplan($pdo, $params) {
    $sql = "INSERT INTO dienstplan (userid, Datum, Kommentar, VonBis1, VonBis2, `Dienstart-ID`, Sollk)
            VALUES (:userId, :datumAttribut, :comment, :time1, :time2, :dienstID, :sollk)";

    $stmt = $pdo->prepare($sql);
    if ($stmt->execute($params)) {
        return ["success" => true];
    } else {
        return ["success" => false, "error" => "Failed to insert into dienstplan."];
    }
}





?>