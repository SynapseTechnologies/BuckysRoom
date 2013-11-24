<?php
require(dirname(__FILE__) . '/includes/bootstrap.php');

$userID = buckys_is_logged_in();

$popularImages = BuckysPost::getPostsFromStats('image');
$popularPosts = BuckysPost::getPostsFromStats('text');
$popularVideos = BuckysPost::getPostsFromStats('video');

$popularPages = BuckysPage::getPopularPagesForHomepage();

$recentTopics = BuckysForumTopic::getTopics(1, 'publish', null, 'lastReplyDate DESC, t.createdDate DESC', 5);

$recentTradeItems = BuckysTradeItem::getRecentItems(3);

buckys_enqueue_stylesheet('index.css');

$BUCKYS_GLOBALS['content'] = "home";

$BUCKYS_GLOBALS['title'] = "BuckysRoom - The Worlds Most Popular Open Source Social Network";

require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php"); 
