<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "todo_db";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_errno) {
    die("Database connection failed: " . $conn->connect_error);
}

$successMessage = "";
$successType = ""; // To define alert color

// ADD a new task
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $item = $_POST['item'];
    if (!empty($item)) {
        $query = "INSERT INTO todo (name, status) VALUES ('$item', 0)";
        $conn->query($query);
        header("Location: index.php?msg=added");
        exit();
    }
}

// DELETE a task
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM todo WHERE id=$id";
    $conn->query($query);
    header("Location: index.php?msg=deleted");
    exit();
}

// MARK AS DONE
if (isset($_GET['done'])) {
    $id = $_GET['done'];
    $query = "UPDATE todo SET status=1 WHERE id=$id";
    $conn->query($query);
    header("Location: index.php?msg=done");
    exit();
}

// HANDLE ALERT MESSAGES
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'added':
            $successMessage = "Item Added Successfully!";
            $successType = "primary"; // Blue
            break;
        case 'deleted':
            $successMessage = "Item Deleted Successfully!";
            $successType = "danger"; // Red
            break;
        case 'done':
            $successMessage = "Item Marked as Done Successfully!";
            $successType = "success"; // Green
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Todo List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .todo-container { max-width: 500px; margin: auto; margin-top: 50px; }
        .completed { text-decoration: line-through; color: gray; }
        .alert-success { background-color: #d4edda; color: #155724; } /* Green */
        .alert-danger { background-color: #f8d7da; color: #721c24; } /* Red */
        .alert-primary { background-color: #cce5ff; color: #004085; } /* Blue */
    </style>
</head>
<body>

<main>
    <div class="container">
        <div class="todo-container">

            <!-- Alert Message (Only Visible When Action is Done) -->
            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-<?php echo $successType; ?> text-center" id="alertBox">
                    <?php echo $successMessage; ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header text-center">
                    <h5 class="mb-0">Todo List</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="input-group mb-3">
                            <input type="text" name="item" class="form-control" placeholder="Add a Todo Item" required>
                            <button type="submit" name="add" class="btn btn-dark">Add Item</button>
                        </div>
                    </form>

                    <?php
                    $query = "SELECT * FROM todo";
                    $result = $conn->query($query);
                    
                    if ($result->num_rows > 0) {
                        echo '<ul class="list-group">';
                        while ($row = $result->fetch_assoc()) {
                            $completed = $row['status'] == 1 ? 'completed' : '';
                            echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                            echo '<span class="' . $completed . '">' . $row['name'] . '</span>';
                            echo '<div>';
                            if ($row['status'] == 0) {
                                echo '<a href="?done=' . $row['id'] . '" class="btn btn-primary btn-sm">Mark as Done</a> ';
                            }
                            echo '<a href="?delete=' . $row['id'] . '" class="btn btn-danger btn-sm">Delete</a>';
                            echo '</div>';
                            echo '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<div class="text-center text-muted mt-3">Your list is empty</div>';
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Remove alert message after 3 seconds
    setTimeout(() => {
        let alertBox = document.getElementById('alertBox');
        if (alertBox) {
            alertBox.style.display = 'none';
        }
    }, 1000);
</script>

</body>
</html>
