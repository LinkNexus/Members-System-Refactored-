<?php

require_once 'Include/Load.php';

$session = Session::getInstance();
$auth = App::getAuth();
$link = App::getDatabase();

if (App::has('id') && App::has('token')){

    $user_id = App::get('id');
    $token = App::get('token');

    $result = $link->query('SELECT * FROM users WHERE id = :id AND reset_token IS NOT NULL AND reset_token = :reset_token AND reset_at > DATE_SUB(NOW(), INTERVAL 30 MINUTE)', [
        'id' => $user_id,
        'reset_token' => $token
    ])->fetch();

    if ($result) {
        if (!empty($_POST)) {
            $modification = App::getModification();
            $outcome = $modification->resetPassword($result, $_POST['new_password']);

            if (is_array($outcome)) {
                $session->setFlash('alert', implode(', ', $outcome));
            } else {
                $session->setFlash('success', 'Your Password has been successfully modified');
                App::redirect('Account.php');
            }
        }
    } else {
        $session->setFlash('alert', 'This Token is not valid');
        App::redirect('Login.php');

    }
} else {
    App::redirect('Login.php');
}

?>

<?php require_once 'Include/Header.php' ?>
<div class="login-box">
    <h2>Reset Password</h2>
    <form action="" method="post">
        <div class="user-box">
            <input type="password" name="new_password" required="">
            <label>New Password</label>
        </div>
        <div class="user-box">
            <input type="password" name="confirm_password" required="">
            <label>Confirm Password</label>
        </div>
        <button type="submit">Submit</button>
    </form>
</div>
</body>
</html>
