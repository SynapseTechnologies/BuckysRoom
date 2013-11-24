<?php
/**
* Index Page Layout
*/

if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
}
?>
<section id="main_section">
    <section id="main_content">
        <?php render_result_messages(); ?>
        <div id="homepage-lft">
            <ul>
                <li class="item-developers"><a href="/developers.php">Developers</a></li>
                <li class="item-forum"><a href="/forum">Forum</a></li>
                <li class="item-page"><a href="/search.php?type=1&sort=pop">Pages</a></li>
                <li class="item-signup"><a href="/register.php">Sign Up</a></li>
                <li class="item-trade"><a href="/trade">Trade</a></li>
                <li class="item-vote"><a href="/moderator.php?type=community">Vote</a></li>
            </ul>
            <div id="homepage-banner"><a href="http://www.ummchealth.com/children/"><img src="/images/home_banner_sample.png" /></a></div>
        </div>
        <div id="homepage-rgt">
            <div id="large-image"><img src="/images/homepage_main_image.jpg" /></div>
            <div id="popular-pages" class="items-box">
                <h2 class="titles">Popular Pages <span style="font-size:11px;font-family: Arial,Helvetica,sans-serif;"><a href="/search.php?type=1&sort=pop">(view more)</a></span></h2>
                <ul>
                <?php foreach($popularPages as $row){  ?>
                <li>
                    <?php render_pagethumb_link($row, 'mainProfilePic'); ?>
                    <a href="/page.php?pid=<?php echo $row['pageID']?>" class="link"><?php echo buckys_truncate_string($row['title'], 25) ?></a>
                    <span><?php echo $row['followers']?> Follower<?php echo $row['followers'] > 1 ? 's' : ''?></span>
                    <div class="clear"></div>
                </li>
                <?php } ?>
                </ul>
            </div>
            <div class="clear"></div>
            <div id="newest-trade-items" class="items-box">
                <h2 class="titles">Newest Trade Items <span style="font-size:11px;font-family: Arial,Helvetica,sans-serif;"><a href="/trade/search.php?sort=newly">(view more)</a></span></h2>
                <ul>
                    <?php foreach($recentTradeItems as $row) {?>
                    <li>
                        <a href="/trade/view.php?id=<?php echo $row['itemID']?>" class="item-link">
                            <?php if(!$row['images']){ ?>
                            <img src="/images/trade/no-image-thumb.jpg" />
                            <?php 
                            }else{
                                $tImage = explode("|", $row['images']);
                                $tImage = $tImage[0];
                                $tImageInfo = pathinfo($tImage);
                                $tImage = $tImageInfo['dirname'] . "/" . $tImageInfo['filename'] . TRADE_ITEM_IMAGE_THUMB_SUFFIX . "." . $tImageInfo['extension'];
                            ?>
                            <img src="<?php echo $tImage ?>" />                            
                            <?php } ?>
                            <?php echo buckys_truncate_string($row['title'], 22) ?>
                        </a>
                        <a href="/profile.php?user=<?php echo $row['userID']?>" class="profile-link"><b><?php echo $row['firstName'] . " " . $row['lastName']?></b></a>
                    </li>
                    <?php } ?>
                </ul>
                <div class="clear"></div>
            </div>
            <div id="recent-forum-posts" class="items-box">
                <h2 class="titles">Recent Forum Posts <span style="font-size:11px;font-family: Arial,Helvetica,sans-serif;"><a href="/forum/recent_activity.php">(view more)</a></span></h2>
                <ul>
                    <?php foreach($recentTopics as $row){  ?>
                    <li>
                        <?php if($row['lastReplierID'] > 0){ ?>
                        <?php render_profile_link(array('userID' => $row['lastReplierID'], 'thumbnail' => $row['lastReplierThumbnail']), 'profileIcon') ?>
                        <a href="/forum/topic.php?id=<?php echo $row['topicID']?>" class="title-link"><?php echo buckys_truncate_string($row['topicTitle'], 80)?></a><br />
                        <a href="/profile.php?user=<?php echo $row['lastReplierID']?>"><b><?php echo $row['lastReplierName'] ?></b></a>
                        <span class="date"><?php echo buckys_format_date($row['lastReplyDate']);?></span>
                        <?php }else{ ?>
                        <?php render_profile_link(array('userID' => $row['creatorID'], 'thumbnail' => $row['creatorThumbnail']), 'profileIcon') ?>
                        <a href="/forum/topic.php?id=<?php echo $row['topicID']?>" class="title-link"><?php echo buckys_truncate_string($row['topicTitle'], 80)?></a><br />
                        <a href="/profile.php?user=<?php echo $row['creatorID']?>"><b><?php echo $row['creatorName'] ?></b></a>
                        <span class="date"><?php echo buckys_format_date($row['createdDate']);?></span>
                        <?php } ?>                        
                        <div class="clear"></div>
                    </li>
                    <?php } ?>
                </ul>                
            </div>
            
        </div>
        <div class="clear"></div>
        <!-- Top Images -->
        <h2 class="titles" style="margin-top: 0px;">Popular Images</h2>
        <div class="index_selectDates">
            <a class="select-period-link" href="/tops.php?type=image&period=today" data-type="top-images">Today</a> &middot; <a href="/tops.php?type=image&period=this-week" class="select-period-link" data-type="top-images">This Week</a> &middot; <a href="/tops.php?type=image&period=this-month" class="select-period-link" data-type="top-images">This Month</a> &middot; <a href="/tops.php?type=image" class="select-period-link" data-type="top-images">All Time</a>
        </div>
        <div id="top-images-this-week" class="top-images">
            <?php render_top_images($popularImages); ?>
        </div>
        <div class="clear"></div>
        <!-- Top Posts -->
        <h2 class="titles">Popular Posts</h2>
        <div class="index_selectDates">
            <a class="select-period-link" href="/tops.php?type=text&period=today" data-type="top-posts">Today</a> &middot; <a href="/tops.php?type=text&period=this-week " class="select-period-link" data-type="top-posts">This Week</a> &middot; <a href="/tops.php?type=text&period=this-month" class="select-period-link" data-type="top-posts">This Month</a> &middot; <a href="/tops.php?type=text" class="select-period-link" data-type="top-posts">All Time</a>
        </div>
        <div id="top-posts-this-week" class="top-posts">
            <?php render_top_posts($popularPosts);?>
        </div>
        <div class="clear"></div>
        <!-- Top Videos -->
        <h2 class="titles">Popular Videos</h2>
        <div class="index_selectDates">
            <a class="select-period-link" href="/tops.php?type=video&period=today" data-type="top-videos">Today</a> &middot; <a href="/tops.php?type=video&period=this-week" class="select-period-link" data-type="top-videos">This Week</a> &middot; <a href="/tops.php?type=video&period=this-month" class="select-period-link" data-type="top-videos">This Month</a> &middot; <a href="/tops.php?type=video" class="select-period-link" data-type="top-videos">All Time</a>
        </div>
        <div id="top-videos-this-week" class="top-videos">
            <?php render_top_videos($popularVideos);?>
        </div>
        <div class="clear"></div>
    </section>
</section>