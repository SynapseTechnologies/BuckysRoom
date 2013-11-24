<?php
require(dirname(dirname(__FILE__)) . '/includes/bootstrap.php');

if(isset($_POST['action']))
{
    if($_POST['action'] == 'thumb-up' || $_POST['action'] == 'thumb-down')
    {
        if(!buckys_check_user_acl(USER_ACL_REGISTERED))
        {
            $data = array('status'=>'error', 'message'=>MSG_PLEASE_LOGIN_TO_CAST_VOTE);
        }else{
            if(!$_POST['objectID'] || !$_POST['objectIDHash'] || !$_POST['objectType'] || !buckys_check_id_encrypted($_POST['objectID'], $_POST['objectIDHash']))
            {
                $data = array('status'=>'error', 'message'=>MSG_INVALID_REQUEST);            
            }else{
                if($_POST['objectType'] == 'topic')
                    $result = BuckysForumTopic::voteTopic($BUCKYS_GLOBALS['user']['userID'], $_POST['objectID'], $_POST['action'] == 'thumb-up' ? 1 : -1);
                else
                    $result = BuckysForumReply::voteReply($BUCKYS_GLOBALS['user']['userID'], $_POST['objectID'], $_POST['action'] == 'thumb-up' ? 1 : -1);
                if(is_int($result))
                {
                    $data = array('status'=>'success', 'message'=>MSG_THANKS_YOUR_VOTE, 'votes' => ($result > 0 ? "+" : "") . $result);            
                }else{
                    $data = array('status'=>'error', 'message'=>$result);            
                }
            }
        }
        render_result_xml($data);
        exit;
    }
}
else if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    //Delete this topic
    
    $userID = buckys_is_logged_in();
    
    $topicID = isset($_GET['id']) ? get_secure_integer($_GET['id']) : null;
    
    if (isset($topicID)) {
        $forumTopicIns = new BuckysForumTopic();
        $forumData = $forumTopicIns->getTopic($topicID);
        
        if (isset($forumData) && $forumData['creatorID'] == $userID) {
            //then you can delete this one.
            $forumTopicIns->deleteTopic($topicID);
            
            buckys_redirect('/forum', MSG_TOPIC_REMOVED_SUCCESSFULLY, MSG_TYPE_SUCCESS);
            
        }
        else {
            //You don't have permission
            buckys_redirect('/forum/topic.php?id=' . $topicID, MSG_PERMISSION_DENIED, MSG_TYPE_ERROR);
            
        }
        
    }
    
    
}

$topicID = isset($_GET['id']) ? $_GET['id'] : 0;

$topic = BuckysForumTopic::getTopic($topicID);

if(!$topic)
    buckys_redirect('/forum');

//If the topic is not published(pending or suspended), only forum moderator and administrator can see this
if($topic['status'] != 'publish' && !buckys_is_forum_moderator() && $BUCKYS_GLOBALS['user']['userID'] != $topic['creatorID'])
    buckys_redirect('/forum');
    
$orderBy = isset($_GET['orderby']) ? $_GET['orderby'] : 'oldest';
    
//Getting Replies
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$total = BuckysForumReply::getTotalNumOfReplies($topic['topicID'], 'publish');

$pagination = new Pagination($total, BuckysForumReply::$COUNT_PER_PAGE, $page);
$page = $pagination->getCurrentPage();

$replies = BuckysForumReply::getReplies($topic['topicID'], 'publish', $page, $orderBy);

$hierarchical = BuckysForumCategory::getCategoryHierarchical($topic['categoryID']);

//Mark Forum Notifications to read
if(buckys_check_user_acl(USER_ACL_REGISTERED))
    BuckysForumNotification::makeNotificationsToRead($BUCKYS_GLOBALS['user']['userID'], null, $topic['topicID']);

buckys_enqueue_javascript('jquery-migrate-1.2.0.js');
buckys_enqueue_javascript('editor/jquery.cleditor.js');
buckys_enqueue_javascript('prettyprint/run_prettify.js?skin=default&amp;');
buckys_enqueue_javascript('forum.js');
//buckys_enqueue_javascript('editor/jquery.cleditor.bbcode.js');

buckys_enqueue_stylesheet('editor/jquery.cleditor.css');
buckys_enqueue_stylesheet('prettify.css');
buckys_enqueue_stylesheet('forum.css');

$BUCKYS_GLOBALS['headerType'] = 'forum';
$BUCKYS_GLOBALS['content'] = 'forum/topic';
$BUCKYS_GLOBALS['title'] = $topic['topicTitle'] . ' - BuckysRoomForum';

require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php");  

