<?php
if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
}

$itemData = $view['item'];


/*Get images*/
$imageList = array();
if ($itemData['images'] != '')
    $imageList = explode("|", $itemData['images']);

$imageThumbList = array(); // it will save image & thumb list
    
if (count($imageList) > 0) {
    
    foreach($imageList as $imgData) {
        
        $thumbPathInfo = pathinfo($imgData);
        $thumbFileName = $thumbPathInfo['dirname'] . "/" . $thumbPathInfo['filename'] . TRADE_ITEM_IMAGE_THUMB_SUFFIX . "." . $thumbPathInfo['extension'];
        
        
        $tmpData['image'] = $imgData;
        $tmpData['thumb'] = $thumbFileName;
        
        $imageThumbList[] = $tmpData;
    }
    
    
}
else {
    $imageThumbList[0]['image'] = '/images/trade/no-image.jpg';
    $imageThumbList[0]['thumb'] = '/images/trade/no-image-thumb.jpg';
}


$totalRating = 'No';
$positiveRating = '';

if (isset($itemData['totalRating']) && $itemData['totalRating'] > 0) {
    $totalRating = $itemData['totalRating'];
    if (is_numeric($itemData['positiveRating'])) {
        $positiveRating = number_format($itemData['positiveRating'] / $itemData['totalRating'] * 100, 2, '.', '') . '% Positive';
    }
}

$sendMessageLink = '/register.php';
if (isset($view['myID']) && is_numeric($view['myID']))
    $sendMessageLink = '/messages_compose.php?to=' . $itemData['userID'];

$theirID = $itemData['userID'];
    
?>

