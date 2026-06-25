<?php
require_once __DIR__ . '/includes/bootstrap.php';

echo "<h1>System Test Page</h1>";
echo "<p><strong>Environment:</strong> " . APP_ENV . "</p>";
echo "<p><strong>Base URL:</strong> " . BASE_URL . "</p>";

// Test Database Connection
echo "<h2>Database Connection Test</h2>";
try {
    global $pdo;
    if (isset($pdo)) {
        $stmt = $pdo->query("SELECT VERSION() as version");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p style='color: green;'>✅ Database connected successfully! MySQL Version: " . htmlspecialchars($row['version']) . "</p>";
    } else {
         echo "<p style='color: red;'>❌ Database connection failed: \$pdo object not found.</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>Session Info</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<hr>";
echo "<p>Ready for further testing.</p>";
