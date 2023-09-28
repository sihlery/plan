<?php
require_once '../db/connect.php';
require_once '../db/queries.php';
require_once '../functions/ajax_getfunctions.php';
// $test_Query = getBereicheVonAbteilung($pdo, "Zentrale AV-Technik");
// var_dump($test_Query);
?>
<!DOCTYPE html>
<html>

<head>

    <link rel="stylesheet" type="text/css" href="css/main.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- days.js - fuers Datum -->
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.10.7/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs/plugin/isoWeek.js"></script>
    <!-- DataTables CSS and JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <!-- Eigene Funktionen -->
    <script src="./js/date_functions.js"></script>
    <script src="./js/table_functions.js"></script>
</head>

<body>
    <!-- Ihre HTML-Elemente ... -->
    <input type="text" id="usernameInput" placeholder="Benutzername eingeben">
    <div style="display: inline-block;">
        <button id="confirmButton">Bestätigen</button>
    </div>
    <!-- ... -->
    <div style="display: inline-block;">
        <select id="abteilungDropdown"></select>
    </div>
    <div style="display: inline-block;">
        <select id="bereichDropdown"></select>
    </div>
    <!-- ... -->

    <p>Aktuelles Datum: <span id="DatumVonHeute"></span> | Aktuelles KW: <span id="KWvonHeute"></span></p>
    <div id="weekControls">
        <button id="prevWeek">Letzte Woche</button>
        <button id="currentWeek">Aktuelle Woche</button>
        <button id="nextWeek">Nächste Woche</button>
    </div>
    <p>Ansicht Datum: <span id="aktuellesDatum"></span> | Ansicht KW: <span id="aktuelleKW"></span></p>

    <div id="tableDiv" style="display: none;">
        <table id="mitarbeiterTabelle" class="display">
            <thead>
                <tr>
                    <th>Arbeitnehmer</th>
                    <th>Montag</th>
                    <th>Dienstag</th>
                    <th>Mittwoch</th>
                    <th>Donnerstag</th>
                    <th>Freitag</th>
                    <th>Samstag</th>
                    <th>Sonntag</th>
                    <th>Wochenstunden</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <!-- Hier werden die Daten eingefügt -->
            </tbody>
        </table> <!-- Ihre Tabelle ... -->
    </div>
    
    
    <script>
        // Globale Variablen
        let aktuelleWocheOffset = 0;
        let username = '';
        
        document.addEventListener("DOMContentLoaded", function () {


            
            
            // Initialisierungen 
            dayjs.extend(dayjs_plugin_isoWeek); // Plugin für ISO Woche

            // Event-Listener
            document.getElementById('prevWeek').addEventListener('click', function() {
                adjustWeekOffset(-1);
            });
            document.getElementById('currentWeek').addEventListener('click', function() {
                aktuelleWocheOffset = 0;
                updateDateAndWeek(0);
            });
            document.getElementById('nextWeek').addEventListener('click', function() {
                adjustWeekOffset(1);
            });
            $('#confirmButton').on('click', function() {
                const inputUsername = $('#usernameInput').val().trim();
                if (inputUsername && inputUsername !== '') {
                    username = handleConfirmButton(username);
                    // Zeigt die Tabelle an
                    $('#tableDiv').css('display', 'block');
                } else {
                    alert("Bitte Benutzername eingeben.");
                }
            });
            $('#abteilungDropdown').on('change', function(event) {
                handleAbteilungChange(event, username);
            });
            $('#bereichDropdown').on('change', function(event) {
                handleBereichChange(event, username);
            });

            // $('#mitarbeiterTabelle').on('click', 'td', function() {
                //     console.log('Zelle geklickt');
                // });
                
                // Initialisierungen
                
                updateDateAndWeek();
                updateTableHeaders();
                $('#mitarbeiterTabelle').on('click', 'td', function() {
                    
                    var benutzerId = $(this).parent().data('userid');
                    var datumAttribut = $(this).data('datumattribut');
                
                    //console.log($(this));
                    console.log('Benutzer-ID:', benutzerId);
                    console.log('Datum-Wert:', datumAttribut);
                    
                
                    var comment = 'Neuer Kommentar';
                    var time1 = '12:00 14:00';
                    var time2 = '16:00 19:00';
                    var dienstID = 1;
                    var sollk = 5.2;
                
                    $.ajax({
                        url: 'setEntry.php', // Pfade zur PHP-Datei anpassen
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            userId: benutzerId,
                            datumAttribut: datumAttribut,
                            comment: comment,
                            time1: time1,
                            time2: time2,
                            dienstID: dienstID,
                            sollk: sollk
                        },
                        success: function(response) {
                            if (response.success) {
                                var selectedAbteilungValue = $('#abteilungDropdown').val();
                                var selectedBereichValue = $('#bereichDropdown').val();
                                loadTable(selectedAbteilungValue, selectedBereichValue);
                                console.log('Daten erfolgreich aktualisiert');
                            } else {
                                console.error('Fehler beim Aktualisieren der Daten:', response.error);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Fehler bei der AJAX-Anfrage:', error);
                            console.log(response);
                        }
                    });
                });
                
        });
    </script>
</body>

</html>