<?php
session_start();
$errors = array();
$_SESSION['user_id'] = 22;
// Database configuration
$hostname = "localhost";
$userdb = "root";
$passdb = "";
$dbname = "techbulletin1";

$conn = new mysqli($hostname, $userdb, $passdb, $dbname);
if ($conn->connect_errno) {
    echo "Connection failed: " . $conn->connect_error;
    $conn->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit'])) {
        // Sanitizing user input
        $topic_by = $_SESSION['user_id'];
        $topic_title = strip_tags($_POST['Title']);
        $topic_content = strip_tags($_POST['Content']);
        $topic_category = $_POST['Category'];
        $topic_date = date('Y-m-d H:i:s');

        // Inserting topic into database
        $sql = "INSERT INTO topics(topic_by, topic_title, topic_content, topic_category,topic_date) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issis", $topic_by, $topic_title, $topic_content, $topic_category,$topic_date);
        if ($stmt->execute()) {
            $stmt->close();
            echo "Topic created successfully.";
        } else {
            $errors[] = "Failed to create topic. Please try again later.";
        }
    } else {
        $errors[] = "Please fill in all the required fields.";
    }
}

// Fetch categories from the database
$category_sql = "SELECT category_id, category_name FROM categories";
$category_result = $conn->query($category_sql);

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
	<style>
        body {
			background-image: url('bg.jpg');
			background-size: 500%;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 650px;
			height: 400px;
            margin: 25px auto;
            padding: 10px 50px 25px;
            background-image: url('blur.jpg');
			background-size: 150%;
            border-radius: 20px;
            box-shadow: 30px 30px 30px rgba(0, 0, 0, 0.2);
			position: fixed;
			bottom: 100px;
			right: 300px;
			
        }

		.container1 {
            width: 645px;
			height: 13px;
            margin: 25px auto;
            padding: 0px 0px 0px;
            background-image: url('blur.jpg');
			background-size: 150%;
            border-radius: 20px;
            box-shadow: 30px 30px 30px rgba(0, 0, 0, 0.2);
			position: fixed;
			bottom: 70px;
			right: 301px;
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
	<h2>Create a Topic</h2>
<form method="post">
    <center><label for="categories">Select Category:</label></center>
    <center><select name="Category" id="categories">
        <?php
        if ($category_result && $category_result->num_rows > 0) {
            while ($row = $category_result->fetch_assoc()) {
                $cat_id = $row['category_id'];
                $cat_name = $row['category_name'];

                echo '<option value="' . $cat_id . '">' . $cat_name . '</option>';
            }
        } else {
            echo '<option value="">No categories found</option>';
        }
        $conn->close();
        ?>
    </select></center>
    Enter Topic Title:
    <input type="text" name="Title" placeholder="Enter Title" required>
    <br>
    <label for="message">Content</label><br>
    <textarea id="message" name="Content" rows="4" cols="66" required></textarea>
    <br>
    <button type="submit" name="submit">Create</button>
</form>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
		
	<div class = "container1">
	<a href="tb_admin_dashboard.php"><button type="submit" value="Back">Back to Dashboard</button>
	</div>
	</div>
		
</body>
</html>
