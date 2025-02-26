<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8"> <!-- Zeichensatz auf UTF-8 setzen für Sonderzeichen -->
    <meta name="Felix Mocker" content="KÜ PR3_HFH" /> <!-- Autor und Inhalt der Seite -->
    <title>Tierfutter-Webshop - Produktsuche</title> <!-- Titel der Seite -->
    <link rel="stylesheet" href="styles.css?v=1.0"> <!-- Externe CSS-Datei einbinden -->
</head>
<body>
    <header>
        <h1>Tierfutter-Webshop</h1> <!-- Hauptüberschrift der Seite -->
    </header>

    <?php
    // Verbindungsdaten für die Datenbank
    $db_host = "localhost"; // Hostname der Datenbank (lokaler Server)
    $db_user = "root"; // Benutzername für die Datenbank
    $db_password = ""; // Passwort (leer, da lokale Entwicklung)
    $db_name = "tierfutter_webshop"; // Name der Datenbank
    
    // Verbindung zur Datenbank herstellen
    $mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);
    
    // Überprüfung, ob die Verbindung erfolgreich war
    if ($mysqli->connect_errno) {
        echo "Fehler beim Zugriff auf MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        exit(); // Skript beenden, falls Verbindung fehlschlägt
    }

    // Zeichensatz auf UTF-8 setzen, um Sonderzeichen korrekt darzustellen
    $mysqli->set_charset("utf8");

    // Variablen aus der GET-Anfrage auslesen
    $suche = $_GET['suche'] ?? ''; // Suchbegriff aus dem URL-Parameter
    $sortierung = $_GET['sort_text'] ?? 'ASC'; // Standardmäßig aufsteigende Sortierung für Freitextsuche
    $kategorie = $_GET['kategorie'] ?? '0'; // Ausgewählte Kategorie aus der URL übernehmen
    $sortCat = $_GET['sort_category'] ?? 'ASC'; // Standardmäßig aufsteigende Sortierung für die Kategoriesuche
    ?>

    <nav>
        <a href="index.php">Startseite</a> <!-- Link zur Startseite -->
    </nav>

    <main>
        <h2>Freitextsuche</h2> <!-- Bereich für die Freitextsuche -->
        <form method="GET" action="">
            <input type="text" name="suche" placeholder="Hier Suchbegriff eingeben" value="<?php echo($suche); ?>"> <!-- Suchfeld -->
            <!-- Sortieroptionen für die Freitextsuche -->
            <label><input type="radio" name="sort_text" value="ASC" onchange="this.form.submit()" <?php echo ($sortierung == 'ASC') ? 'checked' : ''; ?>> Aufsteigend</label>
            <label><input type="radio" name="sort_text" value="DESC" onchange="this.form.submit()" <?php echo ($sortierung == 'DESC') ? 'checked' : ''; ?>> Absteigend</label>
            <button type="submit">Suchen</button> <!-- Suchbutton -->
        </form>

        <?php if (!empty($suche)): ?>
            <h3>Gefundene Produkte</h3> <!-- Überschrift für Ergebnisse -->
            <?php
            // SQL-Abfrage für die Freitextsuche
            $sql = "SELECT name, preis FROM Produkt WHERE name LIKE '%" . $suche . "%' ORDER BY preis $sortierung";
            $result = $mysqli->query($sql);
            
            if ($result->num_rows > 0): ?>
                <ul>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <li><strong><?php echo($row['name']); ?></strong><br>Preis: <?php echo number_format($row['preis'], 2, ',', '.'); ?> €</li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Keine Produkte gefunden.</p>
            <?php endif;
            $result->free(); // Speicher für das Ergebnis freigeben
            ?>
        <?php endif; ?>

        <h2>Kategoriesuche</h2> <!-- Bereich für die Kategoriesuche -->
        <form method="GET" action="">
            <label for="kategorie">Kategorie:</label>
            <select name="kategorie"> <!-- Dropdown für die Auswahl der Kategorie -->
                <option value="" <?php echo ($kategorie == '') ? 'selected' : ''; ?>>--Alle--</option>
                <?php
                // Kategorien aus der Datenbank abrufen
                $katAbfrage = "SELECT kategorie_id, kategorie FROM Produktkategorie";
                $katResult = $mysqli->query($katAbfrage);
                while ($kat = $katResult->fetch_assoc()): ?>
                    <option value="<?php echo $kat['kategorie_id']; ?>" <?php echo ($kategorie == $kat['kategorie_id']) ? 'selected' : ''; ?>>
                        <?php echo($kat['kategorie']); ?>
                    </option>
                <?php endwhile;
                $katResult->free(); ?>
            </select>
            <!-- Sortieroptionen für die Kategoriesuche -->
            <label><input type="radio" name="sort_category" value="ASC" onchange="this.form.submit()" <?php echo ($sortCat == 'ASC') ? 'checked' : ''; ?>> Aufsteigend</label>
            <label><input type="radio" name="sort_category" value="DESC" onchange="this.form.submit()" <?php echo ($sortCat == 'DESC') ? 'checked' : ''; ?>> Absteigend</label>
            <button type="submit">Suchen</button> <!-- Suchbutton -->
        </form>

        <?php if (isset($_GET['kategorie'])): ?>
            <h3>Gefundene Produkte</h3> <!-- Überschrift für Ergebnisse -->
            <?php
            // SQL-Abfrage für die Kategoriesuche
            $sqlCat = "SELECT name, preis FROM Produkt";
            if (!empty($kategorie)) {
                $sqlCat .= " WHERE kategorie_id = " . (int)$kategorie;
            }
            $sqlCat .= " ORDER BY preis $sortCat";
            $resultCat = $mysqli->query($sqlCat);
            
            if ($resultCat->num_rows > 0): ?>
                <ul>
                    <?php while ($rowCat = $resultCat->fetch_assoc()): ?>
                        <li><strong><?php echo($rowCat['name']); ?></strong><br>Preis: <?php echo number_format($rowCat['preis'], 2, ',', '.'); ?> €</li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Keine Produkte gefunden.</p>
            <?php endif;
            $resultCat->free(); // Speicher für das Ergebnis freigeben
            ?>
        <?php endif; ?>
    </main>
</body>
</html>
<?php
$mysqli->close(); // Datenbankverbindung schließen
?>
