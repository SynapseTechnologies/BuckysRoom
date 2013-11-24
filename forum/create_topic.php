<?php
require(dirname(dirname(__FILE__)) . '/includes/bootstrap.php');

if(!buckys_check_user_acl(USER_ACL_REGISTERED))
{
    buckys_redirect('/forum', MSG_PERMISSION_DENIED, MSG_TYPE_ERROR);
}

if(isset($_POST['action']))
{
    if($_POST['action'] == 'create-topic')
    {
        
        $result = BuckysForumTopic::createTopic($_POST);
        if($result == 'publish' || $result == 'pending')
        {
            buckys_redirect("/forum", MSG_TOPIC_POSTED_SUCCESSFULLY . ( $result == 'pending' ? ' ' . MSG_POST_IS_UNDER_PREVIEW : ''), MSG_TYPE_SUCCESS);
        }else{
            buckys_redirect("/forum/create_topic.php", $result, MSG_TYPE_ERROR);
        }
    }
}
$curCatID = isset($_GET['category']) ? $_GET['category'] : 0;

$categories = BuckysForumCategory::getAllCategories();

buckys_enqueue_stylesheet('editor/jquery.cleditor.css');
buckys_enqueue_stylesheet('uploadify.css');
buckys_enqueue_stylesheet('forum.css');


buckys_enqueue_javascript('jquery-migrate-1.2.0.js');
buckys_enqueue_javascript('uploadify/jquery.uploadify.js');
buckys_enqueue_javascript('editor/jquery.cleditor.js');

//buckys_enqueue_javascript('editor/jquery.cleditor.bbcode.js');

$view['action_type'] = 'create';

$BUCKYS_GLOBALS['headerType'] = 'forum';
$BUCKYS_GLOBALS['content'] = 'forum/create_topic';
$BUCKYS_GLOBALS['title'] = 'Create a New Topic - BuckysRoomForum';

require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php");  

