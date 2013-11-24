<?php
/**
* Show All Top Images, Videos or Text
*/
require(dirname(__FILE__) . '/includes/bootstrap.php');

$userID = buckys_is_logged_in();

$type = isset($_GET['type']) ? strtolower($_GET['type']) : '';

//If the url param is not correct, go to index page
if( !$type || !in_array($type, array('image', 'text', 'video')) )
{
    buckys_redirect('/index.php');
}

//Perios = Today, This Week, This Month, All Time
$period = isset($_GET['period']) ? strtolower($_GET['period']) : 'all'; //Default all
if( !in_array($period, array('today', 'this-week', 'this-month', 'all')) )
{
    $period = 'all';
}

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$totalCount = BuckysPost::getNumberOfPosts(BuckysPost::INDEPENDENT_POST_PAGE_ID, $period, $type);

//Init Pagination Class
$pagination = new Pagination($totalCount, BuckysPost::${COUNT_PER_PAGE . strtoupper("_$type")}, $page);
$page = $pagination->getCurrentPage();

//Getting Results
$results = BuckysPost::getTopPosts(BuckysPost::INDEPENDENT_POST_PAGE_ID, $period, $type, $page);

buckys_enqueue_stylesheet('index.css');

$BUCKYS_GLOBALS['content'] = "tops";

$typeString = array('image'=>'Images ', 'video' => 'Videos ', 'text' => 'Posts ');
$periodString = array('today'=>'Today ', 'this-month' => 'This Month ', 'all' => '');

//Page title
$BUCKYS_GLOBALS['title'] = "Most Popular " . $typeString[$type] . $periodString[$period] . '- BuckysRoom';

require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php");  


