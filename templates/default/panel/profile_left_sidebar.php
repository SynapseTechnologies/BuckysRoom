<?php
/**
* Profile Left Sidebar
*/
if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
} 
global $userData, $profileID, $canViewPrivate, $userID; 
?>
<aside id="main_aside" style="overflow:auto"> <!-- 241px -->
    <span class="titles"><?php echo $userData['firstName'] . " " . $userData['lastName']; ?></span>
    <br/>        
    <?php render_profile_link($userData, 'mainProfilePic'); ?>
    <br/>
    <a href="/photos.php?user=<?php echo $userData['userID']?>">View All Photos (<?php echo BuckysPost::getNumberOfPhotosByUserID($userData['userID']) ?>)</a> <br/>
    <!-- Friend Links -->
    <?php
    if( buckys_not_null($userID) && $userID != $profileID) { //If this is not current logged user, Show Friends, Message Links
        //Show Friend Links
        if( ($fid = BuckysFriend::isFriend($userID, $profileID)) )
        {
            ?>
            <a href="/myfriends.php?action=unfriend&friendID=<?php echo $profileID?>&return=<?php echo base64_encode("/profile.php?user=" . $profileID) ?>">Unfriend</a> <br/>
            <?php
        }else{
            //Check Friend Request
            if( ($fid = BuckysFriend::isSentFriendRequest($userID, $profileID)) ) 
            {
                ?>
                <a href="/myfriends.php?action=delete&friendID=<?php echo $profileID?>&return=<?php echo base64_encode("/profile.php?user=" . $profileID) ?>">Delete Friend Request</a> <br/>
                <?php
            }else if( ($fid = BuckysFriend::isSentFriendRequest($profileID, $userID)) ){
                ?>
                <a href="/myfriends.php?action=accept&friendID=<?php echo $profileID?>&return=<?php echo base64_encode("/profile.php?user=" . $profileID) ?>">Approve Friend Request</a> <br/>
                <a href="/myfriends.php?action=decline&friendID=<?php echo $profileID?>&return=<?php echo base64_encode("/profile.php?user=" . $profileID) ?>">Decline Friend Request</a> <br/>
                <?php
            }else{
                ?>
                <a href="/myfriends.php?action=request&friendID=<?php echo $profileID?>&friendIDHash=<?php echo buckys_encrypt_id($profileID)?>&return=<?php echo base64_encode("/profile.php?user=" . $profileID) ?>">Send Friend Request</a> <br/>
                <?php
            }
        }
        
        //Show Message
        ?>
        <a href="/messages_compose.php?to=<?php echo $profileID?>">Send Message</a> <br/>
        <?php        
        //For Community Moderator
        if(BuckysModerator::isModerator($userID, MODERATOR_FOR_COMMUNITY) && !BuckysBanUser::isBannedUser($profileID)){
            ?>
            <a href="/profile.php?action=ban-user&userID=<?php echo $profileID?>" onclick="return confirm('<?php echo MSG_ARE_YOU_SURE_WANT_TO_BAN_THIS_USER ?>')">Banned User</a><br />
            <?php
        }
        //For Administrator
        if(buckys_check_user_acl(USER_ACL_ADMINISTRATOR))
        {
            ?>
            <a href="/banned_users.php?action=deletebyid&userID=<?php echo $profileID?>" onclick="return confirm('<?php echo MSG_ARE_YOU_SURE_WANT_TO_DELETE_THIS_ACCOUNT ?>')">Delete Account</a><br />
            <?php
        }
    }
    ?>        
    <!-- User About Section -->
    <br />
    <?php
        //Check it has any data for About Section
        if( 
            (buckys_not_null($userData['gender']) && ($userData['gender_visibility'] || $canViewPrivate)) ||
            (($userData['birthdate'] != '0000-00-00') && ($userData['birthdate_visibility'] || $canViewPrivate)) ||
            (($userData['relationship_status'] > 0) && ($userData['relationsihp_status_visibility'] || $canViewPrivate)) ||
            (buckys_not_null($userData['political_views']) && ($userData['political_views_visibility'] || $canViewPrivate)) ||
            (buckys_not_null($userData['religion']) && ($userData['religion_visibility'] || $canViewPrivate)) ||
            (buckys_not_null($userData['birthplace']) && ($userData['birthplace_visibility'] || $canViewPrivate)) ||
            (buckys_not_null($userData['current_city']) && ($userData['current_city_visibility'] || $canViewPrivate)) ||
            (buckys_not_null($userData['timezone']) && ($userData['timezone_visibility'] || $canViewPrivate))
         ){
    ?>
    <div id="user-about-box" class="info-box">
        <h3>About <?php if($userData['userID'] == $userID){?><a href="/info_basic.php" class="edit-info">(edit)</a><?php } ?></h3>
        <?php if( buckys_not_null($userData['gender']) && ($userData['gender_visibility'] || $canViewPrivate) ){ ?>
        <p><label>Gender:</label> <?php echo $userData['gender'] ?> </p>
        <?php } ?>
        <?php if( buckys_not_null($userData['birthdate']) && $userData['birthdate'] != '0000-00-00' && ($userData['birthdate_visibility'] || $canViewPrivate) ){ ?>
        <p><label>Birthday:</label> <?php echo date("F j, Y", strtotime($userData['birthdate'])) ?> </p>
        <?php } ?>
        <?php if( $userData['relationship_status'] > 0 && ($userData['relationship_status_visibility'] || $canViewPrivate) ){ ?>
        <p>
            <label>Relationship Status:</label>  
            <?php
                switch($userData['relationship_status'])
                {
                    case 1:
                        echo 'Single';
                        break;
                    case 2:
                        echo 'In a Relationship';
                        break;
                }
            ?> 
        </p>
        <?php } ?>
        <?php if( buckys_not_null($userData['religion']) && ($userData['religion_visibility'] || $canViewPrivate) ){ ?>
        <p><label>Religion:</label> <?php echo $userData['religion'] ?> </p>
        <?php } ?>
        <?php if( buckys_not_null($userData['political_views']) && ($userData['political_views_visibility'] || $canViewPrivate) ){ ?>
        <p><label>Political Views:</label> <?php echo $userData['political_views'] ?> </p>
        <?php } ?>
        <?php if( buckys_not_null($userData['birthplace']) && ($userData['birthplace_visibility'] || $canViewPrivate) ){ ?>
        <p><label>Birthplace:</label> <?php echo $userData['birthplace'] ?> </p>
        <?php } ?>
        <?php if( buckys_not_null($userData['current_city']) && ($userData['current_city_visibility'] || $canViewPrivate) ){ ?>
        <p><label>Current City:</label> <?php echo $userData['current_city'] ?> </p>
        <?php } ?>            
        <?php /*if( buckys_not_null($userData['timezone']) && ($userData['timezone_visibility'] || $canViewPrivate) ){ ?>
        <p><label>Time zone:</label> <?php echo substr($userData['timezone'], 0, strpos($userData['timezone'], ') ') + 2) ?> </p>
        <?php }*/ ?>            
        
    </div>
    <?php
         }
    ?>
    <?php
        //Check the user has educations
        $hasEducations = false;
        foreach($userData['educations'] as $e)
        {
            if($canViewPrivate || $e['visibility'])
            {
                $hasEducations = true;
                break;
            }
        }
    ?>
    <?php if($hasEducations){ ?>
    <br >
    <div id="user-educations-box" class="info-box">
        <h3>Education <?php if($userData['userID'] == $userID){?><a href="/info_education.php" class="edit-info">(edit)</a><?php } ?></h3>
        <?php
            foreach($userData['educations'] as $e)
            {
                if($canViewPrivate || $e['visibility'])
                {
        ?>
            <p><label><?php echo $e['school']?></label>: <?php echo $e['start']?> - <?php echo $e['end']?></p>
        <?php
                }
            }
        ?>
    </div>
    <?php } ?>
    <!-- Employment Section -->
    <?php
        //Check the user has employments
        $hasEmployments = false;
        foreach($userData['employments'] as $e)
        {
            if($canViewPrivate || $e['visibility'])
            {
                $hasEmployments = true;
                break;
            }
        }
    ?>
    <?php if($hasEmployments){ ?>
    <br >
    <div id="user-employments-box" class="info-box">
        <h3>Employment <?php if($userData['userID'] == $userID){?><a href="/info_employment.php" class="edit-info">(edit)</a><?php } ?></h3>
        <?php
            foreach($userData['employments'] as $e)
            {
                if($canViewPrivate || $e['visibility'])
                {
        ?>
            <p><label><?php echo $e['employer']?></label>: <?php echo $e['start']?> - <?php echo $e['end']?></p>
        <?php
                }
            }
        ?>
    </div>
    <?php } ?>
    
    
    <!-- Followed Page Section-->
    <?php
        
        $pageFollowerIns = new BuckysPageFollower();
        $followedPageData = $pageFollowerIns->getPagesByFollowerID($profileID, 1, 10);
        
        if (count($followedPageData) > 0) {
    ?>
            <br >
            <div id="user-following-box" class="info-box">
                <h3>Following <a href="/follows.php?user=<?php echo $profileID;?>" class="edit-info">(view all)</a></h3>
                <?php
                    foreach($followedPageData as $data)
                    {
                        render_pagethumb_link($data, 'followPageIcons');                        
                    }
                ?>
                <div class="clear"></div>
            </div>
    
    <?php
        }
    ?>
    
    
    <!-- User Links Section -->
    <?php
        //Check the user has links
        $hasLinks = false;
        foreach($userData['links'] as $row)
        {
            if($canViewPrivate || $row['visibility'])
            {
                $hasLinks = true;
                break;
            }
        }
    ?>
    <?php if($hasLinks){ ?>
    <br >
    <div id="user-links-box" class="info-box">
        <h3>Links <?php if($userData['userID'] == $userID){?><a href="/info_links.php" class="edit-info">(edit)</a><?php } ?></h3>
        <?php
            foreach($userData['links'] as $row)
            {
                if($canViewPrivate || $row['visibility'])
                {
                    if(strpos($row['url'], "http://") === false && strpos($row['url'], "https://") === false)
                        $row['url'] = "http://" . $row['url'];
        ?>
            <p><a href='<?php echo $row['url']?>' target="_blank"><?php echo $row['title']?></a></p>
        <?php
                }
            }
        ?>
    </div>
    <?php } ?>
    <!-- User Contact Information Section -->
    <br >
    <div id="user-contact-box" class="info-box">
        <h3>Contact <?php if($userData['userID'] == $userID){?><a href="/info_contact.php" class="edit-info">(edit)</a><?php } ?></h3>
        <?php if($userData['email_visibility'] != -1 && ($canViewPrivate || $userData['email_visibility'] == 1)){ ?>
        <p><label>E-mail:</label> <?php echo $userData['email']?></p>
        <?php } ?>
        <!-- Show Phone Numbers -->
    <?php
        if( 
            (buckys_not_null($userData['home_phone']) && ($userData['home_phone_visibilty'] || $canViewPrivate)) ||
            (buckys_not_null($userData['cell_phone']) && ($userData['cell_phone_visibilty'] || $canViewPrivate)) ||
            (buckys_not_null($userData['work_phone']) && ($userData['work_phone_visibilty'] || $canViewPrivate))
            
        ){
            echo "<br />";
            //Display Cell Phone
            if( (buckys_not_null($userData['cell_phone']) && ($userData['cell_phone_visibilty'] || $canViewPrivate)) )
            {
                ?>
                <p><label>Cell Phone:</label> <?php echo $userData['cell_phone']?></p>
                <?php
            }
            //Display Cell Phone
            if( (buckys_not_null($userData['home_phone']) && ($userData['home_phone_visibilty'] || $canViewPrivate)) )
            {
                ?>
                <p><label>Home Phone:</label> <?php echo $userData['home_phone']?></p>
                <?php
            }
            //Display Cell Phone
            if( (buckys_not_null($userData['work_phone']) && ($userData['work_phone_visibilty'] || $canViewPrivate)) )
            {
                ?>
                <p><label>Work Phone:</label> <?php echo $userData['work_phone']?></p>
                <?php
            }
            
    ?>
    <?php
        }
    ?>
        <!-- Show Messenger Account -->
    <?php
        //Check the user has messenger accounts
        $hasMessenger = false;
        foreach($userData['contact'] as $row)
        {
            if($canViewPrivate || $row['visibility'])
            {
                $hasMessenger = true;
                break;
            }
        }
        if($hasMessenger){
            ?>
            <br />
            <?php
            foreach($userData['contact'] as $row)
            {
                if($canViewPrivate || $row['visibility'])
                {
        ?>
            <p><label><?php echo $row['contact_type']?></label>: <?php echo $row['contact_name']?></p>
        <?php
                }
            }
        }
    ?>
        <!-- Show User Address -->
        <?php if( (buckys_not_null($userData['address1']) || buckys_not_null($userData['address2']) || buckys_not_null($userData['city']) || buckys_not_null($userData['state']) || buckys_not_null($userData['zip']) || buckys_not_null($userData['country'])) && ($userData['address_visibility'] || $canViewPrivate) )
        {
            echo "<br />";
            echo '<p>' . $userData['firstName'] . " " . $userData['lastName'] . '</p>';
            if( buckys_not_null($userData['address1']) )    
                echo '<p>' . $userData['address1'] . '</p>';
            if( buckys_not_null($userData['address2']) )    
                echo '<p>' . $userData['address2'] . '</p>';
            if( buckys_not_null($userData['city']) && buckys_not_null($userData['state']) )    
                echo '<p>' . $userData['city'] . ', ' . $userData['state'] . '</p>';
            else if( buckys_not_null($userData['city']) )
                echo '<p>' . $userData['city'] . '</p>';
            else if( buckys_not_null($userData['state']) )
                echo '<p>' . $userData['state'] . '</p>';
            if( buckys_not_null($userData['zip']) )    
                echo '<p>' . $userData['zip'] . '</p>';
            if( buckys_not_null($userData['country']) )    
                echo '<p>' . $userData['country'] . '</p>';
            
        }
        ?>
    </div>
    <!-- Likes -->
</aside>