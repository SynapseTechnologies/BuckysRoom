<?php
/**
* Manage reported
*/

class BuckysReport
{
    public static $COUNT_PER_PAGE = 20;
    
    /**
    * Report post, comment and message
    * 
    * @param Int $userID
    * @param Int $objectID
    * @param String $objectType
    * 
    * @return Boolean or String
    */
    public function reportObject($userID, $objectID, $objectType)
    {
        global $db;
        
        //Check that the object has already been reported by the user
        $query = $db->prepare("SELECT reportID FROM " . TABLE_REPORTS . " WHERE reporterID=%d AND objectID=%d AND objectType=%s", $userID, $objectID, $objectType);
        $rID = $db->getVar($query);
        
        if($rID)
        {
            return MSG_ALREADY_REPORTED;
        }
        
        //Check that the object id is correct
        switch($objectType)
        {
            case 'post':
                $query = $db->prepare("SELECT postID FROM " . TABLE_POSTS . " WHERE postID=%d AND `post_status`=1", $objectID);
                break;
            case 'comment':
                $query = $db->prepare("SELECT commentID FROM " . TABLE_POSTS_COMMENTS . " WHERE commentID=%d AND commentStatus=1", $objectID);
                break;
            case 'message':
                $query = $db->prepare("SELECT messageID FROM " . TABLE_MESSAGES . " WHERE userID=%d AND messageID=%d AND messageStatus=1", $userID, $objectID);
                break;
            case 'topic':
                $query = $db->prepare("SELECT topicID FROM " . TABLE_FORUM_TOPICS . " WHERE topicID=%d AND `status`='publish'", $objectID);
                break;
            case 'reply':
                $query = $db->prepare("SELECT replyID FROM " . TABLE_FORUM_REPLIES . " WHERE replyID=%d AND `status`='publish'", $objectID);
                break;
                        
        }
        $oID = $db->getVar($query);
        
        if(!$oID)
        {
            return MSG_INVALID_REQUEST;
        }
        
        //Report Object
        $nId = $db->insertFromArray(TABLE_REPORTS, array('reporterID'=>$userID, 'objectID'=>$objectID, 'objectType'=>$objectType, 'reportStatus' => 1, 'reportedDate'=>date('Y-m-d H:i:s')));
        if(!$nId)
            return $db->getLastError();
        else
            return true;
    }
    
    /**
    * Get Reported Object Count
    * 
    * @param String $type
    * @return Int
    */
    public function getReportedObjectCount($type)
    {
        global $db;
        
        $query  = $db->prepare("SELECT count(objectID) FROM " . TABLE_REPORTS . " WHERE reportStatus=1 AND objectType=%s", $type);
        $count = $db->getVar($query);
        
        return $count;
    }
    
