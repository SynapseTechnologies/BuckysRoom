<?php
require(dirname(__FILE__) . '/includes/bootstrap.php');

//Getting Current User ID
if( !($userID = buckys_is_logged_in()) )
{
    buckys_redirect('/index.php', MSG_NOT_LOGGED_IN_USER, MSG_TYPE_ERROR);
}

$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
if( !in_array($type, array('all', 'pending', 'requested')) )
    $type = 'all';

if( isset($_REQUEST['action']) )
{
    $return = isset($_REQUEST['return']) ? base64_decode($_REQUEST['return']) : ('/myfriends.php?type=' . $type);
    if( $_REQUEST['action'] == 'unfriend' )
    {
        if( BuckysFriend::unfriend($userID, $_REQUEST['friendID']) )
        {
            buckys_redirect( $return, MSG_FRIEND_REMOVED );
        }else{
            buckys_redirect( $return, $db->getLastError(), MSG_TYPE_ERROR );
        }
    }else if( $_REQUEST['action'] == 'decline' ){
        if( BuckysFriend::decline($userID, $_REQUEST['friendID']) )
        {
            buckys_redirect( $return, MSG_FRIEND_REQUEST_DECLINED );
        }else{
            buckys_redirect( $return, $db->getLastError(), MSG_TYPE_ERROR );
        }
    }else if( $_REQUEST['action'] == 'accept' ){
        if( BuckysFriend::accept($userID, $_REQUEST['friendID']) )
        {
                buckys_redirect( '/myfriends.php?type=requested', MSG_FRIEND_REQUEST_APPROVED );
        }else{
            buckys_redirect( '/myfriends.php?type=requested', $db->getLastError(), MSG_TYPE_ERROR );
        }
    } else if( $_REQUEST['action'] == 'delete' ){
        if( BuckysFriend::delete($userID, $_REQUEST['friendID']) )
        {
            buckys_redirect( $return, MSG_FRIEND_REQUEST_REMOVED);
        }else{
            buckys_redirect( $return, $db->getLastError(), MSG_TYPE_ERROR );
        }
    }else if( $_REQUEST['action'] == 'request' ){
        if( !isset($_REQUEST['friendID']) || !isset($_REQUEST['friendIDHash']) || !buckys_check_id_encrypted($_REQUEST['friendID'], $_REQUEST['friendIDHash']) )
        {
            buckys_redirect( $return, MSG_INVALID_REQUEST, MSG_TYPE_ERROR );
        }
        if(!BuckysUser::checkUserID($_REQUEST['friendID']))
        {
            buckys_redirect( $return, MSG_INVALID_REQUEST, MSG_TYPE_ERROR );
        }
        if(BuckysFriend::isFriend($userID, $_REQUEST['friendID']))
        {
            buckys_redirect( $return, MSG_INVALID_REQUEST, MSG_TYPE_ERROR );
        }
        if(BuckysFriend::isSentFriendRequest($userID, $_REQUEST['friendID']))
        {
            buckys_redirect( $return, MSG_FRIEND_REQUEST_ALREADY_SENT, MSG_TYPE_ERROR );
        }
        if(BuckysFriend::isSentFriendRequest($_REQUEST['friendID'], $userID))
        {
            buckys_redirect( $return, MSG_FRIEND_REQUEST_ALREADY_SENT, MSG_TYPE_ERROR );
        }        
        if( BuckysFriend::sendFriendRequest($userID, $_REQUEST['friendID']) )
        {
            buckys_redirect( $return, MSG_FRIEND_REQUEST_SENT);
        }else{
            buckys_redirect( $return, $db->getLastError(), MSG_TYPE_ERROR );
        }
    }
    exit;
}
    
//Getting UserData from Id
$userData = BuckysUser::getUserData($userID);

$page = isset($_GET['page']) ? $_GET['page'] : 1;

if( $type == 'all' ){
    $totalCount = BuckysFriend::getNumberOfFriends($userID);
}else if( $type == 'pending' ){
    $totalCount = BuckysFriend::getNumberOfPendingRequests($userID);
}else if( $type == 'requested' ){
    $totalCount = BuckysFriend::getNumberOfReceivedRequests($userID);
}

//Init Pagination Class
$pagination = new Pagination($totalCount, BuckysFriend::$COUNT_PER_PAGE, $page);
$page = $pagination->getCurrentPage();

if( $type == 'all' ){
    $friends = BuckysFriend::getAllFriends($userID, $page, BuckysFriend::$COUNT_PER_PAGE);       
}else if( $type == 'pending' ){
    $friends = BuckysFriend::getPendingRequests($userID, $page, BuckysFriend::$COUNT_PER_PAGE);
}else if( $type == 'requested' ){
    $friends = BuckysFriend::getReceivedRequests($userID, $page, BuckysFriend::$COUNT_PER_PAGE);
}


buckys_enqueue_stylesheet('account.css');
buckys_enqueue_stylesheet('friends.css');

buckys_enqueue_javascript('friends.js');

$BUCKYS_GLOBALS['content'] = 'myfriends';


$BUCKYS_GLOBALS['title'] = "My Friends - BuckysRoom";


//if logged user can see all resources of the current user

require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php");  
