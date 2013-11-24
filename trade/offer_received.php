<?php
require(dirname(dirname(__FILE__)) . '/includes/bootstrap.php');

if( !($userID = buckys_is_logged_in()) )
{
    buckys_redirect('/index.php', MSG_NOT_LOGGED_IN_USER, MSG_TYPE_ERROR);
}


buckys_enqueue_stylesheet('trade.css');


buckys_enqueue_javascript('trade.js');


$BUCKYS_GLOBALS['content'] = 'trade/offer_received';
$BUCKYS_GLOBALS['headerType'] = 'trade';


$paramCurrentPage = get_secure_integer($_REQUEST['page']);
$paramTargetID = get_secure_integer($_REQUEST['targetID']);

$view = array();

//Get offer_received info
$tradeOfferIns = new BuckysTradeOffer();
$view['offers'] = $tradeOfferIns->getOfferReceived($userID, $paramTargetID);
$view['offers'] = buckys_trade_pagination($view['offers'], '/trade/offer_received.php', $paramCurrentPage, TRADE_ROWS_PER_PAGE);

$BUCKYS_GLOBALS['title'] = 'Offer Received - BuckysRoomTrade';


//Mark the activity (offer received) as read
$tradeNotificationIns = new BuckysTradeNotification();
$tradeNotificationIns->markAsRead($userID, BuckysTradeNotification::ACTION_TYPE_OFFER_RECEIVED);
$tradeOfferIns->markAsRead($userID, BuckysTradeOffer::STATUS_OFFER_ACTIVE);

require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php"); 
