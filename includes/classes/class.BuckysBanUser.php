<?php
/**
* Manage Banned Users
*/

class BuckysBanUser
{
    public static $COUNT_PER_PAGE = 20;
    /**
    * Ban User
    * 
    * @param Int $userID
    */
    
    public function banUser($userID)
    {
        global $db, $BUCKYS_GLOBALS;
        
        $cUserID = $BUCKYS_GLOBALS['user']['userID'];
        
        $bannedID = $db->getVar("SELECT bannedID FROM " . TABLE_BANNED_USERS . " WHERE bannedUserID=" . $userID);
        
        $userID = intval($userID);
        
        if(!$bannedID)
        {
        
            //Block User
            $db->query("UPDATE " . TABLE_USERS . " SET status=0 WHERE userID=" . $userID);
            //Block Posts
            $db->query("UPDATE " . TABLE_POSTS . " SET post_status=0 WHERE poster=" . $userID);                
            //Block Activities
            $db->query("UPDATE " . TABLE_ACTIVITES . " SET activityStatus=0 WHERE userID=" . $userID);
            //Block Messages
            $db->query("UPDATE " . TABLE_MESSAGES . " SET messageStatus=0 WHERE sender=" . $userID);
            
            //Fix Comments Count
            $query = $db->prepare("SELECT count(commentID) as c, postID FROM " . TABLE_POSTS_COMMENTS . " WHERE commenter=%d AND commentStatus=1 GROUP BY postID", $userID);
            $pcRows = $db->getResultsArray($query);
            
            foreach($pcRows as $row)
            {
                $db->query("UPDATE " . TABLE_POSTS . " SET `comments` = `comments` - " . $row['c'] . " WHERE postID=" . $row['postID']);
                
            }            
            //Block Comments
            $db->query("UPDATE " . TABLE_POSTS_COMMENTS . " SET commentStatus=0 WHERE commenter=" . $userID);
            
            //Fix Likes Count
            $query = $db->prepare("SELECT count(likeID) as c, postID FROM " . TABLE_POSTS_LIKES . " WHERE userID=%d AND likeStatus=1 GROUP BY postID", $userID);
            $plRows = $db->getResultsArray($query);
            foreach($plRows as $row)
            {
                $db->query("UPDATE " . TABLE_POSTS . " SET `likes` = `likes` - " . $row['c'] . " WHERE postID=" . $row['postID']);
            }
            //Block Likes            
            $db->query("UPDATE " . TABLE_POSTS_LIKES . " SET likeStatus=0 WHERE userID=" . $userID);    
            
            //Block Votes for Moderator
            $query = $db->prepare("SELECT count(voteID) as c, candidateID FROM " . TABLE_MODERATOR_VOTES . " WHERE voterID=%d AND voteStatus=1 GROUP BY candidateID", $userID);
            $vRows = $db->getResultsArray($query);
            foreach($vRows as $row)
            {
                $db->query("UPDATE " . TABLE_MODERATOR_CANDIDATES . " SET `votes` = `votes` - " . $row['c'] . " WHERE candidateID=" . $row['candidateID']);
            }
            $db->query("UPDATE " . TABLE_MODERATOR_VOTES . " SET voteStatus=0 WHERE voterID=" . $userID);    
            
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
            $db->query("UPDATE " . TABLE_FORUM_VOTES . " SET voteStatus=0 WHERE voterID=" . $userID);
            
            
            //Disable Page Section and Trade section
            $tradeItemIns = new BuckysTradeItem();
            $tradeOfferIns = new BuckysTradeOffer();
            $pageIns = new BuckysPage();
            $tradeItemIns->massStatusChange($userID, BuckysTradeItem::STATUS_ITEM_INACTIVE);
            $tradeOfferIns->massStatusChange($userID, BuckysTradeOffer::STATUS_OFFER_INACTIVE);
            $pageIns->massStatusChange($userID, BuckysPage::STATUS_INACTIVE);

            
            //Insert New Row to Ban User Table
            $db->insertFromArray(TABLE_BANNED_USERS, array('userID' => $cUserID, 'bannedUserID' => $userID, 'bannedDate' => date('Y-m-d H:i:s')));
        }
             
    }
    
    /**
    * Get Banned Users Count
    * 
    * @return Int
    */
    public function getBannedUsersCount()
    {
        global $db;
        
        $query = "SELECT count(distinct(bannedID)) FROM " . TABLE_BANNED_USERS;
        $count = $db->getVar($query);
        
        return $count;
    }
    
