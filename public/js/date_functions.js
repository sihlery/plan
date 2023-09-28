let startOfWeek;


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


