<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "IRC";


$conn = new mysqli(hostname: $servername, username: $username, password: $password, database: $dbname, port: $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name_with_initials = htmlspecialchars(string: $_POST['name_with_initials']);
    $participation_category = htmlspecialchars($_POST['participation_category']);
    $email_address = htmlspecialchars($_POST['email_address']);
    $mobile_number = htmlspecialchars($_POST['mobile_number']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check for duplicate username or email
    $check_sql = "SELECT * FROM users WHERE email_address = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param(types: "ss", var: $email_address);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<p class='error-message'> email already exists!</p>";
    } else {
        $sql = "INSERT INTO users (name_with_initials, participation_category, email_address, mobile_number, password)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $name_with_initials, $participation_category, $email_address, $mobile_number, $password);

        if ($stmt->execute()) {
            session_start();
            $_SESSION['registration_success'] = true;
            header("Location: log.html");
            exit();
        } else {
            echo "<p class='error-message'>Error executing statement: " . htmlspecialchars($stmt->error) . "</p>";
        }
        $stmt->close();
    }
    $check_stmt->close();
    $conn->close();
}
?>