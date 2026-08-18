<?php
date_default_timezone_set("Asia/Manila");
include("db.php");
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

/*************************
*******LOGIN******
**************************/

if (isset($_POST['logsubmit'])) {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Retrieve and sanitize inputs
    $email = mysqli_real_escape_string($con, stripslashes($_POST['email']));
    $password = mysqli_real_escape_string($con, stripslashes($_POST['password']));

    // Prepare and execute query
    $query = "SELECT * FROM tenant_tbl WHERE BINARY email = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_array($result);
    mysqli_stmt_close($stmt);

    $dateEntry = date('Y-m-d H:i:s');
    $userIp = $_SERVER['REMOTE_ADDR'];

    if ($row) {
        if ($row['status_id'] == '11') {
            if (password_verify($password, $row['password'])) {
                // Log the login attempt
                $logType = 1; // Assuming 1 represents login
                $tenantId = $row['tenant_id'];

                $insertLogQuery = "INSERT INTO log_tbl (log_type_id, tenant_id, date_entry, user_ip) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($con, $insertLogQuery);
                mysqli_stmt_bind_param($stmt, "iiss", $logType, $tenantId, $dateEntry, $userIp);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                // Set session and redirect based on user type
                $_SESSION['email'] = $email;
                $_SESSION['tenant_id'] = $tenantId;
                $_SESSION['last_activity'] = time(); // Set the session's last activity time

                if ($row['user_type_id'] == 1) {
                    header('Location: dashboard.php');
                } elseif ($row['user_type_id'] == 2) {
                    header('Location: home.php');
                }
                exit;
            } else {

                echo "<script type='text/javascript'>";
                echo "alert('Invalid Password!');";
                echo "window.location.href = 'login.php';";
                echo "</script>";
            }
        } else {
            // Log the deny attempt
            $logType = 9; // Assuming 9 represents deny
            $tenantId = $row['tenant_id'];

            $insertLogQuery = "INSERT INTO log_tbl (log_type_id, tenant_id, date_entry, user_ip) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($con, $insertLogQuery);
            mysqli_stmt_bind_param($stmt, "iiss", $logType, $tenantId, $dateEntry, $userIp);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            echo "<script type='text/javascript'>";
            echo "alert('Your account is disabled. Please contact support.');";
            echo "window.location.href = 'login.php';";
            echo "</script>";
        }
    } else {

        echo "<script type='text/javascript'>";
        echo "alert('Invalid Email!');";
        echo "window.location.href = 'login.php';";
        echo "</script>";
    } 
}

/*************************
*******FORGOT PASSWORD******
**************************/

if (isset($_POST["forgotpasssubmit"])) {

    // Sanitize and validate inputs
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    
    // Prepare and execute the query to fetch user data
    $query = "SELECT email, status_id, tenant_id FROM tenant_tbl WHERE email = ?";
    $stmt = mysqli_prepare($con, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email); // Bind email parameter
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_array($result);
        
        if (mysqli_num_rows($result) <= 0) {
            echo "<script>
                    alert('Email does not exist'); 
                    window.location.replace('passforgot.php');
                  </script>";
        } else {
            $status_id = $row["status_id"];
            $tenant_id = $row["tenant_id"];

            if ($status_id == 10) {
                echo "<script>
                        alert('Sorry, your account must be activated first before you recover your password!'); 
                        window.location.replace('homepage.php');
                      </script>";
            } elseif ($status_id == 12) {
                echo "<script>
                        alert('The Email is Disabled, Please Contact Support'); 
                        window.location.replace('homepage.php');
                      </script>";
            } else {
                // Generate a unique token for password reset
                $token = bin2hex(random_bytes(16));
                $token_expiry = date("Y-m-d H:i:s", strtotime('+20 minutes'));

                // Update token and token expiry in the database
                $update_query = "UPDATE tenant_tbl SET token=?, token_expiry=? WHERE email=?";
                $stmt_update = mysqli_prepare($con, $update_query);
                
                if ($stmt_update) {
                    mysqli_stmt_bind_param($stmt_update, "sss", $token, $token_expiry, $email); // Bind parameters
                    mysqli_stmt_execute($stmt_update);

                    // Check if update was successful
                    if (mysqli_stmt_affected_rows($stmt_update) > 0) {
                        // Send password reset email
                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                        $mail->Port = 465;
                        $mail->Username = 'BH.benharrisonofficial@gmail.com';
                        $mail->Password = 'keox mkrb uoal qeyi';
                        $mail->SMTPSecure = 'ssl';
                        $mail->setFrom('bh.benharrisonofficial@gmail.com');
                        $mail->addAddress($email);
                        $mail->isHTML(true);
                        $mail->Subject = "Recover your password";
                        $mail->Body = "<b>Dear User,</b>
                                       <h3>We received a request to reset your password.</h3>
                                       <p>Kindly click the below link to reset your password:</p>
                                       <a href='https://purple-dragonfly-294859.hostingersite.com/passreset.php?token=$token'>Reset Password</a>
                                       <br><br>
                                       <p>With regards,</p><b>Ben Harrison Residence</b>";

                        if (!$mail->send()) {
                            echo "<script>
                                    alert('Invalid Email'); 
                                    window.location.href = 'homepage.php';
                                  </script>";
                        } else {

                            $logType = 8; // 8 represents request
                            $dateEntry = date('Y-m-d H:i:s');
                            $insertLogQuery = "INSERT INTO log_tbl (log_type_id, tenant_id, date_entry, user_ip) VALUES (?, ?, ?, ?)";
                            $stmt_log = mysqli_prepare($con, $insertLogQuery);

                            if ($stmt_log) {
                                // Get the user's IP address, considering proxies
                                $userIp = $_SERVER['REMOTE_ADDR'];
                                if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                                    $userIp = $_SERVER['HTTP_CLIENT_IP'];
                                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                                    $userIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
                                }
                                
                                mysqli_stmt_bind_param($stmt_log, "iiss", $logType, $tenant_id, $dateEntry, $userIp);
                                mysqli_stmt_execute($stmt_log);
                            }

                            echo "<script>
                                    alert('Your request was submitted! Please check your Email Address'); 
                                    window.location.href = 'homepage.php';
                                  </script>";
                        }
                    } else {
                        echo "<script>
                                alert('Failed to update token. Please try again later.'); 
                                window.location.replace('passforgot.php');
                              </script>";
                    }

                    mysqli_stmt_close($stmt_update);
                } else {
                    echo "<script>
                            alert('Database error. Please try again later.'); 
                            window.location.replace('passforgot.php');
                          </script>";
                }
            }
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>
                alert('Database error. Please try again later.'); 
                window.location.replace('passforgot.php');
              </script>";
    }
}

/*************************
*******RESET PASSWORD******
**************************/

