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
            
            <section class="about-us-box">
            <div class="main-about">
            <img class="anim" src="imeg/chair.jpg" alt="tall_building">
                <div class="about-content anim">
                <h1>About <h10 class="sec">Us</h10></h1>
                    <p>
                    Makati City is home to both local enterprises and multinational 
                    corporations in the Philippines. Nestled within the bustling barangay 
                    of Pio Del Pilar, The Ben Harrison Residence offers unparalleled convenience 
                    for professionals seeking accommodation near their workplace in Makati City.
                    </p>
                    <a href="reserve" class="buton">Interested?</a>
                </div>
            </div>
        </section>
            
        <section class="about-us-box2">
            <div class="main-about2">
            <img class="anim" src="imeg/road.jpg" alt="tall_building">
                <div class="about-content2 anim">
                <h1>Our <h10 class="sec">Location</h10></h1>
                    <p>
                    Positioned at 5302 Ben Harrison, Makati, Metro Manila, The Ben Harrison Residence 
                    epitomizes sophisticated urban living in the heart of Pio Del Pilar barangay. With 
                    its strategic location near the bustling business district of Makati, our residence 
                    caters to professionals seeking a seamless blend of career opportunities and contemporary 
                    lifestyle amenities.
                    </p>
                    <a href="contact" class="buton2">Let's Go</a>
                </div>
            </div>
        </section>

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
                <p class="pforg">Send us your email to recover your Password</p>
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
