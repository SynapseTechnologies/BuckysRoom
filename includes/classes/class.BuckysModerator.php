<?php
/**
* Moderator Management
*/

class BuckysModerator
{
    public static $CANDIDATES_PER_PAGE = 15;
    
    /**
    * Get Moderator
    * 
    * @param String $type
    * @return array
    */
    public function getModerator($type)
    {
        global $db, $BUCKYS_GLOBALS;
        
        $typeID = array_search($type, $BUCKYS_GLOBALS['moderatorTypes']);
        
        $query = $db->prepare("SELECT u.firstName, u.lastName, u.thumbnail, u.userID FROM " . TABLE_MODERATOR . " AS m " .
                              "LEFT JOIN " . TABLE_USERS . " AS u ON m.userID=u.userID WHERE m.moderatorStatus=1 AND m.moderatorType=%d", $typeID);
        
        $row = $db->getRow($query);
        
        return $row;
    }
    
    /**
    * Get Candidates Count
    * 
    * @param String $type
    * 
    * @return Int
    */
    public function getCandidatesCount($type)
    {
        global $db, $BUCKYS_GLOBALS;
        
        $type = array_search($type, $BUCKYS_GLOBALS['moderatorTypes']);
        
        $query = $db->prepare("SELECT count(1) FROM " . TABLE_MODERATOR_CANDIDATES . " WHERE candidateType=%d", $type);
        
        $count = $db->getVar($query);
        
        return $count;
    }
    
