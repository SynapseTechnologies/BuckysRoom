<?php
if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
}


$offerMade = $view['offers'];

?>


<section id="main_section">
    
    <?php buckys_get_panel('trade_top_search');?>
        
    <?php buckys_get_panel('trade_main_nav'); ?>
    <section id="right_side" class="floatright">
    
        <span class="titles">Offer Declined</span><br/>
        <div>
            
            <?php if ($view['type'] == 'byme'):?>
                <a href="/trade/offer_declined.php">Declined By Them</a> |
                <span>Declined by Me</span>
            <?php else :?>
                <span>Declined by Them</span> |
                <a href="/trade/offer_declined.php?type=byme">Declined by Me</a>
            <?php endif;?>
            
        
        </div>
        <div class="offer-received offer-declined">
            <input type="hidden" id="offer_declined_type" value="<?php echo $view['type'] == 'byme'?1:0;?>">
            <?php if (isset($offerMade) && count($offerMade) > 0) :?>
                
                <div class="top-header-cont">
                    <div class="n0"><input type="checkbox" class="select-all-offers" id="select_all_offers"></div>
                    <div class="n1">My Item</div>
                    <div class="n2">Their Item</div>
                    <div class="n3">Date Declined</div>
                    <div class="clear"></div>
                </div>
                
                
                <?php 
                    foreach ($offerMade as $offerData) :
                    
                        
                        $targetItemImage = buckys_trade_get_item_thumb($offerData['targetImages']);
                        $offeredItemImage = buckys_trade_get_item_thumb($offerData['offeredImages']);
                        
                        $sendMessageLink = '/messages_compose.php?to=' . $offerData['offeredUserID'];
                        
                        $dateOffered = date('n/j/y H:i', strtotime($offerData['offerCreatedDate']));
                        
                        
                        $targetItemLink = '/trade/view.php?id='.$offerData['targetItemID'];
                        $offeredItemLink = '/trade/view.php?id='.$offerData['offeredItemID'];
                        
                        
                        
                        
                        
                ?>
                    <?php if ($view['type'] != 'byme') : ?>
                        
                        <div class="node">
                            
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="chk">
                                        <input type="checkbox" id="chk_offer_row_<?php echo $offerData['offerID'];?>" class="chk-offer-row">
                                    </td>
                                    <td class="my">
                                        <div class="image">
                                            <img src="<?php echo $offeredItemImage;?>">
                                        </div>
                                        <div class="desc">
                                            <div class="t"><a href="<?php echo $offeredItemLink;?>"><?php echo $offerData['offeredTitle'];?></a></div>
                                            <!-- <div class="st"><?php echo $offerData['offeredSubtitle'];?></div> -->
                                            <div class="i-num">Item Number: <?php echo $offerData['offeredItemID'];?></div>
                                        </div>
                                        <div class="clear"></div>
                                    </td>
                                    <td class="their">
                                        <div class="image">
                                            <img src="<?php echo $targetItemImage;?>">
                                        </div>
                                        <div class="desc">
                                            <div class="t"><a href="<?php echo $targetItemLink;?>"><?php echo $offerData['targetTitle'];?></a></div>
                                            <!-- <div class="st"><?php echo $offerData['targetSubtitle'];?></div> -->
                                            <div class="i-num">Item Number: <?php echo $offerData['targetItemID'];?></div>
                                        </div>
                                        <div class="clear"></div>
                                    </td>
                                    <td class="act bold" style="color:#555555;">
                                        <?php echo date('F d, Y', strtotime($offerData['offerUpdatedDate']));?>
                                    </td>
                                </tr>
                            </table>
                            
                        </div>
                        
                        
                        
                    <?php else :?>
                    
                    
                        <div class="node">
                            
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="chk">
                                        <input type="checkbox" id="chk_offer_row_<?php echo $offerData['offerID'];?>" class="chk-offer-row">
                                    </td>
                                    <td class="my">
                                        <div class="image">
                                            <img src="<?php echo $targetItemImage;?>">
                                        </div>
                                        <div class="desc">
                                            <div class="t"><a href="<?php echo $targetItemLink;?>"><?php echo $offerData['targetTitle'];?></a></div>
                                            <!-- <div class="st"><?php echo $offerData['targetSubtitle'];?></div> -->
                                            <div class="i-num">Item Number: <?php echo $offerData['targetItemID'];?></div>
                                        </div>
                                        <div class="clear"></div>
                                        
                                    </td>
                                    <td class="their">
                                        
                                        <div class="image">
                                            <img src="<?php echo $offeredItemImage;?>">
                                        </div>
                                        <div class="desc">
                                            <div class="t"><a href="<?php echo $offeredItemLink;?>"><?php echo $offerData['offeredTitle'];?></a></div>
                                            <!-- <div class="st"><?php echo $offerData['offeredSubtitle'];?></div> -->
                                            <div class="i-num">Item Number: <?php echo $offerData['offeredItemID'];?></div>
                                        </div>
                                        <div class="clear"></div>                                        
                                        
                                    </td>
                                    <td class="act bold" style="color:#555555;">
                                        <?php echo date('F d, Y', strtotime($offerData['offerUpdatedDate']));?>
                                    </td>
                                </tr>
                            </table>
                            
                        </div>
                    <?php endif;?>
                    
                <?php endforeach;?>
                
                <div class="remove-btn-cont">
                    <input type="button" class="red-btn" value="Remove" id="remove_declined_offers">
                </div>
                
                <?php buckys_get_panel('trade_pagination');?>
                
            <?php else:?>
                
                <div class="no-trade-data"> - No data available - </div>
            
            <?php endif;?>
                
        </div>
        
        <div class="clear"></div>                
        
    </section>
</section>
