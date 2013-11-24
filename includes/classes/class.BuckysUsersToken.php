<?php

class BuckysUsersToken
{
    /**
    * Remove User Token
    * 
    * @param Int $userID
    * @param String $tokenType = password, ...
    */
    public function removeUserToken($userID, $tokenType)
    {
        global $db;
        
        $query = $db->prepare("DELETE FROM " . TABLE_USERS_TOKEN . " WHERE userID=%s AND tokenType=%s", $userID, $tokenType);
        $db->query($query);
        
        return;
    }
    
    public function createNewToken($userID, $tokenType)
    {
        global $db;
        
        $token = md5(mt_rand(0, 99999) . time()  . mt_rand(0, 99999) . $data['email'] . mt_rand(0, 99999));
        $newID = $db->insertFromArray(TABLE_USERS_TOKEN, array('userID' => $userID, 'userToken' => $token, 'tokenDate' => time(), 'tokenType' => 'password'));
        
        return $token;
    }
    
    public function checkTokenValidity($token, $tokenType)
    {
        global $db;
        
        if( $tokenType == 'password' )
        {
            $query = $db->prepare('SELECT userID FROM ' . TABLE_USERS_TOKEN . ' WHERE userToken=%s AND tokenType=%s AND tokenDate > %s', $token, $tokenType, time() - PASSWORD_TOKEN_EXPIRY_DATE * 60 *60 * 24);
            $userID = $db->getVar($query);
            if( !$userID )
            {
                return false;
            }
            return $userID;
        }
        return false;
    }
}