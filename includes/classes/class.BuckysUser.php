<?php
/**
* User Class
*/

class BuckysUser
{
    
    const STATUS_USER_ACTIVE = 1; // User Active
    const STATUS_USER_BANNED = 0; // User Banned
    const STATUS_USER_DELETED = -1; // User Deleted
    
    
    public function getProfileIcon($user)
    {
        global $db;
        
        if(gettype($user) != 'array'){ //Getting From DB
            $query = $db->prepare("SELECT thumbnail FROM " . TABLE_USERS . " WHERE userID=%s", $user);
            $icon = $db->getVar($query);
            
            if(buckys_not_null($icon))
            {
                return DIR_WS_PHOTO . 'users/' . $user . '/resized/' . $icon;
            }else{
                return DIR_WS_IMAGE . 'defaultProfileImage.png';
            }
        }else if(gettype($user) == 'array'){ //Getting From Array
            if(buckys_not_null($user['thumbnail']))
            {
                return DIR_WS_PHOTO . 'users/' . $user['userID'] . '/resized/' . $user['thumbnail'];
            }else{
                return DIR_WS_IMAGE . 'defaultProfileImage.png';
            }
        }
    }
    
    
    
    public function getUserData($userID)
    {
        global $db;
        
        $query = $db->prepare("SELECT * FROM " . TABLE_USERS . " WHERE userID=%d", $userID);
        $data = $db->getRow($query);
        
        if( !$data )
            return null;
            
        //Getting Education
        $query = $db->prepare("SELECT * FROM " . TABLE_USERS_EDUCATIONS . " WHERE userID=%d ORDER BY `order` ASC", $userID);
        $rows = $db->getResultsArray($query);
        $data['educations'] = $rows;
        
        //Getting Employments
        $query = $db->prepare("SELECT * FROM " . TABLE_USERS_EMPLOYMENTS . " WHERE userID=%d ORDER BY `order` ASC", $userID);
        $rows = $db->getResultsArray($query);
        $data['employments'] = $rows;
        
        //Getting Links
        $query = $db->prepare("SELECT * FROM " . TABLE_USERS_LINKS . " WHERE userID=%d ORDER BY `order` ASC", $userID);
        $rows = $db->getResultsArray($query);
        $data['links'] = $rows;
        
        //Getting Contact
        $query = $db->prepare("SELECT * FROM " . TABLE_USERS_CONTACT . " WHERE userID=%d ORDER BY `order` ASC", $userID);
        $rows = $db->getResultsArray($query);
        $data['contact'] = $rows;
        
        return $data;
    }
    
    /**
    * Get User Basic Information by ID
    * 
    * @param int $userID
    * @return array
    */
    public function getUserBasicInfo($userID)
    {
        global $db;
        
        $query = $db->prepare("SELECT 
            userID, 
            firstName, 
            lastName, 
            thumbnail,
            email,
            gender, gender_visibility, 
            birthdate, birthdate_visibility, 
            relationship_status, relationship_status_visibility, 
            religion, religion_visibility ,
            political_views, political_views_visibility,
            birthplace, birthplace_visibility,
            current_city, current_city_visibility,
            messenger_privacy, show_messenger, timezone, timezone_visibility,
            user_type, user_acl_id, ua.Name as aclName, ua.Level as aclLevel, credits
            FROM " . TABLE_USERS . 
            " LEFT JOIN " . TABLE_USER_ACL . " as ua ON " . TABLE_USERS.".user_acl_id=ua.aclID" .
            " WHERE userID=%s", $userID);
        $data = $db->getRow($query);
        
        return $data;
    }
    
    /**
    * Get User ACL by ID
    * 
    * @param int $userID
    * @return array
    */
    public function getUserACL($userID)
    {
        global $db;
        
        $query = $db->prepare("SELECT user_type, user_acl_id, ua.Name as aclName, ua.Level as aclLevel FROM " . TABLE_USERS . 
            " LEFT JOIN " . TABLE_USER_ACL . " as ua ON " . TABLE_USERS.".user_acl_id=ua.aclID" .
            " WHERE userID=%s", $userID);
        $data = $db->getRow($query);
        
        return $data;
    }
    
