<?php
session_start();

// Default message display
$messages = [];
$search_name = '';
$filter_color = '';

// Retrieve the logged-in username or set to 'Guest' if not logged in
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

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

// Get search inputs only when a form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search_name = trim($_POST['search_name'] ?? '');
    $filter_color = trim($_POST['filter_color'] ?? '');
}

// Base query to get all messages initially
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body::-webkit-scrollbar {
            display: none;
        }
    </style>
    <script>
        // JavaScript to confirm logout
        function confirmLogout() {
            var confirmLogout = confirm("Are you sure you want to log out?");
            if (confirmLogout) {
                window.location.href = "logout.php"; // Redirect to logout.php if confirmed
            }
        }
    </script>
    <title>Home</title>
</head>
<body>
    <section id="home">
        <div style="height: 2000px;" class="upper">
            <header>
                <div class="logo" style="display: flex; align-items: center;">
                    <div id="imglogo" style="margin-right: 10px;">
                        <img src="site_logo.png" alt="Logo">
                        <img src="QCU_Logo_2019.png" alt="Logo">
                        <img src="ccs_logo.png" alt="Logo">
                    </div>
                    <div>
                        <h1 style="margin: 0;"><span>Guess</span> | Who</h1>
                        <p style="margin: 0;">SBIT1C</p>
                    </div>
                </div>                
                <nav>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="about.html">About</a></li>
                        <li><a href="submit.html">Messages</a></li>
                        <li><a href="contact.html">Contact</a></li>
                        <li><a href="javascript:void(0);" onclick="confirmLogout()">Log Out</a></li> <!-- Log out link with confirmation -->
                    </ul>
                </nav>
                <div class="socmed_icons">   
                    <i class="fa fa-bars"></i>
                </div>
            </header>
            <div class="container">
                <center>
                    <div class="intro">
                        <h4>WELCOME, <?= htmlspecialchars($username); ?>!</h4>
                        <br>
                        <div class="typewriter">
                            <h5>THIS IS A PLACE WHERE YOU CAN SHARE YOUR UNSAID THOUGHTS</h5>
                            <br>
                            <div class="filter-options">
                            <form action="index.php" method="POST">
    <input type="text" name="search_name" placeholder="Search..." value="<?= htmlspecialchars($search_name); ?>">
    <select name="filter_color">
        <option value="">Select color</option>
        <option value="#FF0000" <?= $filter_color == '#FF0000' ? 'selected' : ''; ?>>Red</option>
        <option value="#FFA500" <?= $filter_color == '#FFA500' ? 'selected' : ''; ?>>Orange</option>
        <option value="#FFFF00" <?= $filter_color == '#FFFF00' ? 'selected' : ''; ?>>Yellow</option>
        <option value="#008000" <?= $filter_color == '#008000' ? 'selected' : ''; ?>>Green</option>
        <option value="#0000FF" <?= $filter_color == '#0000FF' ? 'selected' : ''; ?>>Blue</option>
        <option value="#EE82EE" <?= $filter_color == '#EE82EE' ? 'selected' : ''; ?>>Violet</option>
    </select>
    <button type="submit">Search</button>
</form>


                            </div>
                        </div>

                        <br>

                        <div class="messagescontainer">
                            <h2>All Messages:</h2>
                            <div class="message-grid">
                                <?php if (!empty($messages)): ?>
                                    <?php foreach ($messages as $message): ?>
                                        <?php 
                                        
                                        $message_id = $message['id'] ?? 0;
                                        $message_text = $message['message'] ?? 'No message available';
                                        $recipient = $message['recipient'] ?? 'Unknown';
                                        $submitted_at = $message['submitted_at'] ?? 'Unknown';
                                        $likes = $message['likes'] ?? 0;
                                        $bgColor = $message['color'] ?? 'blue';

                                       
                                        $messageUrl = "http://localhost/WEBSITE/index.php?message_id=" . urlencode($message_id);
                                        ?>
                                        <div class="message-box" style="background-color: <?= htmlspecialchars($bgColor); ?>;">
                                            <div class="message-header">
                                                <i class="fas fa-envelope"> </i>
                                                <span class="recipient"> To: <?= htmlspecialchars($recipient); ?></span>
                                            </div>
                                            <br><br>
                                            <p class="message-content">
                                                <?= htmlspecialchars($message_text); ?>
                                            </p>
                                            <br><br>
                                            <p class="message-time">
                                                <i class="fas fa-clock"></i>
                                                <?= htmlspecialchars($submitted_at); ?>
                                            </p>

                                            <!-- Like button and share button -->
                                            <div class="message-buttons">
                                                <button class="like-button" data-message-id="<?= htmlspecialchars($message_id); ?>">
                                                    <i class="fas fa-heart"></i> Like (<span class="like-count"><?= htmlspecialchars($likes); ?></span>)
                                                </button>
                                                <button class="share-button" data-link="<?= htmlspecialchars($messageUrl); ?>">
                                                    <i class="fas fa-share-alt"></i> Share
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="no-messages"><center>No messages found.</center></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <br><br>
                    <br><br>
                    <div id="about">
                        <p>From Quezon City University<br> Web Development</p>
                    </div>
                </center>
            </div>
        </div>
    </section>
   
    <!-- Like and Share Button Scripts -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".like-button").forEach(function (button) {
                button.addEventListener("click", function () {
                    const messageId = this.getAttribute("data-message-id");
                    const likeCountSpan = this.querySelector(".like-count");

                    fetch("like_handler.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: "message_id=" + encodeURIComponent(messageId),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            let currentLikes = parseInt(likeCountSpan.textContent, 10);
                            likeCountSpan.textContent = currentLikes + 1;
                        } else {
                            alert("Failed to like the message: " + data.error);
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                    });
                });
            });

            document.querySelectorAll(".share-button").forEach(function (button) {
                button.addEventListener("click", function () {
                    const link = this.getAttribute("data-link");

                    if (navigator.share) {
                        navigator.share({
                            title: "Check out this message",
                            url: link,
                        })
                        .then(() => console.log("Shared successfully"))
                        .catch((error) => console.error("Error sharing", error));
                    } else {
                        navigator.clipboard.writeText(link)
                        .then(() => alert("Link copied to clipboard!"))
                        .catch((err) => alert("Failed to copy link: " + err));
                    }
                });
            });
        });
    </script>
    <script>
        document.body.style.overflow = 'auto';
    </script>
</body>
</html>
