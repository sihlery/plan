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
    <script src="./js/page_functions.js"></script>
    <script src="./js/ajax_functions.js"></script>
</head>

<body>

<div id="buttonContainer">
    <input type="text" id="usernameInput" placeholder="Benutzername eingeben">
    <button id="confirmButton">Bestätigen</button>
    <select id="abteilungDropdown"></select>
    <select id="bereichDropdown"></select>
    
    <button id="zoomOutButton">Rauszoomen</button>
    <button id="zoomInButton">Reinzoomen</button>
</div>

<div id="weekControlsContainer">
    <div id="lowerControls">
        <div id="weekControls">
            <button id="prevWeek">Letzte Woche</button>
            <button id="currentWeek">Aktuelle Woche</button>
            <button id="nextWeek">Nächste Woche</button>
        </div>
        <p id="ansichtInfo">Ansicht Datum: <span id="aktuellesDatum"></span> | Ansicht KW: <span id="aktuelleKW"></span></p>
        <div id="customSearchBox">
        <input type="text" id="customSearchInput" placeholder="Suchen...">
    </div>
    </div>
</div>



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


            let zoomLevel = 1;

            $('#zoomInButton').on('click', function() {
                zoomLevel += 0.1;  // Erhöht den Zoom-Level um 10%
                if (zoomLevel > 2.0) zoomLevel = 2.0;  // Setzt einen maximalen Zoom-Level

                $('body').css('zoom', zoomLevel);  // Wendet den Zoom-Level auf den gesamten Body an
            });

            $('#zoomOutButton').on('click', function() {
                zoomLevel -= 0.1;  // Reduziert den Zoom-Level um 10%
                if (zoomLevel < 0.5) zoomLevel = 0.5;  // Setzt einen Mindest-Zoom-Level

                $('body').css('zoom', zoomLevel);  // Wendet den Zoom-Level auf den gesamten Body an
            });

            // Initialisierungen
            
            updateDateAndWeek();
            updateTableHeaders();

            $('#mitarbeiterTabelle').on('click', 'td', function() {
                
                var benutzerId = $(this).parent().data('userid');
                var datumAttribut = $(this).data('datumattribut');
            
                //console.log($(this));
                console.log('Benutzer-ID:', benutzerId);
                console.log('Datum-Wert:', datumAttribut);
                
                if (benutzerId !== undefined && datumAttribut !== undefined){

                    setEintragRequest(benutzerId, datumAttribut);
                }
            });
        });
    </script>
</body>

</html>