<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sozluk";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

if (isset($_GET['q'])) {
    $q = $conn->real_escape_string($_GET['q']);
    $sql = "SELECT name FROM dictionary WHERE name LIKE '%$q%' ORDER BY name ASC LIMIT 10";
    $res = $conn->query($sql);

    $results = [];
    while ($row = $res->fetch_assoc()) {
        $results[] = $row['name'];
    }

    echo json_encode($results);
}

$conn->close();
?>
