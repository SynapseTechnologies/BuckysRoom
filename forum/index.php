<?php
require(dirname(dirname(__FILE__)) . '/includes/bootstrap.php');

$categoryID = isset($_GET['id']) ? $_GET['id'] : null;
if($categoryID != null)
    $category = BuckysForumCategory::getCategory($categoryID);

//If Subcategory, goto category page
if($category && $category['parentID'] != 0)    
    buckys_redirect('/forum/category.php?id=' . $categoryID);
    
$categories = BuckysForumCategory::getAllCategories($categoryID);
if($category)
    $hierarchical = BuckysForumCategory::getCategoryHierarchical($category['categoryID']);

//Mark Forum Notifications to read
if(buckys_check_user_acl(USER_ACL_REGISTERED))
    BuckysForumNotification::makeNotificationsToRead($BUCKYS_GLOBALS['user']['userID']);

buckys_enqueue_stylesheet('forum.css');

$BUCKYS_GLOBALS['headerType'] = 'forum';
$BUCKYS_GLOBALS['content'] = 'forum/index';
$BUCKYS_GLOBALS['title'] = 'BuckysRoomForum';

require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php");  

