<?php

include "db.php";

// Get selected user ID
$id = (int) ($_GET["id"] ?? 0);

// Prepare DELETE statement
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");

// Bind ID
$stmt->bind_param("i", $id);

// Execute
$stmt->execute();

// Close statement
$stmt->close();
$conn->close();

// Return to main page
header("Location: index.php?msg=deleted");
exit();

?>