    /**
    * Get Reported Object Count
    * 
    * @param String $type
    * @param Int $page
    * @param int $limit
    * @return Array
    */
    public function getReportedObject($type, $page = 1, $limit = null)
    {
        global $db;
        
        if($type == 'post')
        {
            $query = $db->prepare("SELECT DISTINCT(r.reportID),
                                          r.objectID,   
                                          CONCAT(ru.firstName, ' ', ru.lastName) as reporterName, 
                                          ru.userID as reporterID, 
                                          ru.thumbnail as reporterThumb,
                                          CONCAT(ou.firstName, ' ', ou.lastName) as ownerName, 
                                          ou.userID as ownerID, 
                                          ou.thumbnail as ownerThumb,
                                          p.content as content,
                                          p.type,
                                          p.poster,
                                          p.postID,
                                          p.youtube_url,
                                          p.image
                                  FROM " . TABLE_REPORTS . " AS r " .
                                  "LEFT JOIN " . TABLE_USERS . " AS ru ON ru.userID=r.reporterID " .
                                  "LEFT JOIN " . TABLE_POSTS . " AS p ON r.objectID=p.postID " .
                                  "LEFT JOIN " . TABLE_USERS . " AS ou ON ou.userID=p.poster " .
                                  "WHERE reportStatus=1 AND objectType=%s ORDER By reportedDate ", $type);
        }else if($type == 'comment'){
            $query = $db->prepare("SELECT DISTINCT(r.reportID), 
                                          r.objectID,
                                          CONCAT(ru.firstName, ' ', ru.lastName) as reporterName, 
                                          ru.userID as reporterID, 
                                          ru.thumbnail as reporterThumb,
                                          CONCAT(ou.firstName, ' ', ou.lastName) as ownerName, 
                                          ou.userID as ownerID, 
                                          ou.thumbnail as ownerThumb,
                                          c.content as content
                                  FROM " . TABLE_REPORTS . " AS r " .
                                  "LEFT JOIN " . TABLE_USERS . " AS ru ON ru.userID=r.reporterID " .
                                  "LEFT JOIN " . TABLE_POSTS_COMMENTS . " AS c ON r.objectID=c.commentID " .
                                  "LEFT JOIN " . TABLE_USERS . " AS ou ON ou.userID=c.commenter " .
                                  "WHERE reportStatus=1 AND objectType=%s ORDER By reportedDate ", $type);
        }else if($type == 'message'){
            $query = $db->prepare("SELECT DISTINCT(r.reportID), 
                                          r.objectID,
                                          CONCAT(ru.firstName, ' ', ru.lastName) as reporterName, 
                                          ru.userID as reporterID, 
                                          ru.thumbnail as reporterThumb,
                                          CONCAT(sender.firstName, ' ', sender.lastName) as ownerName, 
                                          sender.userID as ownerID, 
                                          sender.thumbnail as ownerThumb,
                                          CONCAT(sender.firstName, ' ', sender.lastName) as senderName, 
                                          sender.userID as senderID, 
                                          sender.thumbnail as senderThumb,
                                          CONCAT(receiver.firstName, ' ', receiver.lastName) as receiverName, 
                                          receiver.userID as receiverID, 
                                          receiver.thumbnail as receiverThumb,                                          
                                          m.subject as subject,
                                          m.body as content
                                  FROM " . TABLE_REPORTS . " AS r " .
                                  "LEFT JOIN " . TABLE_USERS . " AS ru ON ru.userID=r.reporterID " .
                                  "LEFT JOIN " . TABLE_MESSAGES . " AS m ON r.objectID=m.messageID " .
                                  "LEFT JOIN " . TABLE_USERS . " AS sender ON sender.userID=m.sender " .
                                  "LEFT JOIN " . TABLE_USERS . " AS receiver ON receiver.userID=m.receiver " .
                                  "WHERE reportStatus=1 AND objectType=%s ORDER By reportedDate ", $type);
        }else if($type == 'topic'){
            $query = $db->prepare("SELECT DISTINCT(r.reportID), 
                                          r.objectID,
                                          CONCAT(ru.firstName, ' ', ru.lastName) as reporterName, 
                                          ru.userID as reporterID, 
                                          ru.thumbnail as reporterThumb,
                                          CONCAT(ou.firstName, ' ', ou.lastName) as ownerName, 
                                          ou.userID as ownerID, 
                                          ou.thumbnail as ownerThumb,
                                          ft.topicContent as content,
                                          ft.topicTitle as title
                                  FROM " . TABLE_REPORTS . " AS r " .
                                  "LEFT JOIN " . TABLE_USERS . " AS ru ON ru.userID=r.reporterID " .
                                  "LEFT JOIN " . TABLE_FORUM_TOPICS . " AS ft ON r.objectID=ft.topicID " .
                                  "LEFT JOIN " . TABLE_USERS . " AS ou ON ou.userID=ft.creatorID " .
                                  "WHERE reportStatus=1 AND objectType=%s ORDER By reportedDate ", $type);
        }else if($type == 'reply'){
            $query = $db->prepare("SELECT DISTINCT(r.reportID), 
                                          r.objectID,
                                          CONCAT(ru.firstName, ' ', ru.lastName) as reporterName, 
                                          ru.userID as reporterID, 
                                          ru.thumbnail as reporterThumb,
                                          CONCAT(ou.firstName, ' ', ou.lastName) as ownerName, 
                                          ou.userID as ownerID, 
                                          ou.thumbnail as ownerThumb,
                                          fr.replyContent as content,
                                          ft.topicTitle as title
                                  FROM " . TABLE_REPORTS . " AS r " .
                                  "LEFT JOIN " . TABLE_USERS . " AS ru ON ru.userID=r.reporterID " .
                                  "LEFT JOIN " . TABLE_FORUM_REPLIES . " AS fr ON r.objectID=fr.replyID " .
                                  "LEFT JOIN " . TABLE_FORUM_TOPICS . " AS ft ON ft.topicID=fr.topicID " .
                                  "LEFT JOIN " . TABLE_USERS . " AS ou ON ou.userID=fr.creatorID " .
                                  "WHERE reportStatus=1 AND objectType=%s ORDER By reportedDate ", $type);
        }
        
        
        if($limit == null)  
            $limit = BuckysReport::$COUNT_PER_PAGE;
            
        $query .= " LIMIT " . ($page - 1) * $limit . ", " . $limit;
        
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    /**
    * Delete Objects
    * 
    * @param Array $ids
    * @param String $objectType
    * @param String $modeartorType
    */
    public function deleteObjects($ids, $objectType, $moderatorType)
    {
        global $db;
        
        if(!is_array($ids))
            $ids = array($ids);
        
        $ids = $db->escapeInput($ids);
        
        $query = $db->prepare("SELECT * FROM " . TABLE_REPORTS . " WHERE objectType=%s AND reportID in (" . implode(", ", $ids) . ")", $objectType);
        $rows = $db->getResultsArray($query);
        
        foreach($rows as $row)
        {
            if($row['objectType'] == 'post')
            {
                $post = $db->getRow("SELECT * FROM " . TABLE_POSTS . " WHERE postID=" . $row['objectID']);                
                BuckysPost::deletePost($post['poster'], $post['postID']);                                
            }else if($row['objectType'] == 'comment'){
                //Getting Data
                $comment = $db->getRow("SELECT * FROM " . TABLE_POSTS_COMMENTS . " WHERE commentID=" . $row['objectID']);
                BuckysComment::deleteComment($comment['commenter'], $comment['commentID']);                
            }else if($row['objectType'] == 'message'){
                //Delete Message
                $db->query("DELETE FROM " . TABLE_MESSAGES . " WHERE messageID=" . $row['objectID']);
            }else if($row['objectType'] == 'topic'){
                //Delete Topic
                BuckysForumTopic::deleteTopic($row['objectID']);
            }else if($row['objectType'] == 'reply'){
                //Delete Topic
                BuckysForumReply::deleteReply($row['objectID']);
            }
            
            //Delete the row on the report table
            $db->query("DELETE FROM " . TABLE_REPORTS . " WHERE reportID=" . $row['reportID']);
        }
        
        return;
    }
    
    /**
    * Approve Reported Objects
    * 
    * @param Array $ids
    * @param Int $objectType
    * @param Int $moderatorType
    */
    public function approveObjects($ids, $objectType, $moderatorType)
    {
        global $db;
        
        if(!is_array($ids))
            $ids = array($ids);
        
        $ids = $db->escapeInput($ids);
        
        $query = $db->prepare("SELECT * FROM " . TABLE_REPORTS . " WHERE objectType=%s AND reportID in (" . implode(", ", $ids) . ")", $objectType);
        $rows = $db->getResultsArray($query);
        
        foreach($rows as $row)
        {
            //Delete the row on the report table
            $db->query("DELETE FROM " . TABLE_REPORTS . " WHERE reportID=" . $row['reportID']);
        }
        
        return;
    }
    
    /**
    * Ban users
    * 
    * @param Array $ids
    * @param Int $objectType
    * @param Int $moderatorType
    */
    public function banUsers($ids, $objectType, $moderatorType)
    {
        global $db;
        
        if(!is_array($ids))
            $ids = array($ids);
            
        $query = $db->prepare("SELECT * FROM " . TABLE_REPORTS . " WHERE objectType=%s AND reportID in (" . implode(", ", $ids) . ")", $objectType);
        $rows = $db->getResultsArray($query);
        
        foreach($rows as $row)
        {
            //Getting User ID
            if($row['objectType'] == 'post')
            {
                $query = "SELECT poster FROM " . TABLE_POSTS . " WHERE postID=" . $row['objectID'];                
            }else if($row['objectType'] == 'comment'){
                $query = "SELECT commenter FROM " . TABLE_POSTS_COMMENTS . " WHERE commentID=" . $row['objectID'];                
            }else if($row['objectType'] == 'message'){
                $query = "SELECT sender FROM " . TABLE_MESSAGES . " WHERE messageID=" . $row['objectID'];                
            }else if($row['objectType'] == 'topic'){
                $query = "SELECT creatorID FROM " . TABLE_FORUM_TOPICS . " WHERE topicID=" . $row['objectID'];                
            }else if($row['objectType'] == 'reply'){
                $query = "SELECT creatorID FROM " . TABLE_FORUM_REPLIES . " WHERE replyID=" . $row['objectID'];                
            }            
            $userID = $db->getVar($query);
            
            if($userID)
                BuckysBanUser::banUser($userID);
                
        }
    }
    
}
