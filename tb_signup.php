<?php
session_start();
$errors = array();

if (isset($_POST['signup'])) {

    // Setting variables and sanitizing inputs from HTML tags
    $fname = strip_tags($_POST['fname']);
    $minitial = strip_tags($_POST['minitial']);
    $lname = strip_tags($_POST['lname']);
    $email = $_POST['email'];
    $password = sha1(strip_tags($_POST['password']));
    $repassword = sha1(strip_tags($_POST['repassword']));
    $username = strip_tags($_POST['username']);
    $bio = strip_tags($_POST['bio']);
    $studNumber = strip_tags($_POST['studNum']);

    // Input validation
    $inputs = array(
        "First Name" => $fname,
        "Middle Initial" => $minitial,
        "Last Name" => $lname,
        "Email" => $email,
        "Password" => $password,
        "Re-entered password" => $repassword,
        "Username" => $username,
        "Bio" => $bio,
        "Student Number" => $studNumber
    );

    // Database connection
    $hostname = "localhost";
    $userdb = "id21040595_ahmer";
    $passdb = "Techbulletin#2023";
    $dbname = "id21040595_techbulletin";
    $conn = new mysqli($hostname, $userdb, $passdb, $dbname);

    if ($conn->connect_errno) {
        echo "Failed to connect to Database: " . $conn->connect_error;
        $conn->close();
        exit;
    }

    // Validating user inputs
    foreach ($inputs as $key => $value) {
        if ($key == "password" || $key == "repassword" || $key == "bio") {
            continue;
        } else {
            switch ($key) {
                case "First Name":
                    if (!preg_match('/^[a-zA-Z]+$/', $value)) {
                        $errors[$key] = $key . " can only have letters.";
                    }
                    if (strlen(trim($value)) == 0) {
                        $errors[$key] = $key . " cannot be empty.";
                    }
                    break;

                
                case "Middle Initial":
                case "Last Name":
                    if (!preg_match('/^[a-zA-Z]+$/', $value)) {
                        $errors[$key] = $key . " can only have letters.";
                    }
                    if (strlen(trim($value)) == 0) {
                        $errors[$key] = $key . " cannot be empty.";
                    }
                    break;
                case "Email":
                    $email_sql = "SELECT user_email FROM user WHERE user_email = ?";
                    $email_stmt = $conn->prepare($email_sql);
                    $email_stmt->bind_param("s", $email);
                    $email_stmt->execute();
                    $email_dupe = $email_stmt->get_result();
                    if ($email_dupe->num_rows > 0) {
                        $errors[$key] = $value . " already exists.";
                    }
                    if (strlen(trim($value)) == 0) {
                        $errors[$key] = $key . " cannot be empty.";
                    }
                    break;
                case "Password":
                    if (strlen(trim($value)) == 0) {
                        $errors[$key] = $key . " cannot be empty.";
                    }
                    if ($value != $repassword) {
                        $errors[$key][] = "Your passwords do not match!";
                    }
                    break;
                case "Username":
                    $username_sql = "SELECT user_username FROM user WHERE user_username = ?";
                    $username_stmt = $conn->prepare($username_sql);
                    $username_stmt->bind_param("s", $username);
                    $username_stmt->execute();
                    $username_dupe = $username_stmt->get_result();
                    if ($username_dupe->num_rows > 0) {
                        $errors[$key] = $value . " already exists.";
                    }
                    if (strlen(trim($value)) == 0) {
                        $errors[$key] = $key . " cannot be empty.";
                    }
                    break;
                case "Student Number":
                    $studNum_sql = "SELECT user_studnumber FROM user WHERE user_studnumber = ?";
                    $studNum_stmt = $conn->prepare($studNum_sql);
                    $studNum_stmt->bind_param("s", $studNumber);
                    $studNum_stmt->execute();
                    $studNum_dupe = $studNum_stmt->get_result();
                    if ($studNum_dupe->num_rows > 0) {
                        $errors[$key] = $value . " already exists.";
                    }
                    if (strlen(trim($value)) == 0) {
                        $errors[$key] = $key . " cannot be empty.";
                    }
                    if (!is_numeric($value)) {
                        $errors[$key] = $key . " is not a number.";
                    }
                    if (strlen((string)$value) !== 9) {
                        $errors[$key] = $key . " should only be 9 digits long.";
                    }
                    break;
                default:
                    break;
            }
        }
    }

    if (empty($errors)) {
        // Prepare and bind parameterized SQL statement
        $stmt = $conn->prepare("INSERT INTO user (user_fname, user_minitial, user_lname, user_email, user_password, user_username, user_bio, user_studnumber, user_profile) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $fname, $minitial, $lname, $email, $password, $username, $bio, $studNumber, $profileData);
         if ($_FILES['profile']['size'] > 0) {
            $profileData = file_get_contents($_FILES['profile']['tmp_name']);
        } else {
            // If no file is uploaded, read from local machine
            $imageName = "default.jpg";
            $profileData = file_get_contents($imageName);
        }

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();

            header("Location: tb_registration_success.php");
            exit;
        } else {
            $stmt->close();
            $conn->close();
            echo "Error signing up: " . $stmt->error . ". Please try again.";
        }
    } else {
        $conn->close();
    }
} else if (isset($_POST['login'])) {
    header("Location: tb_login_prompt.php");
    exit();
} else {
    // Default values
    $fname = "";
    $minitial = "";
    $lname = "";
    $email = "";
    $password = "";
    $repassword = "";
    $username = "";
    $profile = "";
    $bio = "";
    $studNumber = "";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('bg.jpg');
            margin: 0;
            padding: 0;
			background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-image: url('blur.jpg');
			background-size: 500%;
            border-radius: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="file"] {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        button[type="submit"] {
            padding: 10px;
            background-image: url('button1.jpg');
			background-size: 100%;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin: 10px;
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
        <h2>Signup</h2>
        <form method="post" action="" enctype="multipart/form-data">
            Enter First Name:
            <input type="text" name="fname" value="<?php echo isset($_POST['fname']) ? $_POST['fname'] : ""; ?>" placeholder="First Name">
            <?php
            if (array_key_exists("First Name", $errors)) {
                echo '<div class="error">';
                echo $errors["First Name"];
                echo '</div>';
            }
            ?>
            <br>
            Enter Middle Initial:
            <input type="text" name="minitial" value="<?php echo isset($_POST['minitial']) ? $_POST['minitial'] : ""; ?>" placeholder="Middle Initial">
            <?php
            if (array_key_exists("Middle Initial", $errors)) {
                echo '<div class="error">';
                echo $errors["Middle Initial"];
                echo '</div>';
            }
            ?>
            <br>
            Enter Last Name:
            <input type="text" name="lname" value="<?php echo isset($_POST['lname']) ? $_POST['lname'] : ""; ?>" placeholder="Last Name">
            <?php
            if (array_key_exists("Last Name", $errors)) {
                echo '<div class="error">';
                echo $errors["Last Name"];
                echo '</div>';
            }
            ?>
            <br>
            Enter Email:
            <input type="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ""; ?>" placeholder="Email">
            <?php
            if (array_key_exists("Email", $errors)) {
                echo '<div class="error">';
                echo $errors["Email"];
                echo '</div>';
            }
            ?>
            <br>
            Enter Password:
            <input type="password" name="password" placeholder="Password">
            <?php
            if (array_key_exists("Password", $errors)) {
                echo '<div class="error">';
                echo $errors["Password"];
                echo '</div>';
            }
            ?>
            <br>
            Re-enter Password:
            <input type="password" name="repassword" placeholder="Re-enter Password">
            <br>
            Enter Username:
            <input type="text" name="username" placeholder="Username">
            <?php
            if (array_key_exists("Username", $errors)) {
                echo '<div class="error">';
                echo $errors["Username"];
                echo '</div>';
            }
            ?>
            <br>
            Enter Bio:
            <input type="text" name="bio" value="<?php echo isset($_POST['bio']) ? htmlspecialchars($_POST['bio']) : ""; ?>" placeholder="Bio">
            <br>
            Enter Student Number:
            <input type="text" name="studNum" value="<?php echo isset($studNumber) ? $studNumber : ""; ?>" placeholder="Student Number">
            <?php
            if (array_key_exists("Student Number", $errors)) {
                echo '<div class="error">';
                echo $errors["Student Number"];
                echo '</div>';
            }
            ?>
            <br>
            Upload your picture here:
            <input type="file" name="profile"><br>
            <?php
            if (array_key_exists("Profile", $errors)) {
                echo '<div class="error">';
                echo $errors["Profile"];
                echo '</div>';
            }
            ?>
            <button type="submit" name="signup">Signup</button>
            <button type="submit" name="login">Back to Login</button>
        </form>
    </div>
</body>

</html>
