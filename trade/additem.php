<?php
require(dirname(dirname(__FILE__)) . '/includes/bootstrap.php');

if( !($userID = buckys_is_logged_in()) )
{
    buckys_redirect('/index.php', MSG_NOT_LOGGED_IN_USER, MSG_TYPE_ERROR);
}


buckys_enqueue_stylesheet('uploadify.css');
buckys_enqueue_stylesheet('jquery.Jcrop.css');
buckys_enqueue_stylesheet('trade.css');


buckys_enqueue_javascript('uploadify/jquery.uploadify.js');
buckys_enqueue_javascript('jquery.Jcrop.js');
buckys_enqueue_javascript('jquery.color.js');
buckys_enqueue_javascript('trade.js');
buckys_enqueue_javascript('trade-edit.js');


$BUCKYS_GLOBALS['content'] = 'trade/additem';
$BUCKYS_GLOBALS['headerType'] = 'trade';



$view = array();

$tradeCatIns = new BuckysTradeCategory();
$countryIns = new BuckysCountry();
$tradeUserIns = new BuckysTradeUser();


$view['no_credits'] = false;

if (!$tradeUserIns->hasCredits($userID)) {
    $view['no_credits'] = true;
}


$view['category_list'] = $tradeCatIns->getCategoryList(0);
$view['country_list'] = $countryIns->getCountryList();
$view['action_name'] = 'addTradeItem';
$view['page_title'] = 'Add an Item';
$view['type'] = 'additem';


$BUCKYS_GLOBALS['title'] = 'Add an Item - BuckysRoomTrade';

require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php"); 
