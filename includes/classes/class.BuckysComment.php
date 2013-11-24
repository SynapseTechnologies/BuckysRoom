<?php

/**
* Manage Post Comments
*/

class BuckysComment
{
    public static $COMMENT_LIMIT = 5;
    
    //Getting Post Comments
    public function getPostComments($postID, $last_date = null)
    {
        global $db;
        
        $userID = buckys_is_logged_in();
        
        if( !$last_date )
            $last_date = date('Y-m-d H:i:s');
        $query = $db->prepare("SELECT c.*, CONCAT(u.firstName, ' ', u.lastName) as fullName, p.poster, r.reportID FROM " . TABLE_POSTS_COMMENTS . " as c " .
                              "LEFT JOIN " . TABLE_USERS . " as u on u.userID=c.commenter " .
                              "LEFT JOIN " . TABLE_POSTS . " as p on p.postID=c.postID " .
                              "LEFT JOIN " . TABLE_REPORTS . " as r on r.objectID=c.commentID AND r.objectType='comment' AND r.reporterID=%d " .
                              "WHERE c.commentStatus=1 AND c.postID=%s AND c.posted_date < %s Order By c.posted_date DESC LIMIT 5 ", !$userID ? 0 : $userID, $postID, $last_date);
        
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    
    /**
    * Get Post Comments Count
    * 
    * @param mixed $postID
    * @return Int
    */
    public function getPostCommentsCount( $postID )
    {
        global $db;
        
        $query = $db->prepare("SELECT comments FROM " . TABLE_POSTS . " WHERE postID=%d", $postID);
        $c = $db->getVar( $query );
        
        return $c;
    }
    
    /**
    * Save Comment
    * 
    * @param Int $userID
    * @param Int $postID
    * @param String $comment
    */
    public function saveComments($userID, $postID, $comment)
    {
        global $db;
        
        $now = date("Y-m-d H:i:s");
        
        $newId = $db->insertFromArray( TABLE_COMMENTS, array('postID' => $postID, 'commenter'=> $userID, 'content' => $comment, 'posted_date' => $now) );
        
        if( buckys_not_null($newId) )
        {
            //Update comments on the posts table
            $query = $db->prepare('UPDATE ' . TABLE_POSTS . ' SET `comments`=`comments` + 1 WHERE postID=%d', $postID);
            $db->query($query);
            //Add Activity
            BuckysActivity::addActivity($userID, $postID, 'post', 'comment', $newId);
            //Increase Hits
            BuckysHit::addHit($postID, $userID);        
        }   
        return $newId;
    }
    
    /**
    * Get Comment By ID
    */
    public function getComment($commentID)
    {
        global $db;
        
        $query = $db->prepare("SELECT c.*, CONCAT(u.firstName, ' ', u.lastName) as fullName, p.poster FROM " . TABLE_POSTS_COMMENTS . " as c 
                                    LEFT JOIN " . TABLE_USERS . " as u on u.userID=c.commenter
                                    LEFT JOIN " . TABLE_POSTS . " as p on p.postID=c.postID WHERE c.commentID=%s
                                    ", $commentID);
        $row = $db->getRow( $query );
        
        return $row;
    }
    
    public function hasMoreComments($postID, $last_date = null)
    {
        global $db;
        
        if( !$last_date )
            $last_date = date('Y-m-d H:i:s');
        $query = $db->prepare("SELECT count(1) FROM " . TABLE_POSTS_COMMENTS . " WHERE postID=%s AND posted_date < %s ", $postID, $last_date);
        
        $c = $db->getVar($query);
        
        return $c;
    }
    
    public function deleteComment($userID, $commentID)
    {
        global $db;
        
        $query = $db->prepare("SELECT c.commentID, c.postID FROM " . TABLE_COMMENTS . " as c LEFT JOIN " . TABLE_POSTS . " as p ON p.postID=c.postID WHERE c.commentID=%s AND (c.commenter=%s OR p.poster=%s)", $commentID, $userID, $userID); 
        $row = $db->getRow($query);
        
        
        if( !$row )
        {
            return false;
        }else{
            $cID = $row['commentID'];
            $postID = $row['postID'];
            
            $db->query( 'DELETE FROM ' . TABLE_COMMENTS . " WHERE commentID=" . $cID );
            //Remove Activity
            $db->query( 'DELETE FROM ' . TABLE_ACTIVITES . " WHERE actionID=" . $cID );
            //Remove From Report
            $db->query( 'DELETE FROM ' . TABLE_REPORTS . " WHERE objectType='comment' AND objectID=" . $cID );
            
            //Update comments on the posts table
            $query = $db->prepare('UPDATE ' . TABLE_POSTS . ' SET `comments`=`comments` - 1 WHERE postID=%d', $postID);
            $db->query($query);
            
            return true;
        }
    }

}
