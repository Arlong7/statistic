<?php
session_start();

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'dbconnection.php'; // Include database connection script

$action = $_POST['action'] ?? '';
$id = $_POST['id'] ?? '';
$name = $_POST['name'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($action == 'create') {
        $stmt = $conn->prepare("INSERT INTO trade_union (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            $message = "Trade Union created successfully.";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($action == 'update') {
        $stmt = $conn->prepare("UPDATE trade_union SET name=? WHERE id=?");
        $stmt->bind_param("si", $name, $id);
        if ($stmt->execute()) {
            $message = "Trade Union updated successfully.";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($action == 'delete') {
        $stmt = $conn->prepare("DELETE FROM trade_union WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "Trade Union deleted successfully.";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$trade_unions = $conn->query("SELECT * FROM trade_union");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade Union Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include("nav.php"); ?>

    <div class="container mt-5">
        <h1 class="mb-4 text-center">Trade Union Management</h1>

        <?php if (isset($message)): ?>
            <div class="alert alert-<?php echo strpos($message, 'Error') !== false ? 'danger' : 'success'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#tradeUnionModal" onclick="openModal('create')">
            Add Trade Union
        </button>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($trade_union = $trade_unions->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($trade_union['id']); ?></td>
                        <td><?php echo htmlspecialchars($trade_union['name']); ?></td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm" onclick="openModal('update', <?php echo htmlspecialchars($trade_union['id']); ?>)">Edit</button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($trade_union['id']); ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Trade Union Modal -->
    <div class="modal fade" id="tradeUnionModal" tabindex="-1" role="dialog" aria-labelledby="tradeUnionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tradeUnionModalLabel">Trade Union</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" id="tradeUnionForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" id="formAction">
                        <input type="hidden" name="id" id="tradeUnionId">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(action, id = null) {
            document.getElementById('formAction').value = action;
            document.getElementById('tradeUnionId').value = id;

            if (action === 'update' && id) {
                fetch(`get_trade_union.php?id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('name').value = data.name;
                    });
            }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
