<?php
$userId = $_POST['userId'];
$datumAttribut = $_POST['datumAttribut'];
$comment = $_POST['comment'];
$time1 = $_POST['time1'];
$time2 = $_POST['time2'];
$dienstID = $_POST['dienstID'];
$sollk = $_POST['sollk'];
$host = 'localhost';
$dbname = 'diplandb';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql_check = "SELECT COUNT(*) as count FROM dienstplan WHERE userid = $userId AND Datum = '$datumAttribut'";
$result = $conn->query($sql_check);
$row = $result->fetch_assoc();
switch ($dienstID) {
    case 1: //Normal
    case 2: //Spätschicht
    case 9: //Rufbereitschaft
    case 13: //Mittelsch.
    case 15: // Frühschi.
        if ($row['count'] > 0) {
            $sql = "UPDATE dienstplan
                   SET Kommentar = '$comment',
                       VonBis1 = '$time1',
                       VonBis2 = '',
                       `Dienstart-ID` = $dienstID,
                       Sollk = $sollk
                   WHERE userid = $userId AND Datum = '$datumAttribut'";
        } else {
            $sql = "INSERT INTO dienstplan (userid, Datum, Kommentar, VonBis1, VonBis2, `Dienstart-ID`, Sollk)
                   VALUES ($userId, '$datumAttribut', '$comment', '$time1', '', $dienstID, $sollk)";
        }
        break;
    case 8: //geteilter Dienst
        if ($row['count'] > 0) {
            $sql = "UPDATE dienstplan
                   SET Kommentar = '$comment',
                       VonBis1 = '$time1',
                       VonBis2 = '$time2',
                       `Dienstart-ID` = $dienstID,
                       Sollk = $sollk
                   WHERE userid = $userId AND Datum = '$datumAttribut'";
        } else {
            $sql = "INSERT INTO dienstplan (userid, Datum, Kommentar, VonBis1, VonBis2, `Dienstart-ID`, Sollk)
                   VALUES ($userId, '$datumAttribut', '$comment', '$time1', '$time2', $dienstID, $sollk)";
        }
        break;
    case 3: //FZA
    case 4: //Urlaub
    case 5: //AZV
    case 6: //Kurs
    case 7: //Krank
    case 10: //Feiert.
    case 11: //Extern
    case 12: //Frei
    case 14: //AFT
    case 16: //LZK
}
if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $conn->error]);
}
$conn->close();
?>
