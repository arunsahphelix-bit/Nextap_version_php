<?php
$host = 'localhost';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = file_get_contents('database.sql');

$queries = explode(';', $sql);

foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        if ($conn->query($query) === FALSE) {
            echo "Error: " . $conn->error . "\n";
        }
    }
}

echo "Database setup completed successfully!\n";
echo "Admin credentials:\n";
echo "Email: admin@nexttap.in\n";
echo "Password: password\n";

$conn->close();
?>
