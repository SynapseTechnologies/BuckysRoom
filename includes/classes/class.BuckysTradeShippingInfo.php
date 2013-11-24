<?php

/**
* Trade Shipping info management
*/

class BuckysTradeShippingInfo
{
    
    /**
    * * Add trade shipping info
    * 
    * @param integer $userID
    * @return int
    */
    public function addTradeShippingInfo($userID)
    {
        global $db;
        
        
        if (!is_numeric($userID))
            return; // failed
        
        //Get shipping info from trade_user table
        $query = sprintf('SELECT tradeUser.*, tUser.firstName, tUser.lastName FROM %s AS tradeUser LEFT JOIN %s AS tUser ON tUser.userID=tradeUser.userID WHERE tradeUser.userID=%d', TABLE_TRADE_USERS, TABLE_USERS, $userID);
        $query = $db->prepare($query);
        $data = $db->getRow($query);
        
        if (!empty($data) && $data['shippingCountryID'] > 0) {
            //it means there are shipping info
            
            $param = array(
                    'firstName'=>$data['firstName'],
                    'lastName'=>$data['lastName'],
                    'address'=>$data['shippingAddress'],
                    'city'=>$data['shippingCity'],
                    'state'=>$data['shippingState'],
                    'zip'=>$data['shippingZip'],
                    'countryID'=>$data['shippingCountryID'],
                );
            
            $newID = $db->insertFromArray(TABLE_TRADE_SHIPPING_INFO, $param);
        
            return $newID;
            
        }
        
        
        return; //failed
        
    }
       
    
}