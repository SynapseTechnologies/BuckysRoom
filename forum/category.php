<?php
require(dirname(dirname(__FILE__)) . '/includes/bootstrap.php');

$categoryID = isset($_GET['id']) ? $_GET['id'] : 0;

$category = BuckysForumCategory::getCategory($categoryID);
if(!$category)
{
    buckys_redirect('/forum');
}

//Getting Topics by category id
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$total = BuckysForumTopic::getTotalNumOfTopics('publish', $category['categoryID']);

$pagination = new Pagination($total, BuckysForumTopic::$COUNT_PER_PAGE, $page);
$page = $pagination->getCurrentPage();

$topics = BuckysForumTopic::getTopics($page, 'publish', $category['categoryID'], null, BuckysForumTopic::$COUNT_PER_PAGE);

$hierarchical = BuckysForumCategory::getCategoryHierarchical($category['categoryID']);

//Mark Forum Notifications to read
if(buckys_check_user_acl(USER_ACL_REGISTERED))
    BuckysForumNotification::makeNotificationsToRead($BUCKYS_GLOBALS['user']['userID'], $category['categoryID']);

buckys_enqueue_javascript('jquery-migrate-1.2.0.js');

buckys_enqueue_stylesheet('forum.css');

$BUCKYS_GLOBALS['headerType'] = 'forum';
$BUCKYS_GLOBALS['content'] = 'forum/category';
$BUCKYS_GLOBALS['title'] = $category['categoryName'] . ' - BuckysRoomForum';

require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php");  


