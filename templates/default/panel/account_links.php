<?php
/**
* User Account Links
*/
if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
}
//Getting Current User ID if $userID is not set  
if( !isset($userID) )
{
    $userID = buckys_is_logged_in();
}
//If the user is logged in, show account links
if($userID){
?>
<aside id="main_aside">
    <span class="titles">Account</span>
        
    <a href="/account.php" class="accountLinks" style="margin-top:10px;">Stream</a>
        
    <a href="/messages_inbox.php" class="accountLinks">Messages</a>
    <?php
        $newMsgNum = BuckysMessage::getNumOfNewMessages( $userID );                    
    ?>
    <a href="/messages_inbox.php" class="accountSubLinks<?php echo $newMsgNum > 0 ? 'Bold' : ''?>">Inbox<?php echo $newMsgNum > 0 ? ' (' . $newMsgNum . ')' : ''?></a><br />
    <a href="/messages_sent.php" class="accountSubLinks">Sent</a> <br/>
    <a href="/messages_trash.php" class="accountSubLinks">Trash</a> <br/>
    <a href="/messages_compose.php" class="accountSubLinks">Compose</a> <br/>
        
    <a href="/photo_manage.php" class="accountLinks">Pictures</a>
    <a href="/photo_add.php" class="accountSubLinks">Add Photo</a> <br/>
    <a href="/photo_albums.php" class="accountSubLinks">Manage Albums</a> <br/>
    <a href="/photo_manage.php" class="accountSubLinks">Manage Photos</a> <br/>
    <a href="/photos.php?user=<?php echo $userID?>" class="accountSubLinks">View All</a> <br/>                
    <a href="/info_basic.php" class="accountLinks">Information</a>
    <a href="/info_basic.php" class="accountSubLinks">Basic Info</a> <br/>
    <a href="/info_contact.php" class="accountSubLinks">Contact</a> <br/>
    <a href="/info_education.php" class="accountSubLinks">Education</a> <br/>
    <a href="/info_employment.php" class="accountSubLinks">Employment</a> <br/>
    <a href="/info_links.php" class="accountSubLinks">Links</a> <br/>
    
    <a href="/myfriends.php" class="accountLinks">Friends</a>
    <a href="/myfriends.php" class="accountSubLinks">All</a> <br/>
    <?php
        $newFriendRequestsNum = BuckysFriend::getNewFriendRequests($userID);
    ?>
    <a href="/myfriends.php?type=requested" class="accountSubLinks<?php echo $newFriendRequestsNum > 0 ? 'Bold' : ''?>">Requests<?php echo $newFriendRequestsNum > 0 ? ' (' . $newFriendRequestsNum . ')' : ''?></a> <br/>
    <a href="/myfriends.php?type=pending" class="accountSubLinks">Pending</a> <br/>
	
    <!--
    <a href="/moderator.php?type=community" class="accountLinks">Vote</a>
    <a href="/moderator.php?type=community" class="accountSubLinks">Community Moderator</a><br />
    <a href="/moderator.php?type=forum" class="accountSubLinks">Forum Moderator</a><br />
    <a href="/moderator.php?type=trade" class="accountSubLinks">Trade Moderator</a><br />
	-->
    
    <?php
        
    ?>
    <a href="/page.php?action=add" class="accountLinks">Pages</a>
    <a href="/page.php?action=add" class="accountSubLinks">Create New Page</a><br />
    <?php 
        //Get my created pages link
        $pageIns = new BuckysPage();
        $pageList = $pageIns->getPagesByUserId($userID);
        
        if (count($pageList) > 0) {
            foreach($pageList as $pageD) {
                echo sprintf('<a href="/page.php?pid=%d" class="accountSubLinks">%s</a><br/>', $pageD['pageID'], $pageD['title']);
            }
        }
        
        
    ?>
    
    <!-- Controll Panel-->
        <?php if(BuckysModerator::isModerator($userID, MODERATOR_FOR_COMMUNITY)){ ?>
            <?php
                $reportedPosts = BuckysReport::getReportedObjectCount('post');
                $reportedComments = BuckysReport::getReportedObjectCount('comment');
                $reportedMessages = BuckysReport::getReportedObjectCount('message');
            ?>
            <a href="/reported.php?type=post" class="accountLinks">Moderator Panel</a>
            <a href="/reported.php?type=post" class="accountSubLinks<?php echo $reportedPosts > 0 ? 'Bold' : ''?>">Reported Posts<?php echo $reportedPosts > 0 ? ' (' . $reportedPosts . ')' : ''?></a><br />
            <a href="/reported.php?type=comment" class="accountSubLinks<?php echo $reportedComments > 0 ? 'Bold' : ''?>">Reported Comments<?php echo $reportedComments > 0 ? ' (' . $reportedComments . ')' : ''?></a><br />
            <a href="/reported.php?type=message" class="accountSubLinks<?php echo $reportedMessages > 0 ? 'Bold' : ''?>">Reported Messages<?php echo $reportedMessages > 0 ? ' (' . $reportedMessages . ')' : ''?></a><br />
        <?php } ?>
        <?php if(BuckysModerator::isModerator($userID, MODERATOR_FOR_TRADE)){ ?>
            <!-- Trade Moderator Links -->                
        <?php } ?>
        <?php if(BuckysModerator::isModerator($userID, MODERATOR_FOR_FORUM)){ ?>
            <?php
                $reportedTopics = BuckysReport::getReportedObjectCount('topic');
                $reportedReplies = BuckysReport::getReportedObjectCount('reply');
            ?>
            <a href="/reported.php?type=topic" class="accountLinks">Moderator Panel</a>
            <a href="/reported.php?type=topic" class="accountSubLinks<?php echo $reportedTopics > 0 ? 'Bold' : ''?>">Reported Topics<?php echo $reportedTopics > 0 ? ' (' . $reportedTopics . ')' : ''?></a><br />
            <a href="/reported.php?type=reply" class="accountSubLinks<?php echo $reportedReplies > 0 ? 'Bold' : ''?>">Reported Replies<?php echo $reportedReplies > 0 ? ' (' . $reportedReplies . ')' : ''?></a><br />
        <?php } ?>
        <?php if(buckys_check_user_acl(USER_ACL_ADMINISTRATOR)){ ?>
        <a href="/banned_users.php" class="accountLinks">Control Panel</a>
        <?php
            $bannedUsers = BuckysBanUser::getBannedUsersCount();
        ?>
        <a href="/banned_users.php" class="accountSubLinks<?php echo $bannedUsers > 0 ? 'Bold' : ''?>">Banned Users<?php echo $bannedUsers > 0 ? ' (' . $bannedUsers . ')' : ''?></a><br />
        <?php } ?>
        
        <a href="/notify.php" class="accountLinks">Settings</a>
        <a href="/notify.php" class="accountSubLinks">Notification Settings</a> <br/>
        <a href="/change_password.php" class="accountSubLinks">Change Password</a> <br/>
        <a href="/delete_account.php" class="accountSubLinks">Delete Account</a> <br/>
		
</aside>
<?php
}
?>