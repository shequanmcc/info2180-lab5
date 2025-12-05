<?php
// INFO2180 Lab 5 - world.php

$host     = 'localhost';
$username = 'lab5_user';
$password = 'password123';
$dbname   = 'world';

try {
    // Connect to database
    $dsn  = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo  = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Decide what we’re looking up: countries or cities
    $mode = isset($_GET['lookup']) ? $_GET['lookup'] : 'country';
    $mode = ($mode === 'cities') ? 'cities' : 'country'; // validate
    $countryInput = isset($_GET['country']) ? trim($_GET['country']) : '';

    if ($mode === 'cities') {
        // ===== CITIES LOOKUP =====
        if ($countryInput === '') {
            header("Content-Type: text/html; charset=utf-8");
            echo "<p>Please enter a country name to look up its cities.</p>";
            exit;
        }

        $stmt = $pdo->prepare(
            "SELECT cities.name, cities.district, cities.population
             FROM cities
             JOIN countries ON countries.code = cities.country_code
             WHERE countries.name LIKE :country"
        );
        $stmt->execute(['country' => "%$countryInput%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } else {
        // ===== COUNTRY LOOKUP (DEFAULT) =====
        if ($countryInput !== '') {
            $stmt = $pdo->prepare(
                "SELECT name, continent, independence_year, head_of_state
                 FROM countries
                 WHERE name LIKE :country"
            );
            $stmt->execute(['country' => "%$countryInput%"]);
        } else {
            $stmt = $pdo->query(
                "SELECT name, continent, independence_year, head_of_state
                 FROM countries"
            );
        }
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    header("Content-Type: text/html; charset=utf-8");
    echo "Database error: " . htmlspecialchars($e->getMessage());
    exit;
}

// We are returning only a fragment (no <html>, no <head>, etc.)
header("Content-Type: text/html; charset=utf-8");

if ($mode === 'cities'): ?>
    <h2>Cities in <?= htmlspecialchars($countryInput) ?></h2>

    <?php if (count($results) === 0): ?>
        <p>No cities found for that country.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>District</th>
                    <th>Population</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['district']) ?></td>
                        <td><?= htmlspecialchars($row['population']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

<?php else: ?>
    <h2>Country Results</h2>

    <?php if (count($results) === 0): ?>
        <p>No matching countries found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Continent</th>
                    <th>Independence Year</th>
                    <th>Head of State</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['continent']) ?></td>
                        <td><?= htmlspecialchars($row['independence_year']) ?></td>
                        <td><?= htmlspecialchars($row['head_of_state']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

<?php endif; ?>