<section id="main_section">
    
    <?php buckys_get_panel('trade_top_search');?>
        
    <section class="trade-full-panel">
    
        <div class="trade-view-images">
            <div class="m">
                <img id="trade_view_main_image" src="<?php echo $imageThumbList[0]['image'];?>" />
            </div>
            
            <div class="d">mouse over images to zoom</div>
            
            <div class="thumb">
                <ul>
                <?php 
                    for ($idx = 0; $idx < count($imageThumbList); $idx++) {
                        echo sprintf('<li class="%s"><table cellpadding="0" cellspacing="0"><tr><td><img src="%s" /><img src="%s" class="large"/></td></tr></table></li>', $idx==0?'first sel':'', $imageThumbList[$idx]['thumb'], $imageThumbList[$idx]['image']);
                    }
                ?>
                </ul>
                <div class="clear"></div>
            </div>
            
        </div>
        
        <div class="trade-view-info">
            <div><span class="titles">Item Information</span></div>
            
            <div class="i-name"><?php echo $itemData['title']; ?></div>
            <div class="i-subtitle"><?php echo $itemData['subtitle']; ?></div>
            <div class="i-info">
                <dl>
					<!--
                    <dt>Item Number:</dt>
                    <dd> </dd>
                    
                    <dt>Offers:</dt>
                    <dd> </dd>
                    
                    
                    <dt>Category:</dt>
                    <dd> </dd>
                    
                    <dt>Item Location:</dt>
                    <dd> </dd>
                    
                    <dt>Time Left:</dt>
                    <dd class="red"> </dd>
                    -->
					<dt>
						Item Number: <br />
						Offers: <br />
						Category: <br />
						Item Location: <br />
						Time Left:
					</dt>
					<dd>
						<?php echo $itemData['itemID']?> <br />
						<?php echo $itemData['offer']?> <br />
						<a href="/trade/search.php?cat=<?php echo urlencode($itemData['categoryName']);?>"><?php echo $itemData['categoryName']?></a><br />
						<?php echo $itemData['locationName']?> <br />
						<span style="color:#cc0000;"><?php echo buckys_trade_get_item_time_left($itemData['createdDate'])?></span>
					</dd>					
                    
                </dl>
                <div class="clear"></div>
            </div>
            
            <div class="action-cont">
                <div><span class="titles">Action</span></div>
                
                <?php if ($itemData['status'] != BuckysTradeItem::STATUS_ITEM_TRADED) :?>
                        <?php if ($view['myID'] != $itemData['userID']):?>
                            <?php if (isset($view['myID']) && is_numeric($view['myID'])):?>
                                <?php if ($view['offerDisabled'] == false) :?>
                                    <a href="javascript:void(0)" class="make-an-offer">Make an Offer</a>  <br/>
                                <?php endif;?>
                                <a href="<?php echo $sendMessageLink;?>">Send Owner Message</a> <br/>
                            <?php else :?>
                                <a href="/register.php">Make an Offer</a>  <br/>
                                <a href="/register.php">Send Owner Message</a>  <br/>                        
                            <?php endif;?>
                            
                            
                        <?php else : ?>
                        
                            <?php if ($itemData['isExpired'] == true) : ?>
                                <a href="javascript:void(0)" onclick="deleteTradeItem(<?php echo $itemData['itemID'];?>);">Delete</a><br/>
                                <a href="/trade/edititem.php?id=<?php echo $itemData['itemID'];?>&type=relist">Relist Item</a> <br/>
                            <?php else:?>
                                <a href="/trade/edititem.php?id=<?php echo $itemData['itemID'];?>">Edit</a> <br/>
                                <a href="javascript:void(0)" onclick="deleteTradeItem(<?php echo $itemData['itemID'];?>);">Delete</a><br/>
                                <a href="/trade/offer_received.php?targetID=<?php echo $itemData['itemID'];?>">View Offers</a><br/>
                            <?php endif;?>
                            
                        <?php endif;?>
                <?php else:?>
                    <div class="">This item has been traded.</div>
                <?php endif;?>
            </div>
            
            
        </div>
        
        <div class="trade-view-owner">
            <div><span class="titles">Owner Information</span></div>
            <a href="/profile.php?user=<?php echo $itemData['userID'];?>" class="profileLink">
                <img src="<?php echo BuckysUser::getProfileIcon($itemData['userID'])?>" class="postIcons" />
                <span><?php echo trim($itemData['userInfo']['firstName'] . ' ' . $itemData['userInfo']['lastName']);?></span>
            </a>
            <div>
                <?php 
                    if (is_numeric($totalRating)) {
                        echo sprintf('<a href="%s" class="rating">(%d ratings)</a> %s', '/trade/feedback.php?user='.$theirID, $totalRating, $positiveRating);
                    }
                    else {
                        echo sprintf('(%s ratings)', $totalRating);
                    }
                ?>
            </div>
			<div class="clear"></div>
            <div class="action-cont">
                <a href="/trade/search.php?user=<?php echo $itemData['userID']?>">See Other Items</a> <br/>
                <?php if ($view['myID'] != $itemData['userID']):?>
                    <a href="<?php echo $sendMessageLink?>">Send Message</a> <br/>
                <?php endif;?>
                <a href="/profile.php?user=<?php echo $itemData['userID'];?>">View Profile</a> <br/>
            </div>
            
        </div>
        
        <?php if ($view['offerDisabled'] == false) :?>
            <div class="make-offer-panel">
                <div><span class="titles">Select Items to Offer</span></div>
                <div class="inner-p needmask">
                    <input type="hidden" name="targetItemID" id="targetItemID" value="<?php echo $itemData['itemID']?>">
                    <ul id="offer_available_items">
                    <?php 
                        if (count($view['availableItems']) > 0) {
                            foreach($view['availableItems'] as $anItemData) :
                                $thumbImagePath = buckys_trade_get_item_thumb($anItemData['images']);
                                $itemUrl = '/trade/view.php?id=' . $anItemData['itemID'];
                    ?>
                                <li>
                                    <div class="rad">
                                        <input type="hidden" name="" value="<?php echo $anItemData['itemID']?>">
                                        <input type="radio" name="available_item">
                                    </div>
                                    <div class="image">
                                        <img src="<?php echo $thumbImagePath;?>" >                                        
                                    </div>
                                    <div class="desc">
                                        <div class="t"><?php echo $anItemData['title']?></div>
                                        <div class="st"><?php echo $anItemData['subtitle']?></div>
                                        <div class="item-no">Item Number: <?php echo $anItemData['itemID']?></div>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                    <?php
                            endforeach;
                            
                        }
                        
                    ?>
                    </ul>
                    <div class="clear"></div>
                </div>
                <div>
                    <input type="button" id="make_an_offer_btn" class="red-btn" value="Make Offer" />
                    
                    <input type="button" id="cancel_an_offer_btn" class="gray-btn" value="Cancel" />
                </div>
            </div>
        <?php endif;?>
        
        <div class="clear"></div>
        
        
        <div class="trade-view-description">
            <div><span class="titles">Description:</span></div>
            <div class="d">
                <?php echo render_enter_to_br($itemData['description']);?>
            </div>
            
            <div><span class="titles">Items Wanted:</span></div>
            <div class="d">
                <?php echo $itemData['itemWanted'];?>
            </div>
        </div>
        
    </section>
</section>
