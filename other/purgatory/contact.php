<?php 
date_default_timezone_set("Asia/Manila");
session_start(); ?>
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

<div class="contact-box anim">
	<div class="contact-in">
		<h1>Contact<h10 class="sec"> Info</h10></h1>
		<h2><ion-icon name="call"></ion-icon>Phone</h2>
		<p>NUMBER NUMBER NUMBER</p>
        <p>Landline: 8898-2682</p>
		<h2><ion-icon name="mail"></ion-icon>Email</h2>
		<p>BH.benharrisonofficial@gmail.com</p>
		<h2><ion-icon name="map"></ion-icon>Address</h2>
		<p>5302 Ben Harrison, Makati, Metro Manila</p>
	</div>
	<div class="contact-in">
		<h1>Send <h10 class="sec">a Message</h10></h1>
		<form class="contact-form" id="contact_form" action="proc.php" method="POST">
			<input type="text" name="full_name" placeholder="Full Name" class="contact-in-input">
			<input type="text" name="con_email" placeholder="Email" class="contact-in-input">
            <input type="tel" id="phone" name="con_number" placeholder="Contact Number" class="contact-in-input" pattern="[0]{1}[9]{1}[0-9]{9}" maxlength="11" required>
			<input type="text" name="con_sub"  placeholder="Subject" class="contact-in-input">
			<textarea placeholder="Message" name="con_mes" class="contact-in-textarea" required></textarea>
			<input type="submit" name="con-submit" value="Submit" class="contact-in-btn">
		</form>
	</div>
	<div class="contact-in">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15447.258259674856!2d121.0101112695344!3d14.55259437372989!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c9348b89d2d7%3A0x7553301aa2eee3c9!2s5302%20Ben%20Harrison%2C%20Makati%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1716704962617!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
	</div>
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
                <h2>Forgot ID</h2>
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