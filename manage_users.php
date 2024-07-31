<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if user's role is defined and is admin
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';



// Database connection
include_once 'dbconnection.php';

// Function to fetch all users from the database
function getAllUsers($conn) {
    $sql = "
        SELECT u.id, u.username, u.role, e.employee_id, e.surname
        FROM users u
        LEFT JOIN employee e ON u.employee_id = e.employee_id
    ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}

// Function to delete a user by ID
function deleteUser($conn, $userId) {
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    return $stmt->affected_rows;
}

// If the delete action is requested
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (isset($_POST['user_id'])) {
        $userId = $_POST['user_id'];
        deleteUser($conn, $userId);
        // Redirect or show success message
        header("Location: manage_users.php");
        exit();
    }
}

// Fetch all users
$users = getAllUsers($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <?php include("nav.php"); ?>
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-4">Manage Users</h1>
        
        <table class="min-w-full bg-white border rounded-lg overflow-hidden">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Username</th>
                    <th class="px-4 py-2">Role</th>
                    <th class="px-4 py-2">Employee ID</th>
                    <th class="px-4 py-2">Surname</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($user['id']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($user['username']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($user['role']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($user['employee_id']) ? htmlspecialchars($user['employee_id']) : 'None'; ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($user['surname']) ? htmlspecialchars($user['surname']) : 'None'; ?></td>
                        <td class="border px-4 py-2">
                            <!-- Delete form -->
                            <form method="post" action="">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this user?')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