    /**
    * Get Candidates
    * 
    * @param String $type
    * @return Array
    */
    public function getCandidates($type, $page = 1, $limit = null)
    {
        global $db, $BUCKYS_GLOBALS;
        
        if($limit == null)
            $limit = BuckysModerator::$CANDIDATES_PER_PAGE;
        
        $type = array_search($type, $BUCKYS_GLOBALS['moderatorTypes']);
        
        $query = $db->prepare("SELECT mc.*, u.firstName, u.lastName, u.thumbnail, v.voteID FROM " . TABLE_MODERATOR_CANDIDATES . " AS mc " .
                              "LEFT JOIN " . TABLE_USERS . " AS u ON mc.userID=u.userID ". 
                              "LEFT JOIN " . TABLE_MODERATOR_VOTES . " AS v ON v.candidateID=mc.candidateID AND v.voterID=%d ". 
                              "WHERE mc.candidateType=%d ORDER BY mc.votes DESC ", $BUCKYS_GLOBALS['user']['userID'], $type);
        
        $query .= " LIMIT " . ($page - 1) * $limit . ", " . $limit;
        
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    /**
    * Apply For Moderator
    * 
    * @param Int $userID
    * @param String $type
    * @param String $text
    */
    public function applyCandidate($userID, $type, $text)
    {
        global $db, $BUCKYS_GLOBALS;
        
        if(!in_array($type, $BUCKYS_GLOBALS['moderatorTypes']))
        {
            buckys_redirect('/account.php', MSG_INVALID_REQUEST, MSG_TYPE_ERROR);
            return;
        }
        
        $typeID = array_search($type, $BUCKYS_GLOBALS['moderatorTypes']);
        
        //Check whether the user has already applied or not
        $query = $db->prepare("SELECT candidateID FROM " . TABLE_MODERATOR_CANDIDATES . " WHERE userID=%d AND candidateType=%d", $userID, $typeID);
        $candidateID = $db->getVar($query);
        
        if($candidateID)
        {
            buckys_redirect('/moderator.php?type=' . $type, MSG_ALREADY_APPLIED_THE_MODERATOR, MSG_TYPE_ERROR);
            return;
        }
        
        $text = trim($text);
        if(!$text)
        {
            buckys_redirect('/moderator.php?type=' . $type, MSG_TELL_US_WHY_YOU_WOULD_MAKE_MODERATOR, MSG_TYPE_ERROR);
            return;
        }
        
        //Save Candidate
        $newID = $db->insertFromArray(TABLE_MODERATOR_CANDIDATES, array('candidateType' => $typeID, 'userID' => $userID, 'candidateText' => $text, 'votes' => 0, 'appliedDate' => date('Y-m-d H:i:s')));
        
        if(!$newID)
        {
            buckys_redirect('/moderator.php?type=' . $type, $db->getLastError(), MSG_TYPE_ERROR);
            return;
        }
        
        
        return true;
    }
    
    /**
    * Vote Candidate
    * 
    * @param Int $userID : Voter ID
    * @param Int $candidateID
    * @param Boolean $isApproval
    */
    public function voteCandidate($userID, $candidateID, $isApproval = true)
    {
        global $db;
        
        //Get the Candidate User ID        
        $query  = $db->prepare("SELECT * FROM " . TABLE_MODERATOR_CANDIDATES . " WHERE candidateID=%d", $candidateID);
        $candidateRow = $db->getRow($query);
                
        //If the candidate id is not correct,
        if(!$candidateRow) 
        {
            return MSG_INVALID_REQUEST;
        }
        
        //If the candidate's id is the same with the current user id
        if($candidateRow['userID'] == $userID) 
        {
            return MSG_INVALID_REQUEST;
        }
        
        //If the user already took a vote on this candidate
        $query = $db->prepare("SELECT voteID FROM " . TABLE_MODERATOR_VOTES . " WHERE candidateID=%d AND voterID=%d", $candidateID, $userID);
        $voteID = $db->getVar($query);
        if($voteID)
        {
            return MSG_ALREADY_VOTE;
        }
        
        //Take a vote on this candidate
        $newID = $db->insertFromArray(TABLE_MODERATOR_VOTES, array('voterID'=>$userID, 'candidateID'=>$candidateID, 'voteType' => $isApproval ? 1 : 0, 'voteDate'=>date('Y-m-d H:i:s')));
        if(!$newID)
            return $db->getLastError();
        
        //Update moderator_candidates table
        
        if($isApproval)
            $newVotes = intval($candidateRow['votes']) + 1;
        else
            $newVotes = intval($candidateRow['votes']) - 1;
        $query = $db->prepare("UPDATE " . TABLE_MODERATOR_CANDIDATES . " SET `votes`=%d WHERE candidateID=%d", $newVotes, $candidateID);
        $db->query($query);
        
        return $newVotes;
    }
    
    /**
    * Check that the user is a moderator.
    * 
    * @param Int $userID
    * @param String $moderatorType
    * 
    * @return Boolean
    */
    public function isModerator($userID, $moderatorType)
    {
        global $db;
        
        $query = $db->prepare("SELECT moderatorID FROM " . TABLE_MODERATOR . " WHERE moderatorStatus=1 AND moderatorType=%s AND userID=%d",$moderatorType, $userID);
        $mID = $db->getVar($query);
        
        return !$mID ? false : true;
    }
    
    /**
    * Choose Moderator
    * 
    * @param int $candidateID
    * 
    * @return Error Message or True
    */
    public function chooseModerator($candidateID)
    {
        global $db, $BUCKYS_GLOBALS;
        
        //Check user acl again
        if(!buckys_check_user_acl(USER_ACL_ADMINISTRATOR))
        {
            return MSG_PERMISSION_DENIED;
        }
        
        //Check Candidate ID 
        $query = $db->prepare("SELECT candidateID, userID, candidateType FROM " . TABLE_MODERATOR_CANDIDATES . " WHERE candidateID=%d", $candidateID);
        $candidate = $db->getRow($query);
        if(!$candidate)
        {
            return MSG_INVALID_REQUEST;
        }
        
        //Getting Old Moderator
        $query = $db->prepare("SELECT moderatorID, userID FROM " . TABLE_MODERATOR . " WHERE moderatorType=%d AND moderatorStatus=1", $candidate['candidateType']);
        $oldModerator = $db->getRow($query);
        
        if($oldModerator)
        {
            //Update the status to 0 on the Moderator Table
            $db->query("UPDATE " . TABLE_MODERATOR . " SET moderatorStatus=0 WHERE moderatorID=" . $oldModerator['moderatorID']);
            
            //Change the user type and acl id on the users table
            $db->update("UPDATE " . TABLE_USERS . " SET user_type='Registered', user_acl_id='" . BuckysUserAcl::getIdFromName('Registered') . "' WHERE userID='" . $oldModerator['userID'] . "' AND user_acl_id='" . BuckysUserAcl::getIdFromName('Moderator') . "'");
        }
      
        //Create New Moderator
        $mId = $db->insertFromArray(TABLE_MODERATOR, array('moderatorType' => $candidate['candidateType'], 'userID' => $candidate['userID'], 'moderatorStatus' => 1, 'electedDate' => date('Y-m-d H:i:s')));
        //Update user table        
        $db->update("UPDATE " . TABLE_USERS . " SET user_type='Moderator', user_acl_id='" . BuckysUserAcl::getIdFromName('Moderator') . "' WHERE userID='" . $candidate['userID'] . "' AND user_acl_id != '" . BuckysUserAcl::getIdFromName('Administrator') . "'");
      
        //Remove Candidates
        $db->query("DELETE FROM " . TABLE_MODERATOR_CANDIDATES . " WHERE candidateType='" . $candidate['candidateType'] . "'");
        
        return;
    }
}    
