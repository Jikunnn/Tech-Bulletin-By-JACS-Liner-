<?php
session_start();

// Check if the user is signed in
if (!isset($_SESSION['signed_in']) || $_SESSION['signed_in'] !== true) {
    header("Location: tb_login_prompt.php");
    exit;
}

$username = $_SESSION['user_username'];

if (isset($_POST['logout'])) {
  
    session_destroy();
    header("Location: tb_login_prompt.php");
    exit;
}

// Database configuration
$hostname = "localhost";
$userdb = "id21040595_ahmer";
$passdb = "Techbulletin#2023";
$dbname = "id21040595_techbulletin";
$conn = new mysqli($hostname, $userdb, $passdb, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to retrieve the valid topic_ids from the topics table
function getValidTopicIds()
{
    global $conn;

    $validTopicIds = array();

    $sql = "SELECT topic_id, topic_subject FROM topics";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $validTopicIds[$row['topic_id']] = $row['topic_subject'];
        }
    }

    return $validTopicIds;
}

// Function to get the username based on the user ID
function getUsernameByUserId($userId)
{
    global $conn;

    $sql = "SELECT user_username FROM user WHERE user_id = '$userId'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['user_username'];
    }

    return "Unknown";
}

// Function to save a post
function savePost($postContent, $postTopic, $postedBy)
{
    global $conn;

    $postContent = $conn->real_escape_string($postContent);
    $postTopic = $conn->real_escape_string($postTopic);
    $postDate = date('Y-m-d H:i:s');

    // Get the user ID based on the username
    $userId = getUserIdByUsername($postedBy);

    // Insert the post into the posts table
    $sql = "INSERT INTO posts (post_content, post_date, post_topic, post_by) VALUES ('$postContent', '$postDate', '$postTopic', '$userId')";

    if ($conn->query($sql) === TRUE) {
        echo "Post saved successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}


// Function to get the user ID based on the username
function getUserIdByUsername($username)
{
    global $conn;

    $sql = "SELECT user_id FROM user WHERE user_username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['user_id'];
    }

    return null;
}

// Function to get the posts and their replies
function getPostsAndReplies()
{
    global $conn;
    $postsAndReplies = array();

    // Get all posts in descending order by post date
    $sql = "SELECT * FROM posts ORDER BY post_date DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($post = $result->fetch_assoc()) {
            $postId = $post['post_id'];
            $post['replies'] = array();

            $sql = "SELECT * FROM replies WHERE reply_topic = $postId";
            $replyResult = $conn->query($sql);

            if ($replyResult->num_rows > 0) {
                while ($reply = $replyResult->fetch_assoc()) {
                    $post['replies'][] = $reply;
                }
            }

            $postsAndReplies[] = $post;
        }
    }

    return $postsAndReplies;
}

// Function to get the valid topics
function getValidTopics()
{
    global $conn;

    $validTopics = array();

    $sql = "SELECT topic_id, topic_title FROM topics";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $validTopics[$row['topic_id']] = $row['topic_title'];
        }
    }

    return $validTopics;
}

function getTopicTitleByTopicId($topicId)
{
    global $conn;

    $sql = "SELECT topic_title FROM topics WHERE topic_id = '$topicId'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['topic_title'];
    }

    return "Unknown";
}

