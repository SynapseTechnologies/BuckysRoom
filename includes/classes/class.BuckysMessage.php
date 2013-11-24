<?php
/***
* Manage Message Class
*/

class BuckysMessage
{
    public static  $COUNT_PER_PAGE = 20;
    
    /**
    * Getting New Messages
    * 
    * @param mixed $userID
    * @return one
    */
    public function getNumOfNewMessages($userID)
    {
        global $db;
        
        $query = $db->prepare("SELECT count(*) FROM " . TABLE_MESSAGES . " WHERE receiver=%s AND status='unread' AND is_trash=0", $userID);
        $num = $db->getVar($query);
        return $num;
    }
    
    /**
    * Create New Message
    * 
    * @param mixed $data
    */
    public function composeMessage($data)
    {
        global $db;
        
        $receivers = $data['to'];
        if( !buckys_not_null($receivers) )
        {
            buckys_add_message(MSG_SENDER_EMPTY_ERROR, MSG_TYPE_ERROR);
            return false;
        }
        
        if( trim($data['subject']) == '' )
        {
            buckys_add_message(MSG_MESSAGE_SUBJECT_EMPTY_ERROR, MSG_TYPE_ERROR);
            return false;
        }
        
        if( trim($data['body']) == '' )
        {
            buckys_add_message(MSG_MESSAGE_BODY_EMPTY_ERROR, MSG_TYPE_ERROR);
            return false;
        }
        
        
        $createdDate = date("Y-m-d H:i:s");
        
        if(!is_array($receivers))
            $receivers = array($receivers);
        
        //Remove Duplicated Messages
        $receivers = array_unique($receivers);
        
        $nonFriend = array();
        $sents = array();
        $errors = array();
        $isError = false;
        
        foreach($receivers as $receiver){
            
            //Create A message row for Sender
            $sender = $data['userID'];
            
            $receiverInfo = BuckysUser::getUserBasicInfo($receiver);
            
            //confirm that current user and receiver is friend
            /*if(!BuckysFriend::isFriend($receiver, $sender))
            {                                
                $nonFriend[] = $receiverInfo['firstName'] . " " . $receiverInfo['lastName'];
                $isError = true;
                continue;
            }*/
            
            
            $insertData = array(
                'userID' => $sender,
                'sender' => $sender,
                'receiver' => $receiver,
                'subject' => $data['subject'],
                'body' => $data['body'],
                'status' => 'read',
                'created_date' => $createdDate
            );
            $newId1 = $db->insertFromArray(TABLE_MESSAGES, $insertData);
            
            //Create A message row for receiver
            $sender = $data['userID'];
            $insertData = array(
                'userID' => $receiver,
                'sender' => $sender,
                'receiver' => $receiver,
                'subject' => $data['subject'],
                'body' => $data['body'],
                'status' => 'unread',
                'created_date' => $createdDate
            );
            $newId2 = $db->insertFromArray(TABLE_MESSAGES, $insertData);
            
            $sents[] = $receiverInfo['firstName'] . ' ' . $receiverInfo['lastName'];
            
        }
        
        if(count($sents) > 0)
            buckys_add_message(MSG_NEW_MESSAGE_SENT, MSG_TYPE_SUCCESS);
        if(count($nonFriend) > 0){
            if(count($nonFriend) > 1)
                $msg = sprintf(MSG_COMPOSE_MESSAGE_ERROR_TO_NON_FRIENDS, implode(", ", $nonFriend));
            else
                $msg = sprintf(MSG_COMPOSE_MESSAGE_ERROR_TO_NON_FRIEND, $nonFriend[0]);
            buckys_add_message($msg, MSG_TYPE_ERROR);
        }
        
        return !$isError;
    }
    
