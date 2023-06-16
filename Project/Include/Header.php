<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TheBlog</title>
    <link rel="stylesheet" href="Styles/Style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>

<body>

<div class="nav-bar">
    <a href="">TheBlog</a>
    <div>
        <?php if (isset($_SESSION['user_infos'])): ?>
            <span>
                <a href="Logout.php">Logout <span class="material-symbols-outlined">logout</span></a>
            </span>
        <?php else: ?>
            <span>
                <a href="Register.php">Sign Up <span class="material-symbols-outlined">app_registration</span></a>
            </span>
            <span>
                <a href="Login.php">Login <span class="material-symbols-outlined">login</span></a>
            </span>
        <?php endif; ?>
    </div>
</div>

<?php if (Session::getInstance()->hasFlashes()) : ?>

    <?php foreach (Session::getInstance()->getFlashes() as $type => $message) : ?>

        <div class="<?php echo $type . '-msg' ?>">
            <?= $message ?>
        </div>

    <?php endforeach; ?>

<?php endif; ?>
