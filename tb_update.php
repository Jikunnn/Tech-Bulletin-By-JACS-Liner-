<?php
session_start();

if (isset($_POST['update'])) {
    // Database connection
    $hostname = "localhost";
    $userdb = "root";
    $passdb = "";
    $dbname = "techbulletin1";
    $conn = new mysqli($hostname, $userdb, $passdb, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve form data
    $fname = $_POST['fname'];
    $minitial = $_POST['minitial'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];
    $username = $_POST['username'];
    $bio = $_POST['bio'];
    $studNum = $_POST['studNum'];
    $oldProfile = base64_decode($_POST['old_profile']);
    $profile = $_FILES['profile']['tmp_name'];

    // Validate and sanitize form input
    $fname = mysqli_real_escape_string($conn, $fname);
    $minitial = mysqli_real_escape_string($conn, $minitial);
    $lname = mysqli_real_escape_string($conn, $lname);
    $email = mysqli_real_escape_string($conn, $email);
    $password = sha1(strip_tags($_POST['password']));
    $repassword = sha1(strip_tags($_POST['repassword']));
    $username = mysqli_real_escape_string($conn, $username);
    $bio = mysqli_real_escape_string($conn, $bio);
    $studNum = mysqli_real_escape_string($conn, $studNum);

    // Check if passwords match
    if ($password !== $repassword) {
        echo "Passwords do not match!";
        exit();
    }

    // Check if a new profile picture was uploaded
    if (!empty($profile)) {
        $profileImage = addslashes(file_get_contents($_FILES['profile']['tmp_name']));
    } else {
        $profileImage = $oldProfile;
    }

    // Update user data in the database
    $session_id = $_SESSION['user_id'];
    $sql = "UPDATE `user` SET `user_fname`='$fname', `user_minitial`='$minitial', `user_lname`='$lname', `user_email`='$email', `user_password`='$password', `user_username`='$username', `user_profile`='$profileImage', `user_bio`='$bio', `user_studnumber`='$studNum' WHERE `user_id`='$session_id'";

    if ($conn->query($sql) === TRUE) {
        echo "User data updated successfully.";
        header("Location: tb_login_prompt.php");
        session_destroy();
        exit();
    } else {
        echo "Error updating user data: " . $conn->error;
    }
    

    $conn->close();
    exit();
}

// Fetch user data from the database
$hostname = "localhost";
$userdb = "root";
$passdb = "";
$dbname = "techbulletin1";
$conn = new mysqli($hostname, $userdb, $passdb, $dbname);

$session_id = $_SESSION['user_id'];
$query = $conn->query("SELECT * FROM `user` WHERE `user_id` = '$session_id'");
$fetch = $query->fetch_array();

// Convert BLOB data to base64-encoded string
$profileImage = '';
if ($fetch['user_profile']) {
    $profileImage = base64_encode($fetch['user_profile']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('bg.jpg');
            margin: 0;
            padding: 0;
			background-size: 400%;
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
            box-shadow: 30px 30px 30px rgba(0, 0, 0, 0.2);
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
        <h2>Update User Profile</h2>
        <form method="post" action="" enctype="multipart/form-data">
            Enter First Name:
            <input type="text" name="fname" value="<?php echo $fetch['user_fname'] ?>" placeholder="First Name">
           
            <br>
            Enter Middle Initial:
            <input type="text" name="minitial" value="<?php echo $fetch['user_minitial'] ?>" placeholder="Middle Initial">
            
            <br>
            Enter Last Name:
            <input type="text" name="lname" value="<?php echo $fetch['user_lname'] ?>" placeholder="Last Name">
            
            <br>
            Enter Email:
            <input type="email" name="email" value="<?php echo $fetch['user_email'] ?>" placeholder="Email">
            
            <br>
            Enter Password:
            <input type="password" name="password" placeholder="Password">
            
            <br>
            Re-enter Password:
            <input type="password" name="repassword" placeholder="Re-enter Password">
            <br>
            Enter Username:
            <input type="text" name="username" placeholder="Username" value="<?php echo $fetch['user_username'] ?>">
            
            <br>
            Enter Bio:
            <input type="text" name="bio" placeholder="Bio" value="<?php echo $fetch['user_bio'] ?>">
            <br>
            Enter Student Number:
            <input type="text" name="studNum" value="<?php echo $fetch['user_studnumber'] ?>" placeholder="Student Number">
           
            <br>
            Upload your picture here:
            <input type="file" name="profile"><br>
            
            <input type="hidden" name="old_profile" value="<?php echo base64_encode($fetch['user_profile']); ?>">
            <input type="hidden" name="user_id" value="<?php echo $fetch['user_id']; ?>">
            <button type="submit" name="update">Update</button>
            <button type="submit" name="login">Back to Login</button>

        </form>
    </div>
</body>
</html>
