<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: welcome.php");
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if email is empty
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter your email.";
    } else{
        $email = trim($_POST["email"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($email_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, email, password FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = $email;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if email exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $email, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $email;                            
                            
                            // Redirect user to welcome page
                            header("location: welcome.php");
                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid email or password.";
                        }
                    }
                } else{
                    // email doesn't exist, display a generic error message
                    $login_err = "Invalid email or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
 <!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>itech plus - Sign In</title>
  <!--page-icon-->
  <link rel="shortcut icon" href="assets/img/logo.png">
  <meta content="" name="description">
  <meta content="" name="keywords">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

<!-- Template Main CSS File -->
<link href="assets/css/pop.css" rel="stylesheet">
<!--popup.js-->
<script type="text/javascript" src="assets/js/pop.js"></script>
</head>

<body>
<div class="banner">
	<video autoplay muted loop>
		<source src="assets/Blue and Black Clouds Background Loop.mp4" type="video/mp4">
	</video>
    <div class="container" id="container">
	    <div class="form-container sign-in-container">
		    <form action="#">
                <br><br>
		    	<h1>Sign in</h1>
<br>
<br>
<br>
            <?php 
                   if(!empty($login_err)){
                   echo '<div class="alert alert-danger">' . $login_err . '</div>';
                     }        
             ?>
			    <input type="email" placeholder="Email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" />
			    <span class="invalid-feedback"><?php echo $email_err; ?></span>
                <input type="password" placeholder="Password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>"/>
			    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                <a class="forget" href="forgotpwd.html">Forgot your password?</a>
                <br>
                
			    <input type="submit" class="button" value="Sign In">
                <a class="forget1" href="#">Don't have an account ? </a>
                <a class="forget2" href="register.php">Sign Up Now!</a>
		    </form>
	    </div>
	    <div class="overlay-container">
		    <div class="overlay">
			    
			    <div class="overlay-panel overlay-right">
			    	<h1>New to our Website?</h1>
				    <p>Enter your personal details and start journey with us</p>
				    <a href="register.php"><button class="ghost" id="signUp" >Create an account</button></a>
			    </div>
	    	</div>
	    </div>
    </div>

</div>
    </body>
</html>
