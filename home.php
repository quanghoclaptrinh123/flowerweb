<?php 
    session_start();
    require 'config.php';
    $user_id = $_SESSION['user_id'];

    if(!isset($user_id)){
        header('location:login.php');
    }

    if(isset($_POST['add_to_wishlist'])){
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];
        $product_image = $_POST['product_image'];
        $product_quantity = $_POST['product_quantity'];

        $wishlist_check = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
        $cart_check = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
        if(!$wishlist_check || !$cart_check){
            die("SQL error: ". $conn->error);
        }

        $wishlist_check->bind_param('si', $product_name, $user_id);
        $wishlist_check->execute();
        $wishlist_check->store_result();
        $wRow = $wishlist_check->num_rows;

        $cart_check->bind_param('si', $product_name, $user_id);
        $cart_check->execute();
        $cart_check->store_result();
        $cRow = $cart_check->num_rows;

        if($wRow> 0){
            $message[] = "Already added to the wishlist";
        }
        elseif($cRow > 0){
            $message[] = "Already added to the cart";
        }
        else{
            $stmt = $conn->prepare("INSERT INTO `wishlist`(user_id, pid, name, price, image) VALUES(?, ?, ?, ?, ?)");
            $stmt->bind_param('iisss', $user_id, $product_id, $product_name, $product_price, $product_image);
            $stmt->execute();
            $message[] = "Product added into wishlist";
        }
    }
    if(isset($_POST['add_to_cart'])){
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];
        $product_image = $_POST['product_image'];
        $product_quantity = $_POST['product_quantity'];

        $cart_check = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
        if(!$cart_check){
            die("SQL error: ". $conn->error);
        }

        $cart_check->bind_param('si', $product_name, $user_id);
        $cart_check->execute();
        $cart_check->store_result();

        $row = $cart_check->num_rows;
        if($row > 0){
            $message[] = "Product already added into cart";
        }
        else{
            $wishlist_check = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
            if(!$wishlist_check){
                die("SQL error: ". $conn->error);
            }

            $wishlist_check->bind_param('si', $product_name, $user_id);
            $wishlist_check->execute();
            $wishlist_check->store_result();

            $stmt = $conn->prepare("DELETE FROM `wishlist` WHERE name = ? AND user_id = ?");
            $stmt->bind_param('si', $product_name, $user_id);
            $stmt->execute();

            $stmt = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('iisdss', $user_id, $product_id, $product_name, $product_price, $product_quantity, $product_image);
            $stmt->execute();

            $message[] = "Product already added into cart";
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

       <!-- font awesome cdn link  -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <!-- custom css file link  -->
        <link rel="stylesheet" href="css/style.css">
        
    </head>
    <body>
        <?php
            if (isset($message)) {
                echo '<script>';
                echo 'window.onload = function() {';
                foreach ($message as $msg) {
                    echo 'showCustomAlert("' . addslashes($msg) . '");';
                }
                echo '};';
                echo '</script>';
            }
        ?>
        <?php @include 'header.php';?>
        <section class="home">
            <div class="content">
                <p>Honest and transparent content is the best sales tool in the world.</p>
                <a href="about.php" class="btn">Discover more </a>
            </div>
        </section>

        <section class="products">
            <h1 class="title">Latest products</h1>
            <div class="box-container">
                <?php 
                    $stmt = $conn->prepare("SELECT * FROM products LIMIT 6");
                    if(!$stmt){
                        die("Querry failed: ". $conn->error);
                    }   
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if($row = $result->num_rows > 0){
                        while($fetch_products = $result->fetch_assoc()){
                ?>
                            <form action="" method="post" class="box">
                            <a href="view_page.php?pid=<?php echo $fetch_products['id']; ?>" class="fas fa-eye"></a>
                            <div class="price">$<?php echo $fetch_products['price']; ?></div>
                            <img src="flowers/<?php echo $fetch_products['image']; ?>" alt="" class="image">
                            <div class="name"><?php echo $fetch_products['name']; ?></div>
                            <input type="number" name="product_quantity" value="1" min="1" max="100" class="qty">
                            <input type="hidden" name="product_id" value="<?php echo $fetch_products['id']; ?>">
                            <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
                            <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
                            <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
                            <input type="submit" name="add_to_wishlist" value="Add to Wishlist" class="btn">
                            <input type="submit" name="add_to_cart" value="Add to Cart" class="btn">
                            </form>
                <?php
                    }
                }
                else{
                        echo '<p class="empty">no products added yet!</p>';
                    }
                ?>
            </div>
        </section>
        <section class="home-contact">
            <div class="content">
                <h3>We'd love to hear from you</h3>
                <p>Let us know how we can help you, feel free to ask!</p>
                <a href="contact.php" class="btn">Contact us</a>
            </div>
        </section>
        <div id="custom-alert" class="custom-alert" style="display: none;">
            <span id="custom-alert-message"></span>
            <button onclick="hideCustomAlert()">OK</button>
        </div>

        <?php @include 'footer.php'; ?>

        <script>
            document.getElementById('user-icon').addEventListener('click', 
                function() {
                    var box = document.getElementById('account-info');
                    box.style.display = box.style.display === 'none' ? 'block' : 'none';
                }
            );
        </script>
        <script> 
            function showCustomAlert(message) {
                const alertBox = document.getElementById('custom-alert');
                const alertMessage = document.getElementById('custom-alert-message');
                alertMessage.textContent = message;
                alertBox.style.display = 'block';
            }
            function hideCustomAlert() {
                const alertBox = document.getElementById('custom-alert');
                alertBox.style.display = 'none';
            }
        </script>
    </body>
</html>