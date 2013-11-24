<?php
/**
* Manage Topic
*/

class BuckysForumTopic
{
    public static $COUNT_PER_PAGE = 30;
    
    public function createTopic($data)
    {
        global $db, $BUCKYS_GLOBALS;
        
        $title = trim($data['title']);
        $category = trim($data['category']);
        $content = trim($data['content']);
        
        if(!$title || !$category || !$content)
        {
            return MSG_ALL_FIELDS_REQUIRED;
        }
        
        //Check Category ID is valid or not
        $query = $db->prepare("SELECT categoryID FROM " . TABLE_FORUM_CATEGORIES . " WHERE categoryID=%d", $category);
        $categoryID = $db->getVar($query);
        if(!$categoryID)
        {
            return MSG_INVALID_REQUEST;
        }
        
        $content = BuckysForumTopic::_convertHTMLToBBCode($content);
        
        $insertData = array(
            'topicTitle' => $title,
            'topicContent' => $content,
            'categoryID' => $categoryID,
            'creatorID' => $BUCKYS_GLOBALS['user']['userID'],
            'createdDate' => date('Y-m-d H:i:s'),
            'replies' => 0,
            'lastReplyID' => 0,
            'lastReplyDate' => '0000-00-00 00:00:00',
            'lastReplierID' => 0,
            'views' => 0,
            'status' => 'pending'
        );
        
        $newID = $db->insertFromArray(TABLE_FORUM_TOPICS, $insertData);
        if(!$newID)
            return $db->getLastError();
            
        //If the user has more than 5 actived posts(topics or replies), update the topic status to 1
        $count1 = $db->getVar("SELECT count(1) FROM " . TABLE_FORUM_TOPICS . " WHERE creatorID=" . $BUCKYS_GLOBALS['user']['userID'] . " AND `status`='publish'");
        $count2 = $db->getVar("SELECT count(1) FROM " . TABLE_FORUM_REPLIES . " WHERE creatorID=" . $BUCKYS_GLOBALS['user']['userID'] . " AND `status`='publish'");
        if($count1 + $count2 >= 5){
            $db->updateFromArray(TABLE_FORUM_TOPICS, array('status' => 'publish'), array('topicID' => $newID));
            //Update Category Table
            $db->query("UPDATE " . TABLE_FORUM_CATEGORIES . " SET lastTopicID=" . $newID . ", `topics`=`topics` + 1 WHERE categoryID=" . $categoryID);
            
            return 'publish';
        }
        
        return 'pending';
    }
    
