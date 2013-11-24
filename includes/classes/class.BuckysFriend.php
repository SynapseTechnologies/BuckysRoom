<?php

class BuckysFriend
{
    public static $COUNT_PER_PAGE = 15;
    /**
    * Getting Friend Request
    * 
    * @param Int $userID
    * @return one
    */
    public function getNewFriendRequests($userID)
    {
        global $db;
        
        $query = $db->prepare("SELECT COUNT(DISTINCT(userID)) FROM " . TABLE_FRIENDS . " WHERE userFriendID=%d  AND `status`=0", $userID);        
        $num = $db->getVar($query);
        
        return $num;
    }
    
    /**
    * Getting User Friends
    * 
    * @param Int $userID
    */
    public function getFriendIDs($userID)
    {
        global $db;
        
        $query = $db->prepare("SELECT userFriendID FROM " . TABLE_FRIENDS . " WHERE userID=%d AND `status`=1", $userID);
        $rows = $db->getResultsArray($query);
        
        $ids = array();
        foreach($rows as $row)
        {
            if($row['user1'] == $userID)
                $ids[] = $row['user2'];
            else
                $ids[] = $row['user1'];
        }
        return $ids;
    }
    
    /**
    * Getting User Friends
    * 
    * @param Int $userID
    * @param int $limit
    * @param Boolean $isRand
    */
    public function getAllFriends($userID, $page = 1, $limit = 1, $isRand = false)
    {
        global $db;
        
        if( $isRand )
            $query = $db->prepare("SELECT u.*, f.friendID, IF(u.thumbnail = '', 0, 1) as hasThumbnail FROM " . TABLE_FRIENDS . " as f LEFT JOIN " . TABLE_USERS . " as u ON u.userID = f.userFriendID WHERE u.status=1 AND f.userID=%d AND f.status='1' GROUP BY u.userID ORDER BY hasThumbnail DESC, rand() ", $userID);
        else
            $query = $db->prepare("SELECT u.*, CONCAT(u.firstName, ' ', u.lastName) as fullName, f.friendID, IF(u.thumbnail = '', 0, 1) as hasThumbnail FROM " . TABLE_FRIENDS . " as f LEFT JOIN " . TABLE_USERS . " as u ON u.userID = f.userFriendID WHERE u.status=1 AND f.userID=%d AND f.status=1 GROUP BY u.userID ORDER BY hasTHumbnail DESC, fullName ASC ", $userID);
        
        $query .= " LIMIT " . ($page - 1) * $limit . ", " . $limit;
        
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    /**
    * Get Total Count of friends
    * 
    * @param Int $userID
    * @return Int
    */
    public function getNumberOfFriends($userID)    
    {
        global $db;
        
        $query = $db->prepare("SELECT count(distinct(f.userFriendID)) FROM " . TABLE_FRIENDS . " as f LEFT JOIN " . TABLE_USERS . " as u ON u.userID=f.userFriendID WHERE u.status=1 AND f.userID=%d AND f.status=1", $userID);
        $count = $db->getVar($query);
        
        return $count;
    }
    
    /**
    * Search User Friends
    * 
    * @param mixed $userID
    * @param term  String
    */
    public function searchFriends($userID, $term)
    {
        global $db;
        
        $query = "SELECT distinct(u.userID), u.*, CONCAT(u.firstName, ' ', u.lastName) as fullName FROM " . TABLE_FRIENDS . " as f LEFT JOIN " . TABLE_USERS . " as u ON u.userID = f.userFriendID WHERE u.status=1 AND f.userID=" . $userID . " AND f.status=1 and (CONCAT(u.firstName, ' ', u.lastName) LIKE '%" . $db->escapeInput($term) . "%') ORDER BY fullName";
            
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    /**
    * Check that the two users is friend
    * 
    * @param mixed $userID1
    * @param mixed $userID2
    */
    public function isFriend($userID, $userFriendID)
    {
        global $db;
        
        $query = $db->prepare("SELECT friendID FROM " . TABLE_FRIENDS . " WHERE userID=%d AND userFriendID=%d AND `status`='1'", $userID, $userFriendID);
        $fid = $db->getVar($query);
        
        return $fid;
    }
    
    /**
    * Return True if the userID1 sent a friend request to the userID2
    * 
    * @param mixed $userID1
    * @param mixed $userID2
    */
    public function isSentFriendRequest($userID, $userFriendID)
    {
        global $db;
        
        $query = $db->prepare("SELECT friendID FROM " . TABLE_FRIENDS . " WHERE userID=%d AND userFriendID=%s AND `status`='0'", $userID, $userFriendID);
        $fid = $db->getVar($query);
        
        return $fid;
    }
    
    /**
    * Get Pending Request
    * 
    * @param Int $userID
    * @param Int $limit
    * @return Array
    */
    public function getPendingRequests($userID, $page = 1)
    {
        global $db;
        
        $query = $db->prepare("SELECT u.*, CONCAT(u.firstName, ' ', u.lastName) as fullName, f.friendID FROM " . TABLE_FRIENDS . " as f LEFT JOIN " . TABLE_USERS . " as u ON u.userID=f.userFriendID WHERE u.status=1 AND f.userID=%d AND f.status='0' ORDER BY fullName ASC", $userID);
        
        $query .= " LIMIT " . ($page - 1) * BuckysFriend::$COUNT_PER_PAGE . ", " . BuckysMessage::$COUNT_PER_PAGE;
        
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    /**
    * Get Total Number Of Friends
    * 
    * @param Int $userID
    * @return one
    */
    public function getNumberOfPendingRequests($userID)
    {
        global $db;
        
        $query = $db->prepare("SELECT count(f.friendID) FROM " . TABLE_FRIENDS . " as f LEFT JOIN " . TABLE_USERS . " as u ON u.userID=f.userFriendID WHERE u.status=1 AND f.userID=%d AND f.status='0' ", $userID);
            
        $count = $db->getVar($query);
        
        return $count;
    }
    
    /**
    * Get Received Request
    * 
    * @param Int $userID
    * @param Int $limit
    * @return Array
    */
    public function getReceivedRequests($userID, $page = 1)
    {
        global $db;
        
        $query = $db->prepare("SELECT u.*, CONCAT(u.firstName, ' ', u.lastName) as fullName, f.friendID FROM " . TABLE_FRIENDS . " as f LEFT JOIN " . TABLE_USERS . " as u ON u.userID=f.userID WHERE u.status=1 AND f.userFriendID=%d AND f.status='0' ORDER BY fullName ", $userID);
        
        $query .= " LIMIT " . ($page - 1) * BuckysFriend::$COUNT_PER_PAGE . ", " . BuckysFriend::$COUNT_PER_PAGE;
            
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    /**
    * Get Number of Friend Requests
    *     
    * @param Int $userID
    * @return Int
    */
    public function getNumberOfReceivedRequests($userID)
    {
        global $db;
        
        $query = $db->prepare("SELECT count(f.friendID) FROM " . TABLE_FRIENDS . " as f LEFT JOIN " . TABLE_USERS . " as u ON u.userID=f.userID WHERE u.status=1  AND f.userFriendID=%d AND f.status='0'", $userID);
            
        $row = $db->getVar($query);
        
        return $row;
    }
    
    
    
    /**
    * Unfriend
    * 
    * @param Int $userID
    * @param Array $ids
    */
    public function unfriend($userID, $ids)
    {
        global $db;
        
        if( !is_array($ids) )
            $ids = array($ids);
        
        foreach($ids as $id)
        {
            $query = $db->prepare("DELETE FROM " . TABLE_FRIENDS . " WHERE userID=%d AND userFriendID=%d", $userID, $id);         
            $db->query($query);
            $query = $db->prepare("DELETE FROM " . TABLE_FRIENDS . " WHERE userFriendID=%d AND userID=%d", $userID, $id);         
            $db->query($query);
            
        }
        
        return true;
    }
    
    /**
    * Delete
    * 
    * @param Int $userID
    * @param Array $ids
    */
    public function delete($userID, $ids)
    {
        global $db;
        
        if( !is_array($ids) )
            $ids = array($ids);
        
        //Add userID times
        array_push($ids, $userID);
        
        $query = $db->prepare("DELETE FROM " . TABLE_FRIENDS . " WHERE userFriendID IN (" . implode(", ", array_fill(0, count($ids) - 1, '%d')) . ") AND userID=%d AND status=0", $ids); 
        
        return $db->query($query);
    }
    
    /**
    * Decline
    * 
    * @param Int $userID
    * @param Array $ids
    */
    public function decline($userID, $ids)
    {
        global $db;
        
        if( !is_array($ids) )
            $ids = array($ids);
        
        //Add userID times
        array_push($ids, $userID);
        
        $query = $db->prepare("Delete FROM " . TABLE_FRIENDS . "  WHERE userID IN (" . implode(", ", array_fill(0, count($ids) - 1, '%d')) . ") AND userFriendID=%d", $ids); 
//        $query = $db->prepare("UPDATE " . TABLE_FRIENDS . " SET `status`='-1' WHERE friendID IN (" . implode(", ", array_fill(0, count($ids) - 1, '%d')) . ") AND userFriendID=%d", $ids); 
        
        return $db->query($query);
    }
    
    /**
    * Decline
    * 
    * @param Int $userID
    * @param Array $ids
    */
    public function accept($userID, $ids)
    {
        global $db;
        
        if( !is_array($ids) )
            $ids = array($ids);
        
        //Add userID times
        array_push($ids, $userID);
        
        //Getting Friend IDs
        $query = $db->prepare("SELECT friendID, userID FROM " . TABLE_FRIENDS . " WHERE userID IN (" . implode(", ", array_fill(0, count($ids) - 1, '%d')) . ") AND status=0 AND userFriendID=%d", $ids);
        $frows = $db->getResultsArray($query);
        
        foreach($frows as $row)
        {
            $query = $db->prepare("DELETE FROM " . TABLE_FRIENDS . " WHERE (userID=%d AND userFriendID=%d) OR (userID=%d AND userFriendID=%d)", $userID, $row['userID'], $row['userID'], $userID);             
            $db->query($query);
            
            $db->insertFromArray(TABLE_FRIENDS, array('userID'=>$row['userID'], 'userFriendID' => $userID, 'status' => 1));
            $db->insertFromArray(TABLE_FRIENDS, array('userID'=>$userID, 'userFriendID' => $row['userID'], 'status' => 1));
        }
        
        return true;
    }
    
    /**
    * Decline
    * 
    * @param Int $userID
    * @param Array $ids
    */
    public function sendFriendRequest($userID, $id)
    {
        global $db;
        
        $query =  $db->prepare( "INSERT INTO " . TABLE_FRIENDS . "(userID, userFriendID, `status`)values(%d, %d, '0')", $userID, $id ); 
        $db->query($query);
            
        
        return true;
    }
    
    
        
}