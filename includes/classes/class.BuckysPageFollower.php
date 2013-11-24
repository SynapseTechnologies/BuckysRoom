<?php

/**
* Page Followers management
*/

class BuckysPageFollower
{
    
    const COUNT_PER_PAGE = 15;
    
    /**
    * Add followers
    * 
    * @param integer $pageID
    * @param integer $userID
    * @return int
    */
    public function addFollower($pageID, $userID)
    {
        global $db;

        if (!is_numeric($pageID) || !is_numeric($userID))
            return; // failed
        
        if ($this->hasRelationInFollow($pageID, $userID))
            return; // already exists
        
        $pageIns = new BuckysPage();
        $pageData = $pageIns->getPageByID($pageID);
        
        if (isset($pageData)) {
            $data = array();
            $data['pageID'] = $pageID;
            $data['userID'] = $userID;        
            $data['createdDate'] = date('Y-m-d H:i:s');
            $newID = $db->insertFromArray(TABLE_PAGE_FOLLOWERS, $data);
            
            return $newID;
        }
        else {
            return;
        }
        
        
    }
    
    /**
    * Unfollow
    * 
    * @param integer $pageID
    * @param integer $userID
    * @return int
    */
    public function removeFollower($pageID, $userID)
    {
        global $db;

        if (!is_numeric($pageID) || !is_numeric($userID))
            return; // failed
        
        if ($this->hasRelationInFollow($pageID, $userID)) {
            
            $query = sprintf("DELETE FROM %s WHERE pageID=%d AND userID=%d", TABLE_PAGE_FOLLOWERS, $pageID, $userID);
            $db->query($query);
            return true;
        }
        
        return;
    }
    
    /**
    * Check relations if it has already followed the page
    * 
    * @param integer $pageID
    * @param integer $userID
    */
    public function hasRelationInFollow($pageID, $userID) {
        
        global $db;
        $pageIns = new BuckysPage();
        
        if (!is_numeric($pageID) || !is_numeric($userID))
            return false; // failed
        
        $pageData = $pageIns->getPageByID($pageID);
        if ($pageData['userID'] == $userID) {
            //It means you are the owner of this page.
            return true;
        }
        
        
        $query = sprintf("SELECT * FROM %s WHERE pageID=%d AND userID=%d", TABLE_PAGE_FOLLOWERS, $pageID, $userID);
        
        if ($db->getRow($query)) {
            return true;
        }
        else {
            return false;
        }
        
    }
    
    /**
    * Get followers by PageID
    * 
    * @param integer $userID
    * @param integer $page
    * @param integer $limit
    * @param boolean $isRand
    * @return Indexed
    */
    public function getFollowers($pageID, $page = 1, $limit = 1, $isRand = false) {
        
        global $db;
        
        if (!is_numeric($pageID))
            return;
        
        $randStr = '';
        if( $isRand ) {
            
            $randStr = ', rand() ';
        }
            
        $query = sprintf("SELECT distinct(u.userID), u.*, CONCAT(u.firstName, ' ', u.lastName) AS fullName, IF(u.thumbnail = '', 0, 1) AS hasThumbnail 
                FROM %s AS pf 
                LEFT JOIN %s AS u ON u.userID = pf.userID 
                WHERE u.status=1 AND pf.pageID=%d ORDER BY hasTHumbnail DESC, fullName ASC %s
        ", TABLE_PAGE_FOLLOWERS, TABLE_USERS, $pageID, $randStr);
        
        $query .= " LIMIT " . ($page - 1) * $limit . ", " . $limit;
        
        $rows = $db->getResultsArray($query);
        
        return $rows;
        
        
    }
    
    
    /**
    * Remove page followers when removing page
    * 
    * @param mixed $pageID
    */
    public function removeAllFollowersByPageID($pageID) {
        
        global $db;
        
        if (!is_numeric($pageID))
            return;
        
        $query = sprintf("DELETE FROM %s WHERE pageID=%d", TABLE_PAGE_FOLLOWERS, $pageID);
        $db->query($query);
        
        return;
    }
    
    /**
    * Get number of followers
    * 
    * @param integer $pageID
    */
    public function getNumberOfFollowers ($pageID) {
        
        global $db;
        
        if (!is_numeric($pageID))
            return 0;
        
        $query = sprintf("SELECT count(*) FROM %s WHERE pageID=%d", TABLE_PAGE_FOLLOWERS, $pageID);
        return $db->getVar($query);
        
    }
    
    /**
    * Get page list with follower ID, in another words, return pages this user followed
    * 
    * @param integer $userID
    * @param integer $limit
    */
    public function getPagesByFollowerID ($userID, $page=1, $limit=null) {
        
        global $db;
        
        if (!is_numeric($userID))
            return;
            
        $limitCond = '';
        if (isset($limit) && is_numeric($limit) && $limit > 0 && $page >= 1) {
            $limitCond .= " LIMIT " . ($page - 1) * $limit . ", " . $limit;
        }
        
        $query = sprintf("SELECT pf.pageID, p.userID, p.title, p.logo, p.about, p.links, p.createdDate, (SELECT COUNT(*) FROM %s AS fcpf WHERE fcpf.pageID=pf.pageID) AS followerCount FROM %s AS pf LEFT JOIN %s AS p ON p.pageID=pf.pageID WHERE pf.userID=%d AND p.status=%d %s", TABLE_PAGE_FOLLOWERS, TABLE_PAGE_FOLLOWERS, TABLE_PAGES, $userID, BuckysPage::STATUS_ACTIVE, $limitCond);
        
        return $db->getResultsArray($query);
        
    }
    
    /**
    * Get page count by follower ID
    * 
    * @param integer $userID
    */
    public function getPagesCountByFollowerID($userID) {
        
        global $db;
        
        if (!is_numeric($userID))
            return 0;
            
        $query = sprintf("SELECT count(pf.pageID) FROM %s AS pf LEFT JOIN %s AS p ON p.pageID=pf.pageID WHERE pf.userID=%d AND p.status=%d %s", TABLE_PAGE_FOLLOWERS, TABLE_PAGES, $userID, BuckysPage::STATUS_ACTIVE, $limitCond);
        
        return $db->getVar($query);
        
        
    }
    
    


}