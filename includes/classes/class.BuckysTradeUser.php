<?php

/**
* Trade User Info Management
*/

class BuckysTradeUser
{
    
    /**
    * Get Trade User Info
    * 
    * @param integer $userID
    */
    public function getUserByID($userID) {
        
        global $db;
        
        if (!is_numeric($userID))
            return;
        
        $query = $db->prepare('SELECT * FROM ' . TABLE_TRADE_USERS . ' WHERE userID=%d', $userID);
        $data = $db->getRow($query);
        
        if (!$data) {
            $this->addUser($userID);
            $query = $db->prepare('SELECT * FROM ' . TABLE_TRADE_USERS . ' WHERE userID=%d', $userID);
            $data = $db->getRow($query);
        }
        
        
        return $data;
    }
    
    /**
    * Check duplication
    * 
    * @param mixed $userID
    */
    public function checkDuplication($userID) {
        global $db;
        
        if (!is_numeric($userID))
            return;
        
        $query = $db->prepare('SELECT * FROM ' . TABLE_TRADE_USERS . ' WHERE userID=%d', $userID);
        $data = $db->getRow($query);
        
        if ($data)
            return true;
        else
            return false;
    }
    
    /**
    * Add Trade user
    * 
    * @param integer $userID
    * @param array $data
    */
    public function addUser($userID, $data = array()) {
        
        global $db;
        
        $userIns = new BuckysUser();
        if (!is_numeric($userID) || !$userIns->checkUserID($userID, false))
            return;
        
        if ($this->checkDuplication($userID))
            return;
        
        $data['userID'] = $userID;
        
        $newID = $db->insertFromArray(TABLE_TRADE_USERS, $data);
        
        return $newID;
        
    }
    
    /**
    * Update shipping Info
    * It has 2 logic in it. Update your own shipping info, and update already created trade records which has no shipping info.
    * 
    * @param integer $userID
    * @param array $data
    */
    public function updateShippingInfo($userID, $data) {
        
        if (
            !is_numeric($userID) ||
            $data['shippingAddress'] == '' ||
            $data['shippingCity'] == '' ||
            $data['shippingState'] == '' ||
            $data['shippingZip'] == '' ||
            $data['shippingCountryID'] == '' ||
            !is_numeric($data['shippingCountryID'])
            )
            return false;
        
        //Update my shipping info
        global $db;
        $query = sprintf('UPDATE %s SET ', TABLE_TRADE_USERS);
        
        $query = $db->prepare($query . 'shippingAddress=%s, shippingCity=%s, shippingState=%s, shippingZip=%s, shippingCountryID=%d WHERE userID=' . $userID , $data['shippingAddress'], $data['shippingCity'], $data['shippingState'], $data['shippingZip'], $data['shippingCountryID']);
        $db->query($query);
        
        //Update trade table which has no shipping info with this info.
        //It will check trade table, and create records in trade_shipping_info
        $tradeIns = new BuckysTrade();
        $tradeShippingInfoIns = new BuckysTradeShippingInfo();
        
        //---------------- Update for seller ----------------------//
        $requiredList = $tradeIns->getShippingInfoRequiredTrade($userID, 'seller');
        if (!empty($requiredList) && count($requiredList) > 0) {
            foreach($requiredList as $tradeData) {
                //Add shipping info
                $shippingRecID = $tradeShippingInfoIns->addTradeShippingInfo($userID);
                if (!empty($shippingRecID) && is_numeric($shippingRecID)) {
                    //update trade table
                    $tradeIns->updateTrade($tradeData['tradeID'], array('sellerShippingID'=>$shippingRecID));
                }
                
            }
        }
        
        //---------------- Update for buyer ----------------------//
        $requiredList = $tradeIns->getShippingInfoRequiredTrade($userID, 'buyer');
        if (!empty($requiredList) && count($requiredList) > 0) {
            foreach($requiredList as $tradeData) {
                //Add shipping info
                $shippingRecID = $tradeShippingInfoIns->addTradeShippingInfo($userID);
                if (!empty($shippingRecID) && is_numeric($shippingRecID)) {
                    //update trade table
                    $tradeIns->updateTrade($tradeData['tradeID'], array('buyerShippingID'=>$shippingRecID));
                }
                
            }
        }
        
        return true;
        
        
    }
    
    
    
    /**
    * Update Trade User
    * 
    * @param integer $userID
    * @param array $data
    */
    public function updateTradeUser($userID, $data) {

        global $db;
        
        if (!is_numeric($userID))
            return false;

        $res = $db->updateFromArray( TABLE_TRADE_USERS, $data, array('userID' => $userID) );

        return;


    }
    
    
    /**
    * Get users who are at top, by having items
    * @param integer $limit
    */
    public function getUsersTopByItems($limit=10) {
        
        if (!is_numeric($limit))
            return;
        
        global $db;
        
        $avaiableTime = date('Y-m-d H:i:s', time() - TRADE_ITEM_LIFETIME * 3600 * 24);
        
        $query = sprintf("
                        SELECT tUser.*, user.firstName, user.lastName, (SELECT COUNT(*) FROM %s AS tItem WHERE tUser.userID=tItem.userID AND tItem.createdDate >= '%s' AND tItem.status=%d) AS itemCount 
                        FROM %s AS tUser 
                            LEFT JOIN %s AS user ON tUser.userID=user.userID 
                            WHERE user.status=%d ORDER BY itemCount DESC LIMIT %d 
                            
                    ", TABLE_TRADE_ITEMS, $avaiableTime, BuckysTradeItem::STATUS_ITEM_ACTIVE, TABLE_TRADE_USERS, TABLE_USERS, BuckysUser::STATUS_USER_ACTIVE, $limit);
        
        $result = $db->getResultsArray($query);
        
        return $result;
        
    }
    
    /**
    * Check if you have credits
    * 
    * @param integer $userID
    */
    public function hasCredits($userID) {
        
        $userIns = new BuckysUser();
        $userInfo = $userIns->getUserBasicInfo($userID);
        
        if (!$userInfo)
            return;
        
        return $userInfo['credits'] >= 1;
        
    }
    
    /**
    * Get all users
    * 
    */
    public function getAllUsers() {
        
        global $db;
        
        $query = sprintf('SELECT * FROM %s', TABLE_TRADE_USERS);
        
        $result = $db->getResultsArray($query);
        
        return $result;
        
    }
    /**
    * Use one credits
    *
    * @param integer $userID
    * @param double $credits
    */
    public function useCredit($userID, $credits = 1) {
        
        //Update credit activity table (credit has been used)
        $transactionIns = new BuckysTransaction();
        $transactionIns->useCreditsInTrade($userID, $credits);
        
        return $credits;
        
        
    }
       
    
}
