<?php

require_once 'Include/Load.php';

$link = App::getDatabase();
$session = Session::getInstance();
App::getAuth()->restrict();

?>

<?php require_once 'Include/Header.php'; ?>
<div class="info-box">
    <h2>Account</h2>
    <div class="info">
        Username: <?php echo $session->getKey('user_infos')->username; ?> <a href="ChangeUsername.php"><span class="material-symbols-outlined">Edit</span></a>
    </div>
    <div class="info">
        Email: <?php echo $session->getKey('user_infos')->email; ?>
    </div>
    <div class="info">
        Account's Date of Creation: <?php echo $session->getKey('user_infos')->confirmed_at; ?>
    </div>
    <a href="ChangePassword.php">Change Password</a>
</div>
</body>
</html>