    /**
    * Edit topic
    * 
    * @param mixed $data
    */
    public function editTopic($data)
    {
        global $db, $BUCKYS_GLOBALS;
        
        $title = get_secure_string($data['title']);
        $category = get_secure_string($data['category']);
        $content = trim($data['content']);
        
        if(!$title || !$category || !$content || !isset($data['id']))
        {
            return MSG_ALL_FIELDS_REQUIRED;
        }
        
        //Check Category ID is valid or not
        $query = $db->prepare("SELECT categoryID FROM " . TABLE_FORUM_CATEGORIES . " WHERE categoryID=%d", $category);
        $categoryID = $db->getVar($query);
        if(!$categoryID)
        {
            return MSG_INVALID_REQUEST;
        }
        
        $content = BuckysForumTopic::_convertHTMLToBBCode($content);
        
        $updateData = array(
            'topicTitle' => $title,
            'topicContent' => $content,
            'categoryID' => $categoryID
        );
        
        $db->updateFromArray(TABLE_FORUM_TOPICS, $updateData, array('topicID'=>$data['id']));
        
        return true;
    }
    
    
    /**
    * Pending Topics
    * 
    */
    public function getTopics($page = 1, $status = null, $category = null, $orderBy = null, $limit = null)
    {
        global $db;
        
        $query = "SELECT 
                        t.topicID,
                        t.topicTitle,
                        t.topicContent,
                        t.categoryID,
                        t.creatorID,
                        t.createdDate,
                        t.replies,
                        t.lastReplyID,
                        If(t.lastReplyID = 0, t.createdDate, t.lastReplyDate) as lastReplyDate,
                        t.lastReplierID,
                        t.views,
                        t.status,
                        CONCAT(u.firstName, ' ', u.lastName) as creatorName, 
                        u.thumbnail as creatorThumbnail, 
                        c.categoryName, 
                        CONCAT(ul.firstName, ' ', ul.lastName) as lastReplierName,
                        ul.thumbnail as lastReplierThumbnail 
                  FROM " . TABLE_FORUM_TOPICS . " AS t " .
                 "LEFT JOIN " . TABLE_USERS . " AS u ON u.userID=t.creatorID " .      
                 "LEFT JOIN " . TABLE_USERS . " AS ul ON ul.userID=t.lastReplierID " .      
                 "LEFT JOIN " . TABLE_FORUM_CATEGORIES . " AS c ON c.categoryID=t.categoryID ";
                 
        
        if($status != null)
            $where[] = $db->prepare("t.status= %s", $status);
        if($category != null)
            $where[] = $db->prepare("t.categoryID= %d", $category);
        
        if(count($where) > 0)
            $query .= " WHERE " . implode(" AND ", $where);
        
        if($orderBy != null)
            $query .= " ORDER BY " . $orderBy . ", t.topicTitle, t.createdDate ";
        else
            $query .= " ORDER BY lastReplyDate DESC, t.topicTitle , t.createdDate ";
        
        if($limit != null)
            $query .= " LIMIT " . ($page - 1) * $limit . ", " . $limit;
        
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    /**
    * Get The total number of Topics
    * 
    */
    public function getTotalNumOfTopics($status = null, $category = null)
    {
        global $db;
        
        $query = "SELECT count(t.topicID) FROM " . TABLE_FORUM_TOPICS . " AS t ";
        $where = array();
        
        if($status != null)
            $where[] = $db->prepare("`status`= %s", $status);
        if($category != null)
            $where[] = $db->prepare("`categoryID`= %d", $category);
        
        if(count($where) > 0)
            $query .= " WHERE " . implode(" AND ", $where);
        
        $count = $db->getVar($query);
        
        return $count;
    }
    
    /**
    * Approve Pending Topics
    * 
    * @param mixed $ids
    */
    public function approvePendingTopics($ids)
    {
        global $db;
        
        if(!is_array($ids))
            $ids = array($ids);
        
        $ids = $db->escapeInput($ids);
        
        //Getting Topics for confirmation
        $query = "SELECT topicID, categoryID, creatorID FROM " . TABLE_FORUM_TOPICS . " WHERE status='pending' AND topicID in (" . implode(', ', $ids) . ")";
        $rows = $db->getResultsArray($query);
        
        if(!$rows)
            return MSG_INVALID_REQUEST;
           
        $forumNotification = new BuckysForumNotification();
                    
        foreach($rows as $row)
        {
            //Update Topic Status
            $db->updateFromArray(TABLE_FORUM_TOPICS, array('status' => 'publish'), array('topicID' => $row['topicID']));            
            //Update Category Table
            $db->query("UPDATE " . TABLE_FORUM_CATEGORIES . " SET `topics`=`topics` + 1 WHERE categoryID=" . $row['categoryID']);
            $db->query("UPDATE " . TABLE_FORUM_CATEGORIES . " SET `lastTopicID`=" . $row['topicID'] . " WHERE categoryID=" . $row['categoryID'] . " AND lastTopicID < " . $row['topicID']);
            
            $forumNotification->addNotificationsForPendingPost($row['creatorID'], $row['topicID']);
        }
        
        return true;
    }
    
    /**
    * Delete Pending Topics
    * 
    * @param escaped $ids
    */
    public function deletePendingTopics($ids)
    {
        global $db;
        
        if(!is_array($ids))
            $ids = array($ids);
        
        $ids = $db->escapeInput($ids);
        
        //Getting Topics for confirmation
        $query = "SELECT topicID, categoryID FROM " . TABLE_FORUM_TOPICS . " WHERE status='pending' AND topicID in (" . implode(', ', $ids) . ")";
        $rows = $db->getResultsArray($query);
        
        if(!$rows)
            return MSG_INVALID_REQUEST;
                        
        foreach($rows as $row)
        {
            $db->query("DELETE FROM " . TABLE_FORUM_TOPICS . " WHERE topicID=" . $row['topicID']);
        }
        
        return true;
    }
    
    /**
    * Getting Topic
    * 
    * @param Int $id
    */
    public function getTopic($id)
    {
        global $db, $BUCKYS_GLOBALS;
        
        if(!$BUCKYS_GLOBALS['user']['userID'])
        {
            $query = $db->prepare("SELECT t.*, CONCAT(u.firstName, ' ', u.lastName) as creatorName, u.thumbnail, 0 AS reportID, 0 as voteID FROM " . TABLE_FORUM_TOPICS ." as t " .
                                  "LEFT JOIN " . TABLE_USERS . " AS u ON t.creatorID=u.userID WHERE t.topicID=%d", $id);
        }else{
            $query = $db->prepare("SELECT t.*, CONCAT(u.firstName, ' ', u.lastName) as creatorName, u.thumbnail, r.reportID, v.voteID FROM " . TABLE_FORUM_TOPICS ." as t " .
                                  "LEFT JOIN " . TABLE_USERS . " AS u ON t.creatorID=u.userID " .
                                  "LEFT JOIN " . TABLE_REPORTS . " AS r ON r.objectType='topic' AND r.objectID=t.topicID AND r.reporterID=%d " .                                  
                                  "LEFT JOIN " . TABLE_FORUM_VOTES . " AS v ON v.objectID=t.topicID AND v.objectType='topic' AND v.voterID=" . $BUCKYS_GLOBALS['user']['userID'] . " " .
                                  "WHERE t.topicID=%d", $BUCKYS_GLOBALS['user']['userID'], $id);
        }
        $row = $db->getRow($query);
        
        return $row;
    }
    
    /**
    * Delete Topic
    * 
    * @param Int $topicID
    */
    public function deleteTopic($topicID)
    {
        global $db;
        
        $query = $db->prepare("SELECT * FROM " . TABLE_FORUM_TOPICS . " WHERE topicID=%d", $topicID);
        $topic = $db->getRow($query);
        
        if($topic)
        {
            //Getting Published Replies count
            $query = "SELECT COUNT(1) FROM " . TABLE_FORUM_REPLIES . " WHERE `status`='publish' AND topicID=" . $topic['topicID'];
            $publishReplies = $db->getVar($query);
            
            //Remove Reply Votes
            $query = "DELETE FROM " . TABLE_FORUM_VOTES . " WHERE objectID IN (SELECT replyID FROM " . TABLE_FORUM_REPLIES . " WHERE topicID=" . $topic['topicID'] . ")";
            $db->query($query);
            //Remove Replies
            $query = "DELETE FROM " . TABLE_FORUM_REPLIES . " WHERE topicID=" . $topic['topicID'];
            $db->query($query);
            
            //Delete Topics
            $query = "DELETE FROM " . TABLE_FORUM_TOPICS . " WHERE topicID=" . $topic['topicID'];
            $db->query($query);
            
            //Update Category Values
            $query = "UPDATE " . TABLE_FORUM_CATEGORIES . " SET `topics` = `topics` - 1, `replies` = `replies` - " . $publishReplies . " WHERE categoryID=" . $topic['categoryID'];
            $db->query($query);
            
            //Update Last Topic ID of the category
            BuckysForumCategory::updateCategoryLastTopicID($topic['categoryID']);
            
            return true;
        }
        
        return false;
    }
    
    /**
    * Update Topic Last Reply Info
    * 
    * @param mixed $topicID
    */
    public function updateTopicLastReplyID($topicID)
    {
        global $db;
        
        //Get Last Reply ID
        $query = $db->prepare("SELECT * FROM " . TABLE_FORUM_REPLIES . " WHERE topicID=%d AND `status`='publish' ORDER BY createdDate DESC LIMIT 1", $topicID);
        $reply = $db->getRow($query);
        if($reply)
        {
            $db->updateFromArray(TABLE_FORUM_TOPICS, array('lastReplyID' => $reply['replyID'], 'lastReplyDate' => $reply['createdDate'], 'lastReplierID' => $reply['creatorID']), array('topicID'=>$reply['topicID']));
        }
        
        return;
    }    
    /**
    * Convert HTML Tags to BBCode
    * 
    * @param String $html
    */
    function _convertHTMLToBBCode($html)
    {
        $pattern = array(
            '/[\r|\n]/',
            '/<br.*?>/i',
            '/<b.*?>/i',
            '/<\/b>/i',
            '/<strong.*?>/i',
            '/<\/strong>/i',
            '/<div(.*?)>/i',
            '/<\/div>/i',
            '/<pre(.*?)>/i',
            '/<\/pre>/i',
            '/<font(.*?)>/i',
            '/<\/font>/i',
            '/<span(.*?)>/i',
            '/<\/span>/i',
            '/<p(.*?)>/i',
            '/<\/p>/i',
            '/<ul>/i',
            '/<\/ul>/i',
            '/<ol>/i',
            '/<\/ol>/i',
            '/<li>/i',
            '/<\/li>/i',            
            '/<em.*?>/i',
            '/<\/em>/i',
            '/<u.*?>/i',
            '/<\/u>/i',
            '/<ins.*?>/i',
            '/<\/ins>/i',
            '/<strike>/i',
            '/<\/strike>/i',
            '/<del>/i',
            '/<\/del>/i',
            '/<a.*?href="(.*?)".*?>(.*?)<\/a>/i',
            '/<img(.*?)src="(.*?)"(.*?)>/i',     
            '/<i.*?>/i',
            '/<\/i>/i',
            '/<.*?>(.*?)<\/.*?>/'
        );
        
        $replace = array(
          "",
          '\n',
          '[b]',
          '[/b]',
          '[b]',
          '[/b]',
          '[div$1]',
          '[/div]',
          '[code$1]',
          '[/code]',
          '[font$1]',
          '[/font]',
          '[span$1]',
          '[/span]',
          '[p$1]',
          '[/p]',
          '[list]',
          '[/list]',
          '[list=1]',
          '[/list]',
          '[*]',
          '[/*]',
          '[i]',
          '[/i]',          
          '[u]',
          '[/u]',
          '[u]',
          '[/u]',
          '[s]',
          '[/s]',
          '[s]',
          '[/s]',
          '[url=$1]$2[/url]',
          '[img $1$3]$2[/img]',
          '[i]',
          '[/i]',
          '$1'
        );
        
        $html = preg_replace($pattern, $replace, $html);
        
        //Convert Single Quote to Double Quote for div, code, font, img tags
        $html = preg_replace_callback('/\[code(.*?)\](.*?)\[\/code\]/i', create_function('$matches', 'return "[code" . str_replace(\'"\', ";squote;", $matches[1]) . "]" . $matches[2] . "[/code]";'), $html);
        $html = preg_replace_callback('/\[font(.*?)\](.*?)\[\/font\]/i', create_function('$matches', 'return "[font" . str_replace(\'"\', ";squote;", $matches[1]) . "]" . $matches[2] . "[/font]";'), $html);
        $html = preg_replace_callback('/\[span(.*?)\](.*?)\[\/span\]/i', create_function('$matches', 'return "[span" . str_replace(\'"\', ";squote;", $matches[1]) . "]" . $matches[2] . "[/span]";'), $html);
        $html = preg_replace_callback('/\[div(.*?)\](.*?)\[\/div\]/i', create_function('$matches', 'return "[div" . str_replace(\'"\', ";squote;", $matches[1]) . "]" . $matches[2] . "[/div]";'), $html);
        $html = preg_replace_callback('/\[p(.*?)\](.*?)\[\/p\]/i', create_function('$matches', 'return "[p" . str_replace(\'"\', ";squote;", $matches[1]) . "]" . $matches[2] . "[/p]";'), $html);
        $html = preg_replace_callback('/\[img(.*?)\](.*?)\[\/img\]/i', create_function('$matches', 'return "[img" . str_replace(\'"\', ";squote;", $matches[1]) . "]" . $matches[2] . "[/img]";'), $html);
        
        return $html;
    }
    
    /**
    * Convert BBCode To HTML
    * 
    * @param mixed $code
    */
    function _convertBBCodeToHTML($code)
    {
        //For Prettyprint
        $code = str_replace('[code class=&quot;prettyprint&quot;', '<?prettify lang=html linenums=true?>[code class="prettyprint"', $code);
        $code = str_replace('[code class=;squote;prettyprint;squote;', '<?prettify lang=html linenums=true?>[code class="prettyprint"', $code);
        $code = str_replace('[code class=&#039;prettyprint&#039;', '<?prettify lang=html linenums=true?>[code class="prettyprint"', $code);
        //Process Single Quote
        $code = str_replace(';squote;', "'", $code);
        
        $pattern = array(
            '/\\\r/',
            '/\\\n/',
            '/\[b\]/i',
            '/\[\/b\]/i',
            '/\[code(.*?)\]/i',
            '/\[\/code\]/i',
            '/\[font(.*?)\]/i',
            '/\[\/font\]/i',
            '/\[div(.*?)\]/i',            
            '/\[\/div\]/i',            
            '/\[span(.*?)\]/i',
            '/\[\/span\]/i',
            '/\[p(.*?)\]/i',
            '/\[\/p\]/i',
            '/\[i\]/i',
            '/\[\/i\]/i',
            '/\[u\]/i',
            '/\[\/u\]/i',
            '/\[s\]/i',
            '/\[\/s\]/i',
            '/\[url=(.*?)\](.*?)\[\/url\]/i',
            '/\[img(.*?)\](.*?)\[\/img\]/i',
            '/\[list\](.*?)\[\/list\]/i',
            '/\[list=1\](.*?)\[\/list\]/i',
            '/\[list\]/i',
            '/\[list=1\]/i',
            '/\[\*\](.*?)\[\/\*\]/',
            '/\[\*\]/'
        );
        $replace = array(
          "",
          '<br />',
          '<b>',
          '</b>',
          '<pre$1>',
          '</pre>',
          '<font$1>',
          '</font>',
          '<div$1>',          
          '</div>',          
          '<span$1>',
          '</span>',
          '<p$1>',
          '</p>',
          '<i>',
          '</i>',
          '<u>',
          '</u>',
          '<strike>',
          '</strike>',
          '<a href=\'$1\'>$2</a>',
          '<img $1 src=\'$2\'>',
          '<ul>$1</ul>',
          '<ol>$1</ol>',
          '<ul>',
          '<ol>',
          '<li>$1</li>',
          '<li>'
        );
           
        $code = preg_replace($pattern, $replace, $code);
        
        $pos = 0;
        //For PrettyPrint
        while( ($pos = strpos($code, '<?prettify lang=html linenums=true?>', $pos)) !== false)
        {
            $rpos = strpos($code, '</pre>', $pos);
            if($rpos !== false)
            {
                $subcode = substr($code, $pos, $rpos - $pos);
                $subcode = str_replace('<br />', PHP_EOL, $subcode);
                $code = substr($code, 0, $pos) . $subcode . substr($code, $rpos);
                $pos = strpos($code, '</pre>', $pos);
            }else{
                $subcode = substr($code, $pos);
                $subcode = str_replace('<br />', PHP_EOL, $subcode);
                $code = substr($code, 0, $pos) . $subcode;
                break;
            }
        }
        
        return $code;
    }
    
    
    
    
    /**
    * Get the total number of user's post
    * 
    * @param Int $userID
    * @param String $type : all, responded, started
    * @return Int
    */
    public function getTotalNumberOfMyPosts($userID, $type = 'all')
    {
        global $db, $BUCKYS_GLOBALS;
        
        if($type == 'all')
        {
            $query = $db->prepare("SELECT count(1) FROM 
                                    (SELECT topicID FROM " . TABLE_FORUM_TOPICS . " WHERE creatorID=%d 
                                    UNION DISTINCT
                                    SELECT topicID FROM " . TABLE_FORUM_REPLIES . " WHERE creatorID=%d ) as tTable ", $userID, $userID);
        }else if($type == 'started'){
            $query = $db->prepare("SELECT count(DISTINCT(t.topicID)) FROM " . TABLE_FORUM_TOPICS . " AS t WHERE t.creatorID=%d", $userID);
        }else if($type == 'responded'){
            $query = $db->prepare("SELECT count(DISTINCT(t.topicID)) FROM " . TABLE_FORUM_REPLIES . " AS r LEFT JOIN " . TABLE_FORUM_REPLIES . " AS r ON t.topicID=r.topicID " .                     
                     "WHERE t.creatorID != %d AND r.creatorID=%d ", $userID, $userID);
        }
        
        $count = $db->getVar($query);
        
        return $count;
    }
    
    public function getMyPosts($userID, $type = 'all', $page = 1, $limit = null)
    {
        global $db, $BUCKYS_GLOBALS;
        
        if($type == 'all')
        { 
            $query = $db->prepare("SELECT
                                        t.topicID, 
                                        t.topicTitle, 
                                        t.categoryID, 
                                        c.categoryName, 
                                        t.creatorID, 
                                        t.replies, 
                                        t.createdDate, 
                                        t.lastReplierID,
                                        t.lastReplyDate,
                                        CONCAT(u.firstName, ' ', u.lastName) as creatorName,                                        
                                        CONCAT(lu.firstName, ' ', lu.lastName) as lastReplierName
                                   FROM " . TABLE_FORUM_TOPICS . " AS t " .                     
                                  "LEFT JOIN " . TABLE_FORUM_CATEGORIES . " AS c ON c.categoryID=t.categoryID " .
                                  "LEFT JOIN " . TABLE_USERS . " AS u ON u.userID=t.creatorID " .
                                  "LEFT JOIN " . TABLE_USERS . " AS lu ON lu.userID = t.lastReplierID " .
                                  "WHERE t.creatorID=%d " .
                                  "UNION DISTINCT " .
                                  "SELECT  
                                        t.topicID, 
                                        t.topicTitle, 
                                        t.categoryID, 
                                        c.categoryName, 
                                        t.creatorID, 
                                        t.replies, 
                                        t.createdDate, 
                                        t.lastReplierID,
                                        t.lastReplyDate,
                                        CONCAT(u.firstName, ' ', u.lastName) as creatorName,
                                        CONCAT(lu.firstName, ' ', lu.lastName) as lastReplierName
                                    FROM " . TABLE_FORUM_REPLIES . " AS r " .
                                   "LEFT JOIN ". TABLE_FORUM_TOPICS . " AS t ON r.topicID=t.topicID " .                     
                                   "LEFT JOIN " . TABLE_FORUM_CATEGORIES . " AS c ON c.categoryID=t.categoryID " .
                                   "LEFT JOIN " . TABLE_USERS . " AS u ON u.userID=t.creatorID " .
                                   "LEFT JOIN " . TABLE_USERS . " AS lu ON lu.userID = t.lastReplierID " .
                                   "WHERE r.creatorID=%d "
            , $userID, $userID, $userID);
            
        }else if($type == 'started'){
            $query = $db->prepare("SELECT
                                        t.topicID, 
                                        t.topicTitle, 
                                        t.categoryID, 
                                        c.categoryName, 
                                        t.creatorID, 
                                        t.replies, 
                                        t.createdDate, 
                                        t.lastReplierID,
                                        t.lastReplyDate,
                                        CONCAT(u.firstName, ' ', u.lastName) as creatorName,                                        
                                        CONCAT(lu.firstName, ' ', lu.lastName) as lastReplierName
                                   FROM " . TABLE_FORUM_TOPICS . " AS t " .                     
                                  "LEFT JOIN " . TABLE_FORUM_CATEGORIES . " AS c ON c.categoryID=t.categoryID " .
                                  "LEFT JOIN " . TABLE_USERS . " AS u ON u.userID=t.creatorID " .
                                  "LEFT JOIN " . TABLE_USERS . " AS lu ON lu.userID = t.lastReplierID " .
                                  "WHERE t.creatorID=%d " 
                                  , $userID);
        }else if($type == 'responded'){
            $query = $db->prepare("SELECT  
                                        t.topicID, 
                                        t.topicTitle, 
                                        t.categoryID, 
                                        c.categoryName, 
                                        t.creatorID, 
                                        t.replies, 
                                        t.createdDate, 
                                        t.lastReplierID,
                                        t.lastReplyDate,
                                        CONCAT(u.firstName, ' ', u.lastName) as creatorName,
                                        CONCAT(lu.firstName, ' ', lu.lastName) as lastReplierName
                                    FROM " . TABLE_FORUM_REPLIES . " AS r " .
                                   "LEFT JOIN ". TABLE_FORUM_TOPICS . " AS t ON r.topicID=t.topicID " .                     
                                   "LEFT JOIN " . TABLE_FORUM_CATEGORIES . " AS c ON c.categoryID=t.categoryID " .
                                   "LEFT JOIN " . TABLE_USERS . " AS u ON u.userID=t.creatorID " .
                                   "LEFT JOIN " . TABLE_USERS . " AS lu ON lu.userID = t.lastReplierID " .
                                   "WHERE t.creatorID !=%d AND r.creatorID=%d GROUP BY topicID "
                                   ,$userID, $userID);
        }
        $query .= " ORDER BY lastReplyDate DESC, createdDate DESC, topicTitle ";
        $query .= " LIMIT " . ($page-1) * $limit . ', ' . $limit; 
        
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    /**
    * Cast a vote on a topic
    * 
    * @param Int $userID: voterID
    * @param Int $id
    * @param Int $voteType: 1: Thumb up, -1: Thumb Down
    */
    public function voteTopic($userID, $topicID, $voteType)
    {
        global $db, $BUCKYS_GLOBALS;
        
        //Check Reply ID        
        $query = $db->prepare("SELECT topicID, votes FROM " . TABLE_FORUM_TOPICS . " WHERE topicID=%d AND status='publish'", $topicID);
        $topic = $db->getRow($query);
        
        if(!$topic)
            return MSG_INVALID_REQUEST;
        
        $topicID = $topic['topicID'];
        $votes = $topic['votes'];
        
        //Check the user already casted his vote or not
        $query = $db->prepare("SELECT voteID FROM " . TABLE_FORUM_VOTES . " WHERE objectID=%d AND voterID=%d AND objectType='topic'", $topicID, $userID);
        $voteID = $db->getVar($query);
        if($voteID)
            return MSG_ALREADY_CASTED_A_VOTE;
            
        //Add Vote
        $voteID = $db->insertFromArray(TABLE_FORUM_VOTES, array('objectID' => $topicID, 'voterID' => $userID, 'objectType' => 'topic', 'voteDate' => date('Y-m-d H:i:s')));
        if(!$voteID)
            return $db->getLastError();
        
        $votes += $voteType;
        $db->update('UPDATE ' . TABLE_FORUM_TOPICS . ' SET `votes` = ' . $votes . ' WHERE topicID=' . $topicID);
        
        return $votes;
    }
}
