<?php
/**
* Footer Menu for logged users
*/
if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
} 
if( $userID = buckys_is_logged_in() ){
    $userBasicInfo = BuckysUser::getUserBasicInfo($userID);    
?>
<div id="fixed_footer">
    <ul id="footer_menu"><!-- Begin Footer Menu -->
        
        <li class="home_button"><a href="/"></a></li><!-- This Item is an Image Home-->
        
        <li class="has-submenu"><a href="/account.php" class="p_menu">Account</a><!-- 1 Columns Menu Item -->
            <div class="submenu">
                <a href="/account.php" class="redLinks">Stream</a>
                <br />
                <a href="/messages_inbox.php" class="redLinks">Messages</a>
                <?php
                    $newMsgNum = BuckysMessage::getNumOfNewMessages( $userID );                    
                ?>
                <a href="/messages_inbox.php" class="<?php echo $newMsgNum > 0 ? 'highlighted' : ''?>">Inbox<?php echo $newMsgNum > 0 ? ' (' . $newMsgNum . ')' : ''?></a>
                <a href="/messages_sent.php">Sent</a>
                <a href="/messages_trash.php">Trash</a>
                <a href="/messages_compose.php">Compose</a>
                <br />
                <a href="/photo_manage.php" class="redLinks">Pictures</a>
                <a href="/photo_add.php">Add Photo</a>
                <a href="/photo_albums.php">Manage Albums</a>
                <a href="/photo_manage.php">Manage Photos</a>
                <a href="/photos.php?user=<?php echo $userID?>">View All</a>
                <br />
                <a href="/info_basic.php" class="redLinks">Information</a>
                <a href="/info_basic.php">Basic Info</a>
                <a href="/info_contact.php">Contact</a>
                <a href="/info_education.php">Education</a>
                <a href="/info_employment.php">Employment</a>
                <a href="/info_links.php">Links</a>
                <br />
                <?php
                    $newFriendRequestsNum = BuckysFriend::getNewFriendRequests($userID);
                ?>
                <a href="/myfriends.php" class="redLinks">Friends</a>
                <a href="/myfriends.php">All</a>
                <a href="/myfriends.php?type=requested" class="<?php echo $newFriendRequestsNum > 0 ? 'highlighted' : ''?>">Requests<?php echo $newFriendRequestsNum > 0 ? ' (' . $newFriendRequestsNum . ')' : ''?></a>
                <a href="/myfriends.php?type=pending">Pending</a>
                <br />
                <a href="/notify.php" class="redLinks">Settings</a>
                <a href="/notify.php">Notification Settings</a> 
                <a href="/change_password.php">Change Password</a>
                <a href="/delete_account.php">Delete Account</a> 
            </div>
        </li>
        
        <li class="has-submenu"><a href="/profile.php?user=<?php echo $userID?>" class="p_menu">Profile</a>
            <div class="submenu" id="profile-submenu">
                <a href="/profile.php?user=<?php echo $userID?>"><img class="viewProfileImg" src="<?php echo BuckysUser::getProfileIcon($userBasicInfo) ?>"></a>
                <span>
                    <a class="highlighted" href="/profile.php?user=<?php echo $userID?>"><?php echo $userBasicInfo['firstName'] . " " . $userBasicInfo['lastName']?></a>                
                    <br />
                    <a href="/profile.php?user=<?php echo $userID?>">view profile...</a>
                </span>
            </div>
        </li> 
		
		<!-- Forum -->
        <li class="has-submenu"><a href="/forum" class="p_menu">Forum</a><!-- 1 Columns Menu Item -->
            <div class="submenu">
                <a href="/forum" class="redLinks">Forum Home</a>
                <a href="/forum/create_topic.php">Create a New Topic</a>
                <a href="/forum/myposts.php">My Posts</a>
				<a href="/forum/recent_activity.php">Recent Activity</a>				
            </div>
        </li>
		<!-- End Forum -->		

		<!-- Trade -->
        <li class="has-submenu"><a href="/trade/index.php" class="p_menu">Trade</a><!-- 1 Columns Menu Item -->
            <div class="submenu">
				<a href="/trade/additem.php" class="redLinks">Add Item</a>
				<br />
                <a href="/trade/available.php" class="redLinks">My Items</a>
                <a href="/trade/available.php">Available</a>
                <a href="/trade/available.php?type=expired">Expired</a>
                <br />
                <a href="/trade/offer_received.php" class="redLinks">Offers</a>
                <a href="/trade/offer_received.php">Received</a>
                <a href="/trade/offer_declined.php">Declined</a>
                <br />	
                <a href="/trade/traded.php" class="redLinks">Trades</a>
                <a href="/trade/traded.php">Completed Trades</a>
                <a href="/trade/traded.php?type=history">Trade History</a>
                <br />	
                <a href="/trade/notify.php" class="redLinks">Account Settings</a>
<!--                <a href="/trade/notify.php">Notification Settings</a>	-->
				<a href="/trade/shipping_info.php">Shipping Information</a>				
            </div>
        </li>
		<!-- End trade -->
		
		<!-- Vote -->
        <li class="has-submenu"><a href="moderator.php?type=community" class="p_menu">Vote</a><!-- 1 Columns Menu Item -->
            <div class="submenu">
                <a href="/moderator.php?type=community" class="redLinks">Vote</a>
                <a href="/moderator.php?type=community">Community Moderator</a>
                <a href="/moderator.php?type=forum">Forum Moderator</a>			
            </div>
        </li>
		<!-- End Vote -->			
		
        <!--<li class="has-submenu"><a href="#" class="p_menu">More</a>
            <div class="submenu submenu-dropdown">
                <div id="menuItem"><a href="#" id="private_messenger_nav">Private Messenger</a></div>                
                 <div id="menuItem"><a href="/forum">Forum</a></div> 
            </div>
        </li>        -->
		
        
        <?php
            $newMessages = BuckysPrivateMessenger::getNewMessageCount($userID);
        ?>
        <li id="private_messenger_li" class="has-submenu"><a href="#" class="privateMessengerButtonText">Private Messenger</a><?php if($newMessages > 0){?><span id="total-new-msg-count" class="new-msg-count"><?php echo $newMessages?></span><?php } ?><span id="messenger_minimized">Messenger <a href="#"></a></span></li>        
        
		<!-- Search -->
        <li><a href="/search.php" class="p_menu">Search</a><!-- 1 Columns Menu Item -->
        </li>
		<!-- End Search -->		
		
		<li id="lft-last-li">
            <a href="/credits.php"><b>&#946; <?php echo number_format($BUCKYS_GLOBALS['user']['credits'], 2) ?></b></a>
        </li>
        <li class="right"><a href="/logout.php">Log Out</a></li>
        <!-- notifications Icons -->
        <li class="rightIcons"> 
            <?php
                $notifications = BuckysActivity::getNumberOfNotifications($userID);
                if($notifications > 0){
                    $notificationsList = BuckysActivity::getNotifications($userID, 5, 1);
            ?>
            <span class="notificationLinks" id="my-notifications-icon">
                <span class="notification-count"><?php echo $notifications; ?></span>
                    <span class="dropDownNotificationList">
                        <?php foreach($notificationsList as $row){ 
                            echo BuckysActivity::getActivityHTML($row, $userID);
                        } ?>
                        <a class="view-detail-links" href="/account.php">                        
                            View All Notifications
                        </a>       
                    </span>
            </span>
            <?php } ?>
            
            <!-- Start Friend Request Notification -->
            <?php
                $objFriend = new BuckysFriend();
                $friendRequestsNum = $objFriend->getNewFriendRequests($BUCKYS_GLOBALS['user']['userID']);
                if($friendRequestsNum > 0)
                {
                    $friendRequests = $objFriend->getReceivedRequests($BUCKYS_GLOBALS['user']['userID']);                    
            ?>
            <span class="notificationLinks" id="friend-notifications-icon">
                <span class="dropDownNotificationList">                    
                    <?php foreach($friendRequests as $row){ ?>
                    <span class="singleNotificationListItem">
                        <img src="<?php echo BuckysUser::getProfileIcon($row) ?>" class="dropDownNotificationImages" />
                        <span class="redBold"><?php echo $row['fullName'] ?></span> sent you new friend request.
                        <br />
                        <a href="/myfriends.php?type=requested&action=accept&friendID[]=<?php echo $row['userID']?>" class="redButton">Approve</a>
                        <a href="/myfriends.php?type=requested&action=decline&friendID[]=<?php echo $row['userID']?>" class="redButton">Decline</a>
                        <br clear="all" />
                    </span>
                    <?php } ?>
                    <a class="view-detail-links" href="/myfriends.php?type=requested">                        
                        View All Requests
                    </a>       
                </span>
            </span>
            <?php
                }
            ?>
            <!-- End Friend Request Notification -->
            
            <!-- Start Forum Notifications -->
            <?php
                $objForumNotify = new BuckysForumNotification();
                $newNoticeCount = $objForumNotify->getNumOfNewNotifications($userID);
                if($newNoticeCount > 0){
                    $newNotices = $objForumNotify->getNewNotifications($userID);
            ?>
            <span class="notificationLinks" id="forum-notifications-icon">
                <span class="dropDownNotificationList">                    
                    <?php foreach($newNotices as $idx=>$row){ ?>                    
                      <?php if($row['activityType'] == 'topic_approved' || $row['activityType'] == 'reply_approved'){ ?>
                        <a class="singleNotificationListItem" href="/forum/topic.php?id=<?php echo $row['activityType'] == 'topic_approved' ? $row['objectID'] : $row['actionID'] ?>">
                            <span>                 
                            <img src="<?php echo BuckysUser::getProfileIcon($BUCKYS_GLOBALS['user']['userID']) ?>" class="dropDownNotificationImages" />
                            <!--<span class="redBold"><?php echo $BUCKYS_GLOBALS['user']['firstName'] . " " . $BUCKYS_GLOBALS['user']['lastName'] ?></span>                            
                            <br />-->
                            <?php if($row['activityType'] == 'topic_approved'){ ?>
                            Your topic "<?php echo buckys_truncate_string($row['topicTitle'], 30)?>" has been approved.
                            <?php }else{ ?>
                            Your reply to the topic "<?php echo buckys_truncate_string($row['topicTitle'], 30)?>" has been approved.
                            <?php } ?>
                            <br />
                            <span class="createdDate"><?php echo buckys_format_date($row['createdDate']) ?></span>                                                        
                            <br clear="all" />
                            </span>
                        </a>
                      <?php }else{ ?>
                        <a class="singleNotificationListItem" href="/forum/topic.php?id=<?php echo $row['topicID'] ?>">
                            <span>                 
                            <img src="<?php echo BuckysUser::getProfileIcon(array('userID' => $row['replierID'], 'thumbnail' => $row['rThumbnail'])) ?>" class="dropDownNotificationImages" />
                            <span class="redBold"><?php echo $row['rName'] ?></span>                                                        
                            replied to <?php echo $row['activityType'] == "replied_to_topic" ? "your" : "the" ?> topic "<?php echo buckys_truncate_string($row['topicTitle'], 30)?>".
                            <br />
                            <span class="createdDate"><?php echo buckys_format_date($row['createdDate']) ?></span>                                                        
                            <br clear="all" />
                            </span>
                        </a>
                      <?php } ?>
                        
                    
                    <?php 
                    } 
                    ?>               
                    <a class="view-detail-links" href="/forum">                        
                        Go to Forum
                    </a>       
                </span>
            </span>            
            <?php } ?>
            <!-- End Forum Notifications -->
            
            <?php
                $newMsgNum = BuckysMessage::getNumOfNewMessages( $userID );
                if($newMsgNum){
                $newMails = BuckysMessage::getReceivedMessages($userID, 1, 'unread');
            ?>
            <span class="notificationLinks <?php echo $newMsgNum > 0 ? "new-mails" : "no-mails" ?>" id="emails-icon">            
                <span class="dropDownNotificationList">
                    <?php foreach($newMails as $idx=>$row){ ?>
                    <a class="singleNotificationListItem" href="/messages_read.php?message=<?php echo $row['messageID']?>">
                        <span>
                        <img src="<?php echo BuckysUser::getProfileIcon($row['sender']) ?>" class="dropDownNotificationImages" />
                        <span class="redBold"><?php echo $row['senderName'] ?></span>
                        <span style="font-size:11px;color:#cccccc;float:right;"><?php echo buckys_format_date($row['created_date']) ?></span>                                                        
                        <br />
                        <?php
                            echo substr($row['body'], 0, 120);
                            if(strlen($row['body']) > 120)
                                echo "...";
                        ?>
                        </span>
                    </a>
                    <?php 
                        if($idx > 4)
                            break;
                    } 
                    ?>                    
                    <?php if($newMsgNum < 1){ ?>
                    <span class="nodata">There is not a new message.</span>
                    <?php } ?>
                    <a class="view-detail-links" href="/messages_inbox.php">                        
                        Go to Inbox
                    </a>                            
                </span>
            </span>
            <?php
                }
            ?>
            
            <?php
                $tradeNotiIns = new BuckysTradeNotification();
                $newMsgNum = $tradeNotiIns->getNumOfNewMessages($userID);
                
                if($newMsgNum){
                $newMails = $tradeNotiIns->getReceivedMessages($userID);                
                
            ?>
            <span class="notificationLinks" id="trade-notify-icon">
                <span class="dropDownNotificationList">
                    <?php 
                        foreach($newMails as $idx=>$row){ 
                            
                            $htmlBodyContent = '';
                            
                            if ($row['activityType'] == BuckysTradeNotification::ACTION_TYPE_OFFER_ACCEPTED) {
                                $actionUrl = '/trade/traded.php';
                                
                                $htmlBodyContent .= sprintf('<span class="redBold">%s</span>', $row['senderName']);
                                $htmlBodyContent .= sprintf('<span> accepted your </span>');
                                $htmlBodyContent .= sprintf('<span class="redBold">offer</span>');
                            }
                            else if ($row['activityType'] == BuckysTradeNotification::ACTION_TYPE_OFFER_DECLINED) {
                                $actionUrl = '/trade/offer_declined.php';
                                
                                $htmlBodyContent .= sprintf('<span class="redBold">%s</span>', $row['senderName']);
                                $htmlBodyContent .= sprintf('<span> declined your </span>');
                                $htmlBodyContent .= sprintf('<span class="redBold">offer</span>');
                                
                            }
                            else if ($row['activityType'] == BuckysTradeNotification::ACTION_TYPE_OFFER_RECEIVED) {
                                $actionUrl = '/trade/offer_received.php';
                                
                                $htmlBodyContent .= sprintf('<span class="redBold">%s</span>', $row['senderName']);
                                $htmlBodyContent .= sprintf('<span> made you an </span>');
                                $htmlBodyContent .= sprintf('<span class="redBold">offer</span>');
                                
                            }
                            else if ($row['activityType'] == BuckysTradeNotification::ACTION_TYPE_FEEDBACK) {
                                $actionUrl = '/trade/feedback.php?user=' . $userID;
                                
                                $htmlBodyContent .= sprintf('<span class="redBold">%s</span>', $row['senderName']);
                                $htmlBodyContent .= sprintf('<span> left you </span>');
                                $htmlBodyContent .= sprintf('<span class="redBold">feedback</span>');
                                
                                $row['feedback'] = strip_tags($row['feedback']);
                                if (strlen($row['feedback']) > 120) {
                                    $row['feedback'] = substr($row['feedback'], 0, 120) . '...';
                                }                                
                                $htmlBodyContent .= sprintf('<span>:<br/> %s</span>', $row['feedback']);
                                
                            }
                            else {
                                $actionUrl = '#'; //not sure if we can be here.
                            }
                    ?>
                    <a class="singleNotificationListItem" href="<?php echo $actionUrl?>">
                        <img src="<?php echo BuckysUser::getProfileIcon($row['senderID']) ?>" class="dropDownNotificationImages" />
                        <?php echo $htmlBodyContent;?>                        
                    </a>
                    <?php 
                        if($idx > 4)
                            break;
                    } 
                    ?>                    
                    <?php if($newMsgNum < 1){ ?>
                    <span class="nodata">There is not a new message.</span>
                    <?php } ?>
                    <a class="view-detail-links" href="/trade/available.php">
                        My Trading Account
                    </a>                            
                </span>
            </span>
            <?php
                }
            ?>
            
            
            
        </li>
    </ul>    
</div>
<div id="messenger_settings_wrapper">
    <div id="messenger_settings_box">
        <div class="box_nav_row">
            <a href="#" class="close_box_link">&#935;</a>
        </div>
      <form name="messenger_settings_form" id="messenger_settings_form" method="post" action="/">        
        <h3>Who can message me:</h3>
        <label for="messenger_privacy_all"><input type="radio" name="messenger_privacy" id="messenger_privacy_all" <?php if($userBasicInfo['messenger_privacy']== 'all'){ ?>checked="checked"<?php }?> value="all" /> Everyone except the people on my blocklist</label>
        <div id="block-lists">
            <label>Block List:</label>
            <span class="btn-row" id="blocked-users"><input type="text" id="block-username" class="input" /><input type="submit" value="Add" class="redButton" /></span>
            <?php
                $blockList = BuckysPrivateMessenger::getBlockLists($userID);
            ?>
            <ul id="block_list">
                <?php foreach($blockList as $row){ ?>
                <li data-id="<?php echo $row['userID']?>"><img src="<?php echo BuckysUser::getProfileIcon($row)?> " /> <?php echo $row['name']?></li>
                <?php } ?>
            </ul>            
        </div>
        <a href="#" class="remove-from-blocklist">Remove</a>
        <label for="messenger_privacy_buddy"><input type="radio" name="messenger_privacy" id="messenger_privacy_buddy" <?php if($userBasicInfo['messenger_privacy'] == 'buddy'){ ?>checked="checked"<?php }?> value="buddy" /> Only the people on my buddy list</label>        
        <?php render_loading_wrapper(); ?>
      </form>
    </div>
</div>
<?php
    }
?>

