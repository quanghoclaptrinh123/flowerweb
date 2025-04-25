<?php
    session_start();
    require 'config.php';

    if(empty($_SESSION['csrf_token'])){
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(!isset($_POST['csrf_token']) || $_SESSION['csrf_token'] !== $_POST['csrf_token']){
            die("Invalid request");
        }

        $identifier = htmlspecialchars(trim($_POST['identifier']), ENT_QUOTES, 'UTF-8');
        $pass = $_POST['pass'];

        if(filter_var($identifier, FILTER_VALIDATE_EMAIL )){
            $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        }
        elseif(filter_var($identifier, FILTER_SANITIZE_STRING)){
            $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE name = ?");
        }
        else{
            die("SQL error: ". $conn->error);
        }
            
        $stmt->bind_param("s", $identifier);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows === 1){
            $stmt->bind_result($user_id, $user_name, $user_email, $hashed_pass);
            $stmt->fetch();

            if(password_verify($pass, $hashed_pass)){
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $user_name;
                $_SESSION['user_email'] = $user_email;
                header("Location: home.php");
                exit;
            }
            else{
                $message[] = "Incorrect password!";
            }
        }
        else{
            $message[] = "User doesn't exist!, please register!";
        }
    }

?>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Login</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

       <!-- font awesome cdn link  -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <!-- custom css file link  -->
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <?php
            if(isset($message)){
                foreach($message as $message){
                    echo '
                    <div class="message">
                        <span>'.$message.'</span>
                        <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
                    </div>
                    ';
                }
            }
        ?>
        <section class="lform-container">
            <form action="" method="post">
                <h3>Login</h3>
                <input type="hidden" name="csrf_token" value="<?php ECHO $_SESSION['csrf_token'];?>">
                <label for="identifier">Name or email</label><br>
                <input type="text" id="identifier" name="identifier" class="box" required><br>
                <label for="pass">Password</label><br>
                <input type="password" id="pass" name="pass" class="box" required><br>
                <input type="submit" class="btn" name="submit" value="Log in"><br>
                <a href="fgpass.php">Forgot password?</a>
                <p>Don't have an account? <a href="register.php">Sign up</a></p>
            </form>
        </section>
    </body>
</html>