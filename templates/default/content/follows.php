<?php
/**
* Profile Detail Page
*/

if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
}

$followedPages = $view['pages'];

?>
<section id="main_section">    
        
    <!-- Left Side -->
    <?php buckys_get_panel('profile_left_sidebar') ?>
    
    <!-- 752px -->
    <section id="right_side" class="profile-contr">
        
        <div class="info-box" id="friends-box">
            <h3>View All Pages <a href="/profile.php?user=<?php echo $userData['userID']?>" class="view-all">(back to profile)</a></h3>
            
            <?php render_result_messages(); ?>
            <?php $pagination->renderPaginate("/follows.php?user=" . $view['profileID'] . "&", count($followedPages)); ?>
            <div class="table userfriends" id="friends-box">
                <div class="thead">
                    <div class="td td-friend-icon">Page</div>
                    <div class="td td-friend-info"></div>
                    <div class="td td-friend-action">Action</div>
                    <div class="clear"></div>
                </div>
                <?php
                foreach($followedPages as $i=>$row)
                {
                ?>
                <div class="tr <?php echo $i == count($followedPages) - 1 ? 'noborder' : ''?> ">
                    <div class="td td-friend-icon"><?php render_pagethumb_link($row, 'postIcons'); ?></div>
                    <div class="td td-friend-info">
                        <p><a href="/page.php?pid=<?php echo $row['pageID']?>"><b><?php echo $row['title'] ?></b></a></p>
                        <p><?php echo $row['followerCount']?> Follower(s)</p>
                    </div>
                    <div class="td td-friend-action">
                        <p><a href="/page.php?pid=<?php echo $row['pageID']?>">View Page</a></p>
                    </div>
                    <div class="clear"></div>
                </div>
                <?php
                }
                if(count($followedPages) < 1)
                {
                ?>
                <div class="tr noborder">
                    Nothing to see here.
                </div>
                <?php
                }
                ?>
            </div>
            
        </div>        
      
    </section>
</section>