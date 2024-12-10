<?php
include('./config.php');

$message = ''; // Variable to store success or error message

if (isset($_POST['submit'])) {
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $designation =htmlspecialchars($_POST['designation'], ENT_QUOTES, 'UTF-8'); 
    $attendance_rate = htmlspecialchars($_POST['attendance_rate'], ENT_QUOTES, 'UTF-8');
    $average_task_efficiency =htmlspecialchars($_POST['average_task_efficiency'], ENT_QUOTES, 'UTF-8'); 

    // Server-side validation for percentages (between 0 and 100)
    if ($attendance_rate < 0 || $attendance_rate > 100 || $average_task_efficiency < 0 || $average_task_efficiency > 100) {
        $message = "<p class='error-message'>Attendance rate and task efficiency must be between 0 and 100.</p>";
    } else {
        // Insert the data into the database
        $sql = "INSERT INTO employee (name, designation, attendance_rate, average_task_efficiency) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdd", $name, $designation, $attendance_rate, $average_task_efficiency);

        if ($stmt->execute()) {
            $message = "<p class='success-message'>Employee added successfully!</p>";
            // Redirect to main page after 2 seconds
            header("refresh:1;url=index.php");
        } else {
            $message = "<p class='error-message'>Error: " . $conn->error . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #fff;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        form label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            text-align: left;
        }

        form input[type="text"],
        form input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        form button:hover {
            background-color: #0056b3;
        }

        .success-message {
            color: green;
            font-size: 14px;
            margin-top: 10px;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>

</head>
<body>
    <div class="container">
        <h1>Add Employee</h1>

        <!-- Display the success or error message -->
        <?php if (!empty($message)) { echo $message; } ?>

        <form method="POST" action="" onsubmit="return validateForm()">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            
            <label for="designation">Designation:</label>
            <input type="text" id="designation" name="designation" required>
            
            <label for="attendance_rate">Attendance Rate (%):</label>
            <input type="number" id="attendance_rate" name="attendance_rate" step="0.01" required>
            
            <label for="average_task_efficiency">Average Task Efficiency (%):</label>
            <input type="number" id="average_task_efficiency" name="average_task_efficiency" step="0.01" required>
            
            <button type="submit" name="submit">Add Employee</button>
        </form>
    </div>

    <script>
        // Client-side validation
        function validateForm() {
            var attendanceRate = document.getElementById("attendance_rate").value;
            var taskEfficiency = document.getElementById("average_task_efficiency").value;

            if (attendanceRate < 0 || attendanceRate > 100) {
                alert("Attendance rate must be between 0 and 100.");
                return false;
            }

            if (taskEfficiency < 0 || taskEfficiency > 100) {
                alert("Task efficiency must be between 0 and 100.");
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
