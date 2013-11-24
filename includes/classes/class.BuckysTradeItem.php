<?php

class BuckysTradeItem
{
    
    const STATUS_ITEM_INACTIVE = 0; // Item has been inactivated. When user banned, all items (status=new) will be changed to this inactive
    const STATUS_ITEM_ACTIVE = 1; // Item is available for trade.
    const STATUS_ITEM_TRADED = 2; // Item has been traded
    
    
    /**
    * Add Trade Item
    * 
    * @param array $data
    */
    public function addItem($data)
    {
        
        $tradeUserIns = new BuckysTradeUser();
        if (!$tradeUserIns->hasCredits($data['userID']))
            return; // no credits
        
        global $db;
        
        if (empty($data['userID']) || 
            empty($data['title']) ||
            empty($data['subtitle']) ||
            empty($data['catID'])
        )
            return;
            
        $newID = $db->insertFromArray(TABLE_TRADE_ITEMS, $data);
        
        //Trade User has been created?
        $tradeUserIns->addUser($data['userID']);
        
        //Use one credits
        if ($newID)
            $tradeUserIns->useCredit($data['userID']);
        
        return $newID;
    }
    
    /**
    * get Item List Of Personal User's
    * 
    * @param integer $userID
    * @param bool $isExpired
    * @param integer $status 
    * @param integer $catID
    * @param string $searchStr    
    * @param string $sortField
    * @param string $sortDir
    * @return Array
    */
    public function getItemList($userID=null, $isExpired=null, $status=null, $catID=null, $searchStr=null, $sortField='title', $sortDir='ASC')
    {
        global $db;
        
        $whereCondList = array();
        if (isset($userID)) {
            $whereCondList[] = 'i.userID=' . $userID;
        }
        
        if (isset($catID)) {
            $whereCondList[] = 'i.catID=' . $catID;
        }
        
        if (isset($searchStr) && $searchStr != '') {
            $searchStr = addslashes($searchStr);
            $whereCondList[] = sprintf(" MATCH (i.title, i.subtitle, i.description) AGAINST ('%s' IN BOOLEAN MODE)", $searchStr);
        }
        
        
        $avaiableTime = date('Y-m-d H:i:s', time() - TRADE_ITEM_LIFETIME * 3600 * 24);
        if ($isExpired === false) {
            
            $whereCondList[] = "i.createdDate >='" . $avaiableTime . "'";
        }
        else if ($isExpired === true) {
            
            $whereCondList[] = "i.createdDate <'" . $avaiableTime . "'";
        }
        
        if (isset($status)) {
            $whereCondList[] = 'i.status=' . $status;
        }
        
        if (count($whereCondList) > 0)
            $whereCond = ' WHERE ' . implode(' AND ', $whereCondList);
        else 
            $whereCond = ' WHERE 1 ';
        
        $whereCond .= ' GROUP BY i.itemID ';
        
        if (isset($sortField)) {
            $whereCond .= sprintf(" ORDER BY %s %s", $sortField, $sortDir);
        }
        
        
        $query = sprintf("SELECT i.*, (SELECT COUNT(*) FROM %s AS tOffer WHERE i.itemID=tOffer.targetItemID AND tOffer.status=%d) AS offer FROM %s AS i ", TABLE_TRADE_OFFERS, BuckysTradeOffer::STATUS_OFFER_ACTIVE,TABLE_TRADE_ITEMS);
        
        $query = $db->prepare($query . $whereCond);
        
        $data = $db->getResultsArray($query);
        
        return $data;
    }
    
