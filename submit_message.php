<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "GuessWho_db";

$con = new mysqli($servername, $username, $password, $dbname);

if ($con->connect_error) {
    echo "Connection failed: " . $con->connect_error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = ($_POST['message']);
    $recipient = ($_POST['recipient']);
    $color = ($_POST['color']);

    $sql = "INSERT INTO Messages_tbl (message, recipient, color, submitted_at) VALUES ('$message', '$recipient', '$color', NOW())";

    if ($con->query($sql) === TRUE) {
        echo "<script type='text/javascript'>
        alert('Message submitted successfully!');
        window.location.href = 'index.php';
      </script>";
      exit();
    } else {
        echo "Error: " . $sql . "<br>" . $con->error;
    }
}


