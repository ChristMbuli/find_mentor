<?php
$url = "";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (isset($_GET['url'])) {
    $url = explode('/', $_GET['url']);
}

if ($url === '') {
    require './index.php';

} elseif ($url[0] == 'home') {
    require './index.php';

} elseif ($url[0] === 'signin') {
    require './login.php';

} elseif ($url[0] === 'signup') {
    require './register.php';

} elseif ($url[0] === 'account' and $url[1] === 'setting') {
    require './step.php';

} elseif ($url[0] == 'mentors') {
    require './all_mentors.php';

} elseif ($url[0] == 'profil' and !empty($url[1])) {
    require './profil.php';

} else {
    require "./404.php";
}