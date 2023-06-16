<?php

use JetBrains\PhpStorm\NoReturn;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once dirname(__DIR__) . '/vendor/phpmailer/src/Exception.php';
require_once dirname(__DIR__) . '/vendor/phpmailer/src/PHPMailer.php';
require_once dirname(__DIR__) . '/vendor/phpmailer/src/SMTP.php';

class App
{

    public static ?Database $link = null;

    public function __construct()
    {}

    /* Static Method used to instantiate the class Database with the same parameters all over the code and
    to prevent the presence of 2 Instances of the Class */

    public static function getDatabase(): Database
    {
        if (!self::$link) {
            self::$link = new Database('mini-blog', 'root', '');
        }

        return self::$link;
    }

    /* Static Method used to get an Instance of the class Auth avoiding inserting the required
    parameters all along */

    public static function getAuth(): Auth
    {
        return new Auth(Session::getInstance());
    }

    /* Static Method used to get an Instance of the class Modification avoiding inserting the required
    parameters all along */

    public static function getModification (): Modification
    {
        return new Modification(self::getDatabase(), Session::getInstance());
    }

    /* Static Method used to check if values have been passed through the URL */

    public static function has($key): bool
    {
        return isset($_GET[$key]);
    }

    /* Static Method used to get the passed values in the URL */

    public static function get($key)
    {
        return $_GET[$key];
    }

    /* Since the password_hash will be used at different areas of the project,
    create a Method for it. Modifications can easily be set-up through this */

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);  /* The hash method could be modified at once */
    }

    /* Static Method used to redirect the user to a given page and exiting the code to prevent further
    execution of the code */

    #[NoReturn] public static function redirect($page): void
    {
        header('Location: '. $page);
        exit();
    }

    /* Static Method to check if the user is connected to the website */
    public static function isUserConnected(Session $session): bool
    {
        if ($session->getKey('user_infos')){
            return true;
        }

        return false;
    }

    /* Static Method to connect the user to the website */

    public static function connect(Session $session, $result): void
    {
        $session->addKey('user_infos', $result);
    }

    public static function sendWithGmail($to, $subject, $content): void
    {
        // passing true in constructor enables exceptions in PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER; // for detailed debug output
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $username = "nkenengnunlafrancklevy@gmail.com"; // gmail email
            $password = "vqitwblvefidajdw"; // app password

            $mail->Username = $username;
            $mail->Password = $password;

            // Sender and recipient settings
            $mail->setFrom($username, '');
            $mail->addAddress($to, '');

            // Setting the email content
            $mail->IsHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $content;

            $mail->send();
            echo "Email message sent.";
        } catch (Exception $e) {
            echo "Error in sending email. Mailer Error: {$mail->ErrorInfo}";
        }
    }

}