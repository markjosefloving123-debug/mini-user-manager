<?php

include "db.php";

// ========================================
// SEARCH (challenge task)
// ========================================
$search = trim($_GET["search"] ?? "");

if ($search !== "") {

    $like = "%" . $search . "%";

    $stmt = $conn->prepare(
        "SELECT * FROM users
         WHERE username LIKE ? OR email LIKE ?
         ORDER BY id DESC"
    );

    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();

} else {

    $result = $conn->query("SELECT * FROM users ORDER BY id DESC");
}

// ========================================
// SUCCESS / ERROR MESSAGES (challenge task)
// ========================================
$messages = [
    "added"     => "User added successfully.",
    "updated"   => "User updated successfully.",
    "deleted"   => "User deleted successfully.",
];

$errors = [
    "duplicate" => "That email address is already registered.",
    "invalid"   => "Please enter a valid username and email.",
];

$success = $messages[$_GET["msg"] ?? ""] ?? "";
$error   = $errors[$_GET["error"] ?? ""] ?? "";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini User Manager</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <h1>Mini User Manager</h1>

    <p class="subtitle">
        Simple User Management System using PHP and MySQL
    </p>

    <?php if ($success !== ""): ?>
        <div class="alert success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if ($error !== ""): ?>
        <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- ============================= -->
    <!-- ADD USER FORM                 -->
    <!-- ============================= -->

    <div class="card">

        <h2>Add New User</h2>

        <form action="add.php" method="POST">

            <input
                type="text"
                name="username"
                placeholder="Enter username"
                required
            >

            <input
                type="email"
                name="email"
                placeholder="Enter email"
                required
            >

            <button type="submit" class="btn add">
                Add User
            </button>

        </form>

    </div>

    <!-- ============================= -->
    <!-- USER LIST                     -->
    <!-- ============================= -->

    <div class="card">

        <h2>User List</h2>

        <form action="index.php" method="GET" class="search-form">

            <input
                type="text"
                name="search"
                placeholder="Search by username or email"
                value="<?php echo htmlspecialchars($search); ?>"
            >

            <button type="submit" class="btn edit">Search</button>

            <?php if ($search !== ""): ?>
                <a href="index.php" class="btn cancel">Clear</a>
            <?php endif; ?>

        </form>

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

            <?php if ($result->num_rows > 0): ?>

                <?php while ($row = $result->fetch_assoc()): ?>

                    <tr>

                        <td><?php echo (int) $row["id"]; ?></td>

                        <td><?php echo htmlspecialchars($row["username"]); ?></td>

                        <td><?php echo htmlspecialchars($row["email"]); ?></td>

                        <td>
                            <?php echo date("M d, Y", strtotime($row["created_at"])); ?>
                        </td>

                        <td>

                            <!-- EDIT BUTTON -->
                            <a
                                href="edit.php?id=<?php echo (int) $row["id"]; ?>"
                                class="btn edit"
                            >
                                Edit
                            </a>

                            <!-- DELETE BUTTON -->
                            <a
                                href="delete.php?id=<?php echo (int) $row["id"]; ?>"
                                class="btn delete"
                                onclick="return confirm('Are you sure you want to delete this user?');"
                            >
                                Delete
                            </a>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="5">No users found.</td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>
