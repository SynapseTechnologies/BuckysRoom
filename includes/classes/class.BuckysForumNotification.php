<?php

/**
* Manage Forum Notifications
*/
class BuckysForumNotification
{
    const ACTION_TYPE_REPLIED_TO_TOPIC = 'replied_to_topic';
    const ACTION_TYPE_REPLIED_TO_REPLY = 'replied_to_reply';
    const ACTION_TYPE_TOPIC_APPROVED = 'topic_approved';
    const ACTION_TYPE_REPLY_APPROVED = 'reply_approved';
    
    /**
    * Add notification for the repliers whose 'Someone reply to a topic that I replied' set 1
    * 
    * @param Int $ownerID: ReplierID
    * @param Int $topicID
    * @param Int $replyID
    */
    public function addNotificationsForReplies($replierID, $topicID, $replyID)
    {
        global $db, $BUCKYS_GLOBALS;
        
        $query = $db->prepare("SELECT DISTINCT(fr.creatorID), fr.replyID, fs.* FROM " . TABLE_FORUM_REPLIES . " as fr LEFT JOIN " . TABLE_FORUM_SETTINGS . " as fs ON fs.userID=fr.creatorID WHERE fr.topicID=%d", $topicID);
        $rows = $db->getResultsArray($query);
        
        $activity = new BuckysActivity();
        
        foreach($rows as $row)
        {
            $tForumSettings = array(
                'notifyRepliedToMyTopic' => $row['notifyRepliedToMyTopic'] === null ? $BUCKYS_GLOBALS['forum_settings']['notifyRepliedToMyTopic'] : $row['notifyRepliedToMyTopic'],
                'notifyRepliedToMyReply' => $row['notifyRepliedToMyReply'] === null ? $BUCKYS_GLOBALS['forum_settings']['notifyRepliedToMyReply'] : $row['notifyRepliedToMyReply'],
                'notifyMyPostApproved' => $row['notifyMyPostApproved'] === null ? $BUCKYS_GLOBALS['forum_settings']['notifyMyPostApproved'] : $row['notifyMyPostApproved']
            );
            if($row['replyID'] != $replyID && $row['creatorID'] != $replierID && $tForumSettings['notifyRepliedToMyReply'])
            {
                $activity->addActivity($row['creatorID'], $topicID, 'forum', BuckysForumNotification::ACTION_TYPE_REPLIED_TO_REPLY, $replyID);
            }
        }
        
        return true;
    }     
    
    /**
    * Add notification for the repliers whose 'Someone reply to my topic' set 1
    * 
    * @param Int $ownerID
    * @param Int $topicID
    * @param Int $replyID
    */
    public function addNotificationsForTopic($ownerID, $topicID, $replyID)
    {
        global $db, $BUCKYS_GLOBALS;
        
        $forumSettings = BuckysUser::getUserForumSettings($ownerID);
        
        $activity = new BuckysActivity();
        if($forumSettings['notifyRepliedToMyTopic'])
        {
            $activity->addActivity($ownerID, $topicID, 'forum', BuckysForumNotification::ACTION_TYPE_REPLIED_TO_TOPIC, $replyID);
        }
        
        return true;
    }
    
    /**
    * Add notification for the users whose 'My post approved' set 1.
    * 
    * @param Int $ownerID
    * @param Int $topicID
    * @param Int $replyID
    */
    public function addNotificationsForPendingPost($ownerID, $topicID, $replyID = null)
    {
        global $db, $BUCKYS_GLOBALS;
        
        $forumSettings = BuckysUser::getUserForumSettings($ownerID);
        
        $activity = new BuckysActivity();
        if($forumSettings['notifyRepliedToMyTopic'])
        {
            if($replyID == null)
                $activity->addActivity($ownerID, $topicID, 'forum', BuckysForumNotification::ACTION_TYPE_TOPIC_APPROVED, 0);
            else
                $activity->addActivity($ownerID, $topicID, 'forum', BuckysForumNotification::ACTION_TYPE_REPLY_APPROVED, $replyID);
        }
        
        return true;
    }
    
    /**
    * Make Notofications to Read
    * 
    * @param Int $userID
    */
    public function makeNotificationsToRead($userID, $categoryID = null, $topicID = null)
    {
        global $db;
        
        if($categoryID != null){
            $query = $db->prepare("UPDATE " . TABLE_ACTIVITES . " SET isNew=0 WHERE objectType='forum' AND isNew='1' AND userID=%d AND objectID IN " . 
                                  "(SELECT topicID FROM " . TABLE_FORUM_TOPICS . " WHERE categoryID=%d)", 
                                  $userID, $categoryID);
            $db->query($query);            
        }else if($topicID != null){
            $query = $db->prepare("UPDATE " . TABLE_ACTIVITES . " SET isNew=0 WHERE objectType='forum' AND isNew='1' AND userID=%d AND actionID IN " . 
                                  "(SELECT replyID FROM " . TABLE_FORUM_REPLIES . " WHERE topicID=%d)", 
                                  $userID, $topicID);
            $db->query($query);   
            $query = $db->prepare("UPDATE " . TABLE_ACTIVITES . " SET isNew=0 WHERE objectType='forum' AND isNew='1' AND userID=%d AND objectID=%d", $userID, $topicID);
            $db->query($query);   
            
        }else{
            $query = $db->prepare("UPDATE " . TABLE_ACTIVITES . " SET isNew=0 WHERE objectType='forum' AND isNew='1' AND userID=%d", $userID);
            $db->query($query);   
        }
        
    }
    
    /**
    * Get the number of new notifications
    * 
    * @param Int $userID
    * @return Int
    */
    public function getNumOfNewNotifications($userID)
    {
        global $db;
        
        $query = $db->prepare("SELECT count(activityID) FROM " . TABLE_ACTIVITES . " WHERE objectType='forum' AND isNew=1 AND activityStatus=1 AND userID=%d", $userID);
        $count = $db->getVar($query);
        
        return $count;
    }
    
    /**
    * Get Notifications
    * 
    * @param Int $userID
    * @param Int $limit = 10
    */
    public function getNewNotifications($userID, $limit = 10)
    {
        global $db;
        
        $query = $db->prepare("SELECT a.*, t.topicTitle, t.creatorID as topicCreatorID, r.creatorID as replierID, CONCAT(u.firstName, ' ', u.lastName) as rName, u.thumbnail as rThumbnail  FROM " . TABLE_ACTIVITES . " AS a " . 
                              "LEFT JOIN " . TABLE_FORUM_TOPICS . " AS t ON a.objectID=t.topicID " .   
                              "LEFT JOIN " . TABLE_FORUM_REPLIES . " AS r ON a.actionID=r.replyID " .   
                              "LEFT JOIN " . TABLE_USERS . " AS u ON r.creatorID=u.userID " .   
                              "WHERE a.objectType='forum' AND a.isNew=1 AND a.userID=%d AND a.activityStatus=1 ORDER BY a.createdDate DESC", $userID);
        $rows = $db->getResultsArray($query);
       
        return $rows;         
    }
}