function displayPostsAndReplies($postsAndReplies)
{
    foreach ($postsAndReplies as $post) {
        echo "<div class='post'>";
        //echo "<h3>Post ID: " . $post['post_id'] . "</h3>";
        echo "<p>Content: " . $post['post_content'] . "</p>";
        echo "<p>Posted By: " . getUsernameByUserId($post['post_by']) . "</p>";
        echo "<p>Topic: " . getTopicTitleByTopicId($post['post_topic']) . "</p>"; // Added line
        echo "<p>Post Date: " . $post['post_date'] . "</p>";

        echo "<h4>Replies:</h4>";
        foreach ($post['replies'] as $reply) {
            echo "<div class='reply'>";
            //echo "<p>Reply ID: " . $reply['reply_id'] . "</p>";
            echo "<p>Content: " . $reply['reply_content'] . "</p>";
            echo "<p>Posted By: " . getUsernameByUserId($reply['reply_by']) . "</p>";
            echo "<p>Reply Date: " . $reply['reply_date'] . "</p>";
            echo "</div>";
        }

        echo "<form method='POST' action=''>";
        echo "<input type='hidden' name='post_id' value='" . $post['post_id'] . "' />";
        echo "<textarea name='reply_content' rows='3' cols='40'></textarea><br />";
        echo "<input type='submit' name='submit_reply' value='Reply' />";
        echo "</form>";

        echo "</div>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_post'])) {
        $postContent = $_POST['post_content'];
        $postTopic = $_POST['post_topic'];
        $postedBy = $username;
        savePost($postContent, $postTopic, $postedBy);
    } elseif (isset($_POST['submit_reply'])) {
        $replyContent = $_POST['reply_content'];
        $postID = $_POST['post_id'];
        $postedBy = $username;

        $replyContent = $conn->real_escape_string($replyContent);
        $postID = $conn->real_escape_string($postID);
        $postedBy = $conn->real_escape_string($postedBy);
        $replyDate = date('Y-m-d H:i:s');

        $userId = getUserIdByUsername($postedBy);

        $sql = "INSERT INTO replies (reply_content, reply_date, reply_topic, reply_by) VALUES ('$replyContent', '$replyDate', '$postID', '$userId')";

        if ($conn->query($sql) === TRUE) {
            echo "Reply saved successfully!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$postsAndReplies = getPostsAndReplies();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forum Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('bg.jpg');
			background-size: 400%;
            margin: 0;
            padding: 0;
			background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-image: url('blur.jpg');
			background-size: 500%;
			border-radius: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            
			
        }
		
		.container1 {			
            height: 20%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(0, 0, 0, 0.6); /* Set background color with opacity */
			color: #fff; /* Set text color to white */
        }
		
		.container2 {
            width: 430px;
			height: 270px;
            margin: 25px auto;
            padding: 1px 50px 20px;
            background-image: url('blur.jpg');
			background-size: 500%;
            border-radius: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
			right: 50px;
			
        }
		
		.container3 {
            width: 1000px;
            margin: 25px auto;
            padding: 1px 50px 20px;
            background-image: url('blur.jpg');
			background-size: 350%;
            border-radius: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
			left: 50px;
			
        }
		
        h1 {
            font-size: 24px;
            margin: 0;
			
        }

        h2 {
            font-size: 20px;
            margin: 20px 0 10px;
        }

        h3 {
            font-size: 18px;
            margin: 0;
        }

        h4 {
            font-size: 16px;
            margin: 10px 0;
        }

        p {
            margin: 0;
        }

        .post {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #f8f8f8;
        }

        .reply {
            margin-left: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
        }

        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 5px;

			
        }

        .form-group input[type="submit"] {
            padding: 5px 10px;
			width: 80%;
            background-image: url('button1.jpg');
			border-radius: 20px;
			background-size: 100%;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        .form-group input[type="submit"]:hover {
            background-image: url('button1.jpg');
			background-size: 100%;
        }

        .logout-form {
            display: inline-block;
        }

        .logout-form button {
            padding: 5px 10px;
            background-color: red;
            color: #fff;
            border: none;
            cursor: pointer;
			border-radius: 20px;
        }

        .logout-form button:hover {
            background-color: #d32f2f;
        }

        .user-info {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
        }

        .user-info .username {
            margin-right: 10px;
            font-weight: bold;
        }

        .user-info .user-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container1">
     <!-- Display user's icon and link to tb_update.php -->
			<div class="user-info">
				<span class="username"><br>Welcome, <?php echo $username; ?>!</span>
				<?php
				$sql = "SELECT user_profile FROM user WHERE user_username = '$username'";
				$result = $conn->query($sql);

				if ($result->num_rows > 0) {
					$row = $result->fetch_assoc();
					$userProfile = $row['user_profile'];

					echo "<a href='tb_update.php'><img class='user-icon' src='data:image/jpeg;base64," . base64_encode($userProfile) . "' alt='User Icon'></a>";
				}
				?>
			</div>
        <form class="logout-form" method="POST" action="">
            <button type="submit" name="logout">Logout</button>

        </form>
		</div>
		
		<div class="container2">
        <h2>Create a New Post</h2>
			<form method="POST" action="">
				<div class="form-group">
					<label for="post_topic">Select a Topic:</label>
					<select name="post_topic" id="post_topic">
						<?php
						$validTopics = getValidTopics();
						foreach ($validTopics as $topicId => $topicTitle) {
							echo "<option value='$topicId'>$topicTitle</option>";
						}
						?>
					</select>
				</div>
				<div class="form-group">
					<label for="post_content">Post Content:</label>
					<textarea name="post_content" id="post_content" rows="5" cols="40"></textarea>
				</div>
				<div class="form-group">
					<center><input type="submit" name="submit_post" value="Submit Post" /></center>
				</div>
			</form>
		</div>
	
<div class="container3">
<center><h2>Forum Feed</h2></center>
<?php
$postsAndReplies = getPostsAndReplies();
displayPostsAndReplies($postsAndReplies);
?>
    </div>
</body>
</html>

</div>