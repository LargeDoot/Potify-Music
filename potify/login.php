<!DOCTYPE html>

<!-- Template used from https://www.w3schools.com/bootstrap4/tryit.asp?filename=trybs_template1 -->
<!-- The Bootstrap 4 framework was used for this site - see https://getbootstrap.com/ for more details -->

<html lang="en">
<head>

    <title>Potify Music</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Link to Bootstrap and custom stylesheets (Bootstrap via CDN) -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="custom-theme-colours.css">
    <link rel="stylesheet" href="stylesheet.css">

    <!-- Link to font awesome icon library where all icons used are taken from. -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">

    <!-- Link to jQuery and Boostrap javascript files via CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

    <?php
    require( "db_connection.php" );
    ?>

</head>
<body>

<?php
//test_input function is taken from https://www.w3schools.com/php/php_comments.asp

//Variables for holding form contents and
$loginErr = $email = "";

$success = true;

if ( $_SERVER[ "REQUEST_METHOD" ] == "POST" ) {

    // Check if the email field is empty, if so, deny the request
    if ( empty( $_POST[ "email" ] ) ) {
        $success = false;
        $loginErr = "Please fill out all required fields";
    } // Success case
    else {
        $email = test_input( $_POST[ "email" ] );
    }

    // Check if the password field is empty, if so, deny the request
    if ( empty( $_POST[ "password" ] ) ) {
        $success = false;
        $loginErr = "Please fill out all required fields";
    } // Success case
    else {
        $password = htmlspecialchars( $_POST[ "password" ] );
    }

    //Check for successful submission
    if ( $success && check_email( $connection, $email ) ) {

        //Fetch the password hash for the submitted username
        $statement = $connection->prepare("SELECT password FROM login WHERE username = ?");
        $statement->bind_param("s", $email);
        $statement->execute();

        $result = $statement->get_result();
        $storedPassword = mysqli_fetch_row( $result );
        //////////////////////////////////////////////

        //Verify password
        if (password_verify($password, $storedPassword[0])) {

            //Start a new session and redirect user
            session_start();

            $_SESSION[ "username" ] = $email;

            header( "Location: welcome.php" );

        } else {

            $loginErr = "Incorrect email or password!";

        }

    } else {

        $loginErr = "Incorrect email or password!";

    }

}

/**
 * Takes an input and removes slashes, whitespace, and also runs 'htmlSpecialChars' on the
 * input to avoid any exploitation of the form
 *
 * @param data      the data to be cleansed
 * @return string   the cleansed data
 */
function test_input( $data )
{
    $data = trim( $data );
    $data = stripslashes( $data );
    $data = htmlspecialchars( $data );
    return $data;
}

/**
 * Searches the database for an email that matches the input, if it exists it will return true
 *
 * @param connection    the mysqli database object
 * @param email         the email address to search for
 * @return bool         true if email is in database, false otherwise
 */
function check_email( $connection, $email ) {

    //Fetch username to see if it exists
    $statement = $connection->prepare("SELECT username FROM login WHERE username = ?");
    $statement->bind_param("s", $email);
    $statement->execute();

    $result = $statement->get_result();
    ///////////////////////////////////////

    //If it exists, return true
    if ( mysqli_num_rows( $result ) > 0 ) {
        return true;
    } else {
        return false;
    }
}

?>

<div class="container-fluid">

    <!-- Navigation bar -->
    <nav class="navbar fixed-top navbar-expand-sm bg-primary navbar-dark">

        <!-- Max width container to keep content centered on a wide screen -->
        <div class="container w-1200">

            <a href="index.php" class="navbar-brand">
                <img src="images/Potify_Logo.png" alt="Potify icon">
            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar items -->
            <div class="navbar-collapse collapse" id="collapsibleNavbar">

                <ul class="navbar-nav ml-auto">

                    <li class="nav-item float-right">
                        <a class="nav-link px-4" href="offers.php">Go Premium</a>
                    </li>

                    <li class="nav-item float-right">
                        <a class="nav-link border-left px-4 py-0 my-2" href="register.php">Register</a>
                    </li>

                    <li class="nav-item float-right">
                        <a class="nav-link px-4 active" href="login.php">Login</a>
                    </li>

                </ul>

            </div>

        </div>

    </nav>

</div>

<div class="container-fluid">

    <!-- Max width container to keep content centered on a wide screen -->
    <div class="container w-1200 text-center" style="height:80vh;">

        <div class="display-3 pt-5">Log in</div>

        <!-- Form to handle logins -->
        <form method="post" action="<?php echo htmlspecialchars( $_SERVER[ "PHP_SELF" ] ); ?>">

            <div class="form-group w-500 mx-auto my-5">

                <span class="text-danger"><?php echo $loginErr; ?></span>
                <input type="email" class="form-control my-2" placeholder="Email address" value="<?php echo $email; ?>"
                       name="email"/>

                <input type="password" class="form-control my-2" placeholder="Password" name="password"/>

                By logging in you agree to the <a href="#">terms and conditions</a>.

                <br><br>

                <a href="#">Forgot my password</a> <br>

                <input type="submit" name="submit" value="Submit" class="btn btn-secondary mt-5 font"/>

            </div>

        </form>

    </div>

</div>

<!-- Footer -->
<div class="navbar footer bg-primary">

    <div class="row text-white">

        <div class="col w-1200 justify-content-center">
            <i class="fab fa-twitter fa-4x px-4"></i>
            <i class="fab fa-facebook fa-4x px-4"></i>
            <i class="fab fa-instagram fa-4x px-4"></i>
        </div>

    </div>

</div>

</body>

</html>
