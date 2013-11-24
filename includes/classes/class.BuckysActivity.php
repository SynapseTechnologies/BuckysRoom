<?php
/**
* Manage user Activities
*/
class BuckysActivity 
{
    public function addActivity($userID, $objectID, $objectType, $activityType, $actionID = 0)
    {
        global $db;
        
        $db->insertFromArray(TABLE_ACTIVITES, array(
            'userID' => $userID,
            'objectID' => $objectID,
            'objectType' => $objectType,
            'activityType' => $activityType,            
            'createdDate' => date('Y-m-d H:i:s'),            
            'isNew' => 1,            
            'actionID' => $actionID           
        ));
    }
    
    public function getActivities($userID, $limit = 15)
    {
        global $db;
        
        $query = $db->prepare("SELECT distinct(a.activityID), a.*,p.*, pc.content as comment_content FROM " . TABLE_ACTIVITES . " as a 
                    LEFT JOIN " . TABLE_FRIENDS . " as f ON a.userID=f.userFriendID and f.status=1
                    LEFT JOIN " . TABLE_POSTS . " as p ON a.objectID=p.postID
                    LEFT JOIN " . TABLE_POSTS_COMMENTS . " as pc ON a.activityType='comment' AND pc.commentID=a.actionID 
                    WHERE a.userID != %d AND p.poster != %d AND f.userID=%d AND f.status=1 ORDER BY a.createdDate desc LIMIT %d", $userID, $userID, $userID, $limit);
        
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    public function getActivityHTML($row, $userID)
    {
        ob_start();
        $user = BuckysUser::getUserBasicInfo($row['userID']);
        $owner = BuckysUser::getUserBasicInfo($row['poster']);
        if($row['activityType'] == 'like')        
        {            
            
            ?>
            <div class="activityComment">
                <?php render_profile_link($user, 'replyToPostIcons'); ?>
                <span>
                    <a href="/profile.php?user=<?php echo $row['userID']?>"><b><?php echo $user['firstName'] . " " . $user['lastName']?></b></a>
                    liked <?php echo $row['poster'] == $userID ? 'your' : ("<a href='/profile.php?user=" . $row['poster'] . "'><b>" . $owner['firstName'] . " " . $owner['lastName'] . "'s</b></a>") ?>
                    <?php 
                        switch($row['type'])
                        {
                            case "image":   
                                echo "<a href='/posts.php?user=" . $row['poster'] . "&post=" . $row['objectID'] . "'>photo</a>";
                                break;
                            case "video":   
                                echo "<a href='/posts.php?user=" . $row['poster'] . "&post=" . $row['objectID'] . "'>video</a>";
                                break;
                            case "text":
                            default:
                                echo "<a href='/posts.php?user=" . $row['poster'] . "&post=" . $row['objectID'] . "'>post</a> ";
                                if(strlen(buckys_trunc_content($row['content'], 60)) > 0)
                                {
                                    echo '&#8220;' . buckys_trunc_content($row['content'], 60) . '&#8221;' ;
                                }
                                break;
                            
                        }
                    ?>
                </span>
            </div>
            
            <?php
        }else if($row['activityType'] == 'comment'){
            ?>
            <div class="activityComment">                
                <?php render_profile_link($user, 'replyToPostIcons'); ?>
                <span>
                    <a href="/profile.php?user=<?php echo $row['userID']?>"><b><?php echo $user['firstName'] . " " . $user['lastName']?></b></a>
                    left a comment on 
                    <?php 
                        if( $row['poster'] == $userID)
                        {
                            echo 'your';
                        } else if( $row['poster'] == $row['userID'] ){
                            //Getting User Data
                            $tUinfo = BuckysUser::getUserBasicInfo( $row['userID'] );
                            switch(strtolower($tUinfo['gender']))
                            {
                                case 'male':
                                    echo 'his';
                                    break;
                                case 'female':
                                    echo 'her';
                                    break;
                                break;
                                    echo 'their';
                                    break;
                            }
                        } else {
                            echo "<a href='/profile.php?user=" . $row['poster'] . "'><b>" . $owner['firstName'] . " " . $owner['lastName'] . "'s</b></a>";
                        }
                    ?> 
                    <?php 
                        switch($row['type'])
                        {
                            case "image":   
                                echo "<a href='/posts.php?user=" . $row['poster'] . "&post=" . $row['objectID'] . "'>photo</a>";
                                break;
                            case "video":   
                                echo "<a href='/posts.php?user=" . $row['poster'] . "&post=" . $row['objectID'] . "'>video</a>";
                                break;
                            case "text":
                            default:
                                echo "<a href='/posts.php?user=" . $row['poster'] . "&post=" . $row['objectID'] . "'>post</a> ";                                
                                break;
                            
                        }
                        if(strlen(buckys_trunc_content($row['comment_content'], 25)) > 0)
                        {
                            echo ': &#8220;' . buckys_trunc_content($row['comment_content'], 25) . '&#8221;' ;
                        }
                    ?>                    
                </span>
            </div>
            <?php
        }
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
    
    
    public function getNotifications($userID, $limit = 15, $status = null)
    {
        global $db;
        
        $query = $db->prepare("SELECT distinct(a.activityID), a.*,p.*, pc.content as comment_content FROM " . TABLE_ACTIVITES . " as a 
                    LEFT JOIN " . TABLE_POSTS . " as p ON a.objectID=p.postID
                    LEFT JOIN " . TABLE_POSTS_COMMENTS . " as pc ON a.activityType='comment' AND pc.commentID=a.actionID 
                    WHERE a.userID != %d AND p.poster=%d " . ($status != null ? " AND a.isNew=" . $status : "") . " ORDER BY a.createdDate desc LIMIT %d", $userID, $userID, $limit);
        
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    /**
    * Get the number of notifications
    * 
    * @param Int $userID
    * @return Int
    */
    public function getNumberOfNotifications($userID)
    {
        global $db;
        
        $query = $db->prepare("SELECT count(distinct(a.activityID)) FROM " . TABLE_ACTIVITES . " as a 
                    LEFT JOIN " . TABLE_POSTS . " as p ON a.objectID=p.postID                    
                    WHERE a.userID != %d AND p.poster=%d AND a.isNew=1", $userID, $userID);
        
        $count = $db->getVar($query);
        
        return $count;
    }
    
    public function markReadNotifications($userID)
    {
        global $db;
        
        $query = $db->prepare("UPDATE " . TABLE_ACTIVITES . " as a LEFT JOIN " . TABLE_POSTS . " as p ON a.objectID=p.postID set isNew=0
                    WHERE a.userID != %d AND p.poster=%d AND a.isNew=1", $userID, $userID);
        
        $db->query($query);
        
        return ;
    }
    
    
}