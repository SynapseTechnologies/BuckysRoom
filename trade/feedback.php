<?php
require(dirname(dirname(__FILE__)) . '/includes/bootstrap.php');

buckys_enqueue_stylesheet('trade.css');


buckys_enqueue_javascript('trade.js');


$BUCKYS_GLOBALS['content'] = 'trade/feedback';
$BUCKYS_GLOBALS['headerType'] = 'trade';


$paramCurrentPage = get_secure_integer($_REQUEST['page']);
$paramType = get_secure_string($_REQUEST['type']);
$userID = get_secure_integer($_REQUEST['user']);



$userIns = new BuckysUser();


if ($userID == '') {
    if( !($userID = buckys_is_logged_in()) )
    {
        buckys_redirect('/index.php', MSG_NOT_LOGGED_IN_USER, MSG_TYPE_ERROR);
    }
}
else if (!is_numeric($userID)) {
    buckys_redirect('/trade/index.php', MSG_NOT_LOGGED_IN_USER, MSG_TYPE_ERROR);
}
else {
    $userData = $userIns->getUserData($userID);
    if ($userData['status'] != BuckysUser::STATUS_USER_ACTIVE) {
        buckys_redirect('/trade/index.php', MSG_INVALID_REQUEST, MSG_TYPE_ERROR);
    }
}

//Calc base URL
$baseURLParts = array();
if ($paramType == 'given') {
    $baseURLParts [] = "type=" . $paramType;
}
else {
    $paramType = 'received';
}

if ($userID != buckys_is_logged_in()) {
    $baseURLParts [] = "user=" . $userID;
}

$baseURL = '/trade/feedback.php';
if (count($baseURLParts) > 0)
    $baseURL .= '?' . implode('&', $baseURLParts);

$view = array();
$tradeFeedbackIns = new BuckysTradeFeedback();
$tradeUserIns = new BuckysTradeUser();

$view['feedback'] = $tradeFeedbackIns->getFeedbackByUserID($userID, $paramType);

    
$view['feedback'] = buckys_trade_pagination($view['feedback'], $baseURL, $paramCurrentPage, TRADE_ROWS_PER_PAGE);
$view['myID'] = $userID;
$view['type'] = $paramType;
$view['myRatingInfo'] = $tradeUserIns->getUserByID($userID);


$userData = $userIns->getUserBasicInfo($userID);
if ($userData) {
    if ($paramType == 'given') {
        $BUCKYS_GLOBALS['title'] = trim($userData['firstName'] . ' ' . $userData['lastName']) . "'s Feedback Given - BuckysRoomTrade";
    }
    else {
        $BUCKYS_GLOBALS['title'] = trim($userData['firstName'] . ' ' . $userData['lastName']) . "'s Feedback Received- BuckysRoomTrade";
        
        //Mark the activity (offer received) as read
        $tradeNotificationIns = new BuckysTradeNotification();
        $tradeNotificationIns->markAsRead($userID, BuckysTradeNotification::ACTION_TYPE_FEEDBACK);
        
    }
    
}
else {
    $BUCKYS_GLOBALS['title'] = 'Feedback - BuckysRoomTrade';
}


require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php"); 
