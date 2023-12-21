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
    <!-- Cleave.js - Um Uhrzeit in Format zu schreiben (UI) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>


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


<div id="contextMenu" style="display: none; position: absolute; border: 1px solid black; background: white; padding: 10px;">
        <label for="dienstDropdown">Dienstart:</label>
        <select id="dienstDropdown"> <!-- Dropdown für Dienstarten -->
            <option value="1">Normal</option>
            <option value="2">Spaetsch</option>
            <option value="3">FZA</option>
            <option value="4">Urlaub</option>
            <option value="5">AZV</option>
            <option value="6">Kurs</option>
            <option value="7">Krank</option>
            <option value="8">get.Dienst</option>
            <option value="9">Rufber.</option>
            <option value="10">Feiert.</option>
            <option value="11">Extern</option>
            <option value="12">Frei</option>
            <option value="13">Mittelsch.</option>
            <option value="14">AFT</option>
            <option value="15">Frühsch.</option>
            <option value="16">LZK</option>
        </select>
        <br>
        <label for="commentInput">Kommentar:</label>
        <input type="text" id="commentInput">
        <br>
        <label for="timeInputVon">Uhrzeit:</label>
        <input type="text" id="timeInputVon" pattern="[0-9]{2}:[0-9]{2}" maxlength="5" placeholder="HH:MM">
        <br>
        <label for="timeInputBis">Uhrzeit:</label>
        <input type="text" id="timeInputBis" pattern="[0-9]{2}:[0-9]{2}" maxlength="5" placeholder="HH:MM">

        <br>
        <button id="submitButton">Übernehmen</button>
        <button id="cancelButton">Abbrechen</button>
 </div>

    <script>
        // Globale Variablen
        let aktuelleWocheOffset = 0;
        let username = '';
        var benutzerIdForms;
        var datumAttributForms; 

        
        document.addEventListener("DOMContentLoaded", function () {

            new Cleave('#timeInputVon', {
                time: true,
                timePattern: ['h', 'm']
            });
            
            // Initialisierungen 
            dayjs.extend(dayjs_plugin_isoWeek); // Plugin für ISO Woche
            
            
            document.addEventListener('contextmenu', handleContextMenu);
            document.addEventListener('click', function(event) {
                if (contextMenu.style.display === 'block' && !contextMenu.contains(event.target)) {
                    closeContextMenu();
                }
            });
            
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
            document.getElementById('submitButton').addEventListener('click', function() {
                submitForm(benutzerIdForms,datumAttributForms);
                closeContextMenu();

            });
            document.getElementById('cancelButton').addEventListener('click', function() {
                closeContextMenu();
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

                $('#mitarbeiterTabelle').css('zoom', zoomLevel);  // Wendet den Zoom-Level nur auf die Tabelle an
            });

            $('#zoomOutButton').on('click', function() {
                zoomLevel -= 0.1;  // Reduziert den Zoom-Level um 10%
                if (zoomLevel < 0.5) zoomLevel = 0.5;  // Setzt einen Mindest-Zoom-Level

                $('#mitarbeiterTabelle').css('zoom', zoomLevel);  // Wendet den Zoom-Level nur auf die Tabelle an
            });


            // Initialisierungen
            
            updateDateAndWeek();
            updateTableHeaders();
            
            $('#mitarbeiterTabelle').on('contextmenu', 'td.employee-cell', function(event) {
                // Hier kommt der zusätzliche Code, der bei einem Rechtsklick ausgeführt werden soll
                benutzerIdForms = $(this).parent().data('userid');
                datumAttributForms = $(this).data('datumattribut');
                
                console.log('Benutzer-ID:', benutzerIdForms);
                console.log('Datum-Wert:', datumAttributForms);
                
            });
        });
    </script>
</body>

</html>