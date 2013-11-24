<?php

/**
* Page management
*/

class BuckysPage
{
    
    const DEFAULT_PAGE_TITLE = 'Name of Page';
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    
    
    /**
    * Add pages
    * 
    * @param string $data
    * @return int
    */
    public function addPage($data)
    {
        global $db;

        if (!is_numeric($data['userID']) || $data['title'] == '')
            return; // failed
        
        $data['createdDate'] = date('Y-m-d H:i:s');
        $newID = $db->insertFromArray(TABLE_PAGES, $data);
        return $newID;
    }
    
    /**
    * Get Page data by page ID
    * 
    * @param integer $pageID
    */
    public function getPageByID($pageID, $isActive=true) {
        
        global $db;
        
        if (!is_numeric($pageID)) {
            return;
        }
        
        $query = sprintf("SELECT * FROM %s WHERE pageID=%d", TABLE_PAGES, $pageID);
        
        $result = $db->getRow($query);
        
        if ($result) {
            $result['links'] = str_replace('&quot;', '"', $result['links']);
            
        }
        
        
        if ($isActive) {
            if ($result['status'] == BuckysPage::STATUS_ACTIVE) {
                return $result;
            }
            else {
                return;
            }
        }
        
        return $result;
    }
    
    /**
    * Update Data
    * 
    * @param integer $pageID
    * @param array $data
    */
    public function updateData($pageID, $data) {
        
        global $db;
        
        if (!is_numeric($pageID)) {
            return;
        }
        
        $fieldStr = '';
        
        if (is_array($data) && count($data) > 0) {
            
            foreach($data as $key=>$value) {
                $data[$key] = strip_tags($value);
                
                if ($key == 'about') {
                    $data[$key] = substr($data[$key], 0, 4999);
                }
                
                if ($key == 'title') {
                    $data[$key] = substr($data[$key], 0, 499);
                }
            }
            
            
            $db->updateFromArray(TABLE_PAGES, $data, array('pageID'=>$pageID));
            
        }
        
        return;
        
    }
    
    /**
    * Get Pages by User ID
    * 
    * @param mixed $userID
    */
    public function getPagesByUserId($userID) {
        global $db;
        
        if (!is_numeric($userID))
            return;
        $query = sprintf('SELECT * FROM %s WHERE userID=%d ORDER BY title', TABLE_PAGES, $userID);
        $result = $db->getResultsArray($query);
        
        if (is_array($result)) {
            
            foreach($result as $key=>$value) {
                $result[$key]['links'] = str_replace('&quot;', '"', $result[$key]['links']);
            }
            
        }
        
        return $result;
        
    }
    
    /**
    * Delete page by PageID
    * 
    * @param integer $userID
    * @param integer $pageID
    */
    public function deletePageByID($pageID, $userID=null) {
        
        global $db;
        $postIns = new BuckysPost();
        $pageFollowerIns = new BuckysPageFollower();
        
        
        //Get Page info & related posts belonged to this page.
        $pageData = $this->getPageByID($pageID);
        if (!$pageData) {
            return false;
        }
        
        if (!empty($userID) && $pageData['userID'] != $userID) {
            return false; // You don't have permission to delete this page
        }
        
        
        $postList = $postIns->getPostsByPageID($pageData['pageID']);
        
        //Delete related posts
        if (is_array($postList) && count($postList) > 0) {
            foreach($postList as $postData) {
                $postIns->deletePost($pageData['userID'], $postData['postID']);
            }
        }
        
        //Delete page
        $query = sprintf("DELETE FROM %s WHERE pageID=%d", TABLE_PAGES, $pageID);
        $db->query($query);
        
        //Delete followers
        $pageFollowerIns->removeAllFollowersByPageID($pageID);
        
        return true;
        
    }
    
    /**
    * Delete page data by user ID
    * 
    * @param integer $userID
    */
    public function deletePageByUserID($userID) {
        
        global $db;
        
        if (!is_numeric($userID))
            return;
        
        $pageList = $this->getPagesByUserId($userID);
        
        //Delete followers
        $pageFollowerIns = new BuckysPageFollower();
        if (count($pageList) > 0) {
            foreach($pageList as $pageData) {
                $pageFollowerIns->removeAllFollowersByPageID($pageData['pageID']);
            }
        }
        
        $query = sprintf("DELETE FROM %s WHERE userID=%d", TABLE_PAGES, $userID);
        $db->query($query);
        
    }
    
    /**
    * Change page status 1) to Activate 2) to make inactive
    * 
    * It will find all pages belonged to this user, and change status as the $status parameter
    * This function will be called when banning the user or unbanning the user
    * 
    * @param integer $userID
    * @param integer $status : value will be one of (STATUS_INACTIVE, STATUS_ACTIVE)
    */
    public function massStatusChange($userID, $status=BuckysPage::STATUS_INACTIVE) {
        
        global $db;
        
        if (!is_numeric($userID))
            return;
        
        $query = '';
        if ($status == BuckysPage::STATUS_INACTIVE) {
            
            // To make inactive from active
            $query = sprintf('UPDATE %s SET status=%d WHERE status=%d AND userID=%d', TABLE_PAGES, BuckysPage::STATUS_INACTIVE, BuckysPage::STATUS_ACTIVE, $userID);
        }
        else if ($status == BuckysPage::STATUS_ACTIVE) {
            
            // To make active from inactive
            $query = sprintf('UPDATE %s SET status=%d WHERE status=%d AND userID=%d', TABLE_PAGES, BuckysPage::STATUS_ACTIVE, BuckysPage::STATUS_INACTIVE, $userID);
        }
        else {
            //Error
            return;
        }
        
        $db->query($query);
        
        return true;
        
        
        
    }
    
    /**
    * Getting 8 Pages randomly in 100 most popular pages for homepage
    * 
    */
    public function getPopularPagesForHomepage()
    {
        
        $pickUpCount = 8;
        
        global $db;
        
        $query = "SELECT COUNT(pf.id) AS followers, pf.pageID, p.title, p.logo, p.userID FROM " . TABLE_PAGE_FOLLOWERS . " as pf LEFT JOIN " . TABLE_PAGES . " AS p ON pf.pageID=p.pageID WHERE p.logo !='' GROUP BY pf.pageID HAVING followers > 0 ORDER BY followers DESC, RAND() LIMIT 100";
        $rows = $db->getResultsArray($query);
        
        if (count($rows) < $pickUpCount)
            $pickUpCount = count($rows);
        
        $keys = array_rand($rows, $pickUpCount);
        
        $nrows = array();
        foreach($keys as $k)
            $nrows[] = $rows[$k];
        
        return $nrows;
    }
    

}