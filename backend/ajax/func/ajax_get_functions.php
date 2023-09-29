<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/plan/backend/db/connect.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/plan/backend/db/queries.php';





function getAbteilungenDropdown($pdo, $username) {
    $queryResults = getAbteilungUndSeheAbteilung($pdo, $username);
    // var_dump($queryResults);  // Zum Debugging

    if (is_array($queryResults)) {
        $abteilungenFromDB = explode(",", $queryResults["SeheAbteilung"]);
        $abteilungFromDB = explode(",", $queryResults["Abteilung"]);

        $abteilungen = array_merge($abteilungFromDB, $abteilungenFromDB);

        // Entfernen aller Anführungszeichen
        $cleanedAbteilungen = array();
        foreach ($abteilungen as $abteilung) {
            $cleanedAbteilungen[] = str_replace("'", "", $abteilung);
        }

        return json_encode($cleanedAbteilungen);
    } 
    else 
    {
        return json_encode(['Error']);
    }
}
function getBereicheDropdown($pdo, $abteilung) {
    $queryResults = getBereicheVonAbteilung($pdo, $abteilung);
    // var_dump($queryResults);  // Zum Debugging

    if (is_array($queryResults)) {
        return json_encode($queryResults);
    } 
    else 
    {
        return json_encode(['Error']);
    }
}


##################  getTableForAbteilungenUndBereiche START ####################
function getTableForAbteilungenUndBereiche($pdo, $abteilung, $bereich, $KWbeginn, $username) {
    var_dump('Username:_' .$username);
    $mitarbeiterErgebnisse = getMitarbeiterEinerAbteilungUndBereich($pdo, $abteilung, $bereich);
    $berechtigung = checkUserBerechtigung($pdo, $username);
    var_dump('Berechtigungen:_' .$berechtigung);  // Zum Debugging
    $rows = [];
    foreach ($mitarbeiterErgebnisse as $mitarbeiter) {
        $rows[] = generateTableRow($pdo, $mitarbeiter, $KWbeginn, $berechtigung, $username);
    }
    
    return implode("", $rows);
}

function generateTableRow($pdo,$mitarbeiter, $KWbeginn, $berechtigung, $username) {
    $benutzerId = $mitarbeiter['userid'];
    $arbeitsstundenErgebnisse = getArbeitsstundenEinesMitarbeiters($pdo, $benutzerId, $KWbeginn);
    $stundenNachDatum = array_column($arbeitsstundenErgebnisse, null, 'Datum');

    $cells = [];
    $totalHours = 0;
    $mo_do = explode(';', $mitarbeiter['Mo_DoVonBis']);
    $currentDate = new DateTime($KWbeginn);

    for ($i = 0; $i < 7; $i++) {
        $datum = $currentDate->format('Y-m-d');
        $cells[] = generateTableCell($mitarbeiter, $datum, $stundenNachDatum, $mo_do, $i, $totalHours, $berechtigung, $username);
        $currentDate->modify('+1 day');
    }

    $userId = htmlspecialchars($mitarbeiter['userid']);
    $employeeName = htmlspecialchars($mitarbeiter['Arbeitnehmer']);
    return "<tr data-userid='{$userId}'><td class='mitarbeiterClass' data-userid='{$userId}'>{$employeeName}</td>" . implode("", $cells) . "<td>{$totalHours}</td></tr>";
}

function formatArbeitszeit($dienstart, $vonBis1, $vonBis2 = null, $kommentar = '') {
    if (!$vonBis1) {
        $vonBis1 = $dienstart;
    }

    $formatted = htmlspecialchars($vonBis1);
    if ($vonBis2) {
        $formatted .= "<br>" . htmlspecialchars($vonBis2);
    } elseif (!$vonBis1 && !$vonBis2) {
        $formatted .= htmlspecialchars($dienstart);
    }

    if ($kommentar) {
        $formatted .= "<br><small>" . htmlspecialchars($kommentar) . "</small>";
    }

    return $formatted;
}


function generateTableCell($mitarbeiter, $datum, $stundenNachDatum, $mo_do, $i, &$totalHours, $berechtigung, $username) {
    if (isset($stundenNachDatum[$datum])) {
        $arbeitseintrag = $stundenNachDatum[$datum];
        
        $hours = calculateHours($arbeitseintrag['VonBis1']);
        $totalHours += $hours;
        var_dump($mitarbeiter['Login']);
        $formatierteArbeitszeit = formatArbeitszeit($arbeitseintrag['Dienstart'], $arbeitseintrag['VonBis1'], $arbeitseintrag['VonBis2'], $arbeitseintrag['Kommentar']);
        
        if ($berechtigung === 0 && in_array($arbeitseintrag['Dienstart'], ["Krank", "Urlaub", "Frei"])
         && strtolower($mitarbeiter['Login']) !== strtolower($username)
        ) 
        {    
            $formatierteArbeitszeit = "Abw.";
            $hintergrundfarbe = "B0B0B0"; 
        } else {
            $hintergrundfarbe = $arbeitseintrag['Farbe'];
        }

        return "<td style='background-color: #$hintergrundfarbe;' class='employee-cell' data-datumattribut='" . htmlspecialchars($datum) . "'>{$formatierteArbeitszeit}</td>";

    }  else {
        $hours = 0;
        $formattedTime = '';
        $bgColor = '';

        if ($i < 4) {  // Montag bis Donnerstag
            $formattedTime = (count($mo_do) === 1) ? $mo_do[0] : ($mo_do[$i] ?? '');
            $hours = calculateHours($formattedTime);
            $totalHours += $hours;

            if ($formattedTime === "Frei") {
                $bgColor = 'B0B0B0';
                $formattedTime = 'Abw.';
            }
        } elseif ($i == 4) {  // Freitag
            $fridayHours = calculateHours($mitarbeiter['FrVonBis']);
            $totalHours += $fridayHours;
            $formattedTime = $mitarbeiter['FrVonBis'];

            if ($formattedTime === "Frei") {
                $bgColor = 'B0B0B0';
                $formattedTime = 'Abw.';
            }
        } else {  // Samstag und Sonntag
            $bgColor = 'B0B0B0';
            $formattedTime = 'Abw.';
        }

        return "<td style='background-color: #{$bgColor};' class='employee-cell' data-datumattribut='" . htmlspecialchars($datum) . "'>{$formattedTime}</td>";
    }
}







function calculateHours($time) {
    if (!$time || !preg_match('/\d{2}:\d{2} \d{2}:\d{2}/', $time)) return 0;
    list($start, $end) = explode(' ', $time);
    list($startHour, $startMinute) = explode(':', $start);
    list($endHour, $endMinute) = explode(':', $end);
    
    $startTime = $startHour * 60 + $startMinute; // Umwandlung in Minuten
    $endTime = $endHour * 60 + $endMinute; // Umwandlung in Minuten

    // Überprüfung, ob die Endzeit vor der Startzeit liegt
    if ($endTime <= $startTime) {
        $endTime += 24 * 60;  // Addiere 24 Stunden in Minuten
    }
    
    $totalMinutes = $endTime - $startTime;  
    
    // Abzug der Pausenzeit
    $pauseMinutes = 0;
    if ($totalMinutes > 8.5 * 60) {
        $pauseMinutes = 60;
    } elseif ($totalMinutes > 6 * 60) {
        $pauseMinutes = 30;
    }

    $totalMinutes -= $pauseMinutes;

    return $totalMinutes / 60; // Umwandlung zurück in Stunden
}


##################  getTableForAbteilungenUndBereiche ENDE ####################
