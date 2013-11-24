<?php

/**
* Trade management
*/

class BuckysTrade
{
    
    const TRADE_TRADED = 1;

    /**
    * Add trade
    * 
    * @param integer $sellerID
    * @param integer $buyerID
    * @param integer $sellerItemID
    * @param integer $buyerItemID
    * @return int
    */
    public function addTrade($sellerID, $buyerID, $sellerItemID, $buyerItemID)
    {
        global $db;


        if (!is_numeric($sellerID) ||
        !is_numeric($buyerID) ||
        !is_numeric($sellerItemID) ||
        !is_numeric($buyerItemID) 
        )
            return; // failed

        //Check if this trade has been made before.
        $query = sprintf('SELECT * FROM %s WHERE sellerID=%d AND buyerID=%d AND sellerItemID=%d AND buyerItemID=%d', TABLE_TRADE, $sellerID, $buyerID, $sellerItemID, $buyerItemID);
        $query = $db->prepare($query);
        $data = $db->getRow($query);

        if (!empty($data)) {
            return $data['tradeID']; // already exists
        }

        //Add new trade record
        // 1. create shipping address
        $tradeShippingInfo = new BuckysTradeShippingInfo();
        $shippingInfo['seller'] = $tradeShippingInfo->addTradeShippingInfo($sellerID);
        $shippingInfo['buyer'] = $tradeShippingInfo->addTradeShippingInfo($buyerID);

        // 2. create trade record

        $dateTimeStamp = date('Y-m-d H:i:s');

        $param = array(
        'sellerID' => $sellerID,
        'buyerID' => $buyerID,
        'sellerItemID' => $sellerItemID,
        'buyerItemID' => $buyerItemID,
        'sellerShippingID' => $shippingInfo['seller'],
        'buyerShippingID' => $shippingInfo['buyer'],
        'createdDate' => $dateTimeStamp
        );

        $newID = $db->insertFromArray(TABLE_TRADE, $param);

        return $newID;
    }

    /**
    * It will return shipping Info Required Trade records.
    * 
    * @param integer $userID
    * @param string $type : seller / buyer
    */
    public function getShippingInfoRequiredTrade($userID, $type='seller') {

        if (!is_numeric($userID)) {
            return;
        }

        global $db;

        switch ($type) {
            case 'seller' :

                $query = sprintf('SELECT * FROM %s WHERE sellerID=%d AND sellerShippingID=%d', TABLE_TRADE, $userID, 0);
                $result = $db->getResultsArray($query);
                return $result;

                break;
            case 'buyer' :

                $query = sprintf('SELECT * FROM %s WHERE buyerID=%d AND buyerShippingID=%d', TABLE_TRADE, $userID, 0);
                $result = $db->getResultsArray($query);
                return $result;

                break;
        }

        return;
    }

    /**
    * Update Trade Info
    * 
    * @param integer $tradeID
    * @param array $data
    */
    public function updateTrade($tradeID, $data) {

        global $db;
        
        if (!is_numeric($tradeID))
            return false;

        $res = $db->updateFromArray( TABLE_TRADE, $data, array('tradeID' => $tradeID) );

        return;


    }
    
    /**
    * Get trades completed by this user
    * 
    * @param integer $userID
    * @param string $type: one of the following 'history', 'completed'
    */
    public function getTradesByUserID($userID, $type='completed') {
        
        global $db;
        
        if (!is_numeric($userID))
            return;
        
        $query = sprintf('
                    SELECT t.tradeID, t.sellerID, t.buyerID, t.sellerItemID, t.buyerItemID, t.sellerTrackingNo, t.buyerTrackingNo, t.createdDate AS tradeCreatedDate, 
                        sItem.title AS sellerItemTitle, sItem.subtitle AS sellerItemSubtitle, sItem.images AS sellerItemImages, 
                        bItem.title AS buyerItemTitle, bItem.subtitle AS buyerItemSubtitle, bItem.images AS buyerItemImages, 
                        sShipInfo.firstName AS sellerShFristName, sShipInfo.lastName AS sellerShLastName, sShipInfo.address AS sellerShAddress, sShipInfo.city AS sellerShCity, sShipInfo.state AS sellerShState, sShipInfo.zip AS sellerShZip, sShipInfo.countryID AS sellerShCountryID,
                        bShipInfo.firstName AS buyerShFristName, bShipInfo.lastName AS buyerShLastName, bShipInfo.address AS buyerShAddress, bShipInfo.city AS buyerShCity, bShipInfo.state AS buyerShState, bShipInfo.zip AS buyerShZip, bShipInfo.countryID AS buyerShCountryID,
                        sUser.totalRating AS sellerTotalRating, sUser.positiveRating AS sellerPositiveRating,
                        bUser.totalRating AS buyerTotalRating, bUser.positiveRating AS buyerPositiveRating,
                        tFeedback.buyerToSellerScore AS sellerFeedbackScore, tFeedback.sellerToBuyerScore AS buyerFeedbackScore 
                    FROM %s AS t 
                        LEFT JOIN %s AS sItem ON sItem.itemID = t.sellerItemID 
                        LEFT JOIN %s AS bItem ON bItem.itemID = t.buyerItemID 
                        LEFT JOIN %s AS sShipInfo ON sShipInfo.shippingID = t.sellerShippingID 
                        LEFT JOIN %s AS bShipInfo ON bShipInfo.shippingID = t.buyerShippingID 
                        LEFT JOIN %s AS sUser ON sUser.userID = t.sellerID 
                        LEFT JOIN %s AS bUser ON bUser.userID = t.buyerID 
                        LEFT JOIN %s AS tFeedback ON tFeedback.tradeID = t.tradeID 
            ', TABLE_TRADE, TABLE_TRADE_ITEMS, TABLE_TRADE_ITEMS, TABLE_TRADE_SHIPPING_INFO, TABLE_TRADE_SHIPPING_INFO, TABLE_TRADE_USERS, TABLE_TRADE_USERS, TABLE_TRADE_FEEDBACK);
        
        
        
        switch($type) {
            case 'history':
                $query = $db->prepare($query. ' WHERE ((t.sellerID=%d AND tFeedback.sellerToBuyerScore!=0) OR (t.buyerID=%d AND tFeedback.buyerToSellerScore!=0)) AND t.status=%d', $userID, $userID, BuckysTrade::TRADE_TRADED);
                break;
            default:
                $query = $db->prepare($query. " WHERE ((t.sellerID=%d AND (tFeedback.sellerToBuyerScore=0 OR tFeedback.sellerToBuyerScore IS NULL)) OR (t.buyerID=%d AND (tFeedback.buyerToSellerScore=0 OR tFeedback.buyerToSellerScore IS NULL))) AND t.status=%d", $userID, $userID, BuckysTrade::TRADE_TRADED);
                break;
        }
        
        
        $result = $db->getResultsArray($query);
        return $result;
        
    }
    
    /**
    * Get Trade Info
    * 
    * @param mixed $tradeID
    */
    public function getTradeByID($tradeID) {
        
        
        global $db;
        
        if (!is_numeric($tradeID))
            return;
        
        $query = sprintf('SELECT * FROM %s WHERE tradeID=%d', TABLE_TRADE, $tradeID);
        
        $data = $db->getRow($query);
        
        return $data;
        
    }


}