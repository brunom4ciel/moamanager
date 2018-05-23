<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\libraries\core\email;

defined('_EXEC') or die();

use moam\core\AppException;
use moam\core\Framework;
use moam\libraries\phpmailer\PHPMailer;

// error_reporting(E_ALL | E_STRICT);
// ini_set('display_errors', 1);
try {

    Framework::import("PHPMailerAutoload", "phpmailer");
} catch (AppException $e) {

    // exit($e->getMessage());
    exit("Error phpmailer: " . $e->getMessage());
}

// if(file_exists("../../PHPMailer-master/PHPMailerAutoload.php"))
// require_once("../../PHPMailer-master/PHPMailerAutoload.php");
// else
// exit("File Not Found: ../../PHPMailer-master/PHPMailerAutoload.php");
//
class UsageReportMail
{

    var $to = "";

    var $body = "";

    public function sendMail($to, $body, $subject = "")
    {

        // Create a new PHPMailer instance
        $mail = new PHPMailer();

        // Tell PHPMailer to use SMTP
        $mail->isSMTP();

        // Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $mail->SMTPDebug = 0;

        // Ask for HTML-friendly debug output
        $mail->Debugoutput = 'html';

        // Set the hostname of the mail server
        $mail->Host = PHPMAILER_HOST;

        // Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $mail->Port = PHPMAILER_PORT;

        // Set the encryption system to use - ssl (deprecated) or tls
        $mail->SMTPSecure = PHPMAILER_SMTPSECURE;

        // Whether to use SMTP authentication
        $mail->SMTPAuth = PHPMAILER_SMTPAUTH;

        // Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = PHPMAILER_USERNAME;

        // Password to use for SMTP authentication
        $mail->Password = PHPMAILER_PASSWORD;

        // Set who the message is to be sent from
        $mail->setFrom(PHPMAILER_USERNAME, PHPMAILER_FROMNAME);

        // Set an alternative reply-to address
        // $mail->addReplyTo('replyto@example.com', 'First Last');

        // Set who the message is to be sent to
        $mail->addAddress($to, $to);

        // Set the subject line
        $mail->Subject = $subject;

        // Read an HTML message body from an external file, convert referenced images to embedded,
        // convert HTML into a basic plain-text alternative body
        $mail->msgHTML($body); // file_get_contents('contents.html'), dirname(__FILE__));

        // Replace the plain text body with one created manually
        $mail->AltBody = 'This is a plain-text message body';

        // Attach an image file
        // $mail->addAttachment('images/phpmailer_mini.png');

        return $mail->send();

        /*
         * //send the message, check for errors
         * if (!$mail->send()) {
         * // echo "Mailer Error: " . $mail->ErrorInfo;
         * } else {
         * // echo "Message sent!";
         * }
         */
    }
}

?>