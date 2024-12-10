<?php
include('./config.php');

// Initialize variables for pagination, search, and sorting
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$recordsPerPage = 5; // Number of records per page
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id'; // Default sort by 'id'
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC'; // Default order is ascending

// Calculate the starting record based on the page number
$startFrom = ($page - 1) * $recordsPerPage;

// SQL Query for Search with Pagination and Sorting
$sql = "SELECT * FROM employee WHERE name LIKE ? OR designation LIKE ? OR attendance_rate LIKE ? OR average_task_efficiency LIKE ? ORDER BY $sort $order LIMIT ?, ?";
$stmt = $conn->prepare($sql);

// Add `%` to the search term only for the SQL query (not displayed in input)
$searchTermWithPercent = '%' . $searchTerm . '%';
$stmt->bind_param("ssssis", $searchTermWithPercent, $searchTermWithPercent, $searchTermWithPercent, $searchTermWithPercent, $startFrom, $recordsPerPage);
$stmt->execute();
$result = $stmt->get_result();

// Count total number of records
$totalResultSql = "SELECT COUNT(*) AS total FROM employee WHERE name LIKE ? OR designation LIKE ? OR attendance_rate LIKE ? OR average_task_efficiency LIKE ?";
$totalStmt = $conn->prepare($totalResultSql);
$totalStmt->bind_param("ssss", $searchTermWithPercent, $searchTermWithPercent, $searchTermWithPercent, $searchTermWithPercent);
$totalStmt->execute();
$totalResult = $totalStmt->get_result()->fetch_assoc();
$totalRecords = $totalResult['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees</title>
    <style>
        /* Global Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
        }

        .container {
            width: 80%;
            max-width: 1200px;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #444;
            margin-bottom: 20px;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        form input[type="text"], form input[type="number"] {
            padding: 10px;
            width: 300px;
            margin-right: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        form button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        td {
            background-color: #f9f9f9;
        }

        td a {
            color: #007bff;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid #007bff;
        }

        td a:hover {
            background-color: #007bff;
            color: white;
        }

        /* Red Delete Button */
        .delete-btn {
            background-color: red;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
            border: 1px solid red;
        }

        .delete-btn:hover {
            background-color: darkred;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            padding: 10px 15px;
            margin: 0 5px;
            text-decoration: none;
            background-color: #f1f1f1;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        .error-message, .success-message {
            text-align: center;
            font-size: 14px;
            margin-top: 10px;
        }

        .success-message {
            color: green;
        }

        .error-message {
            color: red;
        }

        .add {
            background-color: #007bff;
            font:bold;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            border: 1px solid #007bff;
            margin-bottom: 20px;
            width: 200px;
            display: block;
            text-align: center;
            margin: 20px auto; /* Centers the button horizontally */
        }
        .add:hover {
            background-color: white;
            color: #007bff;
            text-emphasis-style: triangle open;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Employee List</h1>
    <!-- Centered Add New Employee Button -->
    <a class="add" href="add_employee.php">Add New Employee</a>

    <!-- Search Form -->
    <form method="GET" action="">
        <input type="text" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Search by Name, Designation, Attendance Rate, or Efficiency" />
        <button type="submit">Search</button>
    </form>

    <!-- Employee Table -->
    <table>
        <thead>
        <tr>
            <th><a href="?search=<?php echo urlencode($searchTerm); ?>&page=1&sort=id&order=<?php echo $order == 'ASC' ? 'DESC' : 'ASC'; ?>">ID</a></th>
            <th><a href="?search=<?php echo urlencode($searchTerm); ?>&page=1&sort=name&order=<?php echo $order == 'ASC' ? 'DESC' : 'ASC'; ?>">Name</a></th>
            <th><a href="?search=<?php echo urlencode($searchTerm); ?>&page=1&sort=designation&order=<?php echo $order == 'ASC' ? 'DESC' : 'ASC'; ?>">Designation</a></th>
            <th><a href="?search=<?php echo urlencode($searchTerm); ?>&page=1&sort=attendance_rate&order=<?php echo $order == 'ASC' ? 'DESC' : 'ASC'; ?>">Attendance Rate</a></th>
            <th><a href="?search=<?php echo urlencode($searchTerm); ?>&page=1&sort=average_task_efficiency&order=<?php echo $order == 'ASC' ? 'DESC' : 'ASC'; ?>">Average Task Efficiency</a></th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['designation'] . "</td>";
                echo "<td>" . $row['attendance_rate'] . "%</td>";
                echo "<td>" . $row['average_task_efficiency'] . "%</td>";
                echo "<td>";
                echo "<a href='edit_employee.php?id=" . $row['id'] . "'>Edit</a> | ";
                echo "<a href='delete_employee.php?id=" . $row['id'] . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this employee?\");'>Delete</a>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No results found</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        <?php
        // Display Pagination Links
        if ($page > 1) {
            echo "<a href='?search=" . urlencode($searchTerm) . "&page=" . ($page - 1) . "'>Prev</a>";
        }

        for ($i = 1; $i <= $totalPages; $i++) {
            $activeClass = ($i == $page) ? 'active' : '';
            echo "<a href='?search=" . urlencode($searchTerm) . "&page=$i' class='$activeClass'>$i</a>";
        }

        if ($page < $totalPages) {
            echo "<a href='?search=" . urlencode($searchTerm) . "&page=" . ($page + 1) . "'>Next</a>";
        }
        ?>
    </div>
</div>

</body>
</html>
