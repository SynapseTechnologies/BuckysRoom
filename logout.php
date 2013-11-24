<?php
require(dirname(__FILE__) . '/includes/bootstrap.php');

unset($_SESSION['userID']);

setcookie('bkuid0', null, time() - 1000, "/", "buckysroom.com");
setcookie('bkuid1', null, time() - 1000, "/", "buckysroom.com");
setcookie('bkuid2', null, time() - 1000, "/", "buckysroom.com");

buckys_session_destroy();

buckys_redirect('/index.php');