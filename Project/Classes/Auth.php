<?php

class Auth
{

    /* If one or more Message could be used all over the project,
    create an array for the said Messages */

    protected array $options = [
        'restriction_msg' => 'You are not yet authorized to access this page'
    ];

    public function __construct(protected Session $session, array $options = [])
    {
        /* If you have other generic Messages,
        you could replace the one by default with it */

        $this->options = array_merge($this->options, $options);
    }

    /* In order to permit access of the user to specific pages,
    create a Method having the Instructions for the Restriction */

    public function restrict(): void
    {
        if (!$this->session->getKey('user_infos')) {
            $this->session->setFlash('alert', $this->options['restriction_msg']);
            App::redirect('Login.php');
        }
    }

    /* Method grouping all the instructions for the sign-up of a new User */

    public function register(Database $link, string $username, string $password, string $email): void
    {
        $password = App::hashPassword($password);
        $token = Str::random(60);

        $link->query('INSERT INTO users(username, email, password, confirmation_token) VALUES (:username, :email, :password, :confirmation_token)',
            [
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'confirmation_token' => $token
            ]
        );

        $directory = 'Project'; /* The name of the directory where the entire project is */

        $full_URL = "http". (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 's' : '') .'://'. $_SERVER['HTTP_HOST'] . '/'. (empty($directory) ? '' : $directory .'/') .'Confirm.php';


        try
        {
            $user_id = $link->lastInsertId();
            App::sendWithGmail($email, 'Account Confirmation', "In order to confirm your Account, click on this link\n\n$full_URL?id=$user_id&token=$token");
        } catch(Exception $e)
        {
            echo $e->getMessage();
        }
    }

    /* Method grouping all the instructions for the confirmation of the sign-up of a new User */
    public function confirm(Database $link, int $user_id, string $token): bool
    {
        $result = $link->query('SELECT * FROM users WHERE id = :id', ['id' => $user_id])->fetch();

        if ($result && $result->confirmation_token === $token) {
            $link->query('UPDATE users SET confirmation_token = NULL, confirmed_at = NOW() WHERE id = :id', ['id' => $user_id]);
            $this->session->addKey('user_infos', $result);
            return true;
        }

        return false;
    }

    /* Method to check if there is a Cookie containing the connection information
    of the user and if yes, it connects the user */

    public function reconnectFromCookie(Database $link): void
    {
        if (isset($_COOKIE['remember']) && !App::isUserConnected($this->session)) {
            $remember_token = $_COOKIE['remember'];
            $parts = explode('==', $remember_token);

            $user_id = $parts[0];
            $result = $link->query('SELECT * FROM users WHERE id = :id', ['id' => $user_id])->fetch();

            if ($result) {
                $expected = $user_id . '==' . $result->remember_token . sha1($user_id . 'TheBlog');

                if ($expected == $remember_token) {
                    App::connect($this->session, $result);
                    setcookie('remember', $remember_token, time() + 60 * 60 * 24 * 7);
                } else {
                    setcookie('remember', NULL, -1);
                }
            } else {
                setcookie('remember', NULL, -1);
            }
        }
    }

    /* Method to create Cookie stocking the Information Connection of the user */

    public function remember($result, $link): void
    {
        $remember_token = Str::random(250);

        $link->query('UPDATE users SET remember_token = :remember_token WHERE id = :id', [
            'remember_token' => $remember_token,
            'id' => $result->id
        ]);

        setcookie('remember', $result->id . '==' . $remember_token . sha1($result->id . 'TheBlog'), time() + 60 * 60 * 24 * 7);
    }


    /* Method grouping all the instructions for the sign-in of a user to the website */

    public function login(Database $link, string $username, string $password, $remember = false)
    {
        $result = $link->query('SELECT * FROM users WHERE (username = :username OR email = :username) AND confirmed_at IS NOT NULL', ['username' => $username])->fetch();

        if ($result && password_verify($password, $result->password)) {
            App::connect($this->session, $result);

            if ($remember) {
                $this->remember($result, $link);
            }

            return $result;
        }

        return false;

    }

    /* Method used to log out the user */

    public function logout(): void
    {
        setcookie('remember', NULL, -1);
        $this->session->delete('user_infos');
    }

    /* Method used to check the Email of a user during for a Password Reset */

    public function verifyEmail(Database $link, $email)
    {
        $result = $link->query('SELECT * FROM users WHERE email = :email AND confirmed_at IS NOT NULL', ['email' => $email])->fetch();

        if ($result) {
            $reset_token = Str::random(60);

            $link->query('UPDATE users SET reset_token = :reset_token, reset_at = NOW() WHERE id = :id', [
                'reset_token' => $reset_token,
                'id' => $result->id
            ]);

            $directory = 'Mini_Blog'; /* The name of the directory where the entire project is */

            $full_URL = "http". (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 's' : '') .'://'. $_SERVER['HTTP_HOST'] . '/'. (empty($directory) ? '' : $directory .'/') .'ResetPassword.php';


            try {
                App::sendWithGmail($email, 'Reset of your Password', "In order to reset your Password, click on this link\n\n$full_URL?id=$result->id&token=$reset_token");
            } catch (Exception $e)
            {
                echo $e->getMessage();
            }

            return $result;
        }

        return false;
    }

}
