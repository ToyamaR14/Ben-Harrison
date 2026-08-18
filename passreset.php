<?php
session_start();
if (!isset($_GET['token'])) {
    die("Token not provided!");
}
$token = $_GET['token'];
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
            <div class="login">
                <div class="content">
                    <img src="icons/apple-splash-1334-750.jpg" alt="Logo">
                </div>
                <div class="loginform">
                    <h2 class="fir">Reset<span class="sec"> Password</span></h2>
                    <form action="proc.php" method="POST">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                        <div class="input-box">
                            <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                            <input type="password" name="newpass" id="newpass" autocomplete="off" placeholder="New Password" required>
                        </div>
                        <div class="input-box">
                            <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                            <input type="password" name="newpassc" id="newpassc" autocomplete="off" placeholder="Confirm New Password" required>
                        </div>
                        <div class="show-password">
                            <input type="checkbox" id="showPasswordToggle" onclick="togglePasswordVisibility()">
                            <label for="showPasswordToggle">Show Password</label>
                        </div>
                        <input type="submit" name="passressubmit" value="Submit" class="btn">
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
                }
            }
            // Show Password 
            function togglePasswordVisibility() {
                var newPass = document.getElementById('newpass');
                var newPassC = document.getElementById('newpassc');
                var showPasswordToggle = document.getElementById('showPasswordToggle');
                if (showPasswordToggle.checked) {
                    newPass.type = 'text';
                    newPassC.type = 'text';
                } else {
                    newPass.type = 'password';
                    newPassC.type = 'password';
                }
            }
        </script>
        <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    </body>
</html>