if (isset($_POST['passressubmit'])) {
    // Sanitize and validate inputs
    $token = mysqli_real_escape_string($con, $_POST['token']);
    $newpass = mysqli_real_escape_string($con, $_POST['newpass']);
    $newpassc = mysqli_real_escape_string($con, $_POST['newpassc']);

    // Check if passwords match
    if ($newpass !== $newpassc) {
        echo "<script type='text/javascript'>";
        echo "alert('Passwords do not match.');";
        echo "window.location.href = 'passreset.php?token=$token';";
        echo "</script>";
        exit;
    }

    $hashed_password = password_hash($newpass, PASSWORD_DEFAULT);

    // Query to fetch token and token expiry
    $query = "SELECT tenant_id, token_expiry FROM tenant_tbl WHERE token=?";
    $stmt = mysqli_prepare($con, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $token);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_array($result);

        // Check if token is valid
        if (mysqli_num_rows($result) <= 0) {
            echo "<script type='text/javascript'>";
            echo "alert('Invalid or expired token!');";
            echo "window.location.href = 'passreset.php?token=$token';";
            echo "</script>";
        } else {
            $current_time = date("Y-m-d H:i:s");
            if ($current_time > $row['token_expiry']) {
                // Token expired
                $update_query = "UPDATE tenant_tbl SET token=NULL, token_expiry=NULL WHERE token=?";
                $stmt_update = mysqli_prepare($con, $update_query);

                if ($stmt_update) {
                    mysqli_stmt_bind_param($stmt_update, "s", $token);
                    mysqli_stmt_execute($stmt_update);
                    mysqli_stmt_close($stmt_update);
                }

                echo "<script type='text/javascript'>";
                echo "alert('Token expired! Please request a new password reset.');";
                echo "window.location.href = 'passforgot.php';";
                echo "</script>";
            } else {
                // Token is valid, update the password
                $tenant_id = $row['tenant_id'];
                $update_password_query = "UPDATE tenant_tbl SET password=?, token=NULL, token_expiry=NULL WHERE tenant_id=?";
                $stmt_update_pass = mysqli_prepare($con, $update_password_query);

                if ($stmt_update_pass) {
                    mysqli_stmt_bind_param($stmt_update_pass, "si", $hashed_password, $tenant_id);
                    mysqli_stmt_execute($stmt_update_pass);

                    // Check if password update was successful
                    if (mysqli_stmt_affected_rows($stmt_update_pass) > 0) {

                        $logType = 5; // 5 represents modified
                        $dateEntry = date('Y-m-d H:i:s');
                        
                        $userIp = $_SERVER['REMOTE_ADDR'];
                        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                            $userIp = $_SERVER['HTTP_CLIENT_IP'];
                        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                            $userIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
                        }
                        $userIp = filter_var($userIp, FILTER_VALIDATE_IP);

                        $insertLogQuery = "INSERT INTO log_tbl (log_type_id, tenant_id, date_entry, user_ip) VALUES (?, ?, ?, ?)";
                        $stmt_log = mysqli_prepare($con, $insertLogQuery);

                        if ($stmt_log) {
                            mysqli_stmt_bind_param($stmt_log, "iiss", $logType, $tenant_id, $dateEntry, $userIp);
                            mysqli_stmt_execute($stmt_log);
                            mysqli_stmt_close($stmt_log);
                        }

                        echo "<script type='text/javascript'>";
                        echo "alert('Reset Password Successfully');";
                        echo "window.location.href = 'homepage.php';";
                        echo "</script>";
                    } else {
                        echo "<script type='text/javascript'>";
                        echo "alert('Reset Password Unsuccessfully');";
                        echo "window.location.href = 'passreset.php?token=$token';";
                        echo "</script>";
                    }

                    mysqli_stmt_close($stmt_update_pass);
                }
            }
        }
        mysqli_stmt_close($stmt);
    }
}

/*************************
*******CONTACTS******
**************************/

if (isset($_REQUEST['con-submit'])) {

        $full_name  =     $_REQUEST['full_name'];
        $con_email  =     $_REQUEST['con_email'];
        $con_number =     $_REQUEST['con_number'];
        $con_sub    =     $_REQUEST['con_sub'];
        $con_mes    =     $_REQUEST['con_mes'];
        $con_datetime =   date("Y-m-d H:i:s");

        $query    = "INSERT into `contact_tbl` (full_name, con_email, con_number, con_sub, con_mes, con_datetime, status_id)
                     VALUES ('$full_name', '$con_email', '$con_number', '$con_sub', '$con_mes', '$con_datetime', '3')";
        $result   = mysqli_query($con, $query);
        if ($result) {
            echo "<script type = 'text/javascript'>";
            echo "alert('Contact request sent successfully');";
            echo "window.location.href = 'contact.php'";
            echo "</script>";
        }
}

/*************************
*******RESERVATION******
**************************/

if (isset($_REQUEST['res-submit'])) {

        $res_fname =      $_REQUEST['res_fname'];
        $res_lname =      $_REQUEST['res_lname'];
        $res_email =      $_REQUEST['res_email'];
        $res_contact  =   $_REQUEST['res_contact'];
        $date_requested = date("Y-m-d H:i:s");

        $query    = "INSERT into `reserve_tbl` (res_fname, res_lname, res_email, res_contact, date_requested, status_id)
                     VALUES ('$res_fname', '$res_lname', '$res_email', '$res_contact', '$date_requested', '0' )";
        $result   = mysqli_query($con, $query);
        if ($result) {
            echo "<script type = 'text/javascript'>";
            echo "alert('Reservation request sent successfully');";
            echo "window.location.href = 'contact.php'";
            echo "</script>";
        }
}

/*************************
*******RESERVATION ADMIN MODAL******
**************************/

if (isset($_REQUEST['res-modal-submit'])) {

    $resm_fname =      $_REQUEST['resm_fname'];
    $resm_lname =      $_REQUEST['resm_lname'];
    $resm_email =      $_REQUEST['resm_email'];
    $resm_contact  =   $_REQUEST['resm_contact'];
    $date_requested = date("Y-m-d H:i:s");

    $query    = "INSERT into `reserve_tbl` (res_fname, res_lname, res_email, res_contact, date_requested, status_id)
                 VALUES ('$resm_fname', '$resm_lname', '$resm_email', '$resm_contact', '$date_requested', '0' )";
    $result   = mysqli_query($con, $query);
    if ($result) {
        $reserve_id = mysqli_insert_id($con);

        $logType = 6; // 6 represents modified
        $dateEntry = date('Y-m-d H:i:s');
        $insertLogQuery = "INSERT INTO log_tbl_rs (log_type_id, reserve_id, date_entry) VALUES ('$logType', '$reserve_id', '$dateEntry')";
        $logResult = mysqli_query($con, $insertLogQuery);
        
        echo "<script type = 'text/javascript'>";
        echo "alert('Added successfully');";
        echo "window.location.href = 'reservation.php'";
        echo "</script>";
    }
}

/*************************
******* ADMIN MESSAGE TO SPECIFIC TENANT WITH GMAIL NOTIFICATION ******
**************************/

if (isset($_REQUEST['message-tenant'])) { // Match form submit button name
    $tenant_id = $_REQUEST['tenant_id']; // Get specific tenant ID
    $subject = $_REQUEST['subject'];
    $message = $_REQUEST['message'];
    $date_sent = date("Y-m-d H:i:s");

    // Get tenant email
    $query_tenant = "SELECT email, first_name FROM tenant_tbl WHERE tenant_id = '$tenant_id'";
    $result_tenant = mysqli_query($con, $query_tenant);

    if ($result_tenant && $tenant = mysqli_fetch_assoc($result_tenant)) {
        $tenant_email = $tenant['email'];
        $tenant_name = $tenant['first_name'];

        // Insert message into message_tbl
        $query_message = "INSERT INTO `message_tbl` (tenant_id, send_by, subject, message, date_sent, status_id)
                          VALUES ('$tenant_id', 'Admin', '$subject', '$message', '$date_sent', '3')";
        $result_message = mysqli_query($con, $query_message);

        if ($result_message) {
            try {
                // Send Gmail Notification
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;
                $mail->Username = 'BH.benharrisonofficial@gmail.com';
                $mail->Password = 'keox mkrb uoal qeyi';
                $mail->SMTPSecure = 'ssl';
                $mail->setFrom('bh.benharrisonofficial@gmail.com', 'Ben Harrison Admin');
                $mail->addAddress($tenant_email);
                $mail->isHTML(true);
                $mail->Subject = "New Message from Admin - $subject";
                $mail->Body = "<h3>Dear $tenant_name,</h3>
                               <p>You have received a new message from the admin.</p>
                               <p><strong>Subject:</strong> $subject</p>
                               <p><strong>Message:</strong></p>
                               <p>$message</p>
                               <br><br>
                               <b>Ben Harrison Residence</b>";

                $mail->send();

                echo "<script type='text/javascript'>";
                echo "alert('Message Sent & Notification Email Delivered!');";
                echo "window.location.href = 'manage.php';";
                echo "</script>";

            } catch (Exception $e) {
                echo "<script type='text/javascript'>";
                echo "alert('Message sent, but email notification failed!');";
                echo "window.location.href = 'manage.php';";
                echo "</script>";
            }
        } else {
            echo "Error sending message: " . mysqli_error($con);
        }
    } else {
        echo "Error fetching tenant email: " . mysqli_error($con);
    }
}

