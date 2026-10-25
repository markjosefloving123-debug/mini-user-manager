<?php

include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");

    if ($username === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $conn->close();
        header("Location: index.php?error=invalid");
        exit();
    }

    // Duplicate email check (challenge task)
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();

    if ($exists) {

        $conn->close();
        header("Location: index.php?error=duplicate");
        exit();
    }

    // Prepare SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO users (username, email)
         VALUES (?, ?)"
    );

    // Bind values
    $stmt->bind_param("ss", $username, $email);

    // Execute
    $stmt->execute();

    // Close statement
    $stmt->close();
}

// Return to main page
$conn->close();
header("Location: index.php?msg=added");
exit();

?>
