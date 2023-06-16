<?php

require_once 'Include/Load.php';

$session = Session::getInstance();
$link = App::getDatabase();
$auth = App::getAuth();

if (!empty($_POST) && !empty($_POST['email'])){

    if ($auth->verifyEmail($link, $_POST['email'])){
        $session->setFlash('success', 'The mail containing the Instructions for the Password Reset has been sent to you');
        App::redirect('Login.php');
    } else{
        $session->setflash('alert', 'Connection Information are invalid');
    }

}

?>
<?php require_once 'Include/Header.php' ?>
<div class="login-box">
    <h2>Forgotten Password</h2>
    <form action="" method="post">
        <div class="user-box">
            <input type="email" name="email" required="">
            <label>Email</label>
        </div>
        <button type="submit">Submit</button>
    </form>
</div>
</body>
</html>
