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
            
            <div class="login">
                <div class="content">
                        <img src="icons/apple-splash-1334-750.jpg">
                </div>
                <div class="loginform">
                    <h2 class="fir">Reset <span class="sec">Password</span></h2>
                    <p>Enter your Email Address to request for Reset Password</p>
                    <form action="proc.php" method="POST">
                        <div class="input-box">
                            <span class="icon"><ion-icon name="mail"></ion-icon></span>
                            <input type="email" name="email" autocomplete="off" placeholder="Email Address" required>
                        </div>
                        <div class="login-direct">
                            <a href="login.php" class="forgot-link">Return to Log-in</a>
                        </div>
                        <input type="submit" name="forgotpasssubmit" value="Submit" class="btn">
                    </form>
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

<!-- Start of ChatBot (www.chatbot.com) code -->
<script type="text/javascript">
    window.__be = window.__be || {};
    window.__be.id = "668b90a53abf060007819003";
    (function() {
        var be = document.createElement('script'); be.type = 'text/javascript'; be.async = true;
        be.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.chatbot.com/widget/plugin.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(be, s);
    })();
</script>
<noscript>You need to <a href="https://www.chatbot.com/help/chat-widget/enable-javascript-in-your-browser/" rel="noopener nofollow">enable JavaScript</a> in order to use the AI chatbot tool powered by <a href="https://www.chatbot.com/" rel="noopener nofollow" target="_blank">ChatBot</a></noscript>
<!-- End of ChatBot code -->

<script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
     </body>
</html>