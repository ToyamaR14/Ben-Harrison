<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Ben Harrison Residence</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style.css">
    </head>

        <body>

            <header>
                 <a href="homepage.php" class="home">Ben <h10 class="sec">Harrison Residence</h10></a>
                 <nav class="navigation">
                    <a class="active" href="homepage.php">Home</a>
                    <a class="active" href="about.php">About Us</a>
                    <a class="active" href="reserve.php">Reservation</a>
                    <a class="active" href="contact.php">Contact Us</a>
                    <button class="btnLogin-popup">Login</button>
                 </nav>            
            </header>

            <div class="content">
                <h1 class="anim"><h9 class="fir">Ben</h9><br><h10 class="sec">Harrison Residence</h10></h1>
                <p class="anim">Every day is a journey, and the journey itself is home.</p>
                <a href="reserve" class="btn-spec anim">Interested?   Reserve now!</a>
            </div>

            <div class="wrapper">
            <form class="login-form" id="login_form" action="proc.php" method="POST">
                <span class="icon-close"><ion-icon name="close"></ion-icon></span>
                <div class="form-box login">
                    <h2>Login</h2>
                    <form action="">
                        <div class="input-box">
                            <span class="icon"><ion-icon name="mail"></ion-icon></span>
                            <input type="emailaddress" name="email" required>
                            <label>Email Address</label>
                        </div>
                        <div class="input-box">                            
                            <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                            <input type="password" name="password" required>
                            <label>Password</label>
                        </div>
                        <div class="remember-forgot">
                            <label><input type="checkbox" name="remember">Remember me</label>
                            <a href="#" class="forgot-link">Forgot Password?</a>
                        </div>
                        <input type="submit" name="logsubmit" value="Login" class="btn">
                    </form>
                </form>
            </div>

            <div class="form-box forgot">
                <h2>Forgot Password?</h2>
                <p class="pforg">Reset your Password</p>
                <form action="#">
                    <div class="input-box">
                        <span class="icon"><ion-icon name="mail"></ion-icon></span>
                        <input type="emailaddress" required>
                        <label>Email Address</label>
                    </div>
                    <div class="return">
                        <a href="#" class="login-link">Return Login</a>
                    </div>
                    <button type="submit" class="btn">Submit</button>
                </form>
            </div>
        </div>

        
            <footer>
                <div class="ultra-bottom">Copyright &copy;2024; Designed by Logtu</div>
                <nav class="footer-nav">
                    <a href="about.php">About Us</a>
                    <a href="reserve.php">Reservation</a>
                    <a href="contact.php">Contact</a>
                 </nav>  
            </footer>
               
        <script src="script.js"></script>
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
     </body>
</html>