/*************************
*******ACCEPT RESERVATION ******
**************************/
if (isset($_POST['accept'])) {
    $reserve_id = $_POST['reserve_id'];

    // Prepared statement for updating the reservation status
    $query = "UPDATE reserve_tbl SET status_id = ? WHERE reserve_id = ?";
    $stmt = mysqli_prepare($con, $query);
    $status_id = 1; // Accepted status
    mysqli_stmt_bind_param($stmt, "ii", $status_id, $reserve_id);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $logType = 3; // Log type for acceptance
        $dateEntry = date('Y-m-d H:i:s');

        // Log entry for reservation acceptance
        $insertLogQuery = "INSERT INTO log_tbl_rs (log_type_id, reserve_id, date_entry) VALUES (?, ?, ?)";
        $stmtLog = mysqli_prepare($con, $insertLogQuery);
        mysqli_stmt_bind_param($stmtLog, "iis", $logType, $reserve_id, $dateEntry);
        mysqli_stmt_execute($stmtLog);

        // Fetch reservation details for email notification
        $query_reservation = "SELECT res_email, res_fname, res_lname FROM reserve_tbl WHERE reserve_id = ?";
        $stmtRes = mysqli_prepare($con, $query_reservation);
        mysqli_stmt_bind_param($stmtRes, "i", $reserve_id);
        mysqli_stmt_execute($stmtRes);
        $result_reservation = mysqli_stmt_get_result($stmtRes);

        if ($result_reservation && $reservation = mysqli_fetch_assoc($result_reservation)) {
            $res_email = $reservation['res_email'];
            $res_fname = $reservation['res_fname'];
            $res_lname = $reservation['res_lname'];

            try {
                // Send Gmail Notification
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;
                $mail->Username = 'BH.benharrisonofficial@gmail.com';
                $mail->Password = 'keox mkrb uoal qeyi';
                $mail->setFrom('bh.benharrisonofficial@gmail.com', 'Ben Harrison Admin');
                $mail->addAddress($res_email);
                $mail->isHTML(true);
                $mail->Subject = "Reservation Accepted - $reserve_id";
                $mail->Body = "<h3>Dear $res_fname $res_lname,</h3>
                               <p>Your Reservation Request has been Accepted!</p>
                               <p>To Confirm your Reservation, please proceed to this address: <strong>5302 Ben Harrison, Makati, Metro Manila</strong> and contact us through our email or call our Landline: <strong>8898-2682</strong> within 14 working days.</p>
                               <p>Please note that failure to arrive at the scheduled appointment may result in rescheduling or cancellation.</p>
                               <br><br>
                               <b>Ben Harrison Residence</b>";

                $mail->send();

                echo "<script>
                        alert('Reservation Accepted & Notification Email Sent!');
                        window.location.href = 'reservation.php';
                      </script>";
            } catch (Exception $e) {
                echo "<script>
                        alert('Reservation Accepted, but email failed: {$mail->ErrorInfo}');
                        window.location.href = 'reservation.php';
                      </script>";
            }
        } else {
            echo "<script>
                    alert('Reservation Accepted, but failed to fetch reservation details.');
                    window.location.href = 'reservation.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('Failed to accept reservation.');
                window.location.href = 'reservation.php';
              </script>";
    }
}

/*************************
*******CANCEL RESERVATION ******
**************************/
         
if(isset($_POST['cancel'])) {
        $reserve_id = $_POST['reserve_id'];
      
        $query = "UPDATE reserve_tbl SET status_id = '2' WHERE reserve_id = $reserve_id";
        $result = mysqli_query($con,$query);

        $logType = 4; // 4 represents cancel
        $dateEntry = date('Y-m-d H:i:s');
        $insertLogQuery = "INSERT INTO log_tbl_rs (log_type_id, reserve_id, date_entry) VALUES ('$logType', '$reserve_id', '$dateEntry')";
        $logResult = mysqli_query($con, $insertLogQuery);
        
        echo "<script type = 'text/javascript'>";
        echo "alert('Reservation Cancelled');";
        echo "window.location.href = 'reservation.php'";
        echo "</script>";
      } 

/*************************
*******TENANT ADD MODAL******
**************************/

if (isset($_REQUEST['tenant-modal-submit'])) {

    $first_name = $_REQUEST['first_name'];
    $last_name = $_REQUEST['last_name'];
    $email = $_REQUEST['email'];
    $password = $_REQUEST['password'];
    $cpassword = $_REQUEST['cpassword'];
    $contacts = $_REQUEST['contacts'];
    $joined_date = date("Y-m-d H:i:s");

    if ($password !== $cpassword) {
        echo "<script>
                alert('Passwords do not match!');
                window.location.href = 'manage.php';
              </script>";
        exit;
    }

    $validate = "SELECT email FROM tenant_tbl WHERE email = '".$email."';";
    $db_email = mysqli_query($con, $validate);

    if (mysqli_num_rows($db_email) >= 1) {
        echo "<script>
                alert('Email Already Exist!');
                window.location.href = 'manage.php';
              </script>";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT into `tenant_tbl` (user_type_id, first_name, last_name, email, password, contacts, joined_date, status_id)
                  VALUES ('2', '$first_name', '$last_name', '$email', '$hashed_password', '$contacts', '$joined_date', '10')";
        $result = mysqli_query($con, $query);

        if ($result) {
            $tenant_id = mysqli_insert_id($con);

            $logType = 6; // 6 represents add
            $dateEntry = date('Y-m-d H:i:s');
            $insertLogQuery = "INSERT INTO log_tbl (log_type_id, tenant_id, date_entry, user_ip) VALUES ('$logType', '$tenant_id', '$dateEntry', '-')";
            $logResult = mysqli_query($con, $insertLogQuery);

            $insertLogMes = "INSERT INTO message_tbl (tenant_id, send_by, subject, message, date_sent, status_id) VALUES ('$tenant_id', 'Admin', 'Welcome to Ben Harrison!', 
                            'A heartfelt welcome to Ben Harrison Residence! We hope you have a fantastic stay with us. Our team is here to ensure your comfort and satisfaction. There will be a intial payment upon staying the apartment, please reach out us on Google Mail, BH.benharrisonofficial@gmail.com or go to the front desk if you need any assistance.', '$dateEntry', '3')";
            $logMes = mysqli_query($con, $insertLogMes);


            echo "<script type='text/javascript'>
                    alert('Added successfully');
                    window.location.href = 'manage.php';
                  </script>";
        }
    }
}


/*************************
*******TENANT EDIT MODAL******
**************************/

if(isset($_POST['edittenant_id'])) {
    
    $editid      = $_POST['edittenant_id'];
    $first_name  = $_POST['first_name'];
    $last_name   = $_POST['last_name'];
    $contacts    = $_POST['contacts'];
    $status_type = $_POST['status_type'];

    $query = "UPDATE tenant_tbl 
              SET first_name = '$first_name', last_name = '$last_name', 
                  contacts = '$contacts', status_id = '$status_type' 
              WHERE tenant_id = '$editid'";
    
    $result = mysqli_query($con, $query);
    
    if($result) {

        $logType = 5; // 5 represents modify
        $editt = $editid;
        $dateEntry = date('Y-m-d H:i:s');
        $insertLogQuery = "INSERT INTO log_tbl (log_type_id, tenant_id, date_entry, user_ip) 
                           VALUES ('$logType', '$editt', '$dateEntry', '-')";
        $logResult = mysqli_query($con, $insertLogQuery);
        
        echo "<script type='text/javascript'>";
        echo "alert('Edit Successful');";
        echo "window.location.href = 'manage.php'";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('Edit Unsuccessful');";
        echo "window.location.href = 'manage.php'";
        echo "</script>";
    }
}


/*************************
*******TENANT RESET PASSWORD MODAL******
**************************/

if(isset($_POST['reset_pass'])) {
    $respa      = $_POST['reset_pass'];
    $password   = $_REQUEST['password'];
    $cpassword  = $_REQUEST['cpassword'];

    if($password !== $cpassword) {
        echo "<script type = 'text/javascript'>";
        echo "alert('Passwords do not match.');";
        echo "window.location.href = 'manage.php';";
        echo "</script>";
        exit; // Stop further execution
    }
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $query = "UPDATE tenant_tbl SET password = '$hashed_password' WHERE tenant_id = '$respa'";
    $result = mysqli_query($con, $query);
    if($result) {
        $logType = 5; // 5 represents modify
        $resetId = $respa;
        $dateEntry = date('Y-m-d H:i:s');
        $insertLogQuery = "INSERT INTO log_tbl (log_type_id, tenant_id, date_entry, user_ip) VALUES ('$logType', '$resetId', '$dateEntry', '-')";
        $logResult = mysqli_query($con, $insertLogQuery);

        echo "<script type = 'text/javascript'>";
        echo "alert('Reset Password Successfully');";
        echo "window.location.href = 'manage.php';";
        echo "</script>";
    } else {
        echo "<script type = 'text/javascript'>";
        echo "alert('Reset Password Unsuccessfully');";
        echo "window.location.href = 'manage.php';";
        echo "</script>";
    }
}

