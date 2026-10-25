<?php

include "db.php";

// Get the selected user's ID
$id = (int) ($_GET["id"] ?? 0);

$error = "";

// ========================================
// UPDATE USER
// ========================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = (int) ($_POST["id"] ?? 0);
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");

    if ($username === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid username and email.";

    } else {

        // Duplicate email check (ignores this same user)
        $stmt = $conn->prepare(
            "SELECT id FROM users WHERE email = ? AND id <> ?"
        );
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();

        if ($exists) {

            $error = "That email address is already registered.";

        } else {

            // Prepare UPDATE statement
            $stmt = $conn->prepare(
                "UPDATE users
                 SET username = ?, email = ?
                 WHERE id = ?"
            );

            // Bind values
            $stmt->bind_param("ssi", $username, $email, $id);

            // Execute
            $stmt->execute();
            $stmt->close();

            // Return to main page
            header("Location: index.php?msg=updated");
            exit();
        }
    }
}

// ========================================
// GET USER INFORMATION
// ========================================
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Unknown user: go back to the list
if (!$user) {
    header("Location: index.php");
    exit();
}

// Keep what was typed if validation failed
$show_username = $error !== "" ? $username : $user["username"];
$show_email    = $error !== "" ? $email    : $user["email"];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <div class="card">

        <h1>Edit User</h1>

        <?php if ($error !== ""): ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="edit.php">

            <!-- USER ID -->
            <input
                type="hidden"
                name="id"
                value="<?php echo (int) $user["id"]; ?>"
            >

            <!-- USERNAME -->
            <label>Username</label>

            <input
                type="text"
                name="username"
                value="<?php echo htmlspecialchars($show_username); ?>"
                required
            >

            <!-- EMAIL -->
            <label>Email</label>

            <input
                type="email"
                name="email"
                value="<?php echo htmlspecialchars($show_email); ?>"
                required
            >

            <!-- UPDATE BUTTON -->
            <button type="submit" class="btn add">
                Update User
            </button>

            <!-- CANCEL BUTTON -->
            <a href="index.php" class="btn cancel">
                Cancel
            </a>

        </form>

    </div>

</div>

</body>
</html>
