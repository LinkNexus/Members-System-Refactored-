<?php

class Modification
{

    protected Validator $validator;

    public function __construct(protected Database $link, protected Session $session)
    {
        $this->validator = new Validator($_POST);
    }

    public function changeUsername($result, $user_id, $username): bool|array
    {
        if ($result) {

            $this->validator->isAlphanumeric('username', $username);

            if ($this->validator->isValid()) {

                $this->link->query('UPDATE users SET username = :username, modified_at = NOW() WHERE id = :id', [
                    'username' => $_POST['username'],
                    'id' => $user_id
                ]);

                $result = $this->link->query('SELECT * FROM users WHERE id = :id', ['id' => $user_id])->fetch();

                App::connect($this->session, $result);

                return true;
            }

            return $this->validator->getErrors();
        }

        return false;
    }

    public function changePassword($password): bool|array
    {
        if (password_verify($password, $this->session->getKey('user_infos')->password)) {

            $this->validator->isConfirmed(['new_password', 'confirm_password'], ['Password must not be empty and must contain at least 5 characters', 'Passwords do not match']);

            if ($this->validator->isValid()) {

                $user_id = $this->session->getKey('user_infos')->id;
                $password = App::hashPassword($_POST['new_password']);

                $this->link->query('UPDATE users SET password = :password WHERE id = :id', [
                    'password' => $password,
                    'id' => $user_id
                ]);

                return true;

            }

            return $this->validator->getErrors();

        }

        return false;
    }

    public function resetPassword($result, $password): bool|array
    {
        $this->validator->isConfirmed(['new_password', 'confirm_password'], ['Password must not be empty and must contain at least 5 characters', 'Passwords do not match']);

        if ($this->validator->isValid()) {
            $password = App::hashPassword($password);

            $this->link->query('UPDATE users SET password = :password, reset_at = NULL, reset_token = NULL WHERE id = :id', [
                'password' => $password,
                'id' => $result->id
            ]);

            App::connect($this->session, $result);

            return true;
        }

        return $this->validator->getErrors();
    }
}