/*************************
*******ROOM ADD MODAL******
**************************/

if (isset($_REQUEST['room-modal-submit'])) {

    $room_number    =  $_REQUEST['room_number'];
    $room_floor     =  $_REQUEST['room_floor'];
    $tenant_id      =  $_REQUEST['tenant_id'];
    $date_in        =  $_REQUEST['date_in'];
    $date_out       =  $_REQUEST['date_out'];
    
    $query    = "INSERT into `room_tbl` (room_number, room_floor, tenant_id, date_in, date_out, status_id)
                     VALUES ('$room_number', '$room_floor', '$tenant_id', '$date_in', '$date_out', '10')";
    $result   = mysqli_query($con, $query);
        if ($result) {
            $room_id = mysqli_insert_id($con);

            $logType = 6; // 6 represents add
            $dateEntry = date('Y-m-d H:i:s');
            $insertLogQuery = "INSERT INTO log_tbl_r (log_type_id, room_id, date_entry) VALUES ('$logType', '$room_id', '$dateEntry')";
            $logResult = mysqli_query($con, $insertLogQuery);

            echo "<script type = 'text/javascript'>";
            echo "alert('Added successfully');";
            echo "window.location.href = 'room.php'";
            echo "</script>";
    } else {
            echo "<script type = 'text/javascript'>";
            echo "alert('Added unsuccessful');";
            echo "window.location.href = 'room.php'";
            echo "</script>";
    }
}

/*************************
*******ROOM EDIT MODAL******
**************************/

if(isset($_POST['room_edit'])) {
    $roomedit        = $_POST['room_edit'];
    $room_number     = $_POST['room_number'];
    $room_floor      = $_POST['room_floor'];
    $tenant_id       = $_POST['tenant_id'];
    $status_type     = $_POST['status_type'];

    $query = "UPDATE room_tbl SET room_number = '$room_number', room_floor = '$room_floor', tenant_id = '$tenant_id',  status_id = '$status_type' WHERE room_id = '$roomedit'";
    
    $result = mysqli_query($con, $query);
    
    if($result) {
        $logType = 5; // 5 represents modify
        $romed = $roomedit;
        $dateEntry = date('Y-m-d H:i:s');
        $insertLogQuery = "INSERT INTO log_tbl_r (log_type_id, room_id, date_entry) VALUES ('$logType', '$romed', '$dateEntry')";
        $logResult = mysqli_query($con, $insertLogQuery);

        echo "<script type = 'text/javascript'>";
        echo "alert('Edit Successfully');";
        echo "window.location.href = 'room.php'";
        echo "</script>";
    } else {
        echo "<script type = 'text/javascript'>";
        echo "alert('Edit Unsuccessfully');";
        echo "window.location.href = 'room.php'";
        echo "</script>";
    }
}

/*************************
*******PAYMENT ADD MODAL******
**************************/

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['payment-modal-submit'])) {

    $tenant_id      = $_POST['tenant_id'];
    $contact_number = $_POST['contact_number'];
    $email_address  = $_POST['email_address'];
    $purpose        = $_POST['purpose'];
    $amount         = $_POST['amount'];
    $payment_intent_id  = !empty($_POST['payment_intent_id']) ? $_POST['payment_intent_id'] : NULL; // Allow empty intent ID
    $payment_type   = $_POST['payment_type'];
    $status_type    = $_POST['status_type'];
    $date_entry     = date("Y-m-d H:i:s");

    // Set date_paid only if status_id is 5 (PAID)
    $date_paid = ($status_type == 5) ? $date_entry : NULL;

    // Prevent accidental double entry: check if similar entry exists within a short time
    $checkQuery = "SELECT COUNT(*) FROM payment_tbl WHERE tenant_id = ? AND amount = ? AND DATE(date_entry) = CURDATE()";
    $checkStmt = mysqli_prepare($con, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, "ss", $tenant_id, $amount);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_bind_result($checkStmt, $count);
    mysqli_stmt_fetch($checkStmt);
    mysqli_stmt_close($checkStmt);

    if ($count > 0) {
        echo "<script>alert('Duplicate entry detected! Similar payment was made today.'); window.location.href = 'payment.php';</script>";
        exit();
    }

    // Insert payment record
    $query = "INSERT INTO `payment_tbl` (tenant_id, contact_number, email_address, purpose_id, amount, payment_intent_id, payment_type_id, date_entry, status_id, date_paid)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ssssssssis", $tenant_id, $contact_number, $email_address, $purpose, $amount, $payment_intent_id, $payment_type, $date_entry, $status_type, $date_paid);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $payment_id = mysqli_insert_id($con);

        $logType = 6; // 6 represents "add"
        $insertLogQuery = "INSERT INTO log_tbl_pay (log_type_id, payment_id, date_entry) VALUES (?, ?, ?)";
        $logStmt = mysqli_prepare($con, $insertLogQuery);
        mysqli_stmt_bind_param($logStmt, "iis", $logType, $payment_id, $date_entry);
        mysqli_stmt_execute($logStmt);

        echo "<script>alert('Payment added successfully!'); window.location.href = 'payment.php?success=1';</script>";
        exit();
    } else {
        echo "<script>alert('Payment failed. Please try again.'); window.location.href = 'payment.php?error=1';</script>";
        exit();
    }
}

/*************************
*******PAYMENT EDIT MODAL******
**************************/

if (isset($_POST['payment_edit'])) {
    $payedit     = $_POST['payment_edit'];
    $status_type = $_POST['status_type'];

    // Update status_id first
    $query = "UPDATE payment_tbl SET status_id = '$status_type' WHERE payment_id = '$payedit'";
    $result = mysqli_query($con, $query);

    if ($result) {
        // If the status is PAID (5), update date_paid
        if ($status_type == 5) {
            $date_paid = date('Y-m-d');
            $updateDateQuery = "UPDATE payment_tbl SET date_paid = '$date_paid' WHERE payment_id = '$payedit'";
            mysqli_query($con, $updateDateQuery);
        } else {
            // Optional: If status is NOT PAID, you can set date_paid to NULL
            $updateDateQuery = "UPDATE payment_tbl SET date_paid = NULL WHERE payment_id = '$payedit'";
            mysqli_query($con, $updateDateQuery);
        }

        // Insert log entry
        $logType = 5; // 5 represents modify
        $dateEntry = date('Y-m-d H:i:s');
        $insertLogQuery = "INSERT INTO log_tbl_pay (log_type_id, payment_id, date_entry) VALUES ('$logType', '$payedit', '$dateEntry')";
        $logResult = mysqli_query($con, $insertLogQuery);

        if ($logResult) {
            echo "<script type='text/javascript'>";
            echo "alert('Edit Successfully');";
            echo "window.location.href = 'payment.php';";
            echo "</script>";
        } else {
            echo "<script type='text/javascript'>";
            echo "alert('Edit Successfully, but log entry failed');";
            echo "window.location.href = 'payment.php';";
            echo "</script>";
        }
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('Edit Unsuccessful');";
        echo "window.location.href = 'payment.php';";
        echo "</script>";
    }
}


/*************************
*******RESERVATION DELETE******
**************************/

if (isset($_POST['delete_reserve'])) {
    $reserveId = $_POST['delete_reserve'];

    $query = "DELETE FROM `reserve_tbl` WHERE reserve_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $reserveId);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $logType = 7; // Log type for deletion
        $dateEntry = date('Y-m-d H:i:s');

        $insertLogQuery = "INSERT INTO log_tbl_rs (log_type_id, reserve_id, date_entry) VALUES (?, ?, ?)";
        $stmtLog = mysqli_prepare($con, $insertLogQuery);
        mysqli_stmt_bind_param($stmtLog, "iis", $logType, $reserveId, $dateEntry);
        mysqli_stmt_execute($stmtLog);

        echo "<script>
                alert('Reservation deleted successfully');
                window.location.href = 'reservation.php';
              </script>";
    } else {
        echo "<script>
                alert('Delete unsuccessful');
                window.location.href = 'reservation.php';
              </script>";
    }
}

