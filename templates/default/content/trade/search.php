<?php
if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
}


//buckys_trade_search_url($view['param']['q'], $view['param']['cat'], $view['param']['loc'], $view['param']['sort']);


?>

<script type="text/javascript">
    
</script>

<section id="main_section">
    
    <?php buckys_get_panel('trade_top_search');?>
    
    
    <aside id="main_aside" class="trade-left-panel trade-search-left-panel">
        <div class="block-s">
            <span style="color:#666666">Categories</span>
            <ul class="left-cat-list">
            <?php 
                foreach ($view['categoryList'] as $catData) {
                    if ($catData['count'] > 0) {
                        if ($view['param']['cat'] == '' || strtolower($view['param']['cat']) == strtolower($catData['name']))
                            echo sprintf('<li><a href="%s">%s</a> <span>(%d)</span> </li>', buckys_trade_search_url($view['param']['q'], $catData['name'], $view['param']['loc'], $view['param']['sort'], $view['param']['user']), $catData['name'], $catData['count']);
                    }
                }
            ?>
            </ul>
            
        </div>
        
        
        <div class="block-s">
            <span style="color:#666666">Item Location</span>
                <select id="trade_search_location">
                    <option value="">Anywhere</option>
                    <?php 
                        if (count($view['countryList']) > 0) {
                            foreach ($view['countryList'] as $countryData) {
                                
                                $selected = '';
                                if (strtolower($view['param']['loc']) == strtolower($countryData['country_title'])) {
                                    $selected = 'selected="selected"';
                                }
                                
                                echo sprintf('<option value="%s" %s>%s</option>', $countryData['country_title'], $selected, $countryData['country_title']);
                            }
                        }
                    ?>
                </select>
        </div>
        

    </aside>
        
    
    <section id="right_side" class="floatright trade-search-result" style="border-left:none;">
    
        <div class="trade-item-list">
            <div class="breadcrumb">
                <a href="/trade/search.php">All Categories</a>
                <?php 
                    if ($view['param']['cat'] != '')
                        echo sprintf(' &gt <a href="%s">%s</a>', buckys_trade_search_url('', $view['param']['cat'], '', $view['param']['sort'], $view['param']['user']), $view['param']['cat']);
                ?>
                
                <?php 
                    if ($view['param']['loc'] != '')
                        echo sprintf(' &gt <a href="%s">%s</a>', buckys_trade_search_url('', $view['param']['cat'], $view['param']['loc'], $view['param']['sort'], $view['param']['user']), $view['param']['loc']);
                ?>
                
                <?php 
                    if ($view['param']['q'] != '')
                        echo sprintf(' &gt %s', $view['param']['q']);
                ?>
                
                
            </div>
            
            <div class="sort-box">
                <div class="l">
                    <div class="total-record-p"><?php echo sprintf('Showing %d - %d of %s Results', $BUCKYS_GLOBALS['tradePagination']['startIndex'], $BUCKYS_GLOBALS['tradePagination']['endIndex'], number_format($BUCKYS_GLOBALS['tradePagination']['totalRecords']))?></div>
                </div>
                <div class="r">
                    <select id="trade_search_sort">
                        <?php 
                            $sortOptionList = array(
                                        'best'          => 'Best Match',
                                        'endsoon'       => 'Time: ending soonest',
                                        'newly'         => 'Time: newly listed',
                                        'offersmost'    => 'Offers: most to least',
                                        'offersleast'   => 'Offers: least to most'
                                    );
                                    
                            
                            foreach ($sortOptionList as $key=>$val) {
                                $selected = '';
                                if ($view['param']['sort'] == $key)
                                    $selected = 'selected="selected"';
                                echo sprintf('<option value="%s" %s>%s</option>', $key, $selected, $val);
                            }
                        
                            
                        ?>
                    </select>
                    <label>Sort by </label>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </div>
            
            <div class="trade-search-result-list">
            <?php 
                if (isset($view['items']) && count($view['items']) > 0) :
            ?>
                    
                <?php 
                    $firstNodeFlag = true;
                    foreach ($view['items'] as $itemData) :
                        $thumbFileUrl = buckys_trade_get_item_thumb($itemData['images']);
                        $timeLeftStr = buckys_trade_get_item_time_left($itemData['createdDate']);
                        
                        $viewLink = '/trade/view.php?id=' . $itemData['itemID'];
                        
                        
                        $totalRating = 'No';
                        $positiveRating = '';

                        if (isset($itemData['totalRating']) && $itemData['totalRating'] > 0) {
                            $totalRating = $itemData['totalRating'];
                            if (is_numeric($itemData['positiveRating'])) {
                                $positiveRating = number_format($itemData['positiveRating'] / $itemData['totalRating'] * 100, 2, '.', '') . '% Positive';
                            }
                        }
                        
                        
                        $theirID = $itemData['userID'];
                        
                        
                        
                        
                ?>
                    <div class="node <?php if ($firstNodeFlag) {$firstNodeFlag = false; echo 'first';}?>">
                        <div class="item-img">
                            <a href="<?php echo $viewLink;?>"><img src="<?php echo $thumbFileUrl;?>" /></a>
                        </div>
                        <div class="item-desc">
                            <a href="<?php echo $viewLink;?>" class="item-name"><?php echo $itemData['title']?></a>
                            <div><?php echo $itemData['subtitle']?></div>
                            <div class="btm">
                                <div><a class="uname" href="/profile.php?user=<?php echo $theirID;?>"><?php echo trim($itemData['firstName'] . ' ' . $itemData['lastName']);?></a></div>
                            
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
                            </div>
                        </div>
                        <div class="item-offer">
                            <?php if ($itemData['offer'] > 0) :?>
                                <span><?php echo $itemData['offer'];?> Offers</span>
                            <?php else :?>
                                <span>No Offers</span>
                            <?php endif;?>
                        </div>
                        <div class="item-time-left">
                            <?php echo $timeLeftStr;?> left
                        </div>
                        <div class="clear"></div>
                    </div>
                <?php endforeach;?>
                
                
            <?php else:?>
                
                <div class="no-trade-data"> - No data available - </div>
            
            <?php endif;?>
            
            </div>
            <?php if (isset($view['items']) && count($view['items']) > 0) :?>
                <?php buckys_get_panel('trade_pagination');?>                
            <?php endif;?>
                
        </div>
        
        <div class="clear"></div>                
        
    </section>
</section>
