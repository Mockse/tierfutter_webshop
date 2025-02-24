<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Tierfutter-Webshop - Produktsuche</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Tierfutter-Webshop</h1>
    </header>

    <?php
    $db_host = "localhost";
    $db_user = "root";
    $db_password = "";
    $db_name = "tierfutter_webshop";
// Verbindung zur Datenbank herstellen
    $mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);
    if ($mysqli->connect_errno) {
        echo "Fehler beim Zugriff auf MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error; //Fehlerbehandlung
        exit();
    }

    $mysqli->set_charset("utf8");

// Variablen für die Suche und Sortierung
$suche = $_GET['suche'] ?? '';
$sortierung = $_GET['sort_text'] ?? 'ASC';
$kategorie = $_GET['kategorie'] ?? '';
$sortCat = $_GET['sort_category'] ?? 'ASC';
?>

    <nav>
        <a href="index.php">Startseite</a>
    </nav>

    <main>
        <h2>Freitextsuche</h2>
        <form method="GET" action="">
            <input type="text" name="suche" placeholder="Hier suchen" value="<?php echo htmlspecialchars($suche); ?>">
            <label><input type="radio" name="sort_text" value="ASC" onchange="this.form.submit()" <?php echo ($sortierung == 'ASC') ? 'checked' : ''; ?>> Aufsteigend</label>
            <label><input type="radio" name="sort_text" value="DESC" onchange="this.form.submit()" <?php echo ($sortierung == 'DESC') ? 'checked' : ''; ?>> Absteigend</label>
            <button type="submit">Suchen</button>
        </form>

        <?php if (!empty($suche)): ?>
            <h3>Gefundene Produkte</h3>
            <?php
            $sql = "SELECT name, preis FROM Produkt WHERE name LIKE '%" . $mysqli->real_escape_string($suche) . "%' ORDER BY preis $sortierung";
            $result = $mysqli->query($sql);
            if ($result->num_rows > 0): ?>
                <ul>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <li><strong><?php echo htmlspecialchars($row['name']); ?></strong><br>Preis: <?php echo number_format($row['preis'], 2, ',', '.'); ?> €</li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Keine Produkte gefunden.</p>
            <?php endif;
            $result->free();
            ?>
        <?php endif; ?>

        <h2>Kategoriesuche</h2>
        <form method="GET" action="">
            <label for="kategorie">Kategorie:</label>
            <select name="kategorie">
                <option value="" <?php echo ($kategorie == '') ? 'selected' : ''; ?>>--Alle--</option>
                <?php
                $katAbfrage = "SELECT kategorie_id, kategorie FROM Produktkategorie";
                $katResult = $mysqli->query($katAbfrage);
                while ($kat = $katResult->fetch_assoc()): ?>
                    <option value="<?php echo $kat['kategorie_id']; ?>" <?php echo ($kategorie == $kat['kategorie_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($kat['kategorie']); ?>
                    </option>
                <?php endwhile;
                $katResult->free(); ?>
            </select>
            <label><input type="radio" name="sort_category" value="ASC" onchange="this.form.submit()" <?php echo ($sortCat == 'ASC') ? 'checked' : ''; ?>> Aufsteigend</label>
            <label><input type="radio" name="sort_category" value="DESC" onchange="this.form.submit()" <?php echo ($sortCat == 'DESC') ? 'checked' : ''; ?>> Absteigend</label>
            <button type="submit">Suchen</button>
        </form>

        <?php if (isset($_GET['kategorie'])): ?>
            <h3>Gefundene Produkte</h3>
            <?php
            $sqlCat = "SELECT name, preis FROM Produkt";
            if (!empty($kategorie)) {
                $sqlCat .= " WHERE kategorie_id = " . (int)$kategorie;
            }
            $sqlCat .= " ORDER BY preis $sortCat";
            $resultCat = $mysqli->query($sqlCat);
            if ($resultCat->num_rows > 0): ?>
                <ul>
                    <?php while ($rowCat = $resultCat->fetch_assoc()): ?>
                        <li><strong><?php echo htmlspecialchars($rowCat['name']); ?></strong><br>Preis: <?php echo number_format($rowCat['preis'], 2, ',', '.'); ?> €</li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Keine Produkte gefunden.</p>
            <?php endif;
            $resultCat->free();
            ?>
        <?php endif; ?>
    </main>
</body>
</html>
<?php
$mysqli->close();
?>
