<?php
session_start();

if (isset($_POST['signup'])) {
    header("Location: tb_signup.php");
    exit();
} else if (isset($_POST['login'])) {
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
    $userdb = "id21040595_ahmer";
    $passdb = "Techbulletin#2023";
    $dbname = "id21040595_techbulletin";

    if (empty($errors)) {
        $conn = new mysqli($hostname, $userdb, $passdb, $dbname);
        if ($conn->connect_errno) {
            echo "Connection failed: " . $conn->connect_error;
            $conn->close();
        } else {
            $password = sha1($password);

            $sql = "SELECT user_id, user_email, user_username, user_password FROM user WHERE (user_username = ? OR user_email = ?) AND user_password = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username_email, $username_email, $password);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $_SESSION['signed_in'] = true;
                $_SESSION['user_username'] = $row['user_username'];
                $_SESSION['username'] = $row['user_username'];
                $_SESSION['user_id'] = $row['user_id'];
                $stmt->close();
                $conn->close();
                header("Location: tb_dashboard.php");
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
    header("Location: tb_dashboard.php");
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
			height: 270px;
            margin: 25px auto;
            padding: 1px 50px 20px;
            background-image: url('bg.jpg');
            border-radius: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
			position: fixed;
			bottom: 50px;
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
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 10px;

        }

        button[type="submit"]:hover {
			
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
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
            <button type="submit" name="signup">Signup</button>
            <text align="center">Logging in as Admin? Click <a href="tb_admin_login_prompt.php">here.</a></text>
        </form>
    </div>
</body>
</html>