/*************************
*******CONTACT DELETE******
**************************/

if (isset($_POST['con_id'])) {
    $conid = $_POST['con_id'];
    $query = "DELETE FROM `contact_tbl` WHERE con_id = '$conid'";
    $result = mysqli_query($con, $query);
    
    if ($result) {
        $logType = 7; // 7 represents delete
        $cId = $conid;
        $dateEntry = date('Y-m-d H:i:s'); 
        $insertLogQuery = "INSERT INTO log_tbl_c (log_type_id, con_id, date_entry) VALUES ('$logType', '$cId', '$dateEntry')";
        mysqli_query($con, $insertLogQuery);

        echo "<script type = 'text/javascript'>";
        echo "alert('Deleted successfully');";
        echo "window.location.href = 'notification.php'";
        echo "</script>";
    } else {
        echo "<script type = 'text/javascript'>";
        echo "alert('Delete unsuccessful');";
        echo "window.location.href = 'notification.php'";
        echo "</script>";
        
    }
}
      
/*************************
*******ROOM DELETE******
**************************/

if (isset($_POST['delete_room'])) {
    $roomid = $_POST['delete_room'];
    
    // Prepared statement for deleting the room
    $query = "DELETE FROM `room_tbl` WHERE room_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $roomid);
    
    if (mysqli_stmt_execute($stmt)) {
        // Log deletion in log_tbl_r
        $logType = 7; // 7 represents delete
        $dateEntry = date('Y-m-d H:i:s');
        $insertLogQuery = "INSERT INTO log_tbl_r (log_type_id, room_id, date_entry) VALUES (?, ?, ?)";
        
        $logStmt = mysqli_prepare($con, $insertLogQuery);
        mysqli_stmt_bind_param($logStmt, "iis", $logType, $roomid, $dateEntry);
        mysqli_stmt_execute($logStmt);

        echo "<script type='text/javascript'>";
        echo "alert('Deleted successfully');";
        echo "window.location.href = 'room.php';";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('Delete unsuccessful');";
        echo "window.location.href = 'room.php';";
        echo "</script>";
    }
}

/*************************
*******TENANT DELETE******
**************************/

if (isset($_POST['tenant-del'])) {
    $tenaid = $_POST['tenant-del'];

    $query_get_tenant = "SELECT tenant_id, first_name FROM tenant_tbl WHERE tenant_id = '$tenaid'";
    $result_get_tenant = mysqli_query($con, $query_get_tenant);

    if ($result_get_tenant && mysqli_num_rows($result_get_tenant) > 0) {
        $tenant = mysqli_fetch_assoc($result_get_tenant);

        $query_delete = "DELETE FROM `tenant_tbl` WHERE tenant_id = '$tenaid'";
        $result_delete = mysqli_query($con, $query_delete);

        if ($result_delete) {
            $logType = 7; // 7 represents delete
            $tenantId = $tenaid;
            $firstName = $tenant['first_name']; 
            $dateEntry = date('Y-m-d H:i:s');
            $insertLogQuery = "INSERT INTO log_tbl (log_type_id, tenant_id, l_first_name, date_entry, user_ip) 
                               VALUES ('$logType', '$tenantId', '$firstName', '$dateEntry', '-')";
            mysqli_query($con, $insertLogQuery);

            echo "<script type='text/javascript'>";
            echo "alert('Deleted successfully');";
            echo "window.location.href = 'manage.php';";
            echo "</script>";
        } else {
            echo "<script type='text/javascript'>";
            echo "alert('Delete unsuccessful');";
            echo "window.location.href = 'manage.php';";
            echo "</script>";
        }
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('Tenant not found');";
        echo "window.location.href = 'manage.php';";
        echo "</script>";
    }
}


/*************************
*******TENANT USER UPDATE******
**************************/

if(isset($_POST['update-tenant'])) {

    $update = mysqli_real_escape_string($con, $_POST['update-tenant']);
    $first_name = mysqli_real_escape_string($con, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($con, $_POST['last_name']);
    $contacts = mysqli_real_escape_string($con, $_POST['contacts']);

    $query = "UPDATE tenant_tbl 
              SET first_name = '$first_name', last_name = '$last_name', 
                  contacts = '$contacts'
              WHERE tenant_id = '$update'";
    
    $result = mysqli_query($con, $query);
    
    if($result) {

        $logType = 5; // 5 represents modify
        $updoot = $update; 
        $dateEntry = date('Y-m-d H:i:s');
        $insertLogQuery = "INSERT INTO log_tbl (log_type_id, tenant_id, date_entry, user_ip) 
                           VALUES ('$logType', '$updoot', '$dateEntry', '-')";
        $logResult = mysqli_query($con, $insertLogQuery);
        
        echo "<script type='text/javascript'>";
        echo "alert('Update Successful');";
        echo "window.location.href = 'profile.php'";
        echo "</script>";
    } else {

        echo "<script type='text/javascript'>";
        echo "alert('Update Unsuccessful');";
        echo "window.location.href = 'profile.php'";
        echo "</script>";
        exit();
    }
} 

/*************************
*******ADMIN MESSAGE TO USERS******
**************************/

if (isset($_REQUEST['message-tenant-admin'])) {
    $subject = $_REQUEST['subject'];
    $message = $_REQUEST['message'];
    $date_sent = date("Y-m-d H:i:s");

    $query_tenants = "SELECT tenant_id, email, first_name FROM tenant_tbl WHERE user_type_id = 2";
    $result_tenants = mysqli_query($con, $query_tenants);

    if ($result_tenants) {

        while ($row = mysqli_fetch_assoc($result_tenants)) {
            $tenant_id = $row['tenant_id'];
            $tenant_email = $row['email'];
            $tenant_name = $row['first_name'];

            // Insert the message into the database
            $query_message = "INSERT INTO `message_tbl` (tenant_id, send_by, subject, message, date_sent, status_id)
                              VALUES ('$tenant_id', 'Admin', '$subject', '$message', '$date_sent', '3')";
            $result_message = mysqli_query($con, $query_message);

            if (!$result_message) {
                echo "Error sending message to tenant ID: $tenant_id. " . mysqli_error($con);
            }
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;
                $mail->Username = 'BH.benharrisonofficial@gmail.com';
                $mail->Password = 'keox mkrb uoal qeyi';
                $mail->SMTPSecure = 'ssl';
                $mail->setFrom('bh.benharrisonofficial@gmail.com', 'Ben Harrison Admin');
                $mail->addAddress($tenant_email, $tenant_name);
                $mail->isHTML(true);
                $mail->Subject = "Message from Admin: $subject";
                $mail->Body = "
                    <h3>Hello $tenant_name,</h3>
                    <p>You have received a new message from the Admin:</p>
                    <p><b>Subject:</b> $subject</p>
                    <p><b>Message:</b><br>$message</p>
                    <br><br>
                    <p>Best regards,<br>Admin Team</p>";

                $mail->send();
            } catch (Exception $e) {
                echo "Message could not be sent to $tenant_email. Mailer Error: {$mail->ErrorInfo}";
            }
        }

        echo "<script type='text/javascript'>";
        echo "alert('Message Sent!');";
        echo "window.location.href = 'manage.php'";
        echo "</script>";
    } else {
        echo "Error fetching tenant IDs: " . mysqli_error($con);
    }
}

/*************************
*******ITEM ADD MODAL******
**************************/

