<?php
/***
* Including functions related view
*/
//Display Single Post
function buckys_get_single_post_html($post, $userID, $isPostPage = false, $pageData = null)
{
    ob_start();
    
    
    if ($post['pageID'] != BuckysPost::INDEPENDENT_POST_PAGE_ID) {
        $pageIns = new BuckysPage();
        $pageData = $pageIns->getPageByID($post['pageID']);
    }
    
    $pagePostFlag = false;
    if (isset($pageData)) {
        $pagePostFlag = true;
    }
    
    
    
    ?>
    <div class="post-item" id=<?php echo $post['postID']?>>
            
                <?php if ($pagePostFlag):?>
                    <?php render_pagethumb_link($pageData, 'postIcons'); ?>
                <?php else:?>
                    <a href="/profile.php?user=<?php echo $post['poster']?>" class="poster-thumb"><img src="<?php echo BuckysUser::getProfileIcon($post['poster'])?>" class="postIcons" /></a>
                <?php endif;?>
            
            <div class="post-content">
                
                <?php if ($pagePostFlag):?>
                    <div class="post-author"><a href="page.php?pid=<?php echo $pageData['pageID']?>"><b><?php echo $pageData['title']?></b></a></div>
                <?php else:?>
                    <div class="post-author"><a href="profile.php?user=<?php echo $post['poster']?>"><b><?php echo $post['posterFullName']?></b></a></div>
                <?php endif;?>
                
                
                <?php
                    echo buckys_process_post_content($post, $pageData);
                ?>
                <div class="post-date">
                    <span class="lft">
                        <?php if( buckys_not_null($userID) && $post['poster'] != $userID ){ ?>
                        <a href='/manage_post.php?action=<?php echo buckys_not_null($post['likeID']) ? 'unlikePost' : 'likePost'?>&postID=<?php echo $post['postID']?>' class="like-post-link"><?php echo buckys_not_null($post['likeID']) ? 'Unlike' : 'Like'?></a> &middot;
                        <?php } ?>
                        <?php if( buckys_not_null($userID) && $post['poster'] == $userID ){ ?>
                        <a href='/manage_post.php?action=delete-post&userID=<?php echo $userID?>&postID=<?php echo $post['postID']?>' class="remove-post-link">Delete</a> &middot;
                        <?php } ?>
                        <span><?php echo buckys_format_date($post['post_date'])?></span>
                        <?php if( buckys_not_null($userID) && $post['poster'] != $userID && !$post['reportID']){ ?>
                        &middot; <a href="/report_object.php" data-type="post" data-id="<?php echo $post['postID']?>" data-idHash="<?php echo buckys_encrypt_id($post['postID']) ?>" class="report-link">Report</a>
                        <?php } ?>
                    </span>
                    <span class="rgt">
                        <?php echo $post['visibility'] ? 'Public' : 'Private' ?>
                    </span>
                    <div class="clear"></div>
                </div>
                <div class="post-like-comment"> 
                    <?php if ($pagePostFlag): ?>
                        
                        <a href="/page.php?pid=<?php echo $pageData['pageID']?>&post=<?php echo $post['postID']?>" class="usersThatLiked likes-link"><?php echo $post['likes'] > 1 ? ($post['likes'] . " likes") : ($post['likes'] . " like")?> </a>
                        &middot;
                        <a href="/page.php?pid=<?php echo $pageData['pageID']?>&post=<?php echo $post['postID']?>" class="usersThatLiked"><?php echo $post['comments'] > 1 ? ($post['comments'] . " comments") : ($post['comments'] . " comment")?> </a>
                        
                    <?php else :?>
                        <a href="/posts.php?user=<?php echo $post['poster']?>&post=<?php echo $post['postID']?>" class="usersThatLiked likes-link"><?php echo $post['likes'] > 1 ? ($post['likes'] . " likes") : ($post['likes'] . " like")?> </a>
                        &middot;
                        <a href="/posts.php?user=<?php echo $post['poster']?>&post=<?php echo $post['postID']?>" class="usersThatLiked"><?php echo $post['comments'] > 1 ? ($post['comments'] . " comments") : ($post['comments'] . " comment")?> </a>
                    <?php endif;?>
                </div>
                <?php
                    if( $post['likes'] > 0 ){ 
                        $likedUsers = BuckysPost::getLikedUsers($post['postID']);                                            
                ?>
                <div class="liked-users">
                    <ul>
                        <?php foreach($likedUsers as $l): ?>
                        <li><a href="/profile.php?user=<?php echo $l['userID']?>"><img src="<?php echo BuckysUser::getProfileIcon($l)?>"> <span><?php echo $l['firstName'] . " " . $l['lastName'] ?></span></a></li>
                        <?php endforeach; ?>
                        <?php if( $post['likes'] > 30 ){ ?>
                        <li class="more-likes">+ <?php echo $post['likes'] - count($likedUsers)?> more</li>
                        <?php } ?>
                    </ul>                    
                </div>
                <?php
                    }
                ?>
                <?php if( buckys_not_null($userID) ){?>
                <div class="post-new-comment"> 
                    <a href="/profile.php?user=<?php echo $userID?>"><img src="<?php echo BuckysUser::getProfileIcon($userID)?>" class="replyToPostIcons" /></a>
                    <form method="post" class="postcommentform" name="postcommentform" action="">
                        <input type="text" class="input" name="comment" placeholder="Write a comment...">
                        <input type="hidden" name="postID" value="<?php echo $post['postID']?>" />
                        <input type="submit" value="Post Comment" id="submit_post_reply" class="redButton" />
                        <?php render_loading_wrapper(); ?>
                    </form>
                </div>
                <?php } ?>
                <?php 
                    $comments = BuckysComment::getPostComments($post['postID']);
                    echo render_post_comments($comments, $userID);
                    if( count($comments) > 0 && BuckysComment::hasMoreComments($post['postID'], $comments[count($comments) - 1]['posted_date']) )
                    {
                ?>                                
                <a href="#" class="show-more-comments" data-last-date="<?php echo $comments[count($comments) - 1]['posted_date']?>" data-post-id="<?php echo $post['postID']?>">view more</a>
                <?php
                    }
                ?>
            </div>
            <input type="hidden" class="post-created-date" value="<?php echo $post['post_date']?>" />
        </div>    
    <?php
    $html = ob_get_contents();
    ob_end_clean();    
    return $html;
}

