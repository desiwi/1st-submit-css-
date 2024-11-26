<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
    $messageId = isset($_POST['message_id']) ? intval($_POST['message_id']) : null;

    if (!$username) {
        echo json_encode(['success' => false, 'error' => 'You must be logged in to like messages.']);
        exit;
    }

    if (!$messageId) {
        echo json_encode(['success' => false, 'error' => 'Invalid message ID.']);
        exit;
    }

    $conn = new mysqli("localhost", "root", "", "GuessWho_db");

    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT id FROM MessageLikes_tbl WHERE message_id = ? AND username = ?");
    $stmt->bind_param("is", $messageId, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'You have already liked this message.']);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO MessageLikes_tbl (message_id, username) VALUES (?, ?)");
    $stmt->bind_param("is", $messageId, $username);

    if ($stmt->execute()) {
        $updateLikesStmt = $conn->prepare("UPDATE Messages_tbl SET likes = likes + 1 WHERE id = ?");
        $updateLikesStmt->bind_param("i", $messageId);
        $updateLikesStmt->execute();
        $updateLikesStmt->close();

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to like the message.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>
