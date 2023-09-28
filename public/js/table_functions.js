function handleConfirmButton(username) {
    const newUsername = $('#usernameInput').val().trim();
    if (newUsername !== username) {
        resetDropdowns();
    }
    username = newUsername;
    ladeAbteilungen(username, true);

    return newUsername;
}


function handleAbteilungChange(event, username) {
    const selectedAbteilungValue = $(event.target).val();
    if (username) {
        ladeBereiche(selectedAbteilungValue);
        loadTable(selectedAbteilungValue, "Alle", username);
    } else {
        alert("Bitte Benutzername eingeben und bestätigen.");
    }
}

function handleBereichChange(event, username) {
    const selectedAbteilungValue = $('#abteilungDropdown').val();
    const selectedBereichValue = $(event.target).val();
    if (username) {
        loadTable(selectedAbteilungValue, selectedBereichValue, username);
    } else {
        alert("Bitte Benutzername eingeben und bestätigen.");
    }
}


function ladeAbteilungen(username, autoSelect = false) {
    $.ajax({
        url: 'fetch_data.php',
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
        url: 'fetch_data.php',
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


function abteilungDropdownHinzufügen(abteilungen) {
    $('#abteilungDropdown').empty();
    for (var i = 0; i < abteilungen.length; i++) {
        var abteilung = abteilungen[i];
        $('#abteilungDropdown').append('<option value="' + abteilung + '">' + abteilung + '</option>');
    }
}

function bereichDropdownHinzufügen(bereiche) {
    $('#bereichDropdown').empty();
    $('#bereichDropdown').append('<option value="Alle">Alle</option>');
    for (var i = 0; i < bereiche.length; i++) {
        var bereich = bereiche[i]["Bereich"];
        $('#bereichDropdown').append('<option value="' + bereich + '">' + bereich + '</option>');
    }
}

function resetDropdowns() {
    $('#abteilungDropdown, #bereichDropdown').empty();
    $('#abteilungDropdown').append('<option value="" disabled selected>Wählen Sie eine Abteilung</option>');
    $('#bereichDropdown').append('<option value="" disabled selected>Wählen Sie einen Bereich</option>');
}




function loadTable(selectedAbteilungValue, selectedBereichValue, username) {
    if (selectedAbteilungValue && selectedBereichValue) {
        $.ajax({
            url: 'fetch_data.php',
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

                // Initialisiert die DataTables Instanz erneut
                $('#mitarbeiterTabelle').DataTable({
                    pageLength: -1, // Alle Zeilen werden angezeigt
                    lengthChange: false, // Keine Option zur Änderung der Anzahl der angezeigten Zeil                    
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