function buckys_process_post_content($post, $pageData=null)
{
    $content = $post['content'];
    $content = str_replace("\n", "<br />", $content);
    if( buckys_not_null($content) )    
        $content = "<div class='post-content-inner'>" . $content . "</div>";
    if( $post['type'] == 'video' )
    {
        //Getting Youtube Video KEY
        $content .= '<iframe width="560" height="315" src="http://www.youtube.com/embed/' . buckys_get_youtube_video_id($post['youtube_url']) . '?wmode=transparent" frameborder="0" allowfullscreen></iframe>';
    }else if( $post['type'] == 'image' ){
        if (buckys_not_null($pageData)) {
            $content .= '<a href="/page.php?pid=' . $pageData['pageID'] . '&post=' . $post['postID'] . '"><img class="post-image" src="' . DIR_WS_PHOTO . 'users/' . $post['poster'] . '/resized/' . $post['image'] . '" /></a><br />';
        }
        else {
            $content .= '<a href="/posts.php?user=' . $post['poster'] . '&post=' . $post['postID'] . '"><img class="post-image" src="' . DIR_WS_PHOTO . 'users/' . $post['poster'] . '/resized/' . $post['image'] . '" /></a><br />';
        }
        
    }
    else {
        $content = buckys_make_links_clickable($content);
    }
    
    return $content;
}

//Getting Videos For Index Page
function buckys_get_video_from_content($content)
{
    //Getting Youtube Shortcodes
    $pattern = "/\[youtube.*\](.*)\[\/youtube\]/i";
    if(preg_match_all($pattern, $content, $matches))
    {
        foreach($matches[0] as $youtube)
        {
            //Getting Width and height, url
            $pattern1 = "/\[youtube(.*)\](.*)\[\/youtube\]/i";
            if(preg_match($pattern1, $youtube, $matches1))
            {
                
                $videoContent = '<iframe width="238" height="134" src="' . $matches1[2] . '" frameborder="0" allowfullscreen></iframe>';                
                return $videoContent;
            }
        }
    }
    
    return '';
}

