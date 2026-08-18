<?php date_default_timezone_set("Asia/Manila"); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Sekrit Registration</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="adminreg_style.css"/>
</head>

<body>
<?php
    require('db.php');

    if (isset($_REQUEST['submit'])) {
        $user_type = stripslashes($_REQUEST['user_type']);
        $user_type = mysqli_real_escape_string($con, $user_type);
        $first_name = stripslashes($_REQUEST['first_name']);
        $first_name = mysqli_real_escape_string($con, $first_name);
        $last_name = stripslashes($_REQUEST['last_name']);
        $last_name = mysqli_real_escape_string($con, $last_name);
        $password = stripslashes($_REQUEST['password']);
        $password = mysqli_real_escape_string($con, $password);
        $email = stripslashes($_REQUEST['email']);
        $email = mysqli_real_escape_string($con, $email);
        $joined_date = date("Y-m-d H:i:s");

        $query    = "INSERT into `tenant_tbl` (user_type_id, first_name, last_name, password, email, joined_date, status_id)
                        VALUES ('$user_type', '$first_name', '$last_name', '".md5($password)."', '$email', '$joined_date', '11')";
        $result   = mysqli_query($con, $query);
        if ($result) { }

        }
?>  
            <header>
                <h2 class="logo">Ben <h10 class="sec">Harrison Residence</h10></h2>
                 <nav class="navigation">
                    <a class="active" href="homepage.php">Home</a>
                 </nav>            
            </header>
            <?php             
    $query = "SELECT user_type_id, user_type FROM user_type";
    $result = mysqli_query($con, $query);  
?> 

<div class="regform">
    <form class="reg-form" id="reg-form" action="" method="post">
        <div class="form-box register">
            <h2>Registration</h2>

            <div class="input-box">
                <span class="icon"><ion-icon name="mail"></ion-icon></span>
                <input type="email" name="email" required>
                <label>Email Address</label>
            </div>

            <div class="input-box">
                <span class="icon"><ion-icon name="person"></ion-icon></span>
                <input type="text" name="first_name" required>
                <label>First Name</label>
            </div>

            <div class="input-box">
                <span class="icon"><ion-icon name="person"></ion-icon></span>
                <input type="text" name="last_name" required>
                <label>Last Name</label>
            </div>

            <div class="input-box">
                <span class="icon"><ion-icon name="key"></ion-icon></span>
                <input type="password" name="password" required>
                <label>Password</label>
            </div>

            <div class="input-box">
                <span class="icon"><ion-icon name="help-circle"></ion-icon></span>
                <label>User Type: </label>
                <select name="user_type" id="user_type">
                    <?php  
                    while ($row = mysqli_fetch_array($result)) { 
                        echo "<option value='{$row['user_type_id']}'>{$row['user_type']}</option>";
                    } 
                    ?>
                </select>
            </div>
        </div>
        <input type="submit" name="submit" value="Register" class="btn">
    </form>
</div>


    <footer>
                <div class="ultra-bottom">Copyright &copy;2024; Designed by Logtu</div>
                <nav class="footer-nav">
                    <a href="about.php">About</a>
                    <a href="#">Reservation</a>
                    <a href="contact.php">Contact</a>
                 </nav>  
               </footer>
               
    <script src="script.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
     </body>
</html>