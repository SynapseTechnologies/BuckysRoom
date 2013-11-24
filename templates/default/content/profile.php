<?php
/**
* Profile Detail Page
*/

if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
}
?>
<section id="main_section">    
        
    <!-- Left Side -->
    <?php buckys_get_panel('profile_left_sidebar') ?>
    
    <!-- 752px -->
    <section id="right_side" class="profile-contr">
        <?php render_result_messages(); ?>
        <div class="info-box" id="friends-box">
            <h3>Friends <a href="/friends.php?user=<?php echo $userData['userID']?>" class="view-all">(view all)</a></h3>
            <?php 
            foreach($friends as $row){
                render_profile_link($row, 'friendThumbnails');                    
            } 
            ?>
        </div>
        <br />
    
        <div class="info-box" id="posts-box">
            <h3>Posts <a href="/posts.php?user=<?php echo $userData['userID']?>" class="view-all">(view all)</a></h3>
            <?php
            foreach($posts as $post)
            {
                echo buckys_get_single_post_html($post, $userID);
            }
            ?>
            <!-- View More Stream -->
            <div class="clear"></div>
            <div id="more-stream" data-page="post" data-user-id="<?php echo $profileID?>"><img src="<?php echo DIR_WS_IMAGE?>loading1.gif" height="15" /></div>
        </div>
      
    </section>
</section>