if (isset($_REQUEST['add-item'])) {
    $item_name = $_REQUEST['item_name'];
    $quantity  = $_REQUEST['quantity'];
    $on_hand   = $_REQUEST['on_hand'];
    $owner     = $_REQUEST['owner'];
    $date_added = date('Y-m-d H:i:s');

    $query = "INSERT INTO `inventory_tbl` (item_name, quantity, on_hand, owner, status_id, date_added)
              VALUES ('$item_name', '$quantity', '$on_hand', '$owner', '15', '$date_added')";

    $result = mysqli_query($con, $query);

    if ($result) {
        $item_id = mysqli_insert_id($con);

        // Log the addition
        $logType = 6; // Log type 6 represents "add"
        $dateEntry = date('Y-m-d H:i:s');
        $insertLogQuery = "INSERT INTO log_tbl_i (log_type_id, item_id, date_entry)
                           VALUES ('$logType', '$item_id', '$dateEntry')";
        mysqli_query($con, $insertLogQuery);

        echo "<script type='text/javascript'>";
        echo "alert('Item added successfully');";
        echo "window.location.href = 'inventory.php';";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('Failed to add item');";
        echo "window.location.href = 'inventory.php';";
        echo "</script>";
    }
}

/*************************
*******ITEM EDIT MODAL******
**************************/

if (isset($_POST['edit-item-submit'])) {
    $item_id  =    $_POST['edit_item_id'];
    $item_name =   $_POST['item_name'];
    $quantity =    $_POST['quantity'];
    $on_hand =     $_POST['on_hand'];
    $owner =       $_POST['owner'];
    $status_type = $_POST['status_type'];
    $last_updated = date('Y-m-d H:i:s'); 

    // Update the quantity and last_updated fields in inventory_tbl
    $query = "UPDATE inventory_tbl 
              SET item_name = '$item_name', quantity = '$quantity', on_hand = '$on_hand', owner = '$owner', last_updated = '$last_updated', status_id = '$status_type'
              WHERE item_id = '$item_id'";

    $result = mysqli_query($con, $query);

    if ($result) {
        // Log the modification
        $logType = 5; // 5 represents modify
        $dateEntry = $last_updated;
        $insertLogQuery = "INSERT INTO log_tbl_i (log_type_id, item_id, date_entry)
                           VALUES ('$logType', '$item_id', '$dateEntry')";
        $logResult = mysqli_query($con, $insertLogQuery);

        if ($logResult) {
            echo "<script type='text/javascript'>";
            echo "alert('Quantity updated successfully');";
            echo "window.location.href = 'inventory.php';";
            echo "</script>";
        } else {
            echo "<script type='text/javascript'>";
            echo "alert('Quantity updated, but log entry failed');";
            echo "window.location.href = 'inventory.php';";
            echo "</script>";
        }
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('Quantity update failed');";
        echo "window.location.href = 'inventory.php';";
        echo "</script>";
    }
}

/*************************
*******ITEM DELETE******
**************************/

if (isset($_POST['item-del'])) {
    $item_id = $_POST['item-del'];

    // Delete the item from inventory_tbl
    $query = "DELETE FROM `inventory_tbl` WHERE item_id = '$item_id'";
    $result = mysqli_query($con, $query);

    if ($result) {
        // Log the deletion
        $logType = 7; // 7 represents delete
        $dateEntry = date('Y-m-d H:i:s');
        $insertLogQuery = "INSERT INTO log_tbl_i (log_type_id, item_id, date_entry) 
                           VALUES ('$logType', '$item_id', '$dateEntry')";
        mysqli_query($con, $insertLogQuery);

        echo "<script type='text/javascript'>";
        echo "alert('Item deleted successfully');";
        echo "window.location.href = 'inventory.php';";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('Deletion unsuccessful');";
        echo "window.location.href = 'inventory.php';";
        echo "</script>";
    }
}

/*************************
*******BORROWER ADD MODAL******
**************************/

if (isset($_POST['borrow-item'])) { 

    $tenant_id   = mysqli_real_escape_string($con, $_POST['tenant_id']);
    $item_id     = intval($_POST['item_id']);
    $borrow_qty  = intval($_POST['borrow_quantity']); 
    $date_borrow = date('Y-m-d'); 
    $status_id   = 17; // "BORROWED" status

    // Fetch current inventory details
    $query = "SELECT quantity, on_hand, item_name FROM inventory_tbl WHERE item_id = '$item_id'";
    $result = mysqli_query($con, $query);
    $inventory = mysqli_fetch_assoc($result);

    if ($inventory) {
        $available_stock = $inventory['quantity']; // Total quantity available
        $borrow_on_hand = $inventory['on_hand'];   // Current borrowed count

        // Debugging output (remove later)
        echo "Total Quantity: $available_stock <br>";
        echo "Borrow Quantity: $borrow_qty <br>";
        echo "On Hand Before Borrow: $borrow_on_hand <br>";

        // Check if quantity is enough for borrowing
        if ($available_stock >= $borrow_qty) {
            // Increase on_hand since the item is being borrowed
            $new_on_hand = $inventory['on_hand'] + $borrow_qty;
            $borrow_on_hand = $new_on_hand; // Sync borrow_on_hand with on_hand

            // Insert into borrow_tbl
            $insertBorrow = "INSERT INTO borrow_tbl (tenant_id, item_id, borrow_qty, date_borrow, status_id, borrow_on_hand) 
                             VALUES ('$tenant_id', '$item_id', '$borrow_qty', '$date_borrow', '$status_id', '$borrow_on_hand')";
            $insertResult = mysqli_query($con, $insertBorrow);

            if ($insertResult) {
                $borrow_id = mysqli_insert_id($con);
                $logType = 12; // "BORROWED" log type

                // Insert into log_tbl_b
                $logQuery = "INSERT INTO log_tbl_b (log_type_id, borrow_id, date_entry)
                             VALUES ('$logType', '$borrow_id', NOW())";
                mysqli_query($con, $logQuery);

                // Insert into borrow_history
                $insertHistoryQuery = "INSERT INTO borrow_history 
                                       (borrow_id, tenant_id, h_item_name, h_quantity, h_on_hand, h_date_borrow, h_date_return, status_id, h_date_update) 
                                       VALUES ('$borrow_id', '$tenant_id', '{$inventory['item_name']}', '$borrow_qty', '$borrow_on_hand', '$date_borrow', NULL, '$status_id', NOW())";
                mysqli_query($con, $insertHistoryQuery);

                // Update inventory_tbl: Increase `on_hand` to track borrowed items
                $updateInventory = "UPDATE inventory_tbl 
                                    SET on_hand = '$new_on_hand', 
                                        status_id = IF(quantity = '$new_on_hand', 16, status_id) 
                                    WHERE item_id = '$item_id'";
                mysqli_query($con, $updateInventory);

                echo "<script>alert('Item borrowed successfully'); window.location.href = 'inventory.php';</script>";
            } else {
                echo "<script>alert('Failed to borrow item'); window.location.href = 'inventory.php';</script>";
            }
        } else {
            echo "<script>alert('Not enough stock available'); window.location.href = 'inventory.php';</script>";
        }
    } else {
        echo "<script>alert('Item not found'); window.location.href = 'inventory.php';</script>";
    }
}

/*************************
*******BORROW EDIT MODAL******
**************************/

