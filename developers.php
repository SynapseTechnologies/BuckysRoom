<?php

require(dirname(__FILE__) . '/includes/bootstrap.php');

$userID = buckys_is_logged_in();

buckys_enqueue_stylesheet('index.css');

$BUCKYS_GLOBALS['content'] = "developers";

$BUCKYS_GLOBALS['title'] = "Developers - BuckysRoom";

require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php"); 
