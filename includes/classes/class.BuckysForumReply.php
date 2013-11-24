<?php
/**
* Manage Forum Reply
* 
*/

class BuckysForumReply
{
    static $COUNT_PER_PAGE = 10;
    
    /**
    * Get Replies by Topic ID
    * 
    * @param Int $topicID
    * @param Int $page
    * @param String $orderBy : oldest, newest, toprated
    * @return Array
    */
    public function getReplies($topicID = null, $status=null, $page = 1, $orderBy = 'oldest')
    {
        global $db, $BUCKYS_GLOBALS;
        
        if(!$BUCKYS_GLOBALS['user']['userID'])        
            $query = "SELECT r.*, CONCAT(u.firstName, ' ', u.lastName) as creatorName, u.thumbnail, 0 as voteID, 0 AS reportID FROM " . TABLE_FORUM_REPLIES . " AS r " .
                     "LEFT JOIN " . TABLE_USERS . " AS u ON u.userID=r.creatorID ";
        else
            $query = "SELECT r.*, CONCAT(u.firstName, ' ', u.lastName) as creatorName, u.thumbnail, v.voteID, rp.reportID FROM " . TABLE_FORUM_REPLIES . " AS r " .
                     " LEFT JOIN " . TABLE_USERS . " AS u ON u.userID=r.creatorID " .
                     " LEFT JOIN " . TABLE_FORUM_VOTES . " AS v ON v.objectID=r.replyID AND v.voterID=" . $BUCKYS_GLOBALS['user']['userID'] .
                     " LEFT JOIN " . TABLE_REPORTS . " AS rp ON rp.objectType='reply' AND rp.objectID=r.replyID AND rp.reporterID=" . $BUCKYS_GLOBALS['user']['userID'];
        
        $where = array();
        if($status != null)
            $where[] = $db->prepare(' r.status=%s ', $status);
        
        if($topicID != null)
            $where[] = $db->prepare(" r.topicID=%d", $topicID);
        
        if(count($where) > 0)
            $query .= " WHERE " . implode(" AND ", $where);
            
        switch(strtolower($orderBy))         
        {
            case 'highrated':
                $query .= " ORDER BY r.votes DESC ";
                break;
            case 'newest':
                $query .= " ORDER BY r.createdDate DESC ";
                break;            
            case 'oldest':
            default:
                $query .= " ORDER BY r.createdDate ASC ";
                break;            
        }
        
        $query .= " LIMIT " . ($page - 1) * BuckysForumReply::$COUNT_PER_PAGE .", " . BuckysForumReply::$COUNT_PER_PAGE;
        
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    /**
    * get reply by ID
    * 
    * @param mixed $replyID
    */
    public function getReplyByID($replyID) {
        
        
        global $db;
        
        if (!is_numeric($replyID))
            return;
        $query = sprintf('SELECT * FROM %s WHERE replyID=%d', TABLE_FORUM_REPLIES, $replyID);
        
        return $db->getRow($query);        
        
    }
    
    /**
    * Getting Total Number Of Replies
    * 
    * @param Int $topicID
    * @return Int
    */
    public function getTotalNumOfReplies($topicID = null, $status = null)
    {
        global $db;
        
        $query = "SELECT count(1) FROM " . TABLE_FORUM_REPLIES;
                 
        $where = array();
        if($status != null)
            $where[] = $db->prepare(' status=%s ', $status);
        
        if($topicID != null)
            $where[] = $db->prepare(" topicID=%d", $topicID);
        
        if(count($where) > 0)
            $query .= " WHERE " . implode(" AND ", $where);
            
        $count = $db->getVar($query);
        
        return $count;
    }
    
    /**
    * Create Post Reply
    * 
    * @param mixed $data
    */
    public function createReply($data)
    {
        global $db, $BUCKYS_GLOBALS;
        
        $content = trim($data['content']);
        
        if(!$content)
        {
            return MSG_ALL_FIELDS_REQUIRED;
        }
        
        //Check Category ID is valid or not
        $query = $db->prepare("SELECT topicID, categoryID, creatorID FROM " . TABLE_FORUM_TOPICS . " WHERE topicID=%d AND status='publish'", $data['topicID']);
        $topic = $db->getRow($query);
        if(!$topic)
        {
            return MSG_INVALID_REQUEST;
        }
        $content = BuckysForumTopic::_convertHTMLToBBCode($content);
        $insertData = array(
            'topicID' => $topic['topicID'],
            'replyContent' => $content,
            'creatorID' => $BUCKYS_GLOBALS['user']['userID'],
            'createdDate' => date('Y-m-d H:i:s'),
            'votes' => 0,
            'status' => 'pending'
        );
        
        $newID = $db->insertFromArray(TABLE_FORUM_REPLIES, $insertData);
        if(!$newID)
            return $db->getLastError();
            
        //If the user has more than 5 actived topics, update the topic status to 1
        $count1 = $db->getVar("SELECT count(1) FROM " . TABLE_FORUM_TOPICS . " WHERE creatorID=" . $BUCKYS_GLOBALS['user']['userID'] . " AND `status`='publish'");
        $count2 = $db->getVar("SELECT count(1) FROM " . TABLE_FORUM_REPLIES . " WHERE creatorID=" . $BUCKYS_GLOBALS['user']['userID'] . " AND `status`='publish'");
        if($count1 + $count2 >= 5){
            $db->updateFromArray(TABLE_FORUM_REPLIES, array('status' => 'publish'), array('replyID' => $newID));
            //Update Category Table
            $db->query("UPDATE " . TABLE_FORUM_TOPICS . " SET lastReplyID=" . $newID . ", `replies`=`replies` + 1, lastReplyDate='" . date('Y-m-d H:i:s') . "', lastReplierID=" . $BUCKYS_GLOBALS['user']['userID'] . " WHERE topicID=" . $topic['topicID']);
            $db->query("UPDATE " . TABLE_FORUM_CATEGORIES . " SET `replies`=`replies` + 1, lastTopicID='" . $topic['topicID'] . "' WHERE categoryID=" . $topic['categoryID']);
            //Add Notifications
            $forumNotification = new BuckysForumNotification();
            $forumNotification->addNotificationsForReplies($topic['creatorID'], $topic['topicID'], $newID);
            if($topic['creatorID'] != $BUCKYS_GLOBALS['user']['userID'])
                $forumNotification->addNotificationsForTopic($topic['creatorID'], $topic['topicID'], $newID);
            return 'publish';
        }
        
        return 'pending';
    }
    
    
    /**
    * Edit Post Reply
    * 
    * @param mixed $data
    */
    public function editReply($data)
    {
        global $db, $BUCKYS_GLOBALS;
        
        $content = trim($data['content']);
        
        if(!$content)
        {
            return MSG_ALL_FIELDS_REQUIRED;
        }
        
        //Check Category ID is valid or not
        $query = $db->prepare("SELECT topicID, categoryID, creatorID FROM " . TABLE_FORUM_TOPICS . " WHERE topicID=%d AND status='publish'", $data['topicID']);
        $topic = $db->getRow($query);
        if(!$topic)
        {
            return MSG_INVALID_REQUEST;
        }
        $content = BuckysForumTopic::_convertHTMLToBBCode($content);
        
        $updateData = array(
            'replyContent' => $content
        );
        
        $db->updateFromArray(TABLE_FORUM_REPLIES, $updateData, array('replyID'=>$data['replyID']));
        
        return true;
        
    }
    
    
    
    /**
    * Approve Pending Replies
    * 
    * @param Array $ids
    */
    public function approvePendingReplies($ids)
    {
        global $db;
        
        if(!is_array($ids))
            $ids = array($ids);
        
        $ids = $db->escapeInput($ids);
        
        //Getting Topics for confirmation
        $query = "SELECT r.topicID, r.replyID, t.categoryID, r.creatorID, r.createdDate, t.creatorID AS topicCreatorID FROM " . TABLE_FORUM_REPLIES . " AS r LEFT JOIN " . TABLE_FORUM_TOPICS . " AS t ON t.topicID=r.topicID WHERE r.status='pending' AND r.replyID in (" . implode(', ', $ids) . ")";
        $rows = $db->getResultsArray($query);
        
        if(!$rows)
            return MSG_INVALID_REQUEST;
        
        $forumNotification = new BuckysForumNotification();
                
        foreach($rows as $row)
        {
            //Update Topic Status
            $db->updateFromArray(TABLE_FORUM_REPLIES, array('status' => 'publish'), array('replyID' => $row['replyID']));            
            
            //Update Category Table
            $db->query("UPDATE " . TABLE_FORUM_CATEGORIES . " SET `replies`=`replies` + 1, lastTopicID='" . $row['topicID'] . "' WHERE categoryID=" . $row['categoryID']);            
            $db->query("UPDATE " . TABLE_FORUM_TOPICS . " SET `replies`=`replies` + 1 WHERE topicID=" . $row['topicID']);            
            $db->query("UPDATE " . TABLE_FORUM_TOPICS . " SET lastReplyID=" . $row['replyID'] . ", lastReplyDate='" . $row['createdDate'] . "', lastReplierID=" . $row['creatorID'] . " WHERE topicID=" . $row['topicID'] . " AND lastReplyID < " . $row['replyID']);  
            //Add Notifications
            
            $forumNotification->addNotificationsForPendingPost($row['creatorID'], $row['topicID'], $row['replyID']);
            $forumNotification->addNotificationsForReplies($row['creatorID'], $row['topicID'], $row['replyID']);
            if($row['topicCreatorID'] != $row['creatorID'])
                $forumNotification->addNotificationsForTopic($row['topicCreatorID'], $row['topicID'], $row['replyID']);
        }
        
        return true;
    }
    
    /**
    * Delete Pending Replies
    * 
    * @param Array $ids
    */
    public function deletePendingReplies($ids)
    {
        global $db;
        
        if(!is_array($ids))
            $ids = array($ids);
        
        $ids = $db->escapeInput($ids);
        
        //Getting Topics for confirmation
        $query = "SELECT r.topicID, r.replyID FROM " . TABLE_FORUM_REPLIES . " AS r WHERE r.status='pending' AND r.replyID in (" . implode(', ', $ids) . ")";
        $rows = $db->getResultsArray($query);
        
        if(!$rows)
            return MSG_INVALID_REQUEST;
                        
        foreach($rows as $row)
        {
            $db->query("DELETE FROM " . TABLE_FORUM_REPLIES . " WHERE replyID=" . $row['replyID']);            
            
        }
        
        return true;
    }
    
    /**
    * Cast a vote on a reply
    * 
    * @param Int $userID: voterID
    * @param Int $id
    * @param Int $voteType: 1: Thumb up, -1: Thumb Down
    */
    public function voteReply($userID, $replyID, $voteType)
    {
        global $db, $BUCKYS_GLOBALS;
        
        //Check Reply ID        
        $query = $db->prepare("SELECT replyID, votes FROM " . TABLE_FORUM_REPLIES . " WHERE replyID=%d AND status='publish'", $replyID);
        $reply = $db->getRow($query);
        
        if(!$reply)
            return MSG_INVALID_REQUEST;
        
        $replyID = $reply['replyID'];
        $votes = $reply['votes'];
        
        //Check the user already casted his vote or not
        $query = $db->prepare("SELECT voteID FROM " . TABLE_FORUM_VOTES . " WHERE objectID=%d AND voterID=%d AND objectType='reply'", $replyID, $userID);
        $voteID = $db->getVar($query);
        if($voteID)
            return MSG_ALREADY_CASTED_A_VOTE;
            
        //Add Vote
        $voteID = $db->insertFromArray(TABLE_FORUM_VOTES, array('objectID' => $replyID, 'voterID' => $userID, 'objectType' => 'reply', 'voteDate' => date('Y-m-d H:i:s')));
        if(!$voteID)
            return $db->getLastError();
        
        $votes += $voteType;
        $db->update('UPDATE ' . TABLE_FORUM_REPLIES . ' SET `votes` = ' . $votes . ' WHERE replyID=' . $replyID);
        
        return $votes;
    }
    
    /**
    * Delete Reply
    * 
    * @param Int $replyID
    */
    public function deleteReply($replyID)
    {
        global $db;
        
        $query = $db->prepare("SELECT * FROM " . TABLE_FORUM_REPLIES . " WHERE replyID=%d", $replyID);
        $reply = $db->getRow($query);
        if($reply)
        {
            if($reply['status'] == 'publish')
            {
                //Getting Topic
                $query = $db->prepare("SELECT * FROM " . TABLE_FORUM_TOPICS . " WHERE topicID=%d", $reply['topicID']);
                $topic = $db->getRow($query);
                
                //Update Replies Count For Topic
                $query = "UPDATE " . TABLE_FORUM_TOPICS . " SET `replies`=`replies` - 1 WHERE topicID=" . $reply['topicID'];
                $db->query($query);
                //Update Replies Count For Category
                $query = "UPDATE " . TABLE_FORUM_CATEGORIES . " SET `replies`=`replies` - 1 WHERE categoryID=" . $topic['categoryID'];
                $db->query($query);
                
            }            
            //Remove Reply Votes
            $query = "DELETE FROM " . TABLE_FORUM_VOTES . " WHERE objectID=" . $reply['replyID'];
            $db->query($query);
            //Remove Reply
            $query = "DELETE FROM " . TABLE_FORUM_REPLIES . " WHERE replyID=" . $reply['replyID'];
            $db->query($query);
            
            return true;
        }
        
        return false;
    }
}
