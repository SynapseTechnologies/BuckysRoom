<?php

/**
* Trade Notification
*/

class BuckysTradeNotification
{
    
    const ACTION_TYPE_OFFER_RECEIVED = 'offer_received';
    const ACTION_TYPE_OFFER_ACCEPTED = 'offer_accepted';
    const ACTION_TYPE_OFFER_DECLINED = 'offer_declined';
    const ACTION_TYPE_FEEDBACK = 'feedback';
    
    var $objectType = 'trade';
    var $objectID = 0;
    
    /**
    * It will create Notification on Activities table
    * 
    * @param integer $userID
    * @param integer $senderID (the man who creates this alert)
    * @param string $actionType : one of action types (const defined for this class)
    * @param integer $actionID : related trade offer,  feedback ID
    */
    function createNotification($userID, $senderID, $actionType, $actionID) {
        
        
        //Check if this user will get this notification. (it will be set by Notification setting page)
        $tradeUserIns = new BuckysTradeUser();
        $userData = $tradeUserIns->getUserByID($userID);
        
        $flagEnabled = 0; // user checked that he didn't want to have this notification
        
        switch($actionType) {
            case BuckysTradeNotification::ACTION_TYPE_OFFER_ACCEPTED:
                $flagEnabled =  $userData['optOfferAccepted'];
                break;
            
            case BuckysTradeNotification::ACTION_TYPE_OFFER_RECEIVED:
                $flagEnabled =  $userData['optOfferReceived'];
                break;
            
            case BuckysTradeNotification::ACTION_TYPE_OFFER_DECLINED:
                $flagEnabled =  $userData['optOfferDeclined'];
                break;
                
            case BuckysTradeNotification::ACTION_TYPE_FEEDBACK:
                $flagEnabled =  $userData['optFeedbackReceived'];
                break;
        }
        
        
        if ($flagEnabled == 1) {
            //Create Notification.
            $activityIns = new BuckysActivity();
            $activityIns->addActivity($userID, $senderID, $this->objectType, $actionType, $actionID);
            
        }
        
    }
    
    /**
    * get Number of new notification
    * 
    * @param mixed $userID
    * @param string type
    * @return one
    */
    function getNumOfNewMessages($userID, $type=null) {
        
        global $db;
        
        if ($type == null)
            $query = $db->prepare("SELECT count(*) FROM " . TABLE_ACTIVITES . " WHERE userID=%d AND isNew=1 AND objectType=%s", $userID, $this->objectType);
        else
            $query = $db->prepare("SELECT count(*) FROM " . TABLE_ACTIVITES . " WHERE userID=%d AND isNew=1 AND activityType=%s AND objectType=%s", $userID, $type, $this->objectType);
            
        $num = $db->getVar($query);
        
        return $num;
    }
    
    /**
    * get all notification which this user has received.
    * 
    * @param mixed $userID
    * @param mixed $limit
    */
    function getReceivedMessages($userID, $type=null, $limit=10) {
        
        
        global $db;
        
        $whereCond = '';
        if ($type == null)
            $whereCond = sprintf(" WHERE act.userID=%d AND act.isNew=1 AND act.objectType='%s' ORDER BY act.createdDate DESC ", $userID, $this->objectType);
        else
            $whereCond = sprintf(" WHERE act.userID=%d AND act.isNew=1 AND act.activityType='%s' AND act.objectType='%s' ORDER BY act.createdDate DESC ", $userID, $type, $this->objectType);
        
        if (is_numeric($limit) && $limit > 0) {
            $whereCond .= ' LIMIT ' . $limit;
        }
        
        $query = sprintf("
                SELECT act.objectID AS senderID, act.activityType, act.createdDate, act.actionID, feedback.sellerID, feedback.buyerID, feedback.buyerToSellerFeedback, feedback.sellerToBuyerFeedback, user.firstName, user.lastName   
                    FROM %s AS act 
                    LEFT JOIN %s AS feedback ON feedback.feedbackID = act.actionID AND act.activityType='%s' 
                    LEFT JOIN %s AS user ON user.userID = act.objectID  
                %s 
            ", TABLE_ACTIVITES, TABLE_TRADE_FEEDBACK, BuckysTradeNotification::ACTION_TYPE_FEEDBACK, TABLE_USERS, $whereCond);
        
        $messageList = $db->getResultsArray($query);
        $newMessageList = array();
        
        if (count($messageList) > 0) {
            foreach($messageList as $msgData) {
                $data = array();
                $data['senderID'] = $msgData['senderID'];
                $data['senderName'] = trim($msgData['firstName'] . " " . $msgData['lastName']);
                $data['activityType'] = $msgData['activityType'];
                $data['createdDate'] = $msgData['createdDate'];
                $data['actionID'] = $msgData['actionID'];
                $data['feedback'] = '';
                
                if ($data['activityType'] == BuckysTradeNotification::ACTION_TYPE_FEEDBACK) {
                    if ($msgData['sellerID'] == $msgData['senderID']) {
                        $data['feedback'] = $msgData['sellerToBuyerFeedback'];
                    }
                    else {
                        $data['feedback'] = $msgData['buyerToSellerFeedback'];
                    }
                }
                
                $newMessageList[] = $data;
            }
        }
        
        return $newMessageList;
        
        
    }
    
    /**
    * Mark message as read
    * 
    * @param integer $userID
    * @param string $actionType one of value of this action type (offer_received, offer_accepted,offer_declined,feedback)
    */
    function markAsRead($userID, $actionType) {
        
        global $db;
        
        if (!is_numeric($userID))
            return;
        
        $query = sprintf("UPDATE %s SET isNew=0 WHERE userID=%d AND objectType='%s' AND activityType='%s'", TABLE_ACTIVITES, $userID, $this->objectType, $actionType);
        
        $db->query($query);
        
        return;
        
        
    }
    
}
