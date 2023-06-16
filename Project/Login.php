<?php

require_once 'Include/Load.php';

$session = Session::getInstance();
$link = App::getDatabase();
$auth = new Auth($session);
$auth->reconnectFromCookie($link);

if (App::isUserConnected($session)){
    App::redirect('Account.php');
}
if (!empty($_POST) && !empty($_POST['username']) && !empty($_POST['password'])) {

    $result = $auth->login($link, $_POST['username'], $_POST['password'], isset($_POST['remember']));

    if ($result) {
        $session->setFlash('success', 'You are now connected');
        App::redirect('Account.php');
    } else {
        $session->setFlash('alert', 'Connection Information are invalid');
    }
}


?>

<?php require_once 'Include/Header.php' ?>
<div class="login-box">
    <h2>Login</h2>
    <form action="" method="post">
        <div class="user-box">
            <input type="text" name="username" required="">
            <label>Username or Email</label>
        </div>
        <div class="user-box">
            <input type="password" name="password" required="">
            <label>Password</label>
        </div>
        <div class="user-box">
            <label>Remember me</label>
            <input type="checkbox" name="remember" class="remember">
        </div>
        <button type="submit">Submit</button>
        <a href="ForgotPassword.php">Forgot Password?</a>
    </form>
</div>
</body>

</html>
