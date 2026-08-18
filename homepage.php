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
        <script src="page.js"></script>
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
            
            <div class="row">
                <div class="col-1">
                    <h2 class="fir">Ben<br><h2 class="sec">Harrison</h2></h2>
                    <h3 class="fir">Residence</h3>
                    <p>Every day is a journey, and the journey itself is home.</p>
                    <a href="contact.php#reserve" class="btn-spec anim">Interested?   Reserve now!</a>
                </div>
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