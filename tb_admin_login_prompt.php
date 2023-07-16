<?php
session_start();

if (isset($_POST['login'])) {
    $errors = array();
    $username_email = strip_tags($_POST['username_email']);
    $password = $_POST['password'];

    $user = array(
        "Username or Email" => $username_email,
        "Password" => $password
    );

    foreach ($user as $key => $value) {
        if (empty($value) || trim($value) == '') {
            $errors[$key] = $key . " cannot be empty.<br>";
        }
    }

    $hostname = "localhost";
    $userdb = "root";
    $passdb = "";
    $dbname = "techbulletin1";

    if (empty($errors)) {
        $conn = new mysqli($hostname, $userdb, $passdb, $dbname);
        if ($conn->connect_errno) {
            echo "Connection failed: " . $conn->connect_error;
            $conn->close();
        } else {
          

            $sql = "SELECT admin_id, admin_email, admin_username FROM admin WHERE (admin_username = ? OR admin_email = ?) AND admin_password = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username_email, $username_email, $password);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $_SESSION['signed_in'] = true;
                $_SESSION['user_username'] = $row['admin_username'];
                $_SESSION['username'] = $username;
                $_SESSION['admin_id'] = $row['admin_id'];
                $stmt->close();
                $conn->close();
                header("Location: tb_admin_dashboard.php");
                exit();
            } else {
                $errors['Login'] = "Invalid credentials.";
                $stmt->close();
                $conn->close();
            }
        }
    } 
}

// Check if the user is already signed in
if (isset($_SESSION['signed_in']) && $_SESSION['signed_in'] === true) {
    header("Location: tb_admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
			background-image: url('bg.jpg');
			background-size: 100%;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 430px;
			height: 300px;
            margin: 20px auto;
            padding: 1px 50px 20px;
            background-image: url('bg.jpg');
            border-radius: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
			position: fixed;
			bottom: 30px;
			right: 375px;
			
        }
        h2 {
            text-align: center;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button[type="submit"] {
            width: 100%;
            padding: 10px;
            background-image: url('button1.jpg');
			background-size: 100%;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 10px;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Login</h2>
        <h2>Authorized Access Only!</h2>
        <form method="post" action="">
            <?php
            if (!empty($errors)) {
                echo '<div class="error">';
                foreach ($errors as $error) {
                    echo $error . '<br>';
                }
                echo '</div>';
            }
            ?>
            <input type="text" name="username_email" placeholder="Username or Email">
            <br>
            <input type="password" name="password" placeholder="Password">
            <br>
            <button type="submit" name="login">Login</button>
            <br>
            <p>Go back to user login? <a href="tb_login_prompt.php">Click here</a></p>
        </form>
       
        
    

    </div>
</body>
</html>
