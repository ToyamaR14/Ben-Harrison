<?php session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Ben Harrison Residence</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style.css?v=2.0">
        <link rel="manifest" href="manifest.json">
    </head>

        <body>
            <div class="container">
              <div class="navbar">
                  <img src="icons/icon.png" class="logo"></img>
                    <nav>
                        <ul id="menulist">
                            <li><a class="active" href="homepage.php">Home</a></li>
                            <li><a class="active" href="about.php">About Us</a></li>
                            <li><a class="active" href="contact.php">Contact Us</a></li>
                            <li><a class="active" href="login.php">Login</a></li>
                        </ul>
                    </nav>
                <ion-icon name="menu" class="menu-icon" onclick="toggle()"></ion-icon>
              </div>

            <section>
              <div class="contact-box">
                <div class="contact-in">
                    <h1>Contact<h10 class="sec"> Info</h10></h1>
                    <h2><ion-icon name="call"></ion-icon>Phone</h2>
                    <p>Landline: 8898-2682</p>
                    <h2><ion-icon name="mail"></ion-icon>Email</h2>
                    <p>BH.benharrisonofficial@gmail.com</p>
                    <h2><ion-icon name="map"></ion-icon>Address</h2>
                    <p>5302 Ben Harrison, Makati, Metro Manila</p>
                </div>
                <div class="contact-in">
                    <h1>Send <h10 class="sec">a Message</h10></h1>
                    <form class="contact-form" id="contact_form" action="proc.php" method="POST">
                        <input type="text" name="full_name" autocomplete="off" placeholder="Full Name" class="contact-in-input">
                        <input type="text" name="con_email" autocomplete="off" placeholder="Email" class="contact-in-input" id="con_email">
                        <input type="tel" id="phone" name="con_number" autocomplete="off" placeholder="Contact Number" class="contact-in-input" pattern="[0]{1}[9]{1}[0-9]{9}" maxlength="11" required>
                        <input type="text" name="con_sub" autocomplete="off" placeholder="Subject" class="contact-in-input">
                        <textarea placeholder="Message" autocomplete="off" name="con_mes" class="contact-in-textarea" required></textarea>
                        <input type="submit" name="con-submit" value="Submit" class="contact-in-btn">
                    </form>
                </div>
            </div>
            </section>
            <section>
                <div class="contact-map">
                    <div class="location">
                        <h1 style="align-items: center;justify-content: center;margin: 0 auto;position: relative;display: flex;">
                            Our <h10 class="sec">Location</h10></h1>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15447.258259674856!2d121.0101112695344!3d14.55259437372989!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c9348b89d2d7%3A0x7553301aa2eee3c9!2s5302%20Ben%20Harrison%2C%20Makati%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1716704962617!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </section>
            <section>
            <div class="reserve-box" id="reserve">
                <div class="reserve-image">
                    <img src="imeg/pin.png">
                </div>
                <div class="loginform">
                    <h2 class="fir">Reser<span class="sec">vation</span></h2>
                    <p>Fill in the form to reserve a spot</p>
                    <form class="reserve-form" id="reserve_form" action="proc.php" method="POST">
                        <input type="text" name="res_fname" autocomplete="off" placeholder="First Name" class="reserve-input" required>
                        <input type="text" name="res_lname" autocomplete="off" placeholder="Last Name" class="reserve-input" required>
                        <input type="email" name="res_email" autocomplete="off" placeholder="Email" class="reserve-input" required>
                        <input type="tel" id="phone" name="res_contact" autocomplete="off" placeholder="Contact Number" class="reserve-input" pattern="[0]{1}[9]{1}[0-9]{9}" maxlength="11" required>
                        <div class="terms-container">
                            <input type="checkbox" id="terms" name="terms" required>
                            <label for="terms" class="terms">I agree to the <a href="terms_and_condition.html" class="link" target="_blank">Terms and Conditions</a></label>
                        </div>
                        <div class="privacy-container">
                            <input type="checkbox" id="privacy" name="privacy" required>
                            <label for="privacy" class="terms">I agree to the <a href="privacy_policy.html" class="link" target="_blank">Privacy Policy</a></label>
                        </div>
                        <input type="submit" name="res-submit" value="Submit" class="reserve-btn">
                    </form>
                </div>
            </div>
        </div>
        </section>
    </div>
             
  
    <div class="footer">  
            <p>&copy; 2024 Ben Harrison Residence. All rights reserved.</p>
            <p>
                <a href="about.php">About Us</a>
                <a href="contact.php#reserve">Reserve</a>
                <a href="contact.php">Contact Us</a>
            </p>
    </div>

<script>
   
    var menuList = document.getElementById("menulist");
        menuList.style.maxHeight = "0px";
        function toggle() {
            if (menuList.style.maxHeight == "0px"){
                menuList.style.maxHeight = "450px";
            } else {
                menuList.style.maxHeight = "0px";
        }}

    if ('ServiceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js');
    }
</script>

<script src="https://cdn.botpress.cloud/webchat/v2.3/inject.js"></script>
<script src="https://files.bpcontent.cloud/2025/03/27/08/20250327081410-4Q5O1AFM.js"></script>

<script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
     </body>
</html>