    public function getBannedUsers($page, $limit = null)
    {
        global $db;
        
        if(!$limit)
            $limit = BuckysBanUser::$COUNT_PER_PAGE;
        
        $query = "SELECT 
                    CONCAT(bu.firstName, ' ', bu.lastName) as bannedUserName,
                    bu.userID as bannedUserID,
                    bu.thumbnail as bannedUserThumb,
                    CONCAT(m.firstName, ' ', m.lastName) as moderatorName,
                    m.userID as moderatorID,
                    m.thumbnail as moderatorThumb,
                    b.bannedDate,
                    b.bannedID 
                 FROM " . TABLE_BANNED_USERS . " AS b " . 
                "LEFT JOIN " . TABLE_USERS . " AS m ON b.userID=m.userID " .
                "LEFT JOIN " . TABLE_USERS . " AS bu ON bu.userID=b.bannedUserID " .
                "ORDER BY bannedDate LIMIT " . ($page - 1) * $limit . ", " . $limit;
        
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    /**
    * Unban Users
    * 
    * @param mixed $ids
    */
    public function unbanUsers($ids)
    {
        global $db, $BUCKYS_GLOBALS;
        
        if(!is_array($ids))
            $ids = array($ids);
        
        //Check the user has lready been banned or not
        $rows = $db->getResultsArray("SELECT * FROM " . TABLE_BANNED_USERS . " WHERE bannedID IN (" . implode(', ', $ids) . ")");
        
        if($rows)
        {
            foreach($rows as $brow)
            {
                $userID = $brow['bannedUserID'];
                                                   
                //Change User Table
                $db->query("UPDATE " . TABLE_USERS . " SET status=1 WHERE userID=" . $userID);
                //Change Posts table
                $db->query("UPDATE " . TABLE_POSTS . " SET post_status=1 WHERE poster=" . $userID);                
                //Change Activities
                $db->query("UPDATE " . TABLE_ACTIVITES . " SET activityStatus=1 WHERE userID=" . $userID);
                //Change Messages
                $db->query("UPDATE " . TABLE_MESSAGES . " SET messageStatus=1 WHERE sender=" . $userID);
                
                //Fix Comments Count
                $query = $db->prepare("SELECT count(commentID) as c, postID FROM " . TABLE_POSTS_COMMENTS . " WHERE commenter=%d AND commentStatus=0 GROUP BY postID", $userID);
                $pcRows = $db->getResultsArray($query);
                foreach($pcRows as $row)
                {
                    $db->query("UPDATE " . TABLE_POSTS . " SET `comments` = `comments` + " . $row['c'] . " WHERE postID=" . $row['postID']);
                }            
                //Unblock Comments
                $db->query("UPDATE " . TABLE_POSTS_COMMENTS . " SET commentStatus=1 WHERE commenter=" . $userID);
                
                //Fix Likes Count
                $query = $db->prepare("SELECT count(likeID) as c, postID FROM " . TABLE_POSTS_LIKES . " WHERE userID=%d AND likeStatus=0 GROUP BY postID", $userID);
                $plRows = $db->getResultsArray($query);
                foreach($plRows as $row)
                {
                    $db->query("UPDATE " . TABLE_POSTS . " SET `likes` = `likes` + " . $row['c'] . " WHERE postID=" . $row['postID']);
                }
                //Unblock Likes            
                $db->query("UPDATE " . TABLE_POSTS_LIKES . " SET likeStatus=1 WHERE userID=" . $userID);    
                
                //Unblock Votes for Moderator
                $query = $db->prepare("SELECT count(voteID) as c, candidateID FROM " . TABLE_MODERATOR_VOTES . " WHERE voterID=%d AND voteStatus=0 GROUP BY candidateID", $userID);
                $vRows = $db->getResultsArray($query);
                foreach($vRows as $row)
                {
                    $db->query("UPDATE " . TABLE_MODERATOR_CANDIDATES . " SET `votes` = `votes` + " . $row['c'] . " WHERE candidateID=" . $row['candidateID']);
                }
                $db->query("UPDATE " . TABLE_MODERATOR_VOTES . " SET voteStatus=1 WHERE voterID=" . $userID);    
                
                //Unblock Replies
                $query = $db->prepare("SELECT count(r.replyID), r.topicID, t.categoryID FROM " . TABLE_FORUM_REPLIES . " AS r LEFT JOIN " . TABLE_FORUM_TOPICS . " AS t ON t.topicID=r.topicID WHERE r.status='suspended' AND r.creatorID=%d GROUP BY r.topicID", $userID);
                $rRows = $db->getResultsArray($query);                
                $db->query("UPDATE " . TABLE_FORUM_REPLIES . " SET `status`='publish' WHERE creatorID=" . $userID . " AND `status`='suspended'");                
                foreach($rRows as $row)
                {
                    $db->query("UPDATE " . TABLE_FORUM_TOPICS . " SET `replies` = `replies` + " . $row['c'] . " WHERE topicID=" . $row['topicID']);
                    $db->query("UPDATE " . TABLE_FORUM_CATEGORIES . " SET `replies` = `replies` + " . $row['c'] . " WHERE categoryID=" . $row['categoryID']);
                    BuckysForumTopic::updateTopicLastReplyID($row['topicID']);
                    BuckysForumCategory::updateCategoryLastTopicID($row['categoryID']);
                }
                
                
                //unblock Topics
                $query = $db->prepare("SELECT count(topicID) as tc, SUM(replies) as rc, categoryID FROM " . TABLE_FORUM_TOPICS . " WHERE creatorID=%d AND `status`='suspended' GROUP BY categoryID", $userID);
                $tRows = $db->getResultsArray($query);
                $db->query("UPDATE " . TABLE_FORUM_TOPICS . " SET `status`='publish' WHERE creatorID=" . $userID . " AND `status`='suspended'");
                foreach($tRows as $row)
                {
                    $db->query("UPDATE " . TABLE_FORUM_CATEGORIES . " SET `replies` = `replies` + " . $row['rc'] . ", `topics` = `topics` + " . $row['tc'] . " WHERE categoryID=" . $row['categoryID']);
                    BuckysForumCategory::updateCategoryLastTopicID($row['categoryID']);
                }
                
                
                //Unblock Reply Votes
                $query = $db->prepare("SELECT count(voteID) as c, objectID FROM " . TABLE_FORUM_VOTES . " WHERE voterID=%d AND voteStatus=0 GROUP BY objectID", $userID);
                $vRows = $db->getResultsArray($query);
                foreach($vRows as $row)
                {
                    $db->query("UPDATE " . TABLE_FORUM_REPLIES . " SET `votes` = `votes` + " . $row['c'] . " WHERE replyID=" . $row['objectID']);
                }
                $db->query("UPDATE " . TABLE_FORUM_VOTES . " SET voteStatus=1 WHERE voterID=" . $userID);
                
                
                
                //Unblock page section & Trade section
                $tradeItemIns = new BuckysTradeItem();
                $tradeOfferIns = new BuckysTradeOffer();
                $pageIns = new BuckysPage();
                $tradeItemIns->massStatusChange($userID, BuckysTradeItem::STATUS_ITEM_ACTIVE);
                $tradeOfferIns->massStatusChange($userID, BuckysTradeOffer::STATUS_OFFER_ACTIVE);
                $pageIns->massStatusChange($userID, BuckysPage::STATUS_ACTIVE);

                
                                
                //Remove From banned users table
                $db->query("DELETE FROM " . TABLE_BANNED_USERS . "  WHERE bannedID=" . $brow['bannedID']);
            }
              
        }
    }
    
    /**
    * Delete Banned Users
    * 
    * @param mixed $ids
    */
    public function deleteUsers($ids)
    {
        global $db, $BUCKYS_GLOBALS;
        
        if(!is_array($ids))
            $ids = array($ids);
        
        //Check the user has lready been banned or not
        $rows = $db->getResultsArray("SELECT * FROM " . TABLE_BANNED_USERS . " WHERE bannedID IN (" . implode(', ', $ids) . ")");
        
        if($rows)
        {
            foreach($rows as $row)
            {
                $userID = $row['bannedUserID'];
                
                BuckysUser::deleteUserAccount($userID);
                
                //Remove From banned users table
                $db->query("DELETE FROM " . TABLE_BANNED_USERS . "  WHERE bannedID=" . $row['bannedID']);
                
            }
              
        }
    }
    
    /**
    * Check if the user id has been banned or not
    * 
    * @param Int $userID
    * @return Boolean
    */
    public function isBannedUser($userID)
    {
        global $db;
        
        $query = $db->prepare("SELECT bannedID FROM " . TABLE_BANNED_USERS . " WHERE bannedUserID=%d", $userID);
        $bID = $db->getVar($query);
        
        return !$bID ? false : true;
    }
}