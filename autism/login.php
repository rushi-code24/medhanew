<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "medha_db";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$loginError = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = trim($_POST['emailPhone']);
    $password = $_POST['password'];

    if (empty($phone) || empty($password)) {
        $loginError = "Please fill in both fields.";
    } else {
        $sql = "SELECT id, name, password FROM users WHERE phone = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $phone);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($userId, $userName, $hashedPassword);
                $stmt->fetch();

                if (password_verify($password, $hashedPassword)) {
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['user_name'] = $userName;
                    header("Location: parent_nextpage.html");
                    exit();
                } else {
                    $loginError = "Invalid password.";
                }
            } else {
                $loginError = "Invalid phone number.";
            }
            $stmt->close();
        } else {
            $loginError = "Database error: " . $conn->error;
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEDHA - Login</title>
    <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    padding: 20px;
}

.container {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.15);
    width: 400px;
    max-width: 90%;
    text-align: center;
}

.container h2 {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
    text-align: left;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
    font-size: 14px;
}

.form-group input {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 6px;
    transition: 0.3s;
}

.form-group input:focus {
    border-color: #28a745;
    outline: none;
    box-shadow: 0 0 5px rgba(40, 167, 69, 0.2);
}

.btn {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s;
    margin-top: 10px;
}

.btn:hover {
    background: #218838;
}

.error {
    color: red;
    font-size: 14px;
    margin-bottom: 10px;
}

/* Responsive Design */
@media (max-width: 500px) {
    .container {
        width: 100%;
        padding: 20px;
    }
}
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="POST" action="">
            <?php if (!empty($loginError)) : ?>
                <p class="error"><?php echo htmlspecialchars($loginError); ?></p>
            <?php endif; ?>
            <div class="form-group">
                <label for="emailPhone">Phone Number</label>
                <input type="text" id="emailPhone" name="emailPhone" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
            <p>Don't have an account? <a href="registration.php">create one</a></p>
        </form>
    </div>
</body>
</html>