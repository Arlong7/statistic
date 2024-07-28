<?php
session_start();

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'dbconnection.php'; // Include database connection script

$category = $_GET['category'] ?? 'trade_union'; // Default to trade_union if no category is specified
$action = $_POST['action'] ?? '';
$id = $_POST['id'] ?? '';
$name = $_POST['name'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($category == 'trade_union') {
        handleTradeUnion($action, $id, $name, $conn);
    } elseif ($category == 'youth') {
        handleYouth($action, $id, $name, $conn);
    } elseif ($category == 'women') {
        handleWomen($action, $id, $name, $conn);
    }
}

function handleTradeUnion($action, $id, $name, $conn) {
    if ($action == 'create') {
        $stmt = $conn->prepare("INSERT INTO trade_union (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->close();
    } elseif ($action == 'update') {
        $stmt = $conn->prepare("UPDATE trade_union SET name=? WHERE id=?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        $stmt->close();
    } elseif ($action == 'delete') {
        $stmt = $conn->prepare("DELETE FROM trade_union WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

function handleYouth($action, $id, $name, $conn) {
    if ($action == 'create') {
        $stmt = $conn->prepare("INSERT INTO youth (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->close();
    } elseif ($action == 'update') {
        $stmt = $conn->prepare("UPDATE youth SET name=? WHERE id=?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        $stmt->close();
    } elseif ($action == 'delete') {
        $stmt = $conn->prepare("DELETE FROM youth WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

function handleWomen($action, $id, $name, $conn) {
    if ($action == 'create') {
        $stmt = $conn->prepare("INSERT INTO women (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->close();
    } elseif ($action == 'update') {
        $stmt = $conn->prepare("UPDATE women SET name=? WHERE id=?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        $stmt->close();
    } elseif ($action == 'delete') {
        $stmt = $conn->prepare("DELETE FROM women WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

$trade_unions = $conn->query("SELECT * FROM trade_union");
$youth_groups = $conn->query("SELECT * FROM youth");
$women_associations = $conn->query("SELECT * FROM women");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include("nav.php"); ?>

    <div class="container mt-5">
        <h1 class="mb-4 text-center">CRUD Management</h1>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link <?php echo $category == 'trade_union' ? 'active' : ''; ?>" id="trade_union-tab" data-toggle="tab" href="#trade_union" role="tab" aria-controls="trade_union" aria-selected="<?php echo $category == 'trade_union' ? 'true' : 'false'; ?>">Trade Union</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $category == 'youth' ? 'active' : ''; ?>" id="youth-tab" data-toggle="tab" href="#youth" role="tab" aria-controls="youth" aria-selected="<?php echo $category == 'youth' ? 'true' : 'false'; ?>">Youth</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $category == 'women' ? 'active' : ''; ?>" id="women-tab" data-toggle="tab" href="#women" role="tab" aria-controls="women" aria-selected="<?php echo $category == 'women' ? 'true' : 'false'; ?>">Women</a>
            </li>
        </ul>

        <div class="tab-content mt-4" id="myTabContent">
            <div class="tab-pane fade <?php echo $category == 'trade_union' ? 'show active' : ''; ?>" id="trade_union" role="tabpanel" aria-labelledby="trade_union-tab">
                <?php include 'crud_content.php'; ?>
                <form method="POST">
                    <input type="hidden" name="action" id="trade_unionAction">
                    <input type="hidden" name="id" id="trade_unionId">
                    <div class="form-group">
                        <label for="trade_unionName">Name</label>
                        <input type="text" class="form-control" name="name" id="trade_unionName" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
                <table class="table table-bordered mt-3">
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
                                    <button type="button" class="btn btn-warning btn-sm" onclick="openModal('trade_union', 'update', <?php echo htmlspecialchars($trade_union['id']); ?>)">Edit</button>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($trade_union['id']); ?>">
                                        <input type="hidden" name="category" value="trade_union">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="tab-pane fade <?php echo $category == 'youth' ? 'show active' : ''; ?>" id="youth" role="tabpanel" aria-labelledby="youth-tab">
                <!-- Similar content for Youth -->
            </div>

            <div class="tab-pane fade <?php echo $category == 'women' ? 'show active' : ''; ?>" id="women" role="tabpanel" aria-labelledby="women-tab">
                <!-- Similar content for Women -->
            </div>
        </div>
    </div>

    <!-- Modals for Trade Union, Youth, and Women -->
    <div class="modal fade" id="crudModal" tabindex="-1" role="dialog" aria-labelledby="crudModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="crudModalLabel">CRUD</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" id="crudForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" id="modalAction">
                        <input type="hidden" name="category" id="modalCategory">
                        <input type="hidden" name="id" id="modalId">
                        <div class="form-group">
                            <label for="modalName">Name</label>
                            <input type="text" class="form-control" name="name" id="modalName" required>
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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function openModal(category, action, id) {
            $('#modalCategory').val(category);
            $('#modalAction').val(action);
            $('#modalId').val(id);
            $('#crudModal').modal('show');
            if (action === 'update') {
                // Fetch data and populate form fields for update
                $.getJSON(`get_${category}.php?id=${id}`, function(data) {
                    $('#modalName').val(data.name);
                });
            } else {
                $('#modalName').val('');
            }
        }
    </script>
</body>
</html>