if (isset($_POST['edit-borrow-submit'])) {
    $borrow_id     = mysqli_real_escape_string($con, $_POST['edit_borrow_id']);
    $borrow_qty    = intval($_POST['borrow_qty']);
    $borrow_on_hand = intval($_POST['borrow_on_hand']); // Ensure we retrieve this
    $status_type   = intval($_POST['status_type']);
    $last_updated  = date('Y-m-d H:i:s');

    // Fetch the previous borrow record
    $fetchQuery = "SELECT borrow_tbl.*, inventory_tbl.item_id, inventory_tbl.item_name, 
                          inventory_tbl.quantity AS inventory_qty, inventory_tbl.on_hand 
                   FROM borrow_tbl
                   JOIN inventory_tbl ON borrow_tbl.item_id = inventory_tbl.item_id
                   WHERE borrow_tbl.borrow_id = '$borrow_id'";
    $fetchResult = mysqli_query($con, $fetchQuery);
    $oldData = mysqli_fetch_assoc($fetchResult);

    if (!$oldData) {
        echo "<script>alert('Borrow record not found!'); window.location.href = 'inventory.php';</script>";
        exit;
    }

    $old_borrow_qty = intval($oldData['borrow_qty']);
    $item_id        = $oldData['item_id'];
    $h_item_name    = $oldData['item_name'];
    $h_date_borrow  = $oldData['date_borrow'];
    $on_hand        = intval($oldData['on_hand']); // Current stock in inventory
    $inventory_qty  = intval($oldData['inventory_qty']); // Total quantity in inventory

    // Set date_returned if status is "Returned"
    $h_date_return = ($status_type == 18) ? "'$last_updated'" : "NULL";

    // Update borrow_tbl with borrow_on_hand included
    $updateQuery = "UPDATE borrow_tbl 
                    SET borrow_qty = '$borrow_qty', 
                        borrow_on_hand = '$borrow_on_hand', 
                        date_updated = '$last_updated', 
                        status_id = '$status_type', 
                        date_return = $h_date_return
                    WHERE borrow_id = '$borrow_id'";

    if (mysqli_query($con, $updateQuery)) {
        // Insert into borrow_history
        $insertHistoryQuery = "INSERT INTO borrow_history 
                               (borrow_id, tenant_id, h_item_name, h_quantity, h_on_hand, h_date_borrow, h_date_return, status_id, h_date_update) 
                               VALUES ('$borrow_id', '{$oldData['tenant_id']}', '$h_item_name', '$borrow_qty', '$on_hand', '$h_date_borrow', 
                                       $h_date_return, '$status_type', '$last_updated')";
        mysqli_query($con, $insertHistoryQuery);

        // If item is returned, update on_hand in inventory
        if ($status_type == 18) {
            // Calculate how many items were returned by comparing old borrow_on_hand and new borrow_on_hand
            $returned_items = $oldData['borrow_on_hand'] - $borrow_on_hand; // Subtract the new borrow_on_hand from the old one

            // Debugging: Check how many items have been returned and the updated value
            echo "<script>alert('Returned items: $returned_items');</script>";  // Debug: Check how many items are being returned
            echo "<script>alert('Old on_hand: $on_hand');</script>"; // Debugging: Check old on_hand value

            // Update inventory's on_hand by subtracting the returned items from on_hand
            $updated_on_hand = $on_hand + $returned_items;

            // Debugging: Check the updated value
            echo "<script>alert('Updated on_hand: $updated_on_hand');</script>";  // Debugging: Check the updated on_hand value

            // Update inventory table
            $updateInventory = "UPDATE inventory_tbl 
                                SET on_hand = '$updated_on_hand'
                                WHERE item_id = '$item_id'";

            if (mysqli_query($con, $updateInventory)) {
                // Success message
                echo "<script>alert('Inventory updated successfully');</script>";
            } else {
                // Show error message if inventory is not updated
                echo "<script>alert('Error updating inventory: " . mysqli_error($con) . "');</script>";
            }

            // If all items are returned, mark item as AVAILABLE (15)
            if ($updated_on_hand == $inventory_qty) {
                $updateStatusQuery = "UPDATE inventory_tbl 
                                      SET status_id = 15 
                                      WHERE item_id = '$item_id'";
                mysqli_query($con, $updateStatusQuery);
            }
        }

        // Log the modification
        $logType = 5; // 5 represents modification
        $logQuery = "INSERT INTO log_tbl_b (log_type_id, borrow_id, date_entry)
                     VALUES ('$logType', '$borrow_id', '$last_updated')";
        mysqli_query($con, $logQuery);

        echo "<script>alert('Borrow record updated successfully'); window.location.href = 'inventory.php';</script>";
    } else {
        echo "<script>alert('Update failed!'); window.location.href = 'inventory.php';</script>";
    }
}

/*************************
*******BORROW DELETE******
**************************/

if (isset($_POST['bor-del'])) {

    $borrow_id = mysqli_real_escape_string($con, $_POST['bor-del']);

    // Fetch the borrow record before deleting
    $fetchQuery = "SELECT * FROM borrow_tbl WHERE borrow_id = '$borrow_id'";
    $fetchResult = mysqli_query($con, $fetchQuery);

    if ($fetchResult && mysqli_num_rows($fetchResult) > 0) {
        $row = mysqli_fetch_assoc($fetchResult);

        // Move data to borrow_history with status_id = 19 (DELETED) and include tenant_id
        $historyQuery = "INSERT INTO borrow_history 
                    (borrow_id, tenant_id, h_item_name, h_quantity, h_on_hand, h_date_borrow, h_date_return, status_id, h_date_update) 
                 VALUES 
                    ('{$row['borrow_id']}', '{$row['tenant_id']}', '{$row['item_name']}', '{$row['quantity']}', '{$row['on_hand']}', 
                     '{$row['date_borrow']}', '{$row['date_return']}', 19, NOW())";

        if (mysqli_query($con, $historyQuery)) {
            // Update inventory_tbl to restore quantity when a borrowed item is deleted
            $updateInventoryQuery = "UPDATE inventory_tbl 
                                     SET quantity = quantity + '{$row['quantity']}', 
                                         on_hand = on_hand - '{$row['on_hand']}'
                                     WHERE item_id = '{$row['item_id']}'";
            mysqli_query($con, $updateInventoryQuery);
            
            // Delete from borrow_tbl
            $deleteQuery = "DELETE FROM borrow_tbl WHERE borrow_id = '$borrow_id'";
            $deleteResult = mysqli_query($con, $deleteQuery);

            if ($deleteResult) {
                // Log the deletion
                $logType = 7; // 7 represents delete
                $dateEntry = date('Y-m-d H:i:s');
                $insertLogQuery = "INSERT INTO log_tbl_b (log_type_id, borrow_id, date_entry) 
                                   VALUES ('$logType', '$borrow_id', '$dateEntry')";
                mysqli_query($con, $insertLogQuery);

                echo "<script type='text/javascript'>";
                echo "alert('Item deleted successfully and recorded in history');";
                echo "window.location.href = 'borrow_history.php';";
                echo "</script>";
            } else {
                echo "<script type='text/javascript'>";
                echo "alert('Failed to delete from borrow table');";
                echo "window.location.href = 'borrow_history.php';";
                echo "</script>";
            }
        } else {
            echo "<script type='text/javascript'>";
            echo "alert('Failed to insert into borrow history');";
            echo "window.location.href = 'borrow_history.php';";
            echo "</script>";
        }
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('Borrow ID not found');";
        echo "window.location.href = 'borrow_history.php';";
        echo "</script>";
    }
}

/*************************
*******MAINTENANCE ADD MODAL******
**************************/

if (isset($_REQUEST['maintenance-sub'])) {

    $tenant_id    = $_REQUEST['tenant_id'];
    $room_id      = $_REQUEST['room_id'];
    $issue        = $_REQUEST['issue'];
    $description  = $_REQUEST['description'];
    $date_added   = date('Y-m-d H:i:s'); // Automatically setting date_added
    $status_id    = 0; // 0 = Pending

    $query = "INSERT INTO `maintenance_tbl` (tenant_id, room_id, issue, description, date_added, status_id)
              VALUES ('$tenant_id', '$room_id', '$issue', '$description', '$date_added', '$status_id')";

    $result = mysqli_query($con, $query);

    if ($result) {
        $request_id = mysqli_insert_id($con);

        // Log the addition
        $logType = 6; // 6 represents "maintenance request added"
        $insertLogQuery = "INSERT INTO log_tbl_m (log_type_id, request_id, date_entry)
                           VALUES ('$logType', '$request_id', '$date_added')";
        mysqli_query($con, $insertLogQuery);

        echo "<script type='text/javascript'>";
        echo "alert('Maintenance request added successfully');";
        echo "window.location.href = 'maintenance.php';";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('Failed to add maintenance request');";
        echo "window.location.href = 'maintenance.php';";
        echo "</script>";
    }
}

/*************************
*******MAINTENANCE EDIT MODAL******
**************************/

