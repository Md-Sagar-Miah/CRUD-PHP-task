<?php
include('./config.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM employee WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Employee deleted successfully!";
        header("Location: index.php"); // Redirect back to the main page
    } else {
        echo "Error deleting employee: " . $conn->error;
    }
} else {
    echo "Invalid request!";
}
?>
