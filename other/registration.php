<?php date_default_timezone_set("Asia/Manila");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Ben Harrsion</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
<?php
    require('db.php');
    if (isset($_REQUEST['submit'])) {
        
        $joined_date = date("Y-m-d H:i:s");
        $validate = "SELECT email_add FROM user_tbl WHERE email_add= '".$email_add."';";
        $db_email = mysqli_query($con,$validate);
        if  (mysqli_num_rows($db_email) >= 1){
        echo"<script>
        alert('Email Already Exist!');
        </script>";
        }
        else { $query    = "INSERT into `user_tbl` (user_type, username, password, email_add, account_created)
                        VALUES ('$user_type', '$username', '".md5($password)."', '$email_add', '$account_created')";
            $result   = mysqli_query($con, $query);
        
            if ($result) { }
            //unset($user_type,$username,$password,$email_add,$query);
        }
    }
?>  
            <header>
                <h2 class="logo">Ben <h10 class="sec">Harrison Residence</h10></h2>
                 <nav class="navigation">
                    <a class="active" href="dashboard.php">Home</a>
                 </nav>            
            </header>

    <div class="regform">
    <form class="reg-form" id="reg-form" action="" method="post">
        <div class="form-box register">
        <h2>Registration</h2>
            <div class="input-box">
                <span class="icon"><ion-icon name="mail"></ion-icon></span>
                <input type="emailaddress" name="email_add" required>
                <label>Email Address</label>
        </div>
            <div class="input-box">
                <span class="icon"><ion-icon name="person"></ion-icon></span>
                <input type="username" name="username" required>
                <label>Username</label>
        </div>
            <div class="input-box">
                <span class="icon"><ion-icon name="key"></ion-icon></span>
                <input type="password" name="password" required>
                <label>Password</label>
             </div>
            <div class="input-box">
                <span class="icon"><ion-icon name="help-circle"></ion-icon></span>
                <label>User Type: </label>
                <label> Tenant </label>
        </div>
    </div>
        <input type="submit" name="submit" value="Register" class="btn">
    </form>
    </div>
               
     </body>
</html>