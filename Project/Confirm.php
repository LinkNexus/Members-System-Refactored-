<?php

require_once 'Include/Load.php';

$session = Session::getInstance();
$link = App::getDatabase();


if (App::getAuth()->confirm($link, App::get('id'), App::get('token'))) {
    $session->setFlash('success', 'Your Account has been successfully confirmed');
    App::redirect('Account.php');
} else {
    $session->setFlash('alert', 'Token is not valid');
    App::redirect('Login.php');
}