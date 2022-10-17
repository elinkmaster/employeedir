<?php

require $_SERVER['DOCUMENT_ROOT'].'/vendor/PHPMailer/src/Exception.php';
require $_SERVER['DOCUMENT_ROOT'].'/vendor/PHPMailer/src/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'].'/vendor/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class ResetController extends Controller
{
    /**
     * Handle the page request.
     *
     * @param array $request the page parameters from a form post or query string
     */
    protected function handleRequest(&$request)
    {
        $loginFields = $request;
        $obj = Login::verifyEmail($loginFields['acc'],$this->pdo);
        if(count($obj) > 0){
            $temp_pass = uniqid();
            $crypt = Login::cryptPassword($temp_pass);
            Login::updatePassword($obj[0]['login_id'],$crypt,$this->pdo);
            $this->resetPassEmail(['email' => $loginFields['acc'], 'temp_pass' => $temp_pass, 'first_name' => $obj[0]['first_name']]);
            $message = "Please check your email for the password reset information.";
        }else{
            $temp_pass = "";
            $crypt = "";
            $message = "The email you provided is invalid or not found in the system.";
        }
        $this->assign('loginFields', ['temp' => $temp_pass, 'crypt' => $crypt, 'obj' => $obj, 'message' => $message]);
    }

    protected function resetPassEmail($form) {
        $mail = new PHPMailer();
        $mail->SMTPDebug = false;  
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'sysdev.elink@gmail.com';                     // SMTP username
        $mail->Password   = 'dev116@!';                               // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  
        $mail->Port       = 587;  
        $mail->setFrom('noreply@eLink.com', 'eLink');
        $mail->addAddress($form['email'], $form['first_name']);
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Reset Password - eLink Forms';
        $mail->Body    = "Good day ".$form['first_name'].",
                        <br><br>We recieved your request to reset your password. You need to login to form builder using the temporary password we provided.
                        <br><br>Temporary Password: ".$form['temp_pass']."
                        <br><br>From the form builder, on the user tab, click on you name and change the password. If you have any concerns, feel free to reach us anytime. Thank you.
                        <br><br><a href=\"http://".$_SERVER['HTTP_HOST']."/login.php\">Login To Change Your Password</a>";
    
        $mail->send();
    }
}
