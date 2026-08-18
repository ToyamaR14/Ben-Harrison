<?php 
session_start();
include ('db.php');

define('SESSION_TIMEOUT', 3600); // Timeout in seconds (e.g., 60 minute(s))

if (isset($_SESSION['email'])) {
    
    if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
        session_unset();
        session_destroy();

        echo "<script type='text/javascript'>";
        echo "alert('Your session has expired. Please log in again.');";
        echo "window.location.href = 'login.php';";
        echo "</script>";
        exit;
    } else {
        $_SESSION['last_activity'] = time();
    }

    $email = $_SESSION['email'];
    $query = "SELECT first_name, last_name, user_type_id FROM tenant_tbl WHERE email = '$email'";
    $result = mysqli_query($con, $query);
    $show = mysqli_fetch_assoc($result);

    if ($show) {
        $first_name = $show['first_name'];
        $last_name = $show['last_name'];
        $user_type_id = $show['user_type_id'];
        $_SESSION['user_type_id'] = $user_type_id;
    } else {
        $first_name = "Unknown";
        $last_name = " ";
        $user_type_id = null;
    }
} else {
    $first_name = "Unknown";
    $last_name = " ";
    $user_type_id = null;
}
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
            <?php if(isset($_SESSION['email'])): ?>
                <div class="login">
                    <div class="content">
                        <img src="icons/apple-splash-1334-750.jpg">
                    </div>
                    <div class="loginform">
                        <div id="continue-session">
                            <div class="continue">
                                <h2 class="fir">Continue<span class="sec"> as</span></h2>
                                <p><span id="username"><?php echo $first_name . " " . $last_name; ?></span></p>
                            </div>
                            <div class="continue">
                                <button class="btncont" id="continue-btn" onclick="continueSession()">Continue</button>
                            </div>
                            <div class="continue">
                                <button class="btnlog" id="logout-btn" onclick="logout()">Logout</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="login">
                    <div class="content">
                        <img src="icons/apple-splash-1334-750.jpg">
                    </div>
                    <div class="loginform">
                        <h2 class="fir">Log<span class="sec">in</span></h2>
                        <form action="proc.php" method="POST">
                            <div class="input-box">
                                <span class="icon"><ion-icon name="mail"></ion-icon></span>
                                <input type="email" name="email" autocomplete="off" placeholder="Email Address" required>
                            </div>
                            <div class="input-box">
                                <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                                <input type="password" name="password" id="pass" autocomplete="off" placeholder="Password" required>
                            </div>
                            <div class="show-password">
                                <input type="checkbox" id="showPasswordToggle" onclick="togglePasswordVisibility()">
                                <label for="showPasswordToggle">Show Password</label>
                            </div>
                            <div class="remember-forgot">
                                <a href="passforgot.php" class="forgot-link">Forgot Password?</a>
                            </div>
                            <input type="submit" name="logsubmit" value="Login" class="btn">
                        </form>
                    </div>
                </div>
            <?php endif; ?>
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
            // Menu Icon
            var menuList = document.getElementById("menulist");
            menuList.style.maxHeight = "0px";
            function toggle() {
                if (menuList.style.maxHeight == "0px") {
                    menuList.style.maxHeight = "450px";
                } else {
                    menuList.style.maxHeight = "0px";
                }
            }
            // Show Password
            function togglePasswordVisibility() {
                var Pass = document.getElementById('pass');
                var showPasswordToggle = document.getElementById('showPasswordToggle');
                if (showPasswordToggle.checked) {
                    Pass.type = 'text';
                } else {
                    Pass.type = 'password';
                }
            }

            //Service Worker
            if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js')
            .then(function(registration) {
                console.log('ServiceWorker registration successful with scope: ', registration.scope);
            })
            .catch(function(error) {
                console.log('ServiceWorker registration failed: ', error);
            });
            }

            // Continue Session
            function continueSession() {
                <?php if (isset($_SESSION['user_type_id'])): ?>
                    <?php if ($_SESSION['user_type_id'] == 1): ?>
                        window.location.href = 'dashboard.php';
                    <?php elseif ($_SESSION['user_type_id'] == 2): ?>
                        window.location.href = 'home.php';
                    <?php endif; ?>
                <?php else: ?>
                    console.error('User type ID not set in session');
                <?php endif; ?>
            }
            // Logout
            function logout() {
                fetch('logout.php', { 
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'logout=true'
                })
                .then(response => {
                    if (response.ok) {
                        window.location.href = 'login.php';
                    } else {
                        console.error('Logout failed');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
            
        </script>
        <script src="https://cdn.botpress.cloud/webchat/v2.3/inject.js"></script>
        <script src="https://files.bpcontent.cloud/2025/03/27/08/20250327081410-4Q5O1AFM.js"></script>

        <script src="page.js"></script>
        <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    </body>
</html>