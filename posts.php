<?php
require(dirname(__FILE__) . '/includes/bootstrap.php');

//Getting Current User ID
$userID = buckys_is_logged_in();

//Getting User ID from Parameter
$profileID = isset($_GET['user']) ? $_GET['user'] : 0;

//If the parameter is null, goto homepage 
if(!$profileID)
    buckys_redirect('/index.php');
    
//Getting UserData from Id
$userData = BuckysUser::getUserData($profileID);

//Goto Homepage if the userID is not correct
if( !buckys_not_null($userData) || !BuckysUser::checkUserID($profileID, true) )
{
    buckys_redirect('/index.php');
}

//if logged user can see all resources of the current user
$canViewPrivate = $userID == $profileID || BuckysFriend::isFriend($userID, $profileID) || BuckysFriend::isSentFriendRequest($profileID, $userID);

$posts = BuckysPost::getPostsByUserID($profileID, $userID, BuckysPost::INDEPENDENT_POST_PAGE_ID, $canViewPrivate, isset($_GET['post']) ? $_GET['post'] : null);

/*if( !buckys_not_null($posts) )
{
    //Goto Index Page
    buckys_redirect('/index.php', MSG_INVALID_REQUEST, MSG_TYPE_ERROR);
}*/

buckys_enqueue_stylesheet('profile.css');
buckys_enqueue_stylesheet('posting.css');

buckys_enqueue_javascript('posts.js');

$BUCKYS_GLOBALS['content'] = 'posts';

if ($userData) {
    $BUCKYS_GLOBALS['title'] = trim($userData['firstName'] . ' ' . $userData['lastName']) . "'s Posts - BuckysRoom";
}

require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php");  
