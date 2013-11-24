<?php
require(dirname(dirname(__FILE__)) . '/includes/bootstrap.php');

buckys_enqueue_stylesheet('trade.css');


buckys_enqueue_javascript('trade.js');


$BUCKYS_GLOBALS['content'] = 'trade/search';
$BUCKYS_GLOBALS['headerType'] = 'trade';


$paramCurrentPage = get_secure_integer($_REQUEST['page']);
$paramQueryStr = get_secure_string($_REQUEST['q'], true);
$paramCategory = get_secure_string($_REQUEST['cat'], true);
$paramLocation = get_secure_string($_REQUEST['loc'], true);
$paramSort = get_secure_string($_REQUEST['sort']);
$paramUserID = get_secure_integer($_REQUEST['user']);

$view = array();

//Get available items
$tradeItemIns = new BuckysTradeItem();
$countryIns = new BuckysCountry();


$tradeCatIns = new BuckysTradeCategory();

$itemResultList = $tradeItemIns->search($paramQueryStr, $paramCategory, $paramLocation, $paramUserID);
$itemResultList = $tradeItemIns->sortItems($itemResultList, $paramSort);

$view['categoryList'] = $tradeItemIns->countItemInCategory($itemResultList);


//Create Base URL for pagination of search page
$paginationUrlBase = buckys_trade_search_url($paramQueryStr, $paramCategory, $paramLocation, $paramSort, $paramUserID);


    
    
//Display

$view['items'] = buckys_trade_pagination($itemResultList, $paginationUrlBase, $paramCurrentPage, TRADE_ROWS_PER_PAGE);

$view['param']['q'] = $paramQueryStr;
$view['param']['cat'] = $paramCategory;
$view['param']['loc'] = $paramLocation;
$view['param']['sort'] = $paramSort;
$view['param']['user'] = $paramUserID;

$BUCKYS_GLOBALS['tradeSearchParam'] = $view['param'];

$view['countryList'] = $countryIns->getCountryList();

if ($paramQueryStr != '') {
    $BUCKYS_GLOBALS['title'] = $paramQueryStr . ' - BuckysRoomTrade Search';
}
else if ($paramCategory != '') {
    $BUCKYS_GLOBALS['title'] = $paramCategory . ' - BuckysRoomTrade Search';
}
else if ($paramUserID != '' && is_numeric($paramUserID)) {
    
    $userIns = new BuckysUser();
    $userData = $userIns->getUserBasicInfo($paramUserID);
    if ($userData) {
        $BUCKYS_GLOBALS['title'] = trim($userData['firstName'] . ' ' . $userData['lastName']) . "'s Items - BuckysRoomTrade Search";   
    }
    else {
        $BUCKYS_GLOBALS['title'] = 'BuckysRoomTrade Search';
    }
    
}
else if ($paramLocation != '') {
    $BUCKYS_GLOBALS['title'] = $paramLocation . ' - BuckysRoomTrade Search';
}
else{
    $BUCKYS_GLOBALS['title'] = 'BuckysRoomTrade Search';
}



require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php"); 
