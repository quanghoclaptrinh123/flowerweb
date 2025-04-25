<?php
?>

<<!DOCTYPE html>
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
        <?php @include 'header.php' ?>

        <section class="heading">
            <h3>contact us</h3>
            <p> <a href="home.php">home</a> / contact </p>
        </section>
        <section class="contact">
            <form action="" method="POST">
                <h3>send us message!</h3>
                <input type="text" name="name" placeholder="enter your name" class="box" required> 
                <input type="email" name="email" placeholder="enter your email" class="box" required>
                <input type="number" name="number" placeholder="enter your number" class="box" required>
                <textarea name="message" class="box" placeholder="enter your message" required cols="30" rows="10"></textarea>
                <input type="submit" value="send message" name="send" class="btn">
            </form>
        </section>
    </body>
</html>