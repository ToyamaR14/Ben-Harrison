<div class="login">
                <div class="content">
                        <img src="imeg/partment.png">
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




        <div class="reserve-box">
                <div class="reserve-in">
                    <h1 class="fir">Reser<h10 class="sec">vation</h10></h1>
                    <form class="reserve-form" id="reserve_form" action="proc.php" method="POST">
                        <input type="text" name="res_fname" autocomplete="off" placeholder="First Name" class="reserve-input" required>
                        <input type="text" name="res_lname" autocomplete="off" placeholder="Last Name" class="reserve-input" required>
                        <input type="text" name="res_email" autocomplete="off" placeholder="Email" class="reserve-input" required>
                        <input type="tel" id="phone" name="res_contact" placeholder="Contact Number" class="reserve-input" pattern="[0]{1}[9]{1}[0-9]{9}" maxlength="11" required>
                        <input type="submit" name="res-submit" value="Submit" class="reserve-btn">
                    </form>
                </div>
                <div class="reserve-image">
                    <img class="image" src="imeg/pin.png">
                </div>
            </div>