//Display post comments
function render_post_comments($comments, $userID = null)
{
    foreach($comments as $row)
    {
        render_single_comment($row, $userID);
    }    
}

//Render Single Post Comment
function render_single_comment($comment, $userID = null, $isReturn = false)
{
    global $BUCKYS_GLOBALS;
    
    $timeOffset = 0; 
    if(buckys_not_null($userID))
    {
        $userInfo = BuckysUser::getUserBasicInfo($userID);
        $timeOffset = $BUCKYS_GLOBALS['timezone'][$userInfo['timezone']];
    }
    
    ob_start();
    ?>
    <div class="comment-item">                 
        <a href="/profile.php?user=<?php echo $comment['commenter']?>" class="thumb"><img src="<?php echo BuckysUser::getProfileIcon($comment['commenter'])?>" class="replyToPostIcons" /></a>
        <div class="comment-content">
            <a href="/profile.php?user=<?php echo $comment['commenter']?>" style="font-weight:bold"><?php echo $comment['fullName']?></a>
            <br/>
            <?php echo $comment['content'] ?>
            <br/>
            <span style=" color:#999999; "><?php echo buckys_format_date($comment['posted_date'])?></span> 
            
            <?php
                if($comment['commenter'] == $userID || $comment['poster'] == $userID){
            ?>
            &middot;
            <a href="/comments.php?action=delete-comment&userID=<?php echo $userID?>&commentID=<?php echo $comment['commentID']?>&postID=<?php echo $comment['postID'] ?>" class="remove-comment-link">Delete</a>
            <?php
                }
                if(buckys_not_null($userID) && !$comment['reportID'] && ($comment['commenter'] != $userID && $comment['poster'] != $userID)){
            ?>
            &middot; <a href="/report_object.php" data-type="comment" data-id="<?php echo $comment['commentID']?>" data-idHash="<?php echo buckys_encrypt_id($comment['commentID']) ?>" class="report-link" style="color:#999999;">Report</a>
            <?php
                }
            ?>
        </div>        
    </div>
    <?php
    $html = ob_get_contents();
    ob_end_clean();
    if( !$isReturn )
        echo $html;
    else
        return $html;
}

//Display Profile link with Profile Image
function render_profile_link($user, $class = '')
{
    if( buckys_not_null($user['thumbnail']) )
    {
        ?>
            <a href="/profile.php?user=<?php echo $user['userID']?>"><img class="<?php echo $class?>" src="<?php echo DIR_WS_PHOTO?>users/<?php echo $user['userID']?>/resized/<?php echo $user['thumbnail']?>" ></a>
        <?php
    }else{
        ?>
            <a href="/profile.php?user=<?php echo $user['userID']?>"><img class="<?php echo $class?>" src="<?php echo DIR_WS_IMAGE?>defaultProfileImage.png" ></a>
        <?php
    }
}

//Display Page Logo Image
function render_pagethumb_link($pageData, $class = '')
{
    if( buckys_not_null($pageData['logo']) )
    {
        ?>
            <a href="/page.php?pid=<?php echo $pageData['pageID']?>"><img class="<?php echo $class?>" src="<?php echo DIR_WS_PHOTO?>users/<?php echo $pageData['userID']?>/resized/<?php echo $pageData['logo']?>" ></a>
        <?php
    }else{
        ?>
            <a href="/page.php?pid=<?php echo $pageData['pageID']?>"><img class="<?php echo $class?>" src="<?php echo DIR_WS_IMAGE?>newPagePlaceholder.jpg" ></a>
        <?php
    }
}


//Display Visibility Options
function render_visibility_options($optionName, $optionValue = 0, $optionId = null)
{
    if(!$optionId)
        $optionId = $optionName;
    ?>
    <span class="visibility_options">
        <label for="<?php echo $optionId . "_public"?>"><input type="radio" name="<?php echo $optionName?>" id="<?php echo $optionId?>_public" value="1" <?php echo $optionValue ? 'checked="checked"' : ''?> autocomplete="off" > Public</label>
        <label for="<?php echo $optionId . "_private"?>"><input type="radio" name="<?php echo $optionName?>" id="<?php echo $optionId?>_private" value="0" <?php echo !$optionValue ? 'checked="checked"' : ''?> autocomplete="off" > Private</label>
    </span>
    <?php
}

