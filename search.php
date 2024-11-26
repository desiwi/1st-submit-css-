<?php
session_start();

// Default message display
$messages = [];
$search_name = '';
$filter_color = '';

// Retrieve the logged-in username or set to 'Guest' if not logged in
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get search inputs
    $search_name = trim($_POST['search_name'] ?? '');
    $filter_color = trim($_POST['filter_color'] ?? '');

    // Database connection credentials
    $servername = "localhost";
    $dbUsername = "root";
    $password = "";
    $dbname = "GuessWho_db";

    // Create connection
    $conn = new mysqli($servername, $dbUsername, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Base query
    $sql = "SELECT id, message, color, recipient, submitted_at, likes FROM Messages_tbl WHERE 1=1";
    $params = [];
    $types = "";

    // Add filters if provided
    if (!empty($search_name)) {
        $sql .= " AND recipient LIKE ?";
        $params[] = "%" . $search_name . "%";
        $types .= "s";
    }

    if (!empty($filter_color)) {
        $sql .= " AND color = ?";
        $params[] = $filter_color;
        $types .= "s";
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Statement preparation failed: " . $conn->error);
    }

    // Bind parameters dynamically if any
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    // Execute the query
    if (!$stmt->execute()) {
        die("Query execution failed: " . $stmt->error);
    }

    // Fetch results
    $result = $stmt->get_result();
    $messages = $result->fetch_all(MYSQLI_ASSOC);

    // Close connections
    $stmt->close();
    $conn->close();
}
?>
