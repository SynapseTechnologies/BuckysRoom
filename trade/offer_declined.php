<?php
require(dirname(dirname(__FILE__)) . '/includes/bootstrap.php');

if( !($userID = buckys_is_logged_in()) )
{
    buckys_redirect('/index.php', MSG_NOT_LOGGED_IN_USER, MSG_TYPE_ERROR);
}


buckys_enqueue_stylesheet('trade.css');


buckys_enqueue_javascript('trade.js');


$BUCKYS_GLOBALS['content'] = 'trade/offer_declined';
$BUCKYS_GLOBALS['headerType'] = 'trade';


$paramCurrentPage = get_secure_integer($_REQUEST['page']);
$paramType = get_secure_string($_REQUEST['type']); // default 'bythem' or empty, another possible value is 'byme'

$view = array();

//Get offer_received info
$tradeOfferIns = new BuckysTradeOffer();

$baseURL = '/trade/offer_declined.php';

if ($paramType == 'byme') {
    $view['offers'] = $tradeOfferIns->getOfferDeclined($userID, false);
    $baseURL .= "?type=byme";
}
else {
    $paramType = '';
    $view['offers'] = $tradeOfferIns->getOfferDeclined($userID, true);
}
    

$view['offers'] = buckys_trade_pagination($view['offers'], $baseURL, $paramCurrentPage, TRADE_ROWS_PER_PAGE);

$view['type'] = $paramType;

$BUCKYS_GLOBALS['title'] = 'Offer Declined - BuckysRoomTrade';


//Mark the activity (offer received) as read
$tradeNotificationIns = new BuckysTradeNotification();
$tradeNotificationIns->markAsRead($userID, BuckysTradeNotification::ACTION_TYPE_OFFER_DECLINED);


require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php"); 