    /**
    * Return item data by ID
    * 
    * @param mixed $itemID
    */
    public function getItemById($itemID, $isExpired = null) {
        
        if (empty($itemID) || !is_numeric($itemID) || $itemID <= 0)
            return null;
        
        global $db;
        
        $availableQueryStr = '';
        
        
        $avaiableTime = date('Y-m-d H:i:s', time() - TRADE_ITEM_LIFETIME * 3600 * 24);
        if ($isExpired === false) {
            $availableQueryStr = " AND i.createdDate >='" . $avaiableTime . "' AND i.status=" . BuckysTradeItem::STATUS_ITEM_ACTIVE;
        }
        else if ($isExpired === true) {
            $availableQueryStr = " AND i.createdDate <'" . $avaiableTime . "' AND i.status=" . BuckysTradeItem::STATUS_ITEM_ACTIVE;
        }
        
        $query = sprintf("SELECT i.*, 
                (SELECT COUNT(*) FROM %s AS tOffer WHERE i.itemID=tOffer.targetItemID AND tOffer.status=%d) AS offer, 
                tu.totalRating, tu.positiveRating 
                FROM %s AS i 
                    LEFT JOIN %s AS tu ON i.userID=tu.userID", TABLE_TRADE_OFFERS, BuckysTradeOffer::STATUS_OFFER_ACTIVE,TABLE_TRADE_ITEMS, TABLE_TRADE_USERS);
                    
                    
        $query .= ' WHERE i.itemID=' . $itemID . $availableQueryStr . " GROUP BY i.itemID ";
        
        $query = $db->prepare($query);
        
        $data = $db->getRow($query);
        
        if ($data) {
            if (strtotime($avaiableTime) < strtotime($data['createdDate']))
                $data['isExpired'] = false;
            else
                $data['isExpired'] = true;
        }
        
        return $data;
        
    }
    
    /**
    * Update Item data
    * 
    * @param integer $itemID
    * @param array $data
    */
    public function updateItem($itemID, $data) {
        
        global $db;
        
        $res = $db->updateFromArray( TABLE_TRADE_ITEMS, $data, array('itemID' => $itemID) );
        
        return;
        
    }
    
    
    /**
    * Search items
    * 
    * @param string $qStr : Query String
    * @param string $catStr : Category Name/ Category ID
    * @param string $locStr : Location / Location ID
    * @return array
    */
    public function search($qStr, $catStr, $locStr, $userID) {
        
        global $db;
        
        $tradeCatIns = new BuckysTradeCategory();
        $tradeLocationIns = new BuckysCountry();
        
        //Get category data
        $catData = null;
        if (is_numeric($catStr))
            $catData = $tradeCatIns->getCategoryByID($catStr);
        else
            $catData = $tradeCatIns->getCategoryByName($catStr);
        
        //Get Location data
        $locationData = null;
        if (is_numeric($locStr))
            $locationData = $tradeLocationIns->getCountryById($locStr);
        else
            $locationData = $tradeLocationIns->getCountryByName($locStr);
            
        
        
        //Make Where condition
        $whereCondList = array();
        
        if (isset($qStr) && $qStr != '') {
            $qStr = addslashes($qStr);
            $whereCondList[] = sprintf(" MATCH (i.title, i.subtitle, i.description) AGAINST ('%s' IN BOOLEAN MODE)", $qStr);
        }
        
        if (isset($catData)) {
            $whereCondList[] = 'i.catID=' . $catData['catID'];
        }
        else if ($catStr != '') {
            return null;
        }
        
        if (isset($locationData)) {
            $whereCondList[] = 'i.locationID=' . $locationData['countryID'];
        }
        
        if (isset($userID) && is_numeric($userID)) {
            $whereCondList[] = 'i.userID=' . $userID;
        }
        
        //Valid items
        $avaiableTime = date('Y-m-d H:i:s', time() - TRADE_ITEM_LIFETIME * 3600 * 24);
        $whereCondList[] = "i.createdDate >='" . $avaiableTime . "'";
        
        $whereCondList[] = 'i.status=' . BuckysTradeItem::STATUS_ITEM_ACTIVE;
        
        $whereCond = ' WHERE ' . implode(' AND ', $whereCondList);
        
        $whereCond .= ' GROUP BY i.itemID ';
        
        
        $query = sprintf("SELECT i.*, (SELECT COUNT(*) FROM %s AS o WHERE i.itemID=o.targetItemID AND o.status=%d) AS offer, u.firstName, u.lastName, tu.totalRating, tu.positiveRating 
                            FROM %s AS i 
                            LEFT JOIN %s AS tu ON i.userID=tu.userID 
                            LEFT JOIN %s AS u ON i.userID=u.userID 
                            ", TABLE_TRADE_OFFERS, BuckysTradeOffer::STATUS_OFFER_ACTIVE,TABLE_TRADE_ITEMS, TABLE_TRADE_USERS, TABLE_USERS);

        $query = $db->prepare($query . $whereCond);
        
        
        $data = $db->getResultsArray($query);
        
        return $data;
    }
    
    /**
    * Remove Trade Items
    * This function will be used by Super admin. Others will use removeItemByUserID
    * 
    * @param integer/array $itemIDList
    */
    public function removeItems($itemIDList)
    {
        global $db;
        
        
        if (is_numeric($itemIDList) && $itemIDList > 0) {
            $itemIDList = array($itemIDList);
        }
        
        if (is_array($itemIDList) && count($itemIDList) > 0) {
            $idCondStr = implode(',', $itemIDList);
            
            $query = sprintf('SELECT * FROM %s WHERE itemID IN (%s) AND status=%d', TABLE_TRADE_ITEMS, $idCondStr, BuckysTradeItem::STATUS_ITEM_ACTIVE);
            $itemList = $db->getResultsArray($query);
            
            if (count($itemList) > 0) {
                
                //remove item images first
                foreach($itemList as $itemData) {
                    if ($itemData['images'] != '') {
                        $imageList = explode('|', $itemData['images']);
                        
                        if (count($imageList) > 0) {
                            foreach ($imageList as $key=>$val) {
                                if ($val != '') {
                                    $val = ltrim($val, '/');
                                    $thumb = buckys_trade_get_item_thumb($val);
                                    
                                    @unlink(DIR_FS_ROOT . $val);
                                    @unlink(DIR_FS_ROOT . $thumb);
                                }
                            }
                        }
                        
                    }
                }
                
                
                //Delete items
                $query = sprintf('DELETE FROM %s WHERE itemID IN (%s) AND status=%d', TABLE_TRADE_ITEMS, $idCondStr, BuckysTradeItem::STATUS_ITEM_ACTIVE);
                $db->query($query);
            }
            
        }
        
        
        
        $query = $db->prepare("DELETE FROM " . TABLE_TRADE_ITEMS . " WHERE itemID=%d", $itemID);
        
        
        return;
    }
    
    
    /**
    * Remove item id by userID & itemID : the Item should be belonged to the user
    * 
    * @param integer $itemID
    * @param integer $userID
    */
    public function removeItemByUserID($itemID, $userID) {
        
        global $db;
        
        if (is_numeric($userID) && is_numeric($itemID)) {
            
            //Check if this item is new (not traded). If it has been traded already, then it couldn't be deleted
            $itemData = $this->getItemById($itemID);
            
            if ($itemData['status'] == BuckysTradeItem::STATUS_ITEM_ACTIVE && $itemData['userID'] == $userID) {
                $this->removeItems(array($itemID));
                            
                //After deleting the items, it will remove related offers which are related to this item.
                
                $tradeOfferIns = new BuckysTradeOffer();
                $tradeOfferIns->removeRelatedOffers($itemID);
                
                
            }
            
        }

        return;
        
    }
    
    /**
    * Sort Items
    * 
    * @param array $itemList
    */
    public function sortItems($itemList, $sortMod) {
        
        if (!is_array($itemList) || count($itemList) == 0) {
            return array();
        }
        
        $nowTimeVal = time();
        foreach($itemList as &$tmpItem) {
            $tmpItem['leftSec'] = strtotime($tmpItem['createdDate']) + TRADE_ITEM_LIFETIME * 24 * 3600 - $nowTimeVal;
        }
        
        switch($sortMod) {
            
            case 'endsoon' :
                usort($itemList, array($this, '_compareEndSoonFirst'));
                break;
            
            case 'newly' :
                usort($itemList, array($this, '_compareEndSoonLast'));
                break;
            
            case 'offersmost' :
                usort($itemList, array($this, '_compareOfferMostFirst'));
                
                break;
            
            case 'offersleast' :
                usort($itemList, array($this, '_compareOfferMostLast'));
                
                
                break;
            
            case 'best' :
            default:
                //already sorted
                break;
            
        }
        
        
        return $itemList;
        
    }
    
    private function _compareEndSoonFirst($a, $b) {
        if ($a['leftSec'] == $b['leftSec'])
            return 0;
        
        return ($a['leftSec'] > $b['leftSec']) ? 1: -1;
    }
    
    private function _compareEndSoonLast($a, $b) {
        if ($a['leftSec'] == $b['leftSec'])
            return 0;
        
        return ($a['leftSec'] < $b['leftSec']) ? 1: -1;
    }
    
    private function _compareOfferMostFirst($a, $b) {
        if ($a['offer'] == $b['offer'])
            return 0;
        
        return ($a['offer'] < $b['offer']) ? 1: -1;
    }    
    
    private function _compareOfferMostLast($a, $b) {
        if ($a['offer'] == $b['offer'])
            return 0;
        
        return ($a['offer'] > $b['offer']) ? 1: -1;
    }
    
    /**
    * Count items according to the category
    * 
    * 
    * @param array $itemList
    */
    public function countItemInCategory($itemList) {
        
        $tradeCatIns = new BuckysTradeCategory();
        $categoryList = $tradeCatIns->getCategoryList();
        
        
        $catItemCountList = array();
        if (count($itemList) > 0) {
            
            foreach ($itemList as $itemData) {
                if (isset($catItemCountList[$itemData['catID']])) {
                    $catItemCountList[$itemData['catID']] ++;
                }
                else {
                    $catItemCountList[$itemData['catID']] = 1;
                }
            }
        }
        
        if (count($catItemCountList) > 0 && count($categoryList) > 0) {
            foreach($categoryList as &$tmpCatData) {
                isset($catItemCountList[$tmpCatData['catID']]) ? $tmpCatData['count'] = $catItemCountList[$tmpCatData['catID']]: $tmpCatData['count'] = 0;
            }
        }
        
        return $categoryList;
    }
    
    /**
    * Most wanted item list by offer received
    * 
    * @param integer $limit
    */
    public function getItemsTopByOffers($limit = 10) {
        
        if (!is_numeric($limit))
            return;
        
        global $db;
        
        $avaiableTime = date('Y-m-d H:i:s', time() - TRADE_ITEM_LIFETIME * 3600 * 24);
            
        
        
        $query = sprintf("
                        SELECT tItem.*, user.firstName, user.lastName, (SELECT COUNT(*) FROM %s AS tOffer WHERE tOffer.targetItemID=tItem.itemID AND tOffer.status=%d) AS offerCount 
                        FROM %s AS tItem 
                            LEFT JOIN %s AS user ON tItem.userID=user.userID 
                            WHERE tItem.status=%d AND tItem.createdDate >='%s' ORDER BY offerCount DESC LIMIT %d 
                            
                    ", TABLE_TRADE_OFFERS, BuckysTradeOffer::STATUS_OFFER_ACTIVE,TABLE_TRADE_ITEMS, TABLE_USERS, BuckysTradeItem::STATUS_ITEM_ACTIVE, $avaiableTime, $limit);
        

        $result = $db->getResultsArray($query);
        
        return $result;
    }
    
    /**
    * Get recent 10 items
    * 
    * @param integer $limit
    */
    public function getRecentItems($limit = 10) {
        
        if (!is_numeric($limit))
            return;
        
        global $db;
        
        $avaiableTime = date('Y-m-d H:i:s', time() - TRADE_ITEM_LIFETIME * 3600 * 24);
        
        $query = sprintf("
                        SELECT tItem.*, user.firstName, user.lastName 
                        FROM %s AS tItem 
                            LEFT JOIN %s AS user ON tItem.userID=user.userID 
                            WHERE tItem.status=%d AND tItem.createdDate >='%s' ORDER BY tItem.createdDate DESC LIMIT %d 
                            
                    ", TABLE_TRADE_ITEMS, TABLE_USERS, BuckysTradeItem::STATUS_ITEM_ACTIVE, $avaiableTime, $limit);

        $result = $db->getResultsArray($query);
        
        return $result;
    }
    
    /**
    * Remove expired items & related trade offers
    * 
    */
    public function removeExpiredItems() {
        
        global $db;
        
        $limitDate = date('Y-m-d H:i:s', time() - TRADE_ITEM_LIFETIME * 24 * 3600);
        
        $query = sprintf("SELECT itemID FROM %s WHERE status=%d AND createdDate < '%s'", TABLE_TRADE_ITEMS, BuckysTradeItem::STATUS_ITEM_ACTIVE, $limitDate);
        
        $oldItemList = $db->getResultsArray($query);
        $idList = array();
        
        if (count($oldItemList) > 0) {
            
            foreach($oldItemList as $data) {
                $idList[] = $data['itemID'];
            }
            
        }
        
        if (count($idList) > 0) {
            
            //Remove items
            //$this->removeItems($idList);
            
            
            //Remove related trade offers which made with this item
            $tradeOfferIns = new BuckysTradeOffer();
            $tradeOfferIns->removeRelatedOffers($idList);
            
        }
        
        return;
        
    }
    
    /**
    * Delete whole items and related offers when deleting user
    * Please note that we will delete items which has not been traded yet.
    * 
    */
    public function deleteItemsByUserID($userID) {
        
        global $db;
        
        if (!is_numeric($userID))
            return;
        
        $query = sprintf("SELECT itemID FROM %s WHERE status!=%d AND userID=%d", TABLE_TRADE_ITEMS, BuckysTradeItem::STATUS_ITEM_TRADED, $userID);
        
        $oldItemList = $db->getResultsArray($query);
        $idList = array();
        
        if (count($oldItemList) > 0) {
            
            foreach($oldItemList as $data) {
                $idList[] = $data['itemID'];
            }
        }
        
        if (count($idList) > 0) {
            
            //Delete items
            $this->removeItems($idList);
            
            
            //Remove related trade offers which made with this item
            $tradeOfferIns = new BuckysTradeOffer();
            $tradeOfferIns->removeRelatedOffers($idList);
            
        }
        
        return;
        
    }
    
    
    /**
    * Change item status 1) to Activate 2) to make inactive
    * 
    * It will find all items belonged to this user, and change status as the $status parameter
    * This function will be called when banning the user or unbanning the user
    * 
    * @param integer $userID
    * @param integer $status : value will be one of (STATUS_ITEM_INACTIVE, STATUS_ITEM_ACTIVE)
    */
    public function massStatusChange($userID, $status=BuckysTradeItem::STATUS_ITEM_INACTIVE) {
        
        global $db;
        
        if (!is_numeric($userID))
            return;
        
        $query = '';
        if ($status == BuckysTradeItem::STATUS_ITEM_INACTIVE) {
            
            // To make inactive from active
            $query = sprintf('UPDATE %s SET status=%d WHERE status=%d', TABLE_TRADE_ITEMS, BuckysTradeItem::STATUS_ITEM_INACTIVE, BuckysTradeItem::STATUS_ITEM_ACTIVE);
        }
        else if ($status == BuckysTradeItem::STATUS_ITEM_ACTIVE) {
            
            // To make active from inactive
            $query = sprintf('UPDATE %s SET status=%d WHERE status=%d', TABLE_TRADE_ITEMS, BuckysTradeItem::STATUS_ITEM_ACTIVE, BuckysTradeItem::STATUS_ITEM_INACTIVE);
        }
        else {
            //Error
            return;
        }
        
        $db->query($query);
        
        return true;
        
        
        
    }
    
}