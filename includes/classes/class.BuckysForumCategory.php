<?php
/**
* Forum Categories
*/
class BuckysForumCategory
{
    
    /**
    * Getting All Categories
    * 
    */
    public function getAllCategories($categoryID = null)
    {
        global $db;
        
        if($categoryID == null)
            $query = "SELECT c.*, t.topicTitle as lastPostTitle, t.creatorID as lastPosterID, t.createdDate as lastPostDate, CONCAT(u.firstName, ' ', u.lastName) as lastPosterName FROM " . TABLE_FORUM_CATEGORIES . " AS c " .
                    "LEFT JOIN " . TABLE_FORUM_TOPICS . " AS t ON c.lastTopicID=t.topicID AND t.status='publish' " . 
                    "LEFT JOIN " . TABLE_USERS . " AS u ON t.creatorID=u.userID " . 
                    "ORDER BY parentID, sortOrder";
        else
            $query = $db->prepare("SELECT c.*, t.topicTitle as lastPostTitle, t.creatorID as lastPosterID, t.createdDate as lastPostDate, CONCAT(u.firstName, ' ', u.lastName) as lastPosterName FROM " . TABLE_FORUM_CATEGORIES . " AS c " .
                    "LEFT JOIN " . TABLE_FORUM_TOPICS . " AS t ON c.lastTopicID=t.topicID AND t.status='publish' " . 
                    "LEFT JOIN " . TABLE_USERS . " AS u ON t.creatorID=u.userID " . 
                    "Where c.categoryID=%d OR c.parentID=%d " . 
                    "ORDER BY parentID, sortOrder", $categoryID, $categoryID);
        $rows = $db->getResultsArray($query);
        
        $result = array();
        foreach($rows as $row)
        {
            if($row['parentID'] == 0){
                $result[$row['categoryID']] = $row;
            }else{
                if(!isset($result[$row['parentID']]['children']))    
                    $result[$row['parentID']]['children'] = array();
                $result[$row['parentID']]['children'][] = $row;
            }            
        }
        
        return $result;        
    }
    
    /**
    * Get Categories
    * 
    * @param mixed $parentID
    * @return Indexed
    */
    public function getCategories($parentID = 0)
    {
        global $db;
        
        $query  = $db->query("SELECT * FROM " . TABLE_FORUM_CATEGORIES . " WHERE parentID=%d ORDER BY sortOrder", $parentID);
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    /**
    * Update Category Last Topic ID
    * 
    * @param Int $catID
    */
    public function updateCategoryLastTopicID($catID)
    {
        global $db;
        
        $query = $db->prepare("SELECT topicID FROM " . TABLE_FORUM_TOPICS . " WHERE categoryID=%d AND status='publish' ORDER BY createdDate DESC LIMIT 1", $catID);
        $lastID = $db->getVar($query);
        
        if(!$lastID)
            $lastID = 0;
            
        $db->updateFromArray(TABLE_FORUM_CATEGORIES, array('lastTopicID' => $lastID), array('categoryID' => $catID));
        
        return;
    }
    
    /**
    * Update Category Topics count
    * 
    * @param Int $catID
    */
    public function updateCategoryTopicsCount($catID)
    {
        global $db;
        
        $query = $db->prepare("SELECT count(1) FROM " . TABLE_FORUM_TOPICS . " WHERE categoryID=%d AND status='publish'", $catID);
        $count = $db->getVar($query);
        
        $db->updateFromArray(TABLE_FORUM_CATEGORIES, array('topics' => $lastID), array('categoryID' => $catID));
        
        return;
    }
    
    
    
    /**
    * Get Category By ID
    * 
    * @param Int $id
    * @return Array
    */
    public function getCategory($id)
    {
        global $db;
        
        $query = $db->prepare("SELECT * FROM " . TABLE_FORUM_CATEGORIES . " WHERE categoryID=%d", $id);        
        $row = $db->getRow($query);
        
        return $row;
    }
    
    public function getCategoryHierarchical($catID)
    {
        global $db;
        
        $result = array();
        
        $cCat = BuckysForumCategory::getCategory($catID);
        $result[] = $cCat;
        while($cCat && $cCat['parentID'] != 0)
        {
            $cCat = BuckysForumCategory::getCategory($cCat['parentID']);
            $result[] = $cCat;
        }
        
        $result = array_reverse($result);
        
        return $result;
    }
}
