//Globale Variable
let startOfWeek;





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




function adjustWeekOffset(amount) {
    aktuelleWocheOffset += amount;
    updateDateAndWeek();
}    




function updateDateAndWeek() {
    const dateWithOffset = dayjs().add(aktuelleWocheOffset, 'week');
    const aktuellesDatum = dateWithOffset.format('DD.MM.YYYY');
    const aktuelleKW = dateWithOffset.isoWeek();
    document.getElementById('aktuellesDatum').innerText = aktuellesDatum;
    document.getElementById('aktuelleKW').innerText = aktuelleKW;
    const selectedAbteilungValue = $('#abteilungDropdown').val();
    const selectedBereichValue = $('#bereichDropdown').val();
    updateTableHeaders();
    if (username) {
        loadTable(selectedAbteilungValue, selectedBereichValue);
    }    
}    

function updateTableHeaders() {
    const days = ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'];
    const tableHeaders = document.querySelectorAll("#mitarbeiterTabelle th");

    startOfWeek = dayjs().add(aktuelleWocheOffset, 'week').startOf('isoWeek');

    days.forEach((day, index) => {
        const currentDayDate = startOfWeek.add(index, 'day');
        const formattedDate = currentDayDate.format('DD.MM.YYYY');
        for (let th of tableHeaders) {
            if (th.textContent.includes(day)) {
                th.innerHTML = `${day} <br> ${formattedDate}`;
            }    
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



function showContextMenu(event) {
    event.preventDefault();
    var targetElement = event.target;

    // Überprüfe, ob das ausgelöste Event von einer Zelle stammt
    if (targetElement.tagName === 'TD' && targetElement.classList.contains('employee-cell')) {
        var contextMenu = document.getElementById('contextMenu');
        contextMenu.style.display = 'block';
        contextMenu.style.left = event.clientX + 'px';
        contextMenu.style.top = event.clientY + 'px';
    }
}

function closeContextMenu() {
    var contextMenu = document.getElementById('contextMenu');
    contextMenu.style.display = 'none';
}


function handleContextMenu(event) {
    event.preventDefault();

    var targetElement = event.target;

    // Überprüfe, ob das ausgelöste Event von einer Zelle stammt
    if (targetElement.tagName === 'TD' && targetElement.classList.contains('employee-cell')) {
        var contextMenu = document.getElementById('contextMenu');
        contextMenu.style.display = 'block';
        contextMenu.style.left = event.clientX + 'px';
        contextMenu.style.top = event.clientY + 'px';
    }
}





