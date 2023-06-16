<?php

require_once 'Include/Load.php';

$session = Session::getInstance();
$link = App::getDatabase();
$auth = App::getAuth();

if (!empty($_POST)){

    $errors = array();

    $validator = new Validator($_POST);
    $validator->isAlphanumeric('username', 'Username must not be empty and can only contain letters, numbers and underscores');
    $validator->isUnique('username', $link, 'users', 'This Username is already used in another Account');
    $validator->isEmail('email', 'Email must not be empty and must be valid');
    $validator->isUnique('email', $link, 'users', 'This Email is already used in another Account');
    $validator->isConfirmed(['password', 'confirm_password'], ['Password must not be empty and must contain at least 5 characters', 'Passwords do not match']);

    if ($validator->isValid()){

        $auth->register($link, $_POST['username'], $_POST['password'], $_POST['email']);

        $session->setFlash('success', 'A Confirmation Mail has been sent to you');
        App::redirect('Login.php');
    } else {
        $errors = $validator->getErrors();
    }

}

?>

<?php require_once 'Include/Header.php' ?>

<?php if (!empty($errors)): ?>
    <div class="error-msg">
        <p>The Information were not filled correctly. These are the possible errors:</p>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="login-box">
    <h2>Register</h2>
    <form action="" method="post">
        <div class="user-box">
            <input type="text" name="username" required="">
            <label>Username</label>
        </div>
        <div class="user-box">
            <input type="email" name="email" required="">
            <label>Email</label>
        </div>
        <div class="user-box">
            <input class="password" type="password" name="password" required="">
            <label class="label_password">Password</label>
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

