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
<header class="header">
    <a href="home.php" class="logo">Flowers <span>*</span></a>
    <nav class="navbar">
        <a href="home.php">Home</li>
        <a href="about.php">About</li>
        <a href="contact.php">Contact</li>
        <a href="shop.php">Shop</li>
        <a href="order.php">Order</li>
    </nav>
    <div class="account-box">
        <a href="search_page.php" class="fas fa-search"></a>
        <a href="wishlish.php"><i class="fa-solid fa-heart"></i></a>
        <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i></a>
        <i id="user-icon" class="fa-solid fa-user"></i>
        <div id="account-info" class="user-info-box" style="display: none;">
            <?php
                if(isset($_SESSION['user_name']) && isset($_SESSION['user_email'])){
                    echo '<p>Username: <span>' .$_SESSION['user_name'].'</span></p>';
                    echo '<p>Email: <span>' .$_SESSION['user_email'].'</span></p>';
                    echo '<a href="logout.php" class="delete-btn"> Logout</a>';
                }
                else{
                    echo '<a href="login.php" class="btn">Login</a>';
                    echo '<a href="register.php" class="btn">Register</a>';
                }
            ?>
        </div>
    </div>
</header>