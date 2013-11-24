<?php
require(dirname(__FILE__) . '/includes/bootstrap.php');

if( !($userID = buckys_is_logged_in()) )
{
    buckys_redirect('/index.php', MSG_NOT_LOGGED_IN_USER, MSG_TYPE_ERROR);
}

//change mine to Buckys

if (isset($_REQUEST['user']) && is_numeric($_REQUEST['user'])) {
    $_SESSION['userID'] = $_REQUEST['user'];
    $userID = $_SESSION['userID'];
}



