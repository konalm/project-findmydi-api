<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer; 

class MailService 
{
  /**
   * send email with PHPMailer Library
   */
  public function send_email($subject, $body, $to_email, $to_name) {
    $mail = new PHPMailer(true);

    $mail->isSendmail();
    $mail->setFrom('connor@codegood.co', 'Find My Driving Instructor');
    $mail->addAddress($to_email, $to_name);
    $mail->Subject = $subject;
    $mail->msgHTML($body);
    $mail->send();
  }
}