//Display BirthDate Select Boxes: Month, Day, Year
function render_birthdate_selectboxes($birthdate = null)
{
    global $BUCKYS_GLOBALS;
    
    if( !$birthdate ) 
        $birthdate = date("Y-m-d");
        
    list($year, $month, $day) = explode('-', $birthdate);
    ?>
    <select name="birthdate_month" id="birthdate_month" class="select" autocomplete="off">
        <?php 
        for( $i =0; $i < 13; $i++ )
        {
            if( $i == 0 )
                echo '<option value="">- Month -</option>';
            else
                echo '<option value="' . $i . '" ' . ($i == intval($month) ? 'selected="selected"' : '') . '>' . $BUCKYS_GLOBALS['months'][$i - 1] . '</option>';
        }
        ?>
    </select>
    <select name="birthdate_day" id="birthdate_day" class="select" autocomplete="off">
        <?php 
        for( $i =0; $i < 32; $i++ )
        {
            if( $i == 0 )
                echo '<option value="">- Day -</option>';
            else
                echo '<option value="' . $i . '" ' . ($i == intval($day) ? 'selected="selected"' : '') . '>' . str_pad($i, 2, '0', STR_PAD_LEFT) . '</option>';
        }
        ?>
    </select>
    <select name="birthdate_year" id="birthdate_year" class="select" autocomplete="off">
        <?php
        for( $i = 1912; $i <= date("Y"); $i++ )
        {
            if( $i == 1912 )
                echo '<option value="">- Year -</option>';
            else
                echo '<option value="' . $i . '" ' . ($i == intval($year) ? 'selected="selected"' : '') . '>' . $i . '</option>';
        }
        ?>
    </select>
    <?php
}

//Display Relationship Status Selectbox
function render_relationship_status_selectbox($relationship)
{
    global $BUCKYS_GLOBALS;
    ?>
    <select name="relationship_status" id="relationship_status" class="select">
        <option value="0">--</option>
        <?php
            foreach($BUCKYS_GLOBALS['relationShipStatus'] as $k=>$v){
                echo '<option value="' . $k . '" ' . ($k == $relationship ? 'selected="selected"' : '') . '>' . $v . '</option>';
            }
        ?>
    </select>
    <?php
}

//Display Gender Selectbox
function render_gender_selectbox($gender)
{
    global $BUCKYS_GLOBALS;
    ?>
    <select name="gender" id="gender" class="select">
        <option value="">--</option>
        <?php
            foreach($BUCKYS_GLOBALS['genders'] as $k=>$v){
                echo '<option value="' . $k . '" ' . ($k == $gender ? 'selected="selected"' : '') . '>' . $v . '</option>';
            }
        ?>
    </select>
    <?php
}

//Display Messenger Type Selectbox
function render_messenger_type_selectbox($name, $type = null)
{
    global $BUCKYS_GLOBALS;
    ?>
    <select name="<?php echo $name?>" class="select">
        <option value="">--</option>
        <?php
            foreach($BUCKYS_GLOBALS['messengerTypes'] as $v){
                echo '<option value="' . $v . '" ' . ($v == $type ? 'selected="selected"' : '') . '>' . $v . '</option>';
            }
        ?>
    </select>
    <?php
}

function render_timezone_selectbox($timezone = '(UTC) Coordinated Universal Time')
{
    global $BUCKYS_GLOBALS;
    ?>
    <select name="timezone" id="timezone" class="select">        
        <?php
            foreach($BUCKYS_GLOBALS['timezone'] as $k => $v){
                echo '<option value="' . $k . '" ' . ($k == $timezone ? 'selected="selected"' : '') . '>' . $k . '</option>';
            }
        ?>
    </select>
    <?php
}

