<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Homepage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
	<style>
        .title {
            font-size: 50px;
            font-weight: bold;
            padding-top: 0px;
            color: #fff; /* Set text color to white */
        }
        .subtitle {
            font-size: 25px;
            color: #fff; /* Set text color to white */
        }
        body {
            background-image: url('hp.jpg');
            background-size: 100%;
            background-repeat: no-repeat;
            background-attachment: fixed;
            height: 100vh;
            padding: 20px;
        }

		img {
			width: 9%;
			padding-bottom:0px;
		}
		
        .container {			
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(0, 0, 0, 0.5); /* Set background color with opacity */
        }
		
        .welcome-text {
            text-align: center;

        }
        p {
            color: #fff; /* Set text color to white */
        }

    </style>
  </head>
  <body>
    <div class="container">
        <div class="welcome-text">
            <center><marquee><h1 class="title">Welcome to Tech-Bulletin!</h1></marquee></center>
            <h3 class="subtitle">"Where Technology meets Freedom"</h3>
            <p>Join us now and unlock a treasure trove of up-to-the-minute updates exclusively tailored for the FEU Community!</p>
            <a href="tb_login_prompt.php" class="btn btn-light" background-image="button">Get Started</a>		
			
			<br></br>
			<img src= "tech.png">
			<img src= "tb.png">
			<img src= "jacs.png">
        </div>
		
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
	
  </body>
</html>


