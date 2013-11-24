<?php
require(dirname(dirname(__FILE__)) . '/includes/bootstrap.php');

if( !($userID = buckys_is_logged_in()) )
{
    buckys_redirect('/index.php', MSG_NOT_LOGGED_IN_USER, MSG_TYPE_ERROR);
}


buckys_enqueue_stylesheet('trade.css');
buckys_enqueue_javascript('trade.js');


$BUCKYS_GLOBALS['content'] = 'trade/shipping_info';
$BUCKYS_GLOBALS['headerType'] = 'trade';


$view = array();



//Save Shipping info
$tradeUserIns = new BuckysTradeUser();
$countryIns = new BuckysCountry();
$view['update_message'] = '';
if ($_POST['action'] == 'saveShippingInfo') {
    $paramData = array(
                'shippingAddress' => $_POST['shippingAddress'],
                'shippingCity' => $_POST['shippingCity'],
                'shippingState' => $_POST['shippingState'],
                'shippingZip' => $_POST['shippingZip'],
                'shippingCountryID' => $_POST['shippingCountryID']
            );
    $retVal = $tradeUserIns->updateShippingInfo($userID, $paramData);
    
    if ($retVal == false)
        $view['update_message'] = 'Something goes wrong';
    else
        $view['update_message'] = 'Your shipping info has been updated successfully.';
}


//Get offer_received info

$view['trade_user_info'] = $tradeUserIns->getUserByID($userID);
$view['country_list'] = $countryIns->getCountryList();

if (empty($view['trade_user_info'])) {
    buckys_redirect('/trade/index.php', MSG_INVALID_REQUEST, MSG_TYPE_ERROR);
}

$BUCKYS_GLOBALS['title'] = 'Shipping Info - BuckysRoomTrade';

require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php"); 
