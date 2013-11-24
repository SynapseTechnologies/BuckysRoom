<?php
/**
* Display trade left side navitation
* 
*/

$tradeOfferReceived = 0;

$userData = $BUCKYS_GLOBALS['user'];

if (isset($userData) && isset($userData['userID'])) {
    $tradeOfferIns = new BuckysTradeOffer();
    $tradeOfferReceived = $tradeOfferIns->getNewOfferCount($userData['userID']);
}

?>
    <aside id="main_aside" class="trade-left-panel">
		<span class="titles">Trade Account</span>
		
        <a href="/trade/available.php" class="accountLinks" style="margin-top:10px;">My Items</a>
        <a href="/trade/available.php" class="accountSubLinks">Available</a> <br/>
        <a href="/trade/available.php?type=expired" class="accountSubLinks">Expired</a> <br/><br/>
        
        <a href="/trade/offer_received.php" class="accountLinks">Offers</a>
        <a href="/trade/offer_made.php" class="accountSubLinks">Made</a> <br/>
        <a href="/trade/offer_received.php" class="accountSubLinks<?php echo $tradeOfferReceived > 0 ? 'Bold' : ''?>">Received<?php echo $tradeOfferReceived > 0 ? ' ('.$tradeOfferReceived.') ' : ''?></a><br/>
        <a href="/trade/offer_declined.php" class="accountSubLinks">Declined</a> <br/><br/>
		
        <a href="/trade/traded.php" class="accountLinks">Trades</a>
        <a href="/trade/traded.php" class="accountSubLinks">Completed Trades</a> <br/>
        <a href="/trade/traded.php?type=history" class="accountSubLinks">Trade History</a> <br/><br/>		
        
        <?php /*
        <span class="titles">Messages</span><br/>
        <a href="/trade/msg_sent.php" class="accountSubLinks">Sent</a> <br/>
        <a href="/trade/msg_trash.php" class="accountSubLinks">Trash</a> <br/>
        <a href="/trade/msg_compose.php" class="accountSubLinks">Compose</a> <br/><br/>
        
        */
        ?>
        
        
        <a href="/trade/notify.php" class="accountLinks">Account Settings</a>
<!--        <a href="/trade/notify.php" class="accountSubLinks">Notification Settings</a> <br/>-->
        <a href="/trade/shipping_info.php" class="accountSubLinks">Shipping Information</a> <br/><br/>

    </aside>
