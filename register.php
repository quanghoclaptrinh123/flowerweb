<?php
    //using session for CSRF protection
    session_start();
    require 'config.php';
    //generate random token for each session for protection
    if(empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Check CSRF token
        if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("CSRF token mismatch! Security check failed.");
        }

        //Validate and sanitize the input for preventing cross site scripting
        $name = htmlspecialchars(filter_var($_POST['name'], FILTER_SANITIZE_STRING), ENT_QUOTES, 'UTF-8');
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $pass = $_POST['pass'];
        $cpass = $_POST['cpass'];

        if(!$email) {
            $message[] = "Invalid email format!";
        }
        if(strlen($pass) < 8 || !preg_match('/[A-Za-z]/', $pass) || !preg_match('/[0-9]/', $pass) || !preg_match('/[^A-Za-z0-9]/', $pass) ){
            $message[] = "Password must be at least 8 characters and include letters, numbers and at least 1 special character";
        }
        if($pass !==  $cpass) {
            $message[] = "Passwords do not match!";
        }

        //Hashing password
        $hash_pass = password_hash($pass, PASSWORD_DEFAULT);

        //prevent SQL injection by parameterlize the data - take information from the database 
        $stmt = $conn->prepare("SELECT email, name FROM users WHERE email = ? AND name = ?");
        if(!$stmt){
            die("SQL error: ". $conn->error);
        }

        $stmt->bind_param("ss", $email, $name);
        $stmt->execute();
        $result = $stmt->get_result();

        $email_existed = false;
        $name_existed = false;

        while($row = $result->fetch_assoc()) { 
            if($row['email'] === $email) $email_existed = true;
            if($row['name'] === $name) $name_existed = true;
        }
        $stmt->close();

        if($email_existed && $name_existed) {
            $message[] = "Email and name already exist";
        }
        elseif($email_existed){
            $message[] = "Email already exists";
        }
        elseif($name_existed){
            $message[] = "Name already exists";
        }

        //Insert securely new data into database
        if(empty($message)) {
            echo "Using database: " . $conn->query("SELECT DATABASE()")->fetch_row()[0];
            $stmt = $conn->prepare("INSERT INTO users(name, email, password) VALUES (?, ?, ?)");
            if(!$stmt){
                die("SQL error: ". $conn->error);
            }
            
            $stmt->bind_param("sss", $name, $email, $hash_pass);
            if ($stmt->execute()){
                echo "Registration successful!";
                header("Location: login.php");
                exit();
            }
            else{
                echo "Error: " . $conn->error;
            }
            $stmt->close();
            exit();
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>register</title>
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
        <section class="form-container">
            <form action="" method="post">
                <h3>Sign up</h3>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'];?>">

                <label for="name">Name</label><br>
                <input type="text" id="name" name="name" class="box" placeholder="Enter your name" required><br>

                <label for="email">Email</label><br>
                <input type="text" id="email" name="email" class="box" placeholder="Enter your email" required><br>

                <label for="password" id="pass">Password</label><br>
                <input type="password" id="pass" name="pass" class="box" placeholder="Enter your password" required><br>

                <label for="password" id="cpass">Confirm password</label><br>
                <input type="password" id="cpass" name="cpass" class="box" placeholder="Confirm your password" required><br>

                <input type="submit" class="btn" name="submit" value="register now">
                <p>Already signed up? <a href="login.php">Login now!</a></p>
            </form>
        </section>
    </body>
</html>