if (isset($_POST['edit-maintenance-submit'])) {

    $request_id   = mysqli_real_escape_string($con, $_POST['edit_maintenance_id']);
    $status_type  = intval($_POST['status_id']);
    $last_updated = date('Y-m-d H:i:s');

    // If status is "Completed" (status_id = 13), set `date_completed`; otherwise, clear it
    if ($status_type == 13) {
        $date_completed_query = ", date_completed = '$last_updated'";
    } else {
        $date_completed_query = ", date_completed = NULL";
    }

    // Update the maintenance status
    $updateQuery = "UPDATE maintenance_tbl 
                    SET status_id = '$status_type' 
                    $date_completed_query
                    WHERE request_id = '$request_id'";

    $updateResult = mysqli_query($con, $updateQuery);

    if ($updateResult) {
        // Log the status update
        $logType = 5; // 5 represents modification
        $logQuery = "INSERT INTO log_tbl_m (log_type_id, request_id, date_entry)
                     VALUES ('$logType', '$request_id', '$last_updated')";
        mysqli_query($con, $logQuery);

        // If the status is "Completed", send an email notification to the tenant
        if ($status_type == 13) {
            // Get tenant's email and issue based on request_id
            $tenantQuery = "SELECT t.email, t.first_name, m.issue FROM tenant_tbl t 
                            JOIN maintenance_tbl m ON t.tenant_id = m.tenant_id 
                            WHERE m.request_id = '$request_id'";
            $tenantResult = mysqli_query($con, $tenantQuery);

            if ($tenantResult && $tenantRow = mysqli_fetch_assoc($tenantResult)) {
                $tenantEmail = $tenantRow['email'];
                $tenantName  = $tenantRow['first_name'];
                $issue       = $tenantRow['issue']; // Fetching the issue

                try {
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;
                    $mail->Username = 'BH.benharrisonofficial@gmail.com';
                    $mail->Password = 'keox mkrb uoal qeyi';
                    $mail->SMTPSecure = 'ssl';
                    $mail->setFrom('bh.benharrisonofficial@gmail.com', 'Ben Harrison Admin');
                    $mail->addAddress($tenantEmail);
                    $mail->isHTML(true);
                    $mail->Subject = "Maintenance Request Completed";
                    $mail->Body = "<h3>Hello $tenantName,</h3>
                                   <p>Your maintenance request has been successfully completed.</p>
                                   <p><strong>Issue:</strong> $issue</p>
                                   <p>If you have any further issues, feel free to submit another request.</p>
                                   <br><br>
                                   <b>Thank you,</b><br>
                                   <b>Admin Team</b>";

                    $mail->send();
                } catch (Exception $e) {
                    echo "<script>alert('Email notification failed.');</script>";
                }
            }
        }
        echo "<script>alert('Status updated successfully'); window.location.href = 'maintenance.php';</script>";
    } else {
        echo "<script>alert('Update failed'); window.location.href = 'maintenance.php';</script>";
    }
}

/*************************
*******MAINTENANCE DELETE *******
**************************/

if (isset($_POST['delete-maint'])) {
    $request_id = $_POST['delete-maint'];

    // Delete the request from maintenance_tbl
    $query = "DELETE FROM `maintenance_tbl` WHERE request_id = '$request_id'";
    $result = mysqli_query($con, $query);

    if ($result) {
        // Log the deletion
        $logType = 7; // 7 represents delete
        $dateEntry = date('Y-m-d H:i:s');
        $insertLogQuery = "INSERT INTO log_tbl_m (log_type_id, request_id, date_entry) 
                           VALUES ('$logType', '$request_id', '$dateEntry')";
        mysqli_query($con, $insertLogQuery);

        echo "<script type='text/javascript'>";
        echo "alert('Request deleted successfully');";
        echo "window.location.href = 'maintenance.php';";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('Deletion unsuccessful');";
        echo "window.location.href = 'maintenance.php';";
        echo "</script>";
    }
}

/*************************
*******TENANT MESSAGE FORM******
**************************/

if (isset($_POST['submit-message'])) {
    if (!isset($_SESSION['tenant_id'])) {
        die("Unauthorized access.");
    }

    $tenant_id = $_SESSION['tenant_id']; 
    $subject = $_POST['subject'] ?? '';
    $tenant_message = $_POST['tenant_message'] ?? '';
    $date_sent = date("Y-m-d H:i:s");
    $sent_to = "Admin"; 
    $status_id = 3; 

    // Debugging: Ensure values are received
    if (empty($subject) || empty($tenant_message)) {
        die("Error: Subject or message is empty.");
    }

    // Corrected SQL query
    $query_message = "INSERT INTO `tenant_mes_tbl` (tenant_id, sent_to, subject, tenant_message, date_sent, status_id)
                  VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $con->prepare($query_message);
    $stmt->bind_param("issssi", $tenant_id, $sent_to, $subject, $tenant_message, $date_sent, $status_id);

    if ($stmt->execute()) {
        echo "<script>alert('Message Sent!'); window.location.href = 'message.php';</script>";
    } else {
        echo "Error sending message: " . $stmt->error;
    }
}

/*************************
*******TENANT MAINTENANCE ADD MODAL******
**************************/

if (isset($_POST['submit-maintenance'])) {

    $tenant_id    = $_POST['tenant_id']; 
    $room_id      = $_POST['room_id'];
    $issue        = trim($_POST['issue']);
    $description  = trim($_POST['description']);
    $date_added   = date('Y-m-d H:i:s'); // Automatically setting date_added
    $status_id    = 0; // 0 = Pending

    // Securely Insert Data Using Prepared Statements
    $query = "INSERT INTO maintenance_tbl (tenant_id, room_id, issue, description, date_added, status_id)
          VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $con->prepare($query);
    $stmt->bind_param("iisssi", $tenant_id, $room_id, $issue, $description, $date_added, $status_id);

    if ($stmt->execute()) {
        $request_id = $stmt->insert_id;

        // Log the addition
        $logType = 6; // 6 represents "maintenance request added"
        $insertLogQuery = "INSERT INTO log_tbl_m (log_type_id, request_id, date_entry) VALUES (?, ?, ?)";
        $logStmt = $con->prepare($insertLogQuery);
        $logStmt->bind_param("iis", $logType, $request_id, $date_added);
        $logStmt->execute();

        echo "<script>alert('Maintenance request added successfully'); window.location.href = 'request.php';</script>";
    } else {
        echo "<script>alert('Failed to add maintenance request'); window.location.href = 'request.php';</script>";
        }
    }


/*************************
*******PAYMENT TENANT ADD MODAL******
**************************/

if (!isset($_SESSION['email'])) {
    die("Unauthorized access.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenant_id      = $_POST['tenant_id'] ?? null;
    $contact_number = $_POST['contact_number'] ?? null;
    $email_address  = $_POST['email_address'] ?? null;
    $purpose        = $_POST['purpose'] ?? null;
    $amount         = $_POST['amount'] ?? null;
    $payment_type   = $_POST['payment_type'] ?? null;
    $status_type    = $_POST['status_type'] ?? 0; // Default to 0 if not provided
    $date_entry     = date("Y-m-d H:i:s");

    // Treat payment_type 1 (Cash) and 3 (GCash QR) the same (payment_intent_id = NULL)
    $payment_intent_id = (in_array($payment_type, [1, 3])) ? null : ($_POST['payment_intent_id'] ?? null);

    // Validate required fields
    if (!$tenant_id || !$contact_number || !$email_address || !$purpose || !$amount || !$payment_type) {
        echo "<script>
                alert('Error: Missing required fields.');
                window.location.href = 'payments.php';
              </script>";
        exit();
    }

    // Insert payment data
    $query = "INSERT INTO `payment_tbl` 
              (tenant_id, contact_number, email_address, purpose_id, amount, payment_intent_id, payment_type_id, date_entry, status_id, date_paid)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NULL)"; // Set `date_paid` as NULL

    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param("sssdsissi", $tenant_id, $contact_number, $email_address, $purpose, $amount, $payment_intent_id, $payment_type, $date_entry, $status_type);
        
        if ($stmt->execute()) {
            $payment_id = $stmt->insert_id;

            // Log the payment action
            $logType = 6;
            $logQuery = "INSERT INTO log_tbl_pay (log_type_id, payment_id, date_entry) VALUES (?, ?, ?)";
            if ($logStmt = $con->prepare($logQuery)) {
                $logStmt->bind_param("iis", $logType, $payment_id, $date_entry);
                $logStmt->execute();
                $logStmt->close();
            }

            echo "<script>
                    alert('Payment added successfully.');
                    window.location.href = 'payments.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Error: Payment could not be processed.');
                    window.location.href = 'payments.php';
                  </script>";
        }

        $stmt->close();
    } else {
        echo "<script>
                alert('Error: Database query failed.');
                window.location.href = 'payments.php';
              </script>";
    }  
    }

?>