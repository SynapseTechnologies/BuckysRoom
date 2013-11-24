<?php
if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
}

$topTenUsers = $view['top_users'];
$topTenWantedItems = $view['top_wanted_items'];

$topTenRecentItems = $view['recent_items'];

?>
<section id="main_section">    
    <?php buckys_get_panel('trade_top_search');?>
    
    
    <?php 
        $tradeCatIns = new BuckysTradeCategory();
        $categoryList = $tradeCatIns->getCategoryList(0);
    ?>
    
    <aside id="main_aside" class="main_aside_wide">
        <ul class="left-trade-cat-list">
            <li><a href="/trade/search.php">All Categories</a></li>
            <?php
                if (count($categoryList) > 0) {
                    foreach ($categoryList as $catData) {
                        echo sprintf('<li><a href="/trade/search.php?cat=%s">%s</a></li>', urlencode($catData['name']), $catData['name']);                            
                    }
                }
            ?>                
        </ul>
    </aside>
    
    <section id="right_side" class="right_side_narrow" style="border-left:none;">
        
        <div class="home-inner">
            
            <?php render_result_messages(); ?>
            
            <div class="banner-section">
				<img src="/images/tradeHomePageBanner2.png" />
			</div>
			
            <div class="top-users-section">
                <div class="pb10">
                    <span class="titles">Top Users</span>
                </div>
                
                <?php if (is_array($topTenUsers) && count($topTenUsers) > 0) : ?>
                <ul>
                    <?php 
                    
                        foreach ($topTenUsers as $userData) :
                            if ($userData['itemCount'] == 0)
                                break;
                    ?>
                    <li>
                        <div class="f-user-image">
                            <a href="/profile.php?user=<?php echo $userData['userID'];?>" class="profileLink">
                                <img src="<?php echo BuckysUser::getProfileIcon($userData['userID'])?>" class="postIcons" />                                    
                            </a>
                        </div>
                        <div class="f-user-desc">
                            <a href="/profile.php?user=<?php echo $userData['userID'];?>" class="profileLink">
                                <span><?php echo trim($userData['firstName'] . ' ' . $userData['lastName']);?></span>
                            </a>
                        </div>
                        <div class="item-count">
                            <a href="/trade/search.php?user=<?php echo $userData['userID'];?>" class="profileLink">
                                <?php echo $userData['itemCount'];?> items
                            </a>
                        </div>
                        <div class="clear"></div>
                    </li>
                    <?php endforeach;?>
                </ul>
                <?php endif;?>
                
            </div>
            <div class="clear"></div>
            
            <div class="home-block top-ten-products">
                <div class="pb10">
                    <span class="titles">Most Wanted</span> <span><a href="/trade/search.php?sort=offersmost" class="gray">(view more)</a></span>
                </div>
                
                <?php if (is_array($topTenWantedItems) && count($topTenWantedItems) > 0):?>
                    <?php 
                        $index = 0;
                        
                        echo "<div> <!-- 5 items block -->";
                        
                        foreach ($topTenWantedItems as $itemData):
                            
                            $index ++;
                            $thumbFileUrl = buckys_trade_get_item_thumb($itemData['images']);
                            $timeLeftStr = buckys_trade_get_item_time_left($itemData['createdDate']);
                            
                            $itemLink = '/trade/view.php?id='. $itemData['itemID'];
                            $userLink = '/profile.php?user='.$itemData['userID'];
                            
                            if (strlen($itemData['title']) > 100)
                                $itemData['title'] = substr($itemData['title'], 0, 100) . "...";
                            
                    ?>
                            <div class="node <?php if ($index % 5 == 0) echo "nomargin";?>">
                                <div class="thumb">
                                    <a href="<?php echo $itemLink;?>">
                                        <img src="<?php echo $thumbFileUrl;?>">
                                    </a>
                                </div>
                                <div class="tt">
                                    <a href="<?php echo $itemLink;?>"><?php echo $itemData['title'];?></a>
                                </div>
                                <div class="u">
                                    <a href="<?php echo $userLink;?>">
                                        <?php echo trim($itemData['firstName'] . ' ' . $itemData['lastName']);?>
                                    </a>
                                </div>
                                <div class="o">
                                    <?php echo $itemData['offerCount'];?> Offers
                                </div>
                                <div class="tl">
                                    <?php echo $timeLeftStr;?>&nbsp;left
                                </div>
                            </div>
                            
                            <?php 
                                if ($index % 5 == 0) {
                                    echo '<div class="clear"></div></div><div>';
                                }
                            ?>
                            
                        
                    <?php endforeach;?>
                        <?php echo "</div>  <!-- end of 5 items block -->";?>
                <?php endif;?>
                
                <div class="clear"></div>
            </div>
            
            
            <div class="home-block top-ten-products">
                <div class="pb10">
                    <span class="titles">Newest Items</span> <span><a href="/trade/search.php?sort=newly" class="gray">(view more)</a></span>
                </div>
                
                
                <?php if (is_array($topTenRecentItems) && count($topTenRecentItems) > 0):?>
                    <?php 
                        $index = 0;
                        
                        echo "<div> <!-- 5 items block -->";
                        
                        foreach ($topTenRecentItems as $itemData):
                            
                            $index ++;
                            $thumbFileUrl = buckys_trade_get_item_thumb($itemData['images']);
                            $timePastStr = buckys_trade_get_item_time_past($itemData['createdDate']);
                            
                            $itemLink = '/trade/view.php?id='. $itemData['itemID'];
                            $userLink = '/profile.php?user='.$itemData['userID'];
                            
                            if (strlen($itemData['title']) > 100)
                                $itemData['title'] = substr($itemData['title'], 0, 100) . "...";
                            
                    ?>
                            <div class="node <?php if ($index % 5 == 0) echo "nomargin";?>">
                                <div class="thumb">
                                    <a href="<?php echo $itemLink;?>">
                                        <img src="<?php echo $thumbFileUrl;?>">
                                    </a>
                                </div>
                                <div class="tt">
                                    <a href="<?php echo $itemLink;?>"><?php echo $itemData['title'];?></a>
                                </div>
                                <div class="u">
                                    <a href="<?php echo $userLink;?>">
                                        <?php echo trim($itemData['firstName'] . ' ' . $itemData['lastName']);?>
                                    </a>
                                </div>
                                <div class="tl">
                                    <?php echo $timePastStr;?>&nbsp;ago
                                </div>
                            </div>
                            
                            <?php 
                                if ($index % 5 == 0) {
                                    echo '<div class="clear"></div></div><div>';
                                }
                            ?>
                            
                        
                    <?php endforeach;?>
                        <?php echo "</div>  <!-- end of 5 items block -->";?>
                <?php endif;?>
                
                <div class="clear"></div>
                
                
            </div>
            
            <div class="clear"></div>
        </div>
    </section>
</section>