    /**
    * Get Received Messages
    *     
    * @param Int $userID
    * @param Int $page
    * @param String 'all', 'read', 'unread'    
    * @return Array
    */
    public function getReceivedMessages($userID, $page = 1, $status = 'all')
    {
        global $db;
        
        $query  = $db->prepare("SELECT m.*, CONCAT(u.firstName, ' ', u.lastName) as senderName, u.thumbnail FROM " . TABLE_MESSAGES . " as m LEFT JOIN " . TABLE_USERS . " as u ON m.sender = u.userID WHERE m.userID=%s AND m.is_trash = 0 AND m.receiver=%s ", $userID, $userID);
        
        switch($status)
        {
            case 'read':
                $query .= " AND m.status='read'";
                break;
            case 'unread':
                $query .= " AND m.status='unread'";
                break;            
        }
        
        $query .= " ORDER BY created_date desc ";
        //Append Pagination Query
        $query .= " LIMIT " . ($page - 1) * BuckysMessage::$COUNT_PER_PAGE . ", " . BuckysMessage::$COUNT_PER_PAGE;
        
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    /**
    * Get Sent Messages
    *     
    * @param Int $userID
    * @return Array
    */
    public function getSentMessages($userID, $page = 1)
    {
        global $db;
        
        $query  = $db->prepare("SELECT m.*, CONCAT(u.firstName, ' ', u.lastName) as receiverName FROM " . TABLE_MESSAGES . " as m LEFT JOIN " . TABLE_USERS . " as u ON m.receiver = u.userID WHERE m.userID=%s AND m.is_trash=0 AND m.sender=%s ORDER BY created_date desc", $userID, $userID);
        
        //Append Pagination Query
        $query .= " LIMIT " . ($page - 1) * BuckysMessage::$COUNT_PER_PAGE . ", " . BuckysMessage::$COUNT_PER_PAGE;
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    /**
    * Getting Total Messages
    * 
    * @param Int $userID
    * @param String $boxType: inbox, sent, trash
    * @param String $status: all, read, unread
    */
    public function getTotalNumOfMessages($userID, $boxType = 'inbox')
    {
        global $db;
        
        $query = $db->prepare("SELECT count(messageID) FROM " . TABLE_MESSAGES . " WHERE userID=%d ", $userID);
        if($boxType == 'inbox')
            $query .= $db->prepare(" AND is_trash=0 AND receiver=%d", $userID);
        else if($boxType == 'sent')
            $query .= $db->prepare(" AND is_trash=0 AND sender=%d", $userID);
        else if($boxType == 'trash')
            $query .= " AND is_trash=1";
        
        $count = $db->getVar($query);
        
        return $count;
    }
    
    
    /**
    * Get Trash Messages
    *     
    * @param Int $userID
    * @return Array
    */
    public function getDeletedMessages($userID, $page = 1)
    {
        global $db;
        
        $query  = $db->prepare("SELECT m.*, CONCAT(u.firstName, ' ', u.lastName) as userName FROM " . TABLE_MESSAGES . " as m LEFT JOIN " . TABLE_USERS . " as u ON ((m.receiver = u.userID And m.receiver != %d) or (m.sender = u.userID AND m.sender != %d)) WHERE m.userID=%d AND m.is_trash=1 GROUP BY m.messageID ORDER BY created_date desc", $userID, $userID, $userID);
        
        //Append Pagination Query
        $query .= " LIMIT " . ($page - 1) * BuckysMessage::$COUNT_PER_PAGE . ", " . BuckysMessage::$COUNT_PER_PAGE;
        
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    
    /**
    * Remove Messages
    * Sent the removed flag to be 1
    * 
    * @param mixed $ids
    */
    public function deleteMessages($ids)
    {
        global $db, $userID;
        
        if( !is_array($ids) )
            $ids = array($ids);
        
        $ids = $db->escapeInput($ids);
        
        $query = $db->prepare("UPDATE " . TABLE_MESSAGES . " set is_trash=1 WHERE userID=%d AND messageID in (" . implode(', ', $ids) . ")",  $userID);
        
        $db->query($query);
        
        return true;
    }
    
    /**
    * Remove Messages
    * Sent the removed flag to be 1
    * 
    * @param mixed $ids
    */
    public function deleteMessagesForever($ids)
    {
        global $db, $userID;
        
        if( !is_array($ids) )
            $ids = array($ids);
        
        $ids = $db->escapeInput($ids);
        
        $query = $db->prepare("DELETE FROM " . TABLE_MESSAGES . " WHERE userID=%d AND is_trash=1 AND messageID in (" . implode(', ', $ids) . ")", $userID);
        
        $db->query($query);
        
        return true;
    }
    
    /**
    * Get Message Detail
    * 
    * @param Int $messageID
    * @return array
    */
    public function getMessage($messageID)
    {
        global $db, $userID;
        
        $query  = $db->prepare("SELECT m.*, CONCAT(u.firstName, ' ', u.lastName) as senderName, CONCAT(u1.firstName, ' ', u1.lastName) as receiverName, r.reportID FROM " . TABLE_MESSAGES . " as m " .
                               "LEFT JOIN " . TABLE_USERS . " as u ON m.sender = u.userID " . 
                               "LEFT JOIN " . TABLE_USERS . " as u1 ON m.receiver = u1.userID " . 
                               "LEFT JOIN " . TABLE_REPORTS . " as r ON r.objectType='message' AND r.objectID = m.messageID AND r.reporterID=%d " . 
                               "WHERE m.userID=%d AND m.messageID=%d ORDER BY created_date desc", $userID, $userID, $messageID);
        
        $row = $db->getRow($query);
        
        return $row;
    }
    
    /**
    * Change Message Status : read or unread
    */
    public function changeMessageStatus($messageID, $status = 'read')
    {
        global $db, $userID;
        
        $query = $db->prepare('UPDATE ' . TABLE_MESSAGES . ' SET status=%s WHERE messageID=%s', $status, $messageID);
        $db->query($query);
        
        return $row;
    }
    
    /**
    * Getting Next Record
    * 
    * @param Int $userID
    * @param Int $messageID
    * @param String $type
    * @return Int
    */
    public function getNextID($userID, $messageID, $type)
    {
        global $db;
        
        if( $type == 'inbox' )
        {
            $query  = $db->prepare("SELECT m.messageID FROM " . TABLE_MESSAGES . " as m WHERE m.messageID < %s AND m.userID=%s AND m.is_trash = 0 AND m.receiver=%s ORDER BY created_date desc LIMIT 1", $messageID, $userID, $userID);            
        }else if( $type == 'sent' ){
            $query  = $db->prepare("SELECT m.messageID FROM " . TABLE_MESSAGES . " as m WHERE m.messageID < %s AND m.userID=%s AND m.is_trash=0 AND m.sender=%s ORDER BY created_date desc", $messageID, $userID, $userID);
        }else if( $type == 'trash' ){
            $query  = $db->prepare("SELECT m.messageID FROM " . TABLE_MESSAGES . " as m WHERE m.messageID < %s AND m.userID=%s AND m.is_trash=1 GROUP BY m.messageID ORDER BY created_date desc LIMIT 1", $messageID, $userID, $userID);
        }
        $id = $db->getVar($query);
        
        return $id;
    }
    
    /**
    * Getting Prev Record
    * 
    * @param Int $userID
    * @param Int $messageID
    * @param String $type
    * @return Int
    */
    public function getPrevID($userID, $messageID, $type)
    {
        global $db;
        
        if( $type == 'inbox' )
        {
            $query  = $db->prepare("SELECT m.messageID FROM " . TABLE_MESSAGES . " as m WHERE m.messageID > %s AND m.userID=%s AND m.is_trash = 0 AND m.receiver=%s ORDER BY created_date ASC LIMIT 1", $messageID, $userID, $userID);
        }else if( $type == 'sent' ){
            $query  = $db->prepare("SELECT m.messageID FROM " . TABLE_MESSAGES . " as m WHERE m.messageID > %s AND m.userID=%s AND m.is_trash=0 AND m.sender=%s ORDER BY created_date ASC", $messageID, $userID, $userID);
        }else if( $type == 'trash' ){
            $query  = $db->prepare("SELECT m.messageID FROM " . TABLE_MESSAGES . " as m WHERE m.messageID > %s AND m.userID=%s AND m.is_trash=1 GROUP BY m.messageID ORDER BY created_date ASC LIMIT 1", $messageID, $userID, $userID);
        }
        
        $id = $db->getVar($query);
        
        return $id;
    }
}

