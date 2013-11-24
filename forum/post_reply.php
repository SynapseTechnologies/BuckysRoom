<?php
require(dirname(dirname(__FILE__)) . '/includes/bootstrap.php');

if(!buckys_check_user_acl(USER_ACL_REGISTERED))
{
    buckys_redirect('/forum', MSG_PERMISSION_DENIED, MSG_TYPE_ERROR);
}

$topicID = isset($_GET['id']) ? $_GET['id'] : 0;

$topic = BuckysForumTopic::getTopic($topicID);
$forumReplyIns = new BuckysForumReply();
$view = array();
$view['action_type'] = 'create';




if(!$topic)
    buckys_redirect('/forum');


if(isset($_POST['action']))
{
    if($_POST['action'] == 'post-reply')
    {        
        $result = BuckysForumReply::createReply($_POST);
        if($result == 'pending' || $result == 'publish')
        {
            buckys_redirect("/forum/topic.php?id=" . $topicID, MSG_REPLY_POSTED_SUCCESSFULLY . ( $result == 'pending' ? ' ' . MSG_POST_IS_UNDER_PREVIEW : ''), MSG_TYPE_SUCCESS);
        }else{
            buckys_redirect("/forum/post_reply.php?id=" . $topicID, $result, MSG_TYPE_ERROR);
        }
    }    
    else if($_POST['action'] == 'edit-post-reply')
    {
        
        $userID = buckys_is_logged_in();
        $replyID = isset($_REQUEST['replyID']) ? get_secure_integer($_REQUEST['replyID']) : null;
        $replyData = $forumReplyIns->getReplyByID($replyID);
        
        if ($replyData && $replyData['creatorID'] == $userID && $replyData['topicID'] == $topicID) {
            
            $result = $forumReplyIns->editReply($_POST);
            
            if($result == 'pending' || $result == 'publish')
            {
                
                buckys_redirect("/forum/topic.php?id=" . $topicID, MSG_REPLY_POSTED_SUCCESSFULLY, MSG_TYPE_SUCCESS);
                
            }else{
                
                $replyID = isset($_REQUEST['replyID']) ? get_secure_integer($_REQUEST['replyID']) : null;            
                buckys_redirect("/forum/post_reply.php?id=" . $topicID . '&action=edit&replyID=' . $replyID, $result, MSG_TYPE_ERROR);
                
            }
        }
        else {
            //no permission
            //permission error
            buckys_redirect('/forum', MSG_PERMISSION_DENIED, MSG_TYPE_ERROR);
        }
        
    }
}
else if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    //Delete post_reply
    
    $userID = buckys_is_logged_in();
    $replyID = isset($_GET['replyID']) ? get_secure_integer($_GET['replyID']) : null;
    $replyData = $forumReplyIns->getReplyByID($replyID);
    
    if ($replyData && $replyData['creatorID'] == $userID && $replyData['topicID'] == $topicID) {
        //then you can delete this one
        $forumReplyIns->deleteReply($replyID);
        echo 'success';
    }
    else {
        echo MSG_PERMISSION_DENIED;
    }
    
    exit;
    
    
}

else if (isset($_GET['action']) && $_GET['action'] == 'edit') {
    
    //edit post_reply
    
    $forumReplyIns = new BuckysForumReply();
    $userID = buckys_is_logged_in();
    $replyID = isset($_GET['replyID']) ? get_secure_integer($_GET['replyID']) : null;
    $replyData = $forumReplyIns->getReplyByID($replyID);
    
    if ($replyData && $replyData['creatorID'] == $userID && $replyData['topicID'] == $topicID) {
        //then you can edit this one
        $view['replyData'] = $replyData;
        $view['action_type'] = 'edit';
        $view['replyID'] = $replyID;
    }
    else {
        
        //permission error
        buckys_redirect('/forum', MSG_PERMISSION_DENIED, MSG_TYPE_ERROR);
    }
    
    
}


$categories = BuckysForumCategory::getAllCategories();

buckys_enqueue_stylesheet('editor/jquery.cleditor.css');
buckys_enqueue_stylesheet('uploadify.css');
buckys_enqueue_stylesheet('forum.css');

buckys_enqueue_javascript('jquery-migrate-1.2.0.js');
buckys_enqueue_javascript('uploadify/jquery.uploadify.js');
buckys_enqueue_javascript('editor/jquery.cleditor.js');
//buckys_enqueue_javascript('editor/jquery.cleditor.bbcode.js');


$BUCKYS_GLOBALS['headerType'] = 'forum';
$BUCKYS_GLOBALS['content'] = 'forum/post_reply';
$BUCKYS_GLOBALS['title'] = 'Post Reply - BuckysRoomForum';

require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php");  

