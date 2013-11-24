<?php
/**
* Posts Page
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
    <section id="right_side">  
        <div class="info-box" id="posts-box">
            <h3>Posts 
                <?php if(isset($_GET['post'])){ ?>
                <a href="/posts.php?user=<?php echo $userData['userID']?>" class="view-all">(view all)</a>
                <?php }else{ ?>
                <a href="/profile.php?user=<?php echo $userData['userID']?>" class="view-all">(back to profile)</a>
                <?php }?>                
            </h3>
            <?php
            
            foreach($posts as $post)
            {
                echo buckys_get_single_post_html($post, $userID);
            }
            ?>
            <?php if( !isset($_GET['post']) ){ ?>
            <!-- View More Stream -->
            <div class="clear"></div>
            <div id="more-stream" data-page="post" data-user-id="<?php echo $profileID?>"><img src="<?php echo DIR_WS_IMAGE?>loading1.gif" height="15" /></div>
            <?php } ?>
        </div>        
    </section>
</section>