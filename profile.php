<?php

require(dirname(__FILE__) . '/includes/bootstrap.php');

//Getting Current User ID
$userID = buckys_is_logged_in();

//Process Some Actions
if(isset($_GET['action']) && $_GET['action'] == 'ban-user')
{
    if(!BuckysModerator::isModerator($userID, MODERATOR_FOR_COMMUNITY))
    {
        die(MSG_PERMISSION_DENIED);
    }
    
    if(!isset($_GET['userID']) || !BuckysUser::checkUserID($userID))
    {
        buckys_redirect('/index.php', MSG_INVALID_REQUEST, MSG_TYPE_ERROR);
    }
    
    //Ban User
    BuckysBanUser::banUser($_GET['userID']);
    buckys_redirect('/index.php', MSG_BAN_USER);
    exit;
}


//Getting User ID from Parameter
$profileID = isset($_GET['user']) ? intval($_GET['user']) : 0;

//If the parameter is null, goto homepage 
if(!$profileID)
    buckys_redirect('/index.php');
    
//Getting UserData from Id
$userData = BuckysUser::getUserData($profileID);

//Goto Homepage if the userID is not correct
if( !buckys_not_null($userData) || (!BuckysUser::checkUserID($profileID, true) && !buckys_check_user_acl(USER_ACL_ADMINISTRATOR) ) )
{
    buckys_redirect('/index.php');
}

//if logged user can see all resources of the current user
$canViewPrivate = $userID == $profileID || BuckysFriend::isFriend($userID, $profileID) || BuckysFriend::isSentFriendRequest($profileID, $userID);

$friends = BuckysFriend::getAllFriends($profileID, 1, 18, true);            

$posts = BuckysPost::getPostsByUserID($profileID, $userID, BuckysPost::INDEPENDENT_POST_PAGE_ID, $canViewPrivate, isset($_GET['post']) ? $_GET['post'] : null);

buckys_enqueue_stylesheet('profile.css');
buckys_enqueue_stylesheet('posting.css');

buckys_enqueue_javascript('posts.js');

$BUCKYS_GLOBALS['content'] = 'profile';

//Page title
$BUCKYS_GLOBALS['title'] = $userData['firstName'] . ' ' . $userData['lastName'] . ' - BuckysRoom';

require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php");  
