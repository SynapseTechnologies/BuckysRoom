<?php
/**
* Manage Tracker Table
*/                    
class BuckysTracker
{
    //Add current track
    public function addTrack($action = 'login')
    {
        global $db;
        
        $userID = buckys_is_logged_in();
        $ip = $_SERVER['REMOTE_ADDR'];
        $time = time();
        
        $db->insertFromArray(TABLE_TRACKER, array('userID' => !$userID ? 0 : $userID, 'ipAddr' => $ip, 'trackedTime' => $time, 'action' => $action));
        
    }
    
    public function getLoginAttemps()
    {
        global $db;
        
        $ip = $_SERVER['REMOTE_ADDR'];
        
        $time = time() - MAX_LOGIN_ATTEMPT_PERIOD;
        $query = "SELECT COUNT(1) FROM " . TABLE_TRACKER . " WHERE ipAddr='$ip' AND trackedTime > '$time'";
        $count = $db->getVar($query);
        
        return $count;
    }
    
    public function clearLoginAttemps()
    {
        global $db;
        
        $ip = $_SERVER['REMOTE_ADDR'];
        
        $time = time() - MAX_LOGIN_ATTEMPT_PERIOD;
        $query = "DELETE FROM " . TABLE_TRACKER . " WHERE ipAddr='$ip' AND trackedTime > '$time'";
        
        $db->query($query);
        
        return $count;
    }
}
