<?php
include 'config.php';
session_start();

function prepareAndExecute($conn, $sql, $params)
{
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('mysqli error: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $stmt->execute();
    return $stmt;
}

// Handle User Login
$user_login_error = '';
if (isset($_POST['user_login_submit'])) {
    if (empty($_POST['Email']) || empty($_POST['Password'])) {
        $user_login_error = 'Email and password are required';
    } else {
        $email = $_POST['Email'];
        $password = $_POST['Password'];
        $sql = "SELECT * FROM signup WHERE Email = ? AND Password = BINARY ?";
        $stmt = prepareAndExecute($conn, $sql, [$email, $password]);
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['usermail'] = $email;
            header("Location: home.php");
            exit();
        } else {
            $user_login_error = 'Invalid email or password';
        }
    }
}

// Handle Employee Login
$emp_login_error = '';
if (isset($_POST['Emp_login_submit'])) {
    if (empty($_POST['Emp_Email']) || empty($_POST['Emp_Password'])) {
        $emp_login_error = 'Email and password are required';
    } else {
        $email = $_POST['Emp_Email'];
        $password = $_POST['Emp_Password'];
        $sql = "SELECT * FROM emp_login WHERE Emp_Email = ? AND Emp_Password = BINARY ?";
        $stmt = prepareAndExecute($conn, $sql, [$email, $password]);
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['usermail'] = $email;
            header("Location: admin/admin.php");
            exit();
        } else {
            $emp_login_error = 'Invalid email or password';
        }
    }
}

// Handle User Signup
$signup_error = '';
if (isset($_POST['user_signup_submit'])) {
    $username = $_POST['Username'];
    $email = $_POST['Email'];
    $password = $_POST['Password'];
    $cpassword = $_POST['CPassword'];

    if ($username == "" || $email == "" || $password == "") {
        $signup_error = 'Fill the proper details';
    } elseif ($password != $cpassword) {
        $signup_error = 'Password does not match';
    } else {
        $sql_check = "SELECT * FROM signup WHERE Email = ?";
        $stmt_check = prepareAndExecute($conn, $sql_check, [$email]);
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $signup_error = 'Email already exists';
        } else {
            $sql_insert = "INSERT INTO signup (Username, Email, Password) VALUES (?, ?, ?)";
            $stmt_insert = prepareAndExecute($conn, $sql_insert, [$username, $email, $password]);

            if ($stmt_insert->affected_rows > 0) {
                $_SESSION['usermail'] = $email;
                header("Location: home.php");
                exit();
            } else {
                $signup_error = 'Something went wrong';
            }
        }
    }
}


$login_error = '';
if (isset($_POST['user_login_submit']) && !isset($_SESSION['usermail'])) {
    $login_error = $user_login_error;
}

$emp_login_error = '';
if (isset($_POST['Emp_login_submit']) && !isset($_SESSION['usermail'])) {
    $emp_login_error = $emp_login_error;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Sweet Alert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <!-- AOS Animation -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <!-- Loading Bar -->
    <script src="https://cdn.jsdelivr.net/npm/pace-js@latest/pace.min.js"></script>
    <link rel="stylesheet" href="./css/flash.css">
    <title>Hotel Blue Bird</title>
</head>

<body>
    <!-- Carousel -->
    <section id="carouselExampleControls" class="carousel slide carousel_section" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="carousel-image" src="./image/hotel1.jpg">
            </div>
            <div class="carousel-item">
                <img class="carousel-image" src="./image/hotel2.jpg">
            </div>
            <div class="carousel-item">
                <img class="carousel-image" src="./image/hotel3.jpg">
            </div>
            <div class="carousel-item">
                <img class="carousel-image" src="./image/hotel4.jpg">
            </div>
        </div>
    </section>

    <!-- Main Section -->
    <section id="auth_section">
        <div class="logo">
            <img class="bluebirdlogo" src="./image/bluebirdlogo.png" alt="logo">
            <p>BLUEBIRD</p>
        </div>
        <div class="auth_container">
            <!-- Login -->
            <div id="Log_in">
                <h2>Log In</h2>
                <div class="role_btn">
                    <div class="btns active">User</div>
                    <div class="btns">Staff</div>
                </div>

                <!-- User Login -->
                <form class="user_login authsection active" id="userlogin" action="" method="POST">
                    <?php if($user_login_error): ?>
                        <div class="alert alert-danger" role="alert"><?php echo $user_login_error; ?></div>
                    <?php endif; ?>
                    <div class="form-floating">
                        <input type="email" class="form-control" name="Email" placeholder=" " required>
                        <label for="Email">Email</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="Password" placeholder=" " required>
                        <label for="Password">Password</label>
                    </div>
                    <button type="submit" name="user_login_submit" class="auth_btn">Log in</button>
                    <div class="footer_line">
                        <h6>Don't have an account? <span class="page_move_btn" onclick="signuppage()">sign up</span></h6>
                    </div>
                </form>

                <!-- Employee Login -->
                <form class="employee_login authsection" id="employeelogin" action="" method="POST">
                    <?php if($emp_login_error): ?>
                        <div class="alert alert-danger" role="alert"><?php echo $emp_login_error; ?></div>
                    <?php endif; ?>
                    <div class="form-floating">
                        <input type="email" class="form-control" name="Emp_Email" placeholder=" " required>
                        <label for="floatingInput">Email</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="Emp_Password" placeholder=" " required>
                        <label for="floatingPassword">Password</label>
                    </div>
                    <button type="submit" name="Emp_login_submit" class="auth_btn">Log in</button>
                </form>
            </div>

            <!-- Sign Up -->
            <div id="sign_up">
                <h2>Sign Up</h2>
                <form class="user_signup" id="usersignup" action="" method="POST">
                    <?php if($signup_error): ?>
                        <div class="alert alert-danger" role="alert"><?php echo $signup_error; ?></div>
                    <?php endif; ?>
                    <div class="form-floating">
                        <input type="text" class="form-control" name="Username" placeholder=" " required>
                        <label for="Username">Username</label>
                    </div>
                    <div class="form-floating">
                        <input type="email" class="form-control" name="Email" placeholder=" " required>
                        <label for="Email">Email</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="Password" placeholder=" " required>
                        <label for="Password">Password</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="CPassword" placeholder=" " required>
                        <label for="CPassword">Confirm Password</label>
                    </div>
                    <button type="submit" name="user_signup_submit" class="auth_btn">Sign up</button>
                    <div class="footer_line">
                        <h6>Already have an account? <span class="page_move_btn" onclick="loginpage()">Log in</span></h6>
                    </div>
                </form>
            </div>

    <script src="./javascript/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>

</html>