/**
* Render Message from SESSION
* 
*/
function render_result_messages()
{
    if(isset($_SESSION['message']) && buckys_not_null($_SESSION['message']) )
    {
        for( $i=0; $i<sizeof($_SESSION['message']); $i++ )
        {
            switch($_SESSION['message'][$i]['type'])
            {
                case MSG_TYPE_SUCCESS:
                    echo '<p class="message success">' . $_SESSION['message'][$i]['message'] . '</p>';
                    break;
                case MSG_TYPE_ERROR:
                    echo '<p class="message error">' . $_SESSION['message'][$i]['message'] . '</p>';
                    break;
                case MSG_TYPE_NOTIFY:
                    echo '<p class="message notification">' . $_SESSION['message'][$i]['message'] . '</p>';
                    break;
                
            }
        }
        unset($_SESSION['message']);
    }
}

//Display BirthDate Select Boxes: Month, Day, Year
function render_year_selectbox($name, $year = null, $id = null)
{
    if(!$id)
        $id = $name;
    if(!$year)
        $year = date('Y');
    ?>
    <select name="<?php echo $name?>" id="<?php echo $id?>" class="select" autocomplete="off">
        <?php
        for( $i = 1912; $i <= date("Y"); $i++ )
        {
            if( $i == 1912 )
                echo '<option value="">- Year -</option>';
            else
                echo '<option value="' . $i . '" ' . ($i == intval($year) ? 'selected="selected"' : '') . '>' . $i . '</option>';
        }
        ?>
    </select>
    <?php
}

//Render Processing Wrapper 
function render_loading_wrapper()
{
    ?>
    <div class="loading-wrapper"><div></div><img src='<?php echo DIR_WS_IMAGE?>loading.gif' alt="Loading..." /></div>    
    <?php
}

//Render Result XML From Array
function render_result_xml( $data, $isReturn = false )
{
    ob_start();
    echo '<result>';
    foreach( $data as $tag=>$value )
    {
        echo '<' . $tag . '><![CDATA[' . $value . ']]></' . $tag . '>';        
    }
    echo '</result>';
    $content = ob_get_contents();
    ob_end_clean();
    if( $isReturn )
        return $content;
    else
        echo $content;
}

function buckys_format_date( $date )
{
    global $BUCKYS_GLOBALS;
    
    $timeOffset = 0; 
    if($BUCKYS_GLOBALS['user']['userID'] != 0)
    {
        $userInfo = BuckysUser::getUserBasicInfo($userID);
        $timeOffset = $BUCKYS_GLOBALS['timezone'][$userInfo['timezone']];
    }
    
    $strDate = "";
    
    $now = time();
    $today = date("Y-m-d");
    $cToday = date("Y-m-d", strtotime($date));
    
    if( $cToday == $today )
    {
        $h = floor(($now - strtotime($date)) / 3600);
        $m = floor((($now - strtotime($date)) % 3600) / 60);
        $s = floor((($now - strtotime($date)) % 3600) % 60);
        if($s > 40)
            $m++;
        if($h > 0)        
            $strDate = $h . " hour" . ($h > 1 ? "s " : " ");
        if($m > 0)
            $strDate .= $m . " minute" . ($m > 1 ? "s " : " ");        
        
        if($strDate == "")
        {
            if($s == 0)
                $s = 1;
            $strDate .= $s . " second" . ($s > 1 ? "s " : " ");        
        }
        
        $strDate .= "ago";
    }else{
        $strDate = date("F j, Y", strtotime($date)  + $timeOffset * 60 * 60);
//        $strDate = date("F j, Y h:i A", strtotime($date));
    }
    
    return $strDate;
}

