//BASE Url für GET ajax-anfragen
var GET_URL = '/plan/backend/ajax/get_data.php';
var SET_URL = '/plan/backend/ajax/set_data.php';


function ladeAbteilungen(username, autoSelect = false) {
    $.ajax({
        url: GET_URL,
        type: 'GET',
        data: {
            action: 'getAbteilungen',
            user: username
        },    
        success: function(response) {
            

            var abteilungen = JSON.parse(response);
            abteilungDropdownHinzufügen(abteilungen);
            
            if (autoSelect && abteilungen.length > 0) {
                const ersteAbteilung = abteilungen[0];
                $('#abteilungDropdown').val(ersteAbteilung);
                ladeBereiche(ersteAbteilung);
                loadTable(ersteAbteilung, "Alle", username);
            }    
        },    
        error: function(xhr, status, error) {
            console.log("Status: " + status);
            console.log("Error: " + error);
            console.log("XHR: ", xhr);
        }    
    });    
}    

function ladeBereiche(abteilung) {
    $.ajax({
        url: GET_URL,
        type: 'GET',
        data: {
            action: 'getBereiche',
            selectedAbteilung: abteilung
        },    
        success: function(response) {
            
            var bereiche = JSON.parse(response);
            
            bereichDropdownHinzufügen(bereiche);
        },    
        error: function(xhr, status, error) {
            console.log("Status: " + status);
            console.log("Error: " + error);
            console.log("XHR: ", xhr);
        }    
    });    
}    





function loadTable(selectedAbteilungValue, selectedBereichValue, username) {
    if (selectedAbteilungValue && selectedBereichValue) {
        $.ajax({
            url: GET_URL,
            type: 'GET',
            data: {
                action: 'getTable',
                selectedAbteilung: selectedAbteilungValue,
                selectedBereich: selectedBereichValue,
                KWbeginn: startOfWeek.format('YYYY-MM-DD'),
                user: username
            },
            success: function(response) {
                $('#tableBody').empty(); // Leert den Body der Tabelle
                $('#mitarbeiterTabelle').DataTable().destroy(); // Zerstört die DataTable Instanz
                $('#tableBody').html(response); // Fügt die neuen Zeilen hinzu

                // Initialisiert die DataTables Instanz erneut und speichert sie in der "table" Variable
                var table = $('#mitarbeiterTabelle').DataTable({
                    pageLength: -1, // Alle Zeilen werden angezeigt
                    lengthChange: false, // Keine Option zur Änderung der Anzahl der angezeigten Zeil     
                    "dom": 'lrtip'               
                });

                // CustomSearchbar Aufgrund von Style            
                $('#customSearchInput').off('keyup').on('keyup', function() {
                    table.search(this.value).draw();
                });
            },
            error: function(xhr, status, error) {
                console.error("An error occurred: ", error);
            }
        });
    } else {
        $('#tableDiv').html('');
    }
}




function submitForm(benutzerIdForms,datumAttributForms ) { //hier wird bestätigen gedrückt 
    var dienstID = document.getElementById('dienstDropdown').value;
    var comment = document.getElementById('commentInput').value;
    var timeVon = document.getElementById('timeInputVon').value;
    var timeBis = document.getElementById('timeInputBis').value;
    var sollk = 70.0 //nur zum testen

    var time1 = timeVon + ' ' + timeBis;
    
    if (benutzerIdForms !== undefined && datumAttributForms !== undefined) {
            
            
            var time2 = '16:00 19:00';
            
            
            $.ajax({
                url: SET_URL, // Pfade zur PHP-Datei anpassen
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'setEinzelnenArbeitstag',
                    userId: benutzerIdForms,
                    datumAttribut: datumAttributForms,
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
                        loadTable(selectedAbteilungValue, selectedBereichValue, username);
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
        }
}
     


