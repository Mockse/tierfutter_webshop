<?php
/* Studienbrief 1, Seite 12: HTML-Grundstruktur */
?>
<!DOCTYPE html> <!-- Studienbrief 1, Seite 12: Definition des Dokumenttyps HTML5 -->
<html lang="de"> <!-- Studienbrief 1, Seite 15: Spracheinstellung auf Deutsch -->
<head>
    <meta charset="UTF-8"> <!-- Studienbrief 1, Seite 15: Zeichensatz UTF-8 setzen -->
    <title>Tierfutter-Webshop - Produktsuche</title> <!-- Studienbrief 1, Seite 18: Titel der Seite, im Browser-Tab sichtbar -->
</head>
<body> <!-- Studienbrief 1, Seite 15: Rumpf der HTML-Seite beginnt -->
    <header> <!-- Studienbrief 1, Seite 20: Kopfbereich / Header-Element -->
        <h1>Tierfutter-Webshop</h1> <!-- Studienbrief 1, Seite 20: Hauptüberschrift, repräsentiert die Seite -->
    </header>

    <nav> <!-- Studienbrief 1, Seite 25: Navigationsbereich -->
        <a href="index.php">Startseite</a> <!-- Studienbrief 1, Seite 25: Link zur Startseite -->
    </nav>

    <?php
    // Studienbrief 4, Seite 6: Neue MySQLi-Verbindung herstellen
    // Nur eine Verbindung für Freitext- und Kategoriesuche, schließt erst zum Ende.

    $db_host = "localhost";
    $db_user = "root";
    $db_password = "";
    $db_name = "tierfutter_webshop";

    $mysqli = new mysqli($db_host, $db_user, $db_password, $db_name); // Studienbrief 4, Seite 6: Konstruktor für mysqli-Objekt

    if ($mysqli->connect_error) { // Studienbrief 4, Seite 7: Fehlerbehandlung bei Datenbankverbindung
        die("Fehler bei der Verbindung zur Datenbank: " . $mysqli->connect_error); // Studienbrief 3, Seite 9: Skript beenden bei Fehler
    }

    $mysqli->set_charset("utf8"); // Studienbrief 4, Seite 8: Zeichensatz für die Datenbankverbindung auf UTF-8 setzen
    ?>

    <main> <!-- Studienbrief 1, Seite 15: Hauptinhalt der Seite -->
        <h2>Freitextsuche</h2> <!-- Studienbrief 1, Seite 19: Unterüberschrift für die Freitextsuche -->

        <form method="GET" action=""> <!-- Studienbrief 1, Seite 27: Formular, das Daten per GET sendet -->
            <input
                type="text" 
                name="suche" 
                placeholder="Produktname eingeben"> <!-- Studienbrief 1, Seite 29: Texteingabe -->
            <input type="hidden" name="search_text" value="1"> <!-- Studienbrief 1, Seite 30: Verstecktes Feld, kennzeichnet Freitextsuche -->
            <button type="submit">Suchen</button> <!-- Studienbrief 1, Seite 31: Button zum Abschicken des Freitextformulars -->
        </form>

        <section> <!-- Studienbrief 1, Seite 15: Abschnitt für Freitext-Ergebnisse -->
            <?php
            // Studienbrief 3, Seite 10: Auswertung der GET-Parameter
            if (isset($_GET['search_text']) && isset($_GET['suche']) && $_GET['suche'] !== '') {
                // Wenn search_text und suche vorhanden sind und suche nicht leer, wird die Freitextsuche ausgeführt.

                echo '<h2>Gefundene Produkte (Freitextsuche)</h2>'; // Ausgabe einer Überschrift für die Ergebnisse

                // Formular für Sortierung
                echo '<form method="GET" action="" style="display: inline;">'; // Studienbrief 1, Seite 27: Weiteres Formular

                echo '<input type="hidden" name="suche" value="' . strip_tags($_GET['suche']) . '">
                      <input type="hidden" name="search_text" value="1">
                      <button type="submit" name="sort_text" value="' . (isset($_GET['sort_text']) && $_GET['sort_text'] == 'ASC' ? 'DESC' : 'ASC') . '">';

                // Pfeile umdrehen: wenn sort_text noch nicht gesetzt oder "ASC" -> "↑ Preis", sonst "↓ Preis"
                echo (isset($_GET['sort_text']) && $_GET['sort_text'] == 'DESC') ? '↓ Preis' : '↑ Preis';

                echo '</button>';
                // Hidden-Felder für Suche und search_text beibehalten
                // Button wechselt Sortierung: ASC -> DESC, DESC -> ASC

                echo '</form>'; // Schließen des Sortierformulars

                $suche = $mysqli->real_escape_string($_GET['suche']); // Studienbrief 4, Seite 12: Schutz vor SQL-Injection
                $sortierung = isset($_GET['sort_text']) ? $_GET['sort_text'] : 'ASC'; // Default ist aufsteigende Sortierung

                $sqlText = "SELECT name, preis FROM Produkt WHERE name LIKE '%$suche%' ORDER BY preis $sortierung"; // Studienbrief 4, Seite 11: SQL-Abfrage

                $resultText = $mysqli->query($sqlText); // Studienbrief 4, Seite 13: Absetzen der SQL-Abfrage

                if ($resultText->num_rows > 0) { // Studienbrief 4, Seite 14: Prüfen, ob Ergebnisdatensätze vorhanden sind
                    echo "<ul>"; // Hier wird eine ungeordnete Liste (ul) eröffnet
                    while ($rowText = $resultText->fetch_assoc()) { // Studienbrief 4, Seite 14: Ergebnis iterieren
                      /*  Studienbrief 4, Seite 14: fetch_assoc() liefert eine assoziative Array-Darstellung 
                          der gerade gelesenen Zeile aus der Datenbankabfrage (SQL-Ergebnis).  
                          Jede Schleifeniteration repräsentiert einen Datensatz (Produkt). 
                      */                              
                        echo "<li><strong>" . strip_tags($rowText['name']) // Studienbrief 3, Seite 12: strip_tags() entfernt potenzielle HTML-Tags (XSS-Schutz)
                        . "</strong><br>Preis: " 
                        . number_format($rowText['preis'], 2, ',', '.') // Studienbrief 3, Seite 9: number_format() zur Preisformatierung
                        . " €</li>";
                        /*  Diese Zeile gibt pro Datensatz eine Listenzeile (li) aus. 
                            - strip_tags(): Sicherheitsmaßnahme gegen Schadcode in 'name'
                            - number_format(...): Formatiert den Preis auf zwei Nachkommastellen mit Komma als Dezimaltrennzeichen 
                            und Punkt als Tausendertrennzeichen (hier allerdings nicht verwendet, da Standard == '.' als Separator).
                        */
                    }
                    echo "</ul>"; //Liste wird abgeschlossen
                } else {
                    echo "<p>Keine Produkte gefunden.</p>";
                    /*  Studienbrief 4, Seite 15: Keine Datensätze vorhanden
                        Wenn num_rows == 0, wird diese Meldung angezeigt. 
                    */
                }

                $resultText->free(); // Studienbrief 4, Seite 16: Speicher freigeben
            }
            ?>
        </section> <!-- Ende Freitextsuche-Abschnitt -->

        <h2>Kategoriesuche</h2> <!-- Studienbrief 1, Seite 19: Überschrift für den Kategorienabschnitt -->
        <form method="GET" action=""> <!-- Studienbrief 1, Seite 27: GET-Formular für Kategorien -->
            <label for="kategorie">Kategorie:</label> <!-- Studienbrief 1, Seite 28: Label für das Dropdown -->
            <select name="kategorie" id="kategorie"> <!-- Studienbrief 1, Seite 29: Dropdown-Menü zum Auswählen einer Kategorie -->
                <option value="">--Alle--</option>
                <?php
                // Studienbrief 4, Seite 11: SQL-Abfrage, um alle Kategorien anzuzeigen
                $katAbfrage = "SELECT kategorie_id, kategorie FROM Produktkategorie";
                $kategorieErgebnis = $mysqli->query($katAbfrage);

                // Studienbrief 4, Seite 14: Durchlaufen der Ergebniszeilen
                while ($kat = $kategorieErgebnis->fetch_assoc()) {
                    $selected = (isset($_GET['kategorie']) && $_GET['kategorie'] == $kat['kategorie_id']) ? 'selected' : '';
                    echo '<option value="' . $kat['kategorie_id'] . '" ' . $selected . '>' . strip_tags($kat['kategorie']) . '</option>';
                }
                $kategorieErgebnis->free(); // Studienbrief 4, Seite 16: Ergebnisobjekt freigeben
                ?>
            </select>
            <input type="hidden" name="search_category" value="1"> <!-- Studienbrief 1, Seite 30: Verstecktes Feld zur Kennzeichnung der Kategoriesuche -->
            <button type="submit">Suchen</button> <!-- Studienbrief 1, Seite 31: Button zum Abschicken der Kategoriesuche -->
        </form>

        <section> <!-- Bereich zur Ausgabe der Kategorie-Ergebnisse -->
            <?php
            // Studienbrief 3, Seite 10: Prüfen, ob Kategorie-Suche aktiv ist
            if (isset($_GET['search_category'])) {
                echo '<h2>Gefundene Produkte (Kategoriesuche)</h2>';

                // Formular für Sortier-Button
                echo '<form method="GET" action="" style="display:inline;">';

                // Ein verstecktes Eingabefeld wird hinzugefügt, das den Wert der Kategorie aus der GET-Anfrage enthält.
                echo '<input type="hidden" name="kategorie" value="' . (int)$_GET['kategorie'] . '">';

                // Ein weiteres verstecktes Eingabefeld wird hinzugefügt, das einen festen Wert "1" für "search_category" enthält.
                echo '<input type="hidden" name="search_category" value="1">';

                // Ein Button wird erstellt, der die Sortierreihenfolge umschaltet, wenn er geklickt wird.
                // Pfeile umdrehen: wenn sort_category noch nicht gesetzt oder "ASC" -> "↑ Preis", sonst "↓ Preis"
                echo '<button type="submit" name="sort_category" value="' . (isset($_GET['sort_category']) && $_GET['sort_category'] == 'ASC' ? 'DESC' : 'ASC') . '">';

                echo (isset($_GET['sort_category']) && $_GET['sort_category'] == 'DESC') ? '↓ Preis' : '↑ Preis';

                echo '</button>';
                echo '</form>';

                $sortCat = isset($_GET['sort_category']) ? $_GET['sort_category'] : 'ASC'; // Default aufsteigend

                // Studienbrief 4, Seite 11: SQL-Abfrage mit oder ohne Filter auf kategorie_id
                if (isset($_GET['kategorie']) && $_GET['kategorie'] !== '') {
                    $sqlCat = "SELECT name, preis FROM Produkt WHERE kategorie_id = " . (int)$_GET['kategorie'] . " ORDER BY preis $sortCat";
                } else {
                    $sqlCat = "SELECT name, preis FROM Produkt ORDER BY preis $sortCat";
                }

                $resultCat = $mysqli->query($sqlCat); // Studienbrief 4, Seite 13: Abfrage ausführen

                if ($resultCat->num_rows > 0) {
                    echo "<ul>";
                    while ($rowCat = $resultCat->fetch_assoc()) {
                        echo "<li><strong>" . strip_tags($rowCat['name']) . "</strong><br>Preis: " . number_format($rowCat['preis'], 2, ',', '.') . " €</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>Keine Produkte gefunden.</p>";
                }
                $resultCat->free(); // Studienbrief 4, Seite 16: Ergebnisobjekt freigeben
            }

            $mysqli->close(); // Studienbrief 4, Seite 17: Datenbankverbindung schließen, am Ende aller Suchen
            ?>
        </section> <!-- Ende des Kategoriesuche-Abschnitts -->
    </main> <!-- Ende Hauptbereich -->
</body>
</html> <!-- Studienbrief 1, Seite 15: Rumpf der HTML-Seite endet -->
