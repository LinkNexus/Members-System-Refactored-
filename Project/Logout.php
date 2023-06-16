<?php

require_once 'Include/Load.php';

App::getAuth()->logout();
Session::getInstance()->setFlash('success', 'You have been successfully disconnected');
App::redirect('Login.php');
