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
    
        <span class="titles">Offer Made</span><br/>
        <div class="offer-received">
            <?php if (isset($offerMade) && count($offerMade) > 0) :?>
                
                <div class="top-header-cont">
                    <div class="n1">My Item</div>
                    <div class="n2">Their Item</div>
                    <div class="n3">Actions</div>
                    <div class="clear"></div>
                </div>
                
                
                <?php 
                    foreach ($offerMade as $offerData) :
                    
                        $userIns = new BuckysUser();
                        $offerData['basicInfo'] = $userIns->getUserBasicInfo($offerData['targetUserID']);
                        
                        
                        $myItemImage = buckys_trade_get_item_thumb($offerData['offeredImages']);
                        $theirItemImage = buckys_trade_get_item_thumb($offerData['targetImages']);
                        
                        $sendMessageLink = '/messages_compose.php?to=' . $offerData['targetUserID'];
                        $theirID = $offerData['targetUserID'];
                        
                        $dateOffered = date('n/j/y', strtotime($offerData['offerCreatedDate']));
                        
                        
                        $strTimeLeft = '';
                        if (strtotime($offerData['targetCreatedDate']) > strtotime($offerData['offeredCreatedDate'])) {
                            $strTimeLeft = buckys_trade_get_item_time_left($offerData['offeredCreatedDate']);
                        }
                        else {
                            $strTimeLeft = buckys_trade_get_item_time_left($offerData['targetCreatedDate']);
                        }
                        
                        $targetItemLink = '/trade/view.php?id='.$offerData['targetItemID'];
                        $offeredItemLink = '/trade/view.php?id='.$offerData['offeredItemID'];
                        
                        
                        $totalRating = 'No';
                        $positiveRating = '';

                        if (isset($offerData['totalRating']) && $offerData['totalRating'] > 0) {
                            $totalRating = $offerData['totalRating'];
                            if (is_numeric($offerData['positiveRating'])) {
                                $positiveRating = number_format($offerData['positiveRating'] / $offerData['totalRating'] * 100, 2, '.', '') . '% Positive';
                            }
                        }
                        
                        
                ?>
                
                    <div class="node">
                        
                        <table cellpadding="0" cellspacing="0">
                            <tr>    
                                <td class="my">
                                    <div class="image">
                                        <img src="<?php echo $myItemImage;?>">
                                    </div>
                                    <div class="desc">
                                        <div class="t"><a href="<?php echo $offeredItemLink;?>"><?php echo $offerData['offeredTitle'];?></a></div>
                                        <!-- <div class="st"><?php echo $offerData['offeredSubtitle'];?></div> -->
                                        <!-- <div class="i-num">Item Number: <?php echo $offerData['offeredItemID'];?></div> -->
                                    </div>
                                    <div class="clear"></div>
                                </td>
                                <td class="their">
                                    <div class="image">
                                        <img src="<?php echo $theirItemImage;?>">
                                    </div>
                                    <div class="desc">
                                        <div class="t">
											<a href="<?php echo $targetItemLink;?>"><?php echo $offerData['targetTitle'];?></a>
											<div class="clear"></div>
											<div class="f-user" style="margin-top:10px;">
												<!--
												<div class="f-user-image">
													<a href="/profile.php?user=<?php echo $offerData['targetUserID'];?>" class="profileLink">
														<img src="<?php echo BuckysUser::getProfileIcon($offerData['targetUserID'])?>" class="postIcons" />                                    
													</a>
												</div>
												-->
												<div class="f-user-desc">
													<a href="/profile.php?user=<?php echo $offerData['targetUserID'];?>" class="profileLink">
														<span><?php echo trim($offerData['basicInfo']['firstName'] . ' ' . $offerData['basicInfo']['lastName']);?></span>
													</a> <br/>
													<?php 
														if (is_numeric($totalRating)) {
															echo sprintf('<a href="%s" class="rating">(%d ratings)</a> %s', '/trade/feedback.php?user='.$theirID, $totalRating, $positiveRating);
														}
														else {
															echo sprintf('(%s ratings)', $totalRating);
														}
													?>
												</div>
											</div>											
										</div>
                                        <!-- <div class="st"><?php echo $offerData['targetSubtitle'];?></div> -->
                                        <!-- <div class="i-num">Item Number: <?php echo $offerData['targetItemID'];?></div> -->
                                        
                                    </div>
                                    <div class="clear"></div>
                                </td>
                                <td class="act">
                                    <div><a href="javascript:void(0)" onclick="deleteOffer(<?php echo $offerData['offerID']?>)">Delete Offer</a></div>
                                    <div><a href="<?php echo $sendMessageLink;?>">Send Message</a></div>
									<div style="margin-top:10px;">Offer Expires: <span class="red"><?php echo $strTimeLeft;?></span></div>
                                    <div>Item Location: <span><?php echo $offerData['offeredLocationTitle'];?></span></div>
                                </td>
                            </tr>
                        </table>
                        
                        
                        
                    </div>
                    
                <?php endforeach;?>
                
                <?php buckys_get_panel('trade_pagination');?>
                
            <?php else:?>
                
                <div class="no-trade-data"> - No data available - </div>
            
            <?php endif;?>
                
        </div>
        
        <div class="clear"></div>                
        
    </section>
</section>
