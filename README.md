# Dienstplan
## Projekteinrichtung
- [x] Github Repo anlegen und einrichten
- [x] SQL-Datenbank - Tabellen überprüfen (Lokal)
- [x] Webpage Lokal übernehmen
z
## Erste Aufgaben
- [x] Local Web-Hosting hinkriegen (z.B. mit XAMPP)
- [x] Local Webpage mit Lokalen SQL-Datenbank verknüpfen
- [x] Überprüfen ob jetzt "alles" wie in der echten Webpage funktioniert

## Ab hier beginnt die echte Arbeit !

- index.php file: Unsere "main" Datei, die die Seite aufbaut. Hier ist der ganze Code.
- functions file: Funktionen. Datei ist schon verknüpft mit index file


Infos:
- new mysqli($db_host, $db_user, $db_passwd, $db_database); erstellt Verbindng zur Datenbank
- lokale parameter: "localhost", "root"(Username), ""(Passwort leer), "diplandb"(Name der Datenbank)
Datenbank:
- "SeheAbteilung" -> Alle sichtbaren Abteilungen für User

TODO:
Zuerst:
- Arbeitzeiten eintragen
    - copy&paste von Arbeitstagen angenehm gestalten
    - Urlaub, Arbeitsort(Homeoffice, FM), etc.
    - [x] Sobald "kopieren" gedrückt wurde soll Button ausgegraut werden
        - ~~Problem: Format verrutscht bei Wiederanzeige des Buttons.~~
- Wochenverschiebung
    - berechtigt nur auf X Wochen (Standard: 1 woche)
    - Pfeil unten: aktuelle Woche
- neues Design
    - einhaltliche Farben
    - Darkmode
- Funktionen von offline Dienstplan vergleichen 
    - Rechtsklick:
        - Von: (Zeit __:__)
        - Bis: (Zeit __:__)
        - 1,5h Button ?! Keine Ahnung
        - Dienstart:
            - Normal, Spätschicht, FZA, Urlaub, AZV, Kurs, Krank, get. Dienst, Rufber., Feiert, Extern, Frei, Mittelsch., AFT, Frühsch., FZK
        - Kommentar:
            - Nix (leer, default), Instandh., Wunsch, Bitte früh, Bitte spät, Rufbereitsch.
    - Ganz rechts die Wochenstunden (Spalte)
    - Ganz oben Dienstzeiten (Zeile)
    - KEIN Scrollen möglich aktuell, man muss rechts den Balken verschieben
    - Und vieles mehr (folgt)

Später:
- Passwortmanagement 
    - Vershclüsselung (Hilfe fragen) -> Hashwert?
- Datenbank neu schreiben(aktualisieren)
    - Tabellen die nicht mehr benutzt werden löschen 
- Dispo
- Abteilungsübergreifende Leserechte
    - Arbeitsausfallgrund darf nur von Vorgesetztem gesehen werden
- evtl. ein kleines Tutorial als Option -> FAQ einbauen? 


## Marc Zeugs
 
 Ziel der Projekt struktur:
mein_projekt/
│
├── db/
│   ├── connect.php
│   └── queries.php
│
├── functions/
│   ├── utility.php
│   └── business_logic.php
│
├── public/
│   ├── css/
│   │   └── styles.css
│   ├── js/
│   │   └── script.js
│   └── index.php
│
├── .gitignore
└── README.md
