<?php

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

function createCategory($conn, $category_name, $category_description)
{
    $category_sql = "INSERT INTO categories (category_name, category_description) VALUES ('$category_name', '$category_description')";
    $category_result = $conn->query($category_sql);

    if ($category_result == false) {
        return "Error: " . $conn->error;
    } else {
        return "Category created.";
    }
}

function deletePost($conn, $postId)
{
    $delete_sql = "DELETE FROM posts WHERE post_id = '$postId'";
    $post_result = $conn->query($delete_sql);

    if ($post_result === false) {
        return "Error: " . $conn->error;
    } else {
        if ($conn->affected_rows > 0) {
            return "Post deleted successfully.";
        } else {
            return "No matching post found.";
        }
    }
}

function deleteUser($userId, $conn)
{
    $delete_user = "DELETE FROM user WHERE user_id = '$userId'";
    $user_result = $conn->query($delete_user);

    if ($user_result === false) {
        return "Error: " . $conn->error;
    } else {
        if ($conn->affected_rows > 0) {
            return "User deleted successfully.";
        } else {
            return "No matching user found.";
        }
    }
}

function deleteReply($conn, $replyId)
{
    $delete_reply = "DELETE FROM replies WHERE reply_id = '$replyId'";
    $reply_result = $conn->query($delete_reply);

    if ($reply_result === false) {
        return "Error: " . $conn->error;
    } else {
        if ($conn->affected_rows > 0) {
            return "Reply deleted successfully.";
        } else {
            return "No matching reply found.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Manager</title>
    <style>
        body {
			background-image: url('bg.jpg');
			background-size: 400%;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 15px;
            padding: 0;
        }
		.container {
            width: 680px;
			height: 450px;
            margin: 25px auto;
            padding: 10px 50px 25px;
            background-image: url('blur.jpg');
			background-size: 150%;
            border-radius: 20px;
            box-shadow: 30px 30px 30px rgba(0, 0, 0, 0.2);
			position: fixed;
			bottom:60px;
			right: 285px;
		}

		.container1 {
            width: 775px;
			height: 13px;
            margin: 25px auto;
            padding: 0px 0px 0px;
            background-image: url('blur.jpg');
			background-size: 150%;
            border-radius: 20px;
            box-shadow: 30px 30px 30px rgba(0, 0, 0, 0.2);
			position: fixed;
			bottom: 19px;
			left: 218px;
		}

        h2 {
            margin-top: 0;
        }

        label {
            display: block;
            font-weight: bold;
            margin-top: 10px;
        }

        select, input[type="text"], textarea {
            width: 100%;
            padding: 5px;
            margin-top: 5px;
            border-radius: 3px;
            border: 1px solid #ccc;
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

        .form-container {
            background-color: #fff;
            padding: 20px;
            margin-top: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
	
</head>
<body>
<div class="container">
    <h1>Admin Manager</h1>
    <label for="function">Select Function:</label>
    <select name="function" id="function" onchange="showForm()">
        <option value="createCategory">Create New Category</option>
        <option value="deletePost">Delete Post</option>
        <option value="deleteUser">Delete User</option>
        <option value="deleteReply">Delete Reply</option>
    </select>

    <div id="createCategoryForm" class="form-container" style="display: none;">
        <h2>Create New Category</h2>
        <form method="post">
            <label for="categoryName">Category Name:</label>
            <input type="text" name="Name" id="categoryName" placeholder="Category Name" required>
            <label for="categoryDescription">Description:</label>
            <textarea id="categoryDescription" name="Description" rows="4" cols="33" required></textarea>
            <?php 
                if(isset($_POST['create_category'])){
                    $message = createCategory($conn, $_POST['Name'], $_POST['Description']);
                    echo "<div class='message'>$message</div>";
                }
            ?>
            <button type="submit" name="create_category">Create</button>
        </form>
    </div>

    <div id="deletePostForm" class="form-container" style="display: none;">
        <h2>Delete Post</h2>
        <form method="post">
            <label for="postId">Post ID:</label>
            <input type="text" name="postId" id="postId" placeholder="Post ID" required>
            <?php
                if(isset($_POST['deletePost'])){
                    $message = deletePost($conn, $_POST['postId']);
                    echo "<div class='message'>$message</div>";
                }
            ?>
            <button type="submit" name="deletePost">Delete Post</button>
        </form>
    </div>

    <div id="deleteUserForm" class="form-container" style="display: none;">
        <h2>Delete User</h2>
        <form method="post">
            <label for="userId">User ID:</label>
            <input type="text" name="userId" id="userId" placeholder="User ID" required>
            <?php
                if(isset($_POST['deleteUser'])){
                    $message = deleteUser($_POST['userId'], $conn);
                    echo "<div class='message'>$message</div>";
                }
            ?>
            <button type="submit" name="deleteUser">Delete User</button>
        </form>
    </div>

    <div id="deleteReplyForm" class="form-container" style="display: none;">
        <h2>Delete Reply</h2>
        <form method="post">
            <label for="replyId">Reply ID:</label>
            <input type="text" name="replyId" id="replyId" placeholder="Reply ID" required>
            <?php
                if(isset($_POST['deleteReply'])){
                    $message = deleteReply($conn, $_POST['replyId']);
                    echo "<div class='message'>$message</div>";
                }
            ?>
            <button type="submit" name="deleteReply">Delete Reply</button>
        </form>
    </div>

    <script>
        function showForm() {
            var functionSelect = document.getElementById("function");
            var selectedFunction = functionSelect.value;

            // Hide all forms
            var forms = document.querySelectorAll(".form-container");
            forms.forEach(function(form) {
                form.style.display = "none";
            });

            // Show the selected form
            var selectedForm = document.getElementById(selectedFunction + "Form");
            if (selectedForm) {
                selectedForm.style.display = "block";
            }
        }
    
	</script>
	
	<div class = "container1">
	<a href="tb_admin_dashboard.php"><button type="submit" value="Back">Back to Dashboard</button>
	</div>
	</div>
</body>

</html>
