<?php
require(dirname(dirname(__FILE__)) . '/includes/bootstrap.php');

if( !($userID = buckys_is_logged_in()) )
{
    buckys_redirect('/index.php', MSG_NOT_LOGGED_IN_USER, MSG_TYPE_ERROR);
}


buckys_enqueue_stylesheet('trade.css');


buckys_enqueue_javascript('trade.js');


$BUCKYS_GLOBALS['content'] = 'trade/traded';
$BUCKYS_GLOBALS['headerType'] = 'trade';


$paramCurrentPage = get_secure_integer($_REQUEST['page']);
$paramType = get_secure_string($_REQUEST['type']);

$view = array();

$baseURL = '/trade/traded.php';
if ($paramType == 'history') {
    $baseURL .= '?type=' . $paramType;
}
else {
    $paramType = 'completed';
}

//Get offer_received info
$tradeIns = new BuckysTrade();
$countryIns = new BuckysCountry();
$view['trades'] = $tradeIns->getTradesByUserID($userID, $paramType);
$view['trades'] = buckys_trade_pagination($view['trades'], $baseURL, $paramCurrentPage, TRADE_ROWS_PER_PAGE);

$view['myID'] = $userID;


switch($paramType) {
    case 'history':
        $view['pagetitle'] = 'Trade History';
        
        break;
    case 'completed':
    default:
        $view['pagetitle'] = 'Completed Trades';
        
        //Mark the activity (offer received) as read
        $tradeNotificationIns = new BuckysTradeNotification();
        $tradeNotificationIns->markAsRead($userID, BuckysTradeNotification::ACTION_TYPE_OFFER_ACCEPTED);
        
        break;
}

$BUCKYS_GLOBALS['title'] = $view['pagetitle'] . ' - BuckysRoomTrade';

require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php"); 