    /**
    * Save User Basic Information
    * 
    * @param Int $userID
    * @param Array $data
    */
    public function saveUserBasicInfo($userID, $data)
    {
        global $db;
        
        if( $data['birthdate_month'] == '' || $data['birthdate_year'] == '' || $data['birthdate_day'] == '' )
            $birthdate = '0000-00-00';
        else
            $birthdate = date("Y-m-d", strtotime($data['birthdate_year'] . "-" . $data['birthdate_month'] . "-" . $data['birthdate_day']));
        
        $rs = $db->updateFromArray( TABLE_USERS, 
            array( 
                'firstName' => trim($data['firstName']),
                'lastName' => trim($data['lastName']), 
                'gender' => $data['gender'], 
                'gender_visibility' => $data['gender_visibility'], 
                'birthdate' => $birthdate, 
                'birthdate_visibility' => $data['birthdate_visibility'], 
                'relationship_status' => $data['relationship_status'], 
                'relationship_status_visibility' => $data['relationship_status_visibility'], 
                'religion' => $data['religion'], 
                'religion_visibility' => $data['religion_visibility'], 
                'political_views' => $data['political_views'], 
                'political_views_visibility' => $data['political_views_visibility'], 
                'birthplace' => $data['birthplace'], 
                'birthplace_visibility' => $data['birthplace_visibility'], 
                'current_city' => $data['current_city'], 
                'current_city_visibility' => $data['current_city_visibility'], 
                'timezone' => $data['timezone'], 
                'timezone_visibility' => 0 //$data['timezone_visibility'] 
                
            ), array('userID' => $userID) );
            
        return $rs;
    }
    
    
    /**
    * Get User Basic Information by ID
    * 
    * @param int $userID
    * @return array
    */
    public function getUserContactInfo($userID)
    {
        global $db;
        
        $query = $db->prepare("SELECT 
            userID, 
            email, email_visibility,
            home_phone, home_phone_visibility, 
            work_phone, work_phone_visibility, 
            cell_phone, cell_phone_visibility, 
            address1, address2,
            city,
            state,
            country,
            zip,
            address_visibility
            FROM " . TABLE_USERS . " WHERE userID=%s", $userID);
        $data = $db->getRow($query);
        
        //Getting Contact
        $query = $db->prepare("SELECT * FROM " . TABLE_USERS_CONTACT . " WHERE userID=%s ORDER BY `order` ASC", $userID);
        $rows = $db->getResultsArray($query);
        $data['contact'] = $rows;
        
        return $data;
    }
    
    /**
    * Get User Messenger Ids
    * 
    * @param Int $userID
    * @return Indexed
    */
    public function getUserMessengerNames($userID)
    {
        global $db;
        
        $query = $db->prepare( "SELECT * FROM " . TABLE_USERS_CONTACT . " WHERE userID=%s ORDER BY `order`", $userID );
        
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    /**
    * Check if the email address exists or not
    * 
    * @param mixed $email
    * @param mixed $userID
    */
    public function checkEmailDuplication($email, $userID = null)
    {
        global $db;
        
        if( !$userID )
            $query = $db->prepare( "SELECT userID FROM " . TABLE_USERS . " WHERE `email`=%s", $email );
        else
            $query = $db->prepare( "SELECT userID FROM " . TABLE_USERS . " WHERE `email`=%s AND userID != %s", $email, $userID );
        
        $res = $db->getVar($query);
        
        return $res ? true : false;
    }
    
    /**
    * Update User Fields
    * 
    * @param Int $userID
    * @param Array $data
    */
    public function updateUserFields($userID, $data)
    {
        global $db;
        
        $res = $db->updateFromArray( TABLE_USERS, $data, array('userID' => $userID) );
        
        return $res;
    }
    
    /**
    * Update User Messenger Names
    * 
    * @param Int $userID
    * @param Array $data
    */
    public function updateUserMessengerInfo($userID, $data)
    {
        global $db;
        
        //Remove old Data
        $query = "DELETE FROM " . TABLE_USERS_CONTACT . " WHERE userID='" . $userID . "'";
        $db->query($query);
        
        //Insert New Values
        for( $i = 0; $i < count($data); $i++ )
        {
            $row = $data[$i];
            $db->insertFromArray( TABLE_USERS_CONTACT, array('userID' => $userID, 'contact_name' => $row['name'], 'contact_type'=> $row['type'], 'visibility' => $row['visibility'], 'order' => $i + 1) );
        }
        return true;
    }
    
    /**
    * Get User Education by ID
    * 
    * @param int $userID
    * @return array
    */
    public function getUserEducations($userID)
    {
        global $db;
        
        //Getting Contact
        $query = $db->prepare("SELECT * FROM " . TABLE_USERS_EDUCATIONS . " WHERE userID=%s ORDER BY `order` ASC", $userID);
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    /**
    * Update User Education Information
    * 
    * @param Int $userID
    * @param Array $data
    */
    public function updateUserEducationInfo($userID, $data)
    {
        global $db;
        
        //Remove old Data
        $query = "DELETE FROM " . TABLE_USERS_EDUCATIONS . " WHERE userID='" . $userID . "'";
        $db->query($query);
        
        //Insert New Values
        for( $i = 0; $i < count($data); $i++ )
        {
            $row = $data[$i];
            $db->insertFromArray( TABLE_USERS_EDUCATIONS, array('userID' => $userID, 'school' => $row['name'], 'start'=> $row['start'], 'end' => $row['end'], 'visibility' => $row['visibility'], 'order' => $i + 1) );
        }
        return true;
    }
    
    /**
    * Get User Employment History by ID
    * 
    * @param int $userID
    * @return array
    */
    public function getUserEmploymentHistory($userID)
    {
        global $db;
        
        //Getting Contact
        $query = $db->prepare("SELECT * FROM " . TABLE_USERS_EMPLOYMENTS . " WHERE userID=%s ORDER BY `order` ASC", $userID);
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    /**
    * Update User Employment History
    * 
    * @param Int $userID
    * @param Array $data
    */
    public function updateUserEmploymentHistory($userID, $data)
    {
        global $db;
        
        //Remove old Data
        $query = $db->prepare("DELETE FROM " . TABLE_USERS_EMPLOYMENTS . " WHERE userID=%d", $userID);
        $db->query($query);
        
        //Insert New Values
        for( $i = 0; $i < count($data); $i++ )
        {
            $row = $data[$i];
            $db->insertFromArray( TABLE_USERS_EMPLOYMENTS, array('userID' => $userID, 'employer' => $row['employer'], 'start'=> $row['start'], 'end' => $row['end'], 'visibility' => $row['visibility'], 'order' => $i + 1) );
        }
        return true;
    }
    
    /**
    * Get User Links by ID
    * 
    * @param int $userID
    * @return array
    */
    public function getUserLinks($userID)
    {
        global $db;
        
        //Getting Contact
        $query = $db->prepare("SELECT * FROM " . TABLE_USERS_LINKS . " WHERE userID=%s ORDER BY `order` ASC", $userID);
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    /**
    * Update User Links
    * 
    * @param Int $userID
    * @param Array $data
    */
    public function updateUserLinks($userID, $data)
    {
        global $db;
        
        //Remove old Data
        $query = $db->prepare("DELETE FROM " . TABLE_USERS_LINKS . " WHERE userID=%d", $userID);
        $db->query($query);
        
        //Insert New Values
        for( $i = 0; $i < count($data); $i++ )
        {
            $row = $data[$i];
            $db->insertFromArray( TABLE_USERS_LINKS, array('userID' => $userID, 'title' => $row['title'], 'url'=> $row['url'], 'visibility' => $row['visibility'], 'order' => $i + 1) );
        }
        return true;
    }
    
    public function updateUserProfilePhoto($userID, $photoID)
    {
        global $db;
        
        $query = $db->prepare('SELECT image, is_profile FROM ' . TABLE_POSTS . ' WHERE postID=%s AND poster=%s', $photoID, $userID);
        $row = $db->getRow($query);
        
        if( !$row )
        {
            buckys_add_message(MSG_INVALID_REQUEST, MSG_TYPE_ERROR);
            return false;
        }
        if( !$row['is_profile'] )
        {            
            buckys_redirect("/photo_edit.php?photoID=" . $photoID . "&set_profile=1");
            exit;
        }
        
        
        $query = $db->updateFromArray( TABLE_USERS, array('thumbnail' => $row['image']), array('userID' => $userID) );
        
        buckys_add_message(MSG_PROFILE_PHOTO_CHANGED, MSG_TYPE_SUCCESS);
        return true;
    }
    
    /**
    * Create New Account
    * 
    * @param Array $data
    */
    public function createNewAccount($data)
    {
        global $db;
        
        $data = array_map('trim', $data);
        
        if( $data['firstName'] == '' || $data['lastName'] == '' )
        {
            buckys_add_message(MSG_USERNAME_EMPTY_ERROR, MSG_TYPE_ERROR);
            return false;
        }
        
        //Check Email Address
        if( !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $data['email']) )
        {
            buckys_add_message(MSG_INVALID_EMAIL, MSG_TYPE_ERROR);
            return false;
        }
        
        //Check Email Duplication
        if( BuckysUser::checkEmailDuplication($data['email']) )
        {
            buckys_add_message(MSG_EMAIL_EXIST, MSG_TYPE_ERROR);
            return false;
        }
        
        if( !$data['password'] || !$data['password2'] ){
            buckys_add_message(MSG_EMPTY_PASSWORD, MSG_TYPE_ERROR);
            return false;
        }
        if( $data['password'] != $data['password2'] ){
            buckys_add_message(MSG_NOT_MATCH_PASSWORD, MSG_TYPE_ERROR);
            return false;
        }
        
        //Create Token
        $token = md5(mt_rand(0, 99999) . time() . $data['email'] . mt_rand(0, 99999));
        $password = buckys_encrypt_password($data['password']);
        
        //Create New Account
        $newId = $db->insertFromArray(TABLE_USERS, array(
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName'],
            'email' => $data['email'],
            'email_visibility' => 0,
            'password' => $password,
            'thumbnail' => '',
            'user_type' => 'Registered',
            'user_acl_id' => 2,
            'token' => $token
        ));
        
        if(!$newId)
        {
            buckys_add_message($db->getLastError(), MSG_TYPE_ERROR);
            return false;
        }
        
        //Send an email to new user with a validation link
        $link = "http://" . $_SERVER['HTTP_HOST'] . "/register.php?action=verify&email=" . $data['email'] . "&token=" . $token;
        
        $title = "Please verify your account.";
        $body = "Dear " . $data['firstName'] . " " . $data['lastName'] . "\n\n"
               . "Thanks for your registration. \n"
               . "To complete your registration, please verify your email address by clicking the below link:. \n" . $link 
               . "\n\nBuckysroom.com";
        
        buckys_sendmail($data['email'], $data['firstName'] . " " . $data['lastName'], $title, $body);
        
        return $newId;
        
    }
    
    public function verifyAccount($email, $token)
    {
        global $db;
        
        $query = $db->prepare("SELECT userID FROM " . TABLE_USERS . " WHERE token=%s AND email=%s AND status=0", $token, $email);
        $userID = $db->getVar($query);
        if( !$userID )
        {
            buckys_add_message(MSG_INVALID_TOKEN, MSG_TYPE_ERROR);
            return false;
        }
        
        //Verify links
        $query = $db->prepare("UPDATE " . TABLE_USERS . " SET status=1, token='' WHERE userID=%d", $userID);
        $db->query($query);
        buckys_add_message(MSG_ACCOUNT_VERIFIED, MSG_TYPE_SUCCESS);
        
        //make this user to friend with bucky
        $buckysID = $db->getVar("Select userID FROM " . TABLE_USERS . " WHERE email='buckyroberts@gmail.com'");
        $db->insertFromArray(TABLE_FRIENDS, array('userID' => $buckysID, 'userFriendID' => $userID, 'status' => '1'));
        $db->insertFromArray(TABLE_FRIENDS, array('userID' => $userID, 'userFriendID' => $buckysID, 'status' => '1'));
        
        return true;
    }
    
    /**
    * Create new password and send it to user
    * 
    * @param String $email
    */
    public function resetPassword($email)
    {
        global $db;
        
        $email = trim($email);
        if(!$email)
        {
            buckys_redirect('/register.php?forgotpwd=1', MSG_EMPTY_EMAIL, MSG_TYPE_ERROR);
            return; 
        }
        
        //Check Email Address
        if( !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email) )
        {
            buckys_redirect('/register.php?forgotpwd=1', MSG_INVALID_EMAIL, MSG_TYPE_ERROR);
            return false;
        }
        
        $query = $db->prepare("SELECT userID FROM " . TABLE_USERS . " WHERE email=%s", $email);
        $userID = $db->getVar($query);
        
        if( !$userID )
        {
            buckys_redirect('/register.php?forgotpwd=1', MSG_EMAIL_NOT_FOUND, MSG_TYPE_ERROR);
            return false;
        }
        
        $data = BuckysUser::getUserData($userID);
        
        //Remove Old Token
        BuckysUsersToken::removeUserToken($userID, 'password');
        
        //Create New Token
        $token = BuckysUsersToken::createNewToken($userID, 'password');
        
        $link = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . $token;
        
        //Send an email to user with the link
        $title = "Reset your password.";
        $body = "Dear " . $data['firstName'] . " " . $data['lastName'] . "\n\n"
               . "Please reset your password by using the below link:\n" . $link 
               . "\n\nBuckysroom.com";
        require_once(DIR_FS_INCLUDES . "phpMailer/class.phpmailer.php");
                
        buckys_sendmail($data['email'], $data['firstName'] . " " . $data['lastName'], $title, $body);
        
        buckys_redirect('/register.php', MSG_RESET_PASSWORD_EMAIL_SENT, MSG_TYPE_SUCCESS);
        
        return;
    }
    
    /**
    * Check UserID is correct or not
    * 
    * @param Int $userID
    * @param Boolean $onlyActived
    */
    public function checkUserID($userID, $onlyActived = true)
    {
        global $db;
        
        if($onlyActived)
            $query = $db->prepare("SELECT userID FROM " . TABLE_USERS . " WHERE userID=%d AND `status`=1", $userID);
        else
            $query = $db->prepare("SELECT userID FROM " . TABLE_USERS . " WHERE userID=%d AND (`status`=1 OR `status`=0)", $userID);
            
        $id = $db->getVar($query);
        
        return buckys_not_null($id) ? true : false;
    }
    
    /**
    * Get a value from user attributes
    * 
    * @param Int $userID
    * @param String $key
    * @param Mixed $default
    * @return Mixed
    */
    public function getAttribute($userID, $key, $default = null)
    {
        global $db;
        
        $query  = $db->query("SELECT attributes FROM "  . TABLE_USERS . " WHERE userID=%d", $userID);
        $attr = $db->getVar($query);
        
        if(!$attr)
            return $default;
        
        $attr = unserialize($attr);
        if(!isset($attr[$key]))
            return $default;
            
        return $attr[$key];            
    }
    
    /**
    * Save Attribute
    * 
    * @param mixed $userID
    * @param mixed $key
    * @param mixed $value
    */
    public function setAttribute($userID, $key, $value)
    {
        global $db;
        
        $query  = $db->query("SELECT attributes FROM "  . TABLE_USERS . " WHERE userID=%d", $userID);
        $attr = $db->getVar($query);
        
        if(!$attr)
            $attr = array();
        else        
            $attr = unserialize($attr);
        
        $attr[$key] = $value;
        
        //Save Attribute
        return $db->update('UPDATE ' . TABLE_USERS . ' SET attributes="' . serialize($attr) . '" WHERE userID=' . $userID);
        
    }
    
    /**
    * Remove Account
    * 
    */
    public function deleteUserAccount($userID)
    {
        global $db;
        
        $userID = intval($userID);
        
        //Fix Comments Count
        $query = $db->prepare("SELECT count(commentID) as c, postID FROM " . TABLE_POSTS_COMMENTS . " WHERE commenter=%d AND commentStatus=1 GROUP BY postID", $userID);
        $pcRows = $db->getResultsArray($query);        
        foreach($pcRows as $row)
        {
            $db->query("UPDATE " . TABLE_POSTS . " SET `comments` = `comments` - " . $row['c'] . " WHERE postID=" . $row['postID']);
            
        }            
        
        //Fix Likes Count
        $query = $db->prepare("SELECT count(likeID) as c, postID FROM " . TABLE_POSTS_LIKES . " WHERE userID=%d AND likeStatus=1 GROUP BY postID", $userID);
        $plRows = $db->getResultsArray($query);
        foreach($plRows as $row)
        {
            $db->query("UPDATE " . TABLE_POSTS . " SET `likes` = `likes` - " . $row['c'] . " WHERE postID=" . $row['postID']);
        }
        
        //Block Votes for Moderator
        $query = $db->prepare("SELECT count(voteID) as c, candidateID FROM " . TABLE_MODERATOR_VOTES . " WHERE voterID=%d AND voteStatus=1 GROUP BY candidateID", $userID);
        $vRows = $db->getResultsArray($query);
        foreach($vRows as $row)
        {
            $db->query("UPDATE " . TABLE_MODERATOR_CANDIDATES . " SET `votes` = `votes` - " . $row['c'] . " WHERE candidateID=" . $row['candidateID']);
        }
        
        //Block Replies
        $query = $db->prepare("SELECT count(r.replyID), r.topicID, t.categoryID FROM " . TABLE_FORUM_REPLIES . " AS r LEFT JOIN " . TABLE_FORUM_TOPICS . " AS t ON t.topicID=r.topicID WHERE r.status='publish' AND r.creatorID=%d GROUP BY r.topicID", $userID);
        $rRows = $db->getResultsArray($query);
        $db->query("UPDATE " . TABLE_FORUM_REPLIES . " SET `status`='suspended' WHERE creatorID=" . $userID . " AND `status`='publish'");
        foreach($rRows as $row)
        {
            $db->query("UPDATE " . TABLE_FORUM_TOPICS . " SET `replies` = `replies` - " . $row['c'] . " WHERE topicID=" . $row['topicID']);
            $db->query("UPDATE " . TABLE_FORUM_CATEGORIES . " SET `replies` = `replies` - " . $row['c'] . " WHERE categoryID=" . $row['categoryID']);
            BuckysForumTopic::updateTopicLastReplyID($row['topicID']);
        }
                
        //Block Topics
        $query = $db->prepare("SELECT count(topicID) as tc, SUM(replies) as rc, categoryID FROM " . TABLE_FORUM_TOPICS . " WHERE creatorID=%d AND `status`='publish' GROUP BY categoryID", $userID);
        $tRows = $db->getResultsArray($query);
        $db->query("UPDATE " . TABLE_FORUM_TOPICS . " SET `status`='suspended' WHERE creatorID=" . $userID . " AND `status`='publish'");
        foreach($tRows as $row)
        {
            $db->query("UPDATE " . TABLE_FORUM_CATEGORIES . " SET `replies` = `replies` - " . $row['rc'] . ", `topics` = `topics` - " . $row['tc'] . " WHERE categoryID=" . $row['categoryID']);
            BuckysForumCategory::updateCategoryLastTopicID($row['categoryID']);
        }
        
        //Block Reply Votes
        $query = $db->prepare("SELECT count(voteID) as c, objectID FROM " . TABLE_FORUM_VOTES . " WHERE voterID=%d AND voteStatus=1 GROUP BY objectID", $userID);
        $vRows = $db->getResultsArray($query);
        foreach($vRows as $row)
        {
            $db->query("UPDATE " . TABLE_FORUM_REPLIES . " SET `votes` = `votes` - " . $row['c'] . " WHERE replyID=" . $row['objectID']);
        }
        
        
        //Delete Reported Objects
        $db->query("DELETE FROM " . TABLE_REPORTS . " WHERE objectID IN (SELECT postID FROM " . TABLE_POSTS ." WHERE poster=" . $userID . ")");
        $db->query("DELETE FROM " . TABLE_REPORTS . " WHERE objectID IN (SELECT topicID FROM " . TABLE_FORUM_TOPICS ." WHERE creatorID=" . $userID . ")");
        $db->query("DELETE FROM " . TABLE_REPORTS . " WHERE objectID IN (SELECT replyID FROM " . TABLE_FORUM_REPLIES ." WHERE creatorID=" . $userID . ")");
        
        //Delete From banned Users
        $db->query("DELETE FROM " . TABLE_BANNED_USERS . "  WHERE bannedUserID=" . $userID);
        
        //Delete Activities
        $db->query("DELETE FROM " . TABLE_ACTIVITES . " WHERE userID=" . $userID);
        
        //Delete Album Photos
        $db->query("DELETE FROM " . TABLE_ALBUMS_PHOTOS . " WHERE album_id IN (SELECT albumID FROM " . TABLE_ALBUMS . " WHERE owner=" . $userID . ")");
        //Delete ALbums
        $db->query("DELETE FROM " . TABLE_ALBUMS . " WHERE owner=" . $userID);
        
        //Delete Friends
        $db->query("DELETE FROM " . TABLE_FRIENDS . " WHERE userID=" . $userID . " OR userFriendID=" . $userID);
        
        //Delete Messages
        $db->query("DELETE FROM " . TABLE_MESSAGES . " WHERE userID=" . $userID . " OR sender=" . $userID);
        
        //Delete Private Messengers
        $db->query("DELETE FROM " . TABLE_MESSENGER_BLOCKLIST . " WHERE userID=" . $userID . " OR blockedID=" . $userID);
        $db->query("DELETE FROM " . TABLE_MESSENGER_BUDDYLIST . " WHERE userID=" . $userID . " OR buddyID=" . $userID);
        $db->query("DELETE FROM " . TABLE_MESSENGER_MESSAGES . " WHERE userID=" . $userID . " OR buddyID=" . $userID);
        
        //Delete Posts
        $posts = $db->getResultsArray("SELECT * FROM " . TABLE_POSTS . " WHERE poster=" . $userID);
        foreach($posts as $post)
        {
            //Delete Comments
            $db->query("DELETE FROM " . TABLE_POSTS_COMMENTS . " WHERE postID=" . $post['postID']);
            //Delete Likes
            $db->query("DELETE FROM " . TABLE_POSTS_LIKES . " WHERE postID=" . $post['postID']);
            //Delete hits
            $db->query("DELETE FROM " . TABLE_POSTS_HITS . " WHERE postID=" . $post['postID']);
        }
        $db->query("DELETE FROM " . TABLE_POSTS . " WHERE poster=" . $userID);
        
        //Delete Pages
        $pageIns = new BuckysPage();
        $pageIns->deletePageByUserID($userID);
        
        //Delete Trade Section which are related to this user.
        $tradeIns = new BuckysTradeItem();
        $tradeIns->deleteItemsByUserID($userID);
        
        
        //Delete Comments        
        $db->query("DELETE FROM " . TABLE_POSTS_COMMENTS . " WHERE commenter=" . $userID);
        
        //Delete Likes
        $db->query("DELETE FROM " . TABLE_POSTS_LIKES . " WHERE userID=" . $userID);
        
        //Getting Removed Topics
        $topicIDs = $db->getResultsArray("SELECT topicID FROM " . TABLE_FORUM_TOPICS . " WHERE creatorID=" . $userID);
        if(!$topicIDs)        
            $topicIDs = array(0);
        
        //Delete Reply Votes
        $db->query("DELETE FROM " . TABLE_FORUM_VOTES . " WHERE voterID=" . $userID);
        $db->query("DELETE FROM " . TABLE_FORUM_VOTES . " WHERE objectID IN ( SELECT replyID FROM " . TABLE_FORUM_REPLIES . " WHERE creatorID=" . $userID . " OR topicID IN (" . implode(", ", $topicIDs) . ") )");
        
        //Delete Replies
        $db->query("DELETE FROM " . TABLE_FORUM_REPLIES . " WHERE creatorID=" . $userID . " OR topicID IN (" . implode(", ", $topicIDs) . ")");
        
        //Delete Topics
        $db->query("DELETE FROM " . TABLE_FORUM_TOPICS . " WHERE creatorID=" . $userID);
        
        //Delete Users
        /*$db->query("DELETE FROM " . TABLE_USERS . " WHERE userID=" . $userID);
        $db->query("DELETE FROM " . TABLE_USERS_CONTACT . " WHERE userID=" . $userID);
        $db->query("DELETE FROM " . TABLE_USERS_EDUCATIONS . " WHERE userID=" . $userID);
        $db->query("DELETE FROM " . TABLE_USERS_EMPLOYMENTS . " WHERE userID=" . $userID);
        $db->query("DELETE FROM " . TABLE_USERS_LINKS . " WHERE userID=" . $userID);
        $db->query("DELETE FROM " . TABLE_USERS_TOKEN . " WHERE userID=" . $userID);*/
        //Don't delete user from the database, just update the user's status
        $db->query("UPDATE " . TABLE_USERS . " SET `status`=" . BuckysUser::STATUS_USER_DELETED . " WHERE userID=" . $userID);
        
    }
    
    /**
    * Search Users
    * 
    * @param Int $userID
    * @param Int $term
    * @return Array
    */
    public function searchUsers($term, $exclude = array())
    {
        global $db;
        
        if(buckys_not_null($exclude) && !is_array($exclude))
            $exclude = array($exclude);
            
        if(buckys_not_null($exclude))
            $query = "SELECT distinct(u.userID), CONCAT(u.firstName, ' ', u.lastName) as fullName FROM " . TABLE_USERS . " as u WHERE u.status = 1 AND u.userID NOT IN(" . implode(", ", $db->escapeInput($exclude)) . ") AND (CONCAT(u.firstName, ' ', u.lastName) LIKE '%" . $db->escapeInput($term) . "%') ORDER BY fullName";
        else
            $query = "SELECT distinct(u.userID), CONCAT(u.firstName, ' ', u.lastName) as fullName FROM " . TABLE_USERS . " as u WHERE u.status = 1 AND (CONCAT(u.firstName, ' ', u.lastName) LIKE '%" . $db->escapeInput($term) . "%') ORDER BY fullName";
            
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    /**
    * Get User Forum Settings
    * 
    * @param Int $userID
    * 
    * @return Array
    */
    public function getUserForumSettings($userID)
    {
        global $db, $BUCKYS_GLOBALS;
        
        $query = $db->prepare( "SELECT * FROM " . TABLE_FORUM_SETTINGS . " WHERE userID=%s", $userID );        
        $row = $db->getRow($query);
        
        if(!$row)        
            $row = array();
            
        $row = array_merge($BUCKYS_GLOBALS['forum_settings'], $row);
        
        return $row;
    }
    
    /**
    * Save User Notification Settings
    * 
    * @param mixed $userID
    * @param mixed $data
    */
    public function saveUserNotificationSettings($userID, $data)
    {
        global $db;
        
        $userID = intval($userID);
        
        $paramData = array(
                'optOfferReceived' => isset($data['optOfferReceived'])? 1: 0,
                'optOfferAccepted' => isset($data['optOfferAccepted'])? 1: 0,
                'optOfferDeclined' => isset($data['optOfferDeclined'])? 1: 0,
                'optFeedbackReceived' => isset($data['optFeedbackReceived'])? 1: 0,
            );
    
        $res = $db->updateFromArray( TABLE_TRADE_USERS, $paramData, array('userID' => $userID) );
        
        $forumData = array(
            'userID' => $userID,
            'notifyRepliedToMyTopic' => isset($data['notifyRepliedToMyTopic']) ? 1 : 0,
            'notifyRepliedToMyReply' => isset($data['notifyRepliedToMyReply']) ? 1 : 0,
            'notifyMyPostApproved' => isset($data['notifyMyPostApproved']) ? 1 : 0  
        );
        
        //Check if the forum notification exists or not
        $query = $db->prepare("SELECT settingID FROM " . TABLE_FORUM_SETTINGS . " WHERE userID=%d", $userID);
        $sID = $db->getVar($query);
        
        if(!$sID)   
            $fr = $db->insertFromArray(TABLE_FORUM_SETTINGS, $forumData);
        else
            $fr = $db->updateFromArray(TABLE_FORUM_SETTINGS, $forumData, array('settingID' => $sID));
        
        if($fr && $res)            
            return true;
        else
            return $db->getLastError();
        
    }
}
