<?php
require(dirname(dirname(__FILE__)) . '/includes/bootstrap.php');

if(!buckys_check_user_acl(USER_ACL_REGISTERED))
{
    buckys_redirect('/forum');
}

//Getting Topics by category id
$page = isset($_GET['page']) ? $_GET['page'] : 1;

$total = BuckysForumTopic::getTotalNumOfTopics('publish');

$pagination = new Pagination($total, BuckysForumTopic::$COUNT_PER_PAGE, $page);
$page = $pagination->getCurrentPage();

$topics = BuckysForumTopic::getTopics($page, 'publish', null, 'lastReplyDate DESC, t.createdDate DESC', BuckysForumTopic::$COUNT_PER_PAGE);

buckys_enqueue_javascript('jquery-migrate-1.2.0.js');

buckys_enqueue_stylesheet('forum.css');

$BUCKYS_GLOBALS['headerType'] = 'forum';
$BUCKYS_GLOBALS['content'] = 'forum/recent_activity';
$BUCKYS_GLOBALS['title'] = 'Recent Activity - BuckysRoomForum';

require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php");  


