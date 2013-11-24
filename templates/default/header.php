<?php
/**
* Header.php
*/
if($userID = buckys_is_logged_in())
{
    
    switch($BUCKYS_GLOBALS['headerType']) {
        
        case 'trade':
        
            ?>
            <header id="main_header">
                <div id="rightAlignLinks">      
                    <a href="/trade/additem.php" class="headerLinks">Add Item</a> |
                    <a href="/trade/available.php" class="headerLinks">Control Panel</a>
                </div>
                <a href="index.php"><img src="/images/mainLogoTrade.png"></a>
            </header>
        <?php
            
            break;
        case 'forum':
        
            ?>
            <header id="main_header">
                <div id="rightAlignLinks">                          
                <?php if(buckys_check_user_acl(USER_ACL_ADMINISTRATOR) || BuckysModerator::isModerator($BUCKYS_GLOBALS['user']['userID'], MODERATOR_FOR_FORUM)){ ?>                
                <?php
                    $pendingTopics = BuckysForumTopic::getTotalNumOfTopics('pending');
                    $pendingReplies = BuckysForumReply::getTotalNumOfReplies(null, 'pending');
                ?>
                    <?php if($pendingTopics > 0){ ?>
                    <a href="/forum/pending_topics.php" class="headerLinksBold">Pending Topics (<?php echo $pendingTopics?>)</a> |
                    <?php } ?>
                    <?php if($pendingReplies > 0){ ?>
                    <a href="/forum/pending_replies.php" class="headerLinksBold">Pending Replies (<?php echo $pendingReplies?>)</a> |
                    <?php } ?>
                <?php } ?>
                    <a href="/forum/create_topic.php" class="headerLinks">Create a New Topic</a> |
                    <a href="/forum/myposts.php" class="headerLinks">My Posts</a> |
					<a href="/forum/recent_activity.php" class="headerLinks">Recent Activity</a>
                </div>
                <a href="index.php"><img src="/images/mainLogoForum.png"></a>
            </header>
        <?php
            
            break;
        default:
        
            $newMessages = BuckysMessage::getNumOfNewMessages($userID);
        ?>
            <header id="main_header">
                <div id="rightAlignLinks">
                    <a href="/account.php" class="headerLinks">Account</a> |            
                    <a href="/messages_inbox.php" class="headerLinks<?php echo $newMessages > 0 ? 'Bold' : ''?>">Messages<?php echo $newMessages > 0 ? ' ('.$newMessages.') ' : ''?></a> |
                    <a href="/profile.php?user=<?php echo $userID?>" class="headerLinks">Profile</a>
                </div>
                <a href="index.php"><img src="/images/mainLogo.png"></a>
            </header>
        <?php
    }

}else{
?>
    <header id="main_header">
        <div id="rightAlign">
            <?php if($BUCKYS_GLOBALS['content'] != 'register'){ ?>
            <form style="padding-top: 0px;" method="post" action="/login.php"> 
                e-mail: <input type="text" name="email" maxlength="60" class="inputHeader" /> &nbsp;
                password: <input type="password" name="password" maxlength="20" class="inputHeader" /> &nbsp;
                <input type="submit" name="login_submit" class="redButton" value="Log In" />
            </form>
            <?php } ?>
        </div>
        <?php if($BUCKYS_GLOBALS['headerType'] == 'trade') :?>
            <a href="index.php"><img src="/images/mainLogoTrade.png"></a>
        <?php elseif($BUCKYS_GLOBALS['headerType'] == 'forum') :?>
            <a href="index.php"><img src="/images/mainLogoForum.png"></a>
        <?php else :?>
            <a href="index.php"><img src="/images/mainLogo.png"></a>
        <?php endif;?>
    </header>
<?php
}