//Render Top videos on the homepage
function render_top_videos($videos)
{
    foreach($videos as $i => $row){ ?>
    <div class="index_singleListing <?php echo ($i + 1) % 4 == 0 ? 'index_singleListingLast' : ''?> ">                
        <div class="index_singleListingContent">
            <a class="video" href="/posts.php?user=<?php echo $row['userID']?>&post=<?php echo $row['postID']?>"><img src="http://img.youtube.com/vi/<?php echo buckys_get_youtube_video_id($row['youtube_url'])?>/mqdefault.jpg" /></a>
            <div class="video-info">
                <span class="index_timeOfPost">posted <?php echo buckys_format_date($row['post_date'])?> <br /> by </span> 
                <a href="/profile.php?user=<?php echo $row['userID']?>"><b><?php echo $row['userName']?></b></a> <br/>
                <a href="/posts.php?user=<?php echo $row['userID']?>&post=<?php echo $row['postID']?>" class="index_LikesAndComments"><?php echo $row['likes']?> Like<?php echo $row['likes'] > 1 ? "s" : ""?></a> &middot; <a href="/posts.php?user=<?php echo $row['userID']?>&post=<?php echo $row['postID']?>" class="index_LikesAndComments"><?php echo $row['comments']?> Comment<?php echo $row['comments'] > 1 ? 's' : ''?></a>
            </div>
        </div>
    </div>
    <?php } ?>
    <?php if(count($videos) < 1) { ?>
    <div class="index_singleListing index_singleListingEmpty"><?php echo MSG_NO_DATA_FOUND?></div>            
    <?php } 
}

//Render Top Images on the home page
function render_top_images($images){
    
    foreach($images as $i=>$row){ ?>
    <?php if($i % 6 == 0){ ?><div class="clear"></div><?php } ?>
    <div class="index_singleListing">
        <a href="/posts.php?user=<?php echo $row['userID']?>&post=<?php echo $row['postID']?>"><img src="<?php echo DIR_WS_PHOTO . "users/" . $row['userID'] . "/" . ($row['is_profile'] ? 'resized' : 'thumbnail') . "/" . $row['image']?>" class="index_ImageIcons"></a>
        <div class="index_singleListingContent">
            <span class="index_timeOfPost">posted <?php echo buckys_format_date($row['post_date'])?> <br />by</span> <a href="/profile.php?user=<?php echo $row['userID']?>"><b><?php echo $row['userName']?></b></a> <br/>
            <a href="/posts.php?user=<?php echo $row['userID']?>&post=<?php echo $row['postID']?>" class="index_LikesAndComments"><?php echo $row['likes']?> Like<?php echo $row['likes'] > 1 ? "s" : ""?></a> &middot; <a href="/posts.php?user=<?php echo $row['userID']?>&post=<?php echo $row['postID']?>" class="index_LikesAndComments"><?php echo $row['comments']?> Comment<?php echo $row['comments'] > 1 ? 's' : ''?></a>
        </div>
    </div>            
    <?php } ?>
    <?php if(count($images) < 1) { ?>
    <div class="index_singleListing index_singleListingEmpty"><?php echo MSG_NO_DATA_FOUND?></div>            
    <?php }
    
}

//Render Top Posts on the home page
function render_top_posts($posts)
{    
    foreach($posts as $row){ ?>
    <div class="index_singleListing">
        <?php render_profile_link($row, 'index_PostIcons'); ?>        
        <div class="index_singleListingContent">
            <a href="/posts.php?user=<?php echo $row['userID']?>&post=<?php echo $row['postID']?>" class="index_singleListingTitles"><?php echo strlen($row['content']) > 600 ? substr($row['content'], 0, 600) . "...": $row['content'];?></a> <br/>
            <span class="index_timeOfPost">posted <?php echo buckys_format_date($row['post_date'])?> by</span> <a href="/profile.php?user=<?php echo $row['userID']?>"><b><?php echo $row['userName']?></b></a> <br/>
            <a href="/posts.php?user=<?php echo $row['userID']?>&post=<?php echo $row['postID']?>" class="index_LikesAndComments"><?php echo $row['likes']?> Like<?php echo $row['likes'] > 1 ? "s" : ""?></a> &middot; <a href="/posts.php?user=<?php echo $row['userID']?>&post=<?php echo $row['postID']?>" class="index_LikesAndComments"><?php echo $row['comments']?> Comment<?php echo $row['comments'] > 1 ? 's' : ''?></a>
        </div>
    </div>
    <?php } ?>
    <?php if(count($posts) < 1) { ?>
    <div class="index_singleListing index_singleListingEmpty"><?php echo MSG_NO_DATA_FOUND?></div>            
    <?php 
    }
}

/**
* Change enter to html (<br> tag)
* 
* @param mixed $content
*/
function render_enter_to_br($content) {
    return str_replace("\n", "<br />", $content);
}