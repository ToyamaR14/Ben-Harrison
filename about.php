<?php session_start(); ?>
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
            
                <div class="about-us-box">
                    <div class="content-container">
                        <div class="main-about">
                            <div class="about-content">
                                <h1 class="fir">About <span class="sec">Us</span></h1>
                                <p>
                                    Makati City is a place to both local enterprises and multinational
                                    corporations in the Philippines. Located within the streets of barangay
                                    of Pio Del Pilar, The Ben Harrison Residence offers comfortable and safe place
                                    for both local and professional that seeking accommodation near their workplace in Makati City.
                                </p>
                            </div>
                        </div>
                
                        <div class="main-about">
                            <div class="about-content">
                                <h1 class="fir">Our <span class="sec">Location</span></h1>
                                <p>
                                    Positioned at 5302 Ben Harrison, Makati, Metro Manila, The Ben Harrison Residence
                                    illustrates a civilized urban living in the streets of Pio Del Pilar barangay. With
                                    its location near the active businesses district of Makati, our residence
                                    caters to people who looks for a place to stay or professionals that is seeking a seamless
                                    blend of career opportunities and current lifestyle amenities.
                                </p>
                            </div>
                        </div>
                        <a href="contact.php#reserve" class="btn-about">Interested?</a>
                    </div>
                </div>
                  
        
    <footer class="footer">
        <div>
            <p>&copy; 2024 Ben Harrison Residence. All rights reserved.</p>
            <p>
                <a href="about.php">About Us</a>
                <a href="contact.php#reserve">Reserve</a>
                <a href="contact.php">Contact Us</a>
            </p>
            </div>
    </footer>

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