<?php
require(dirname(__FILE__) . '/includes/bootstrap.php');

//Getting Current User ID
if( !buckys_check_user_acl(USER_ACL_MODERATOR) )
{
    buckys_redirect('/index.php', MSG_PERMISSION_DENIED, MSG_TYPE_ERROR);
}  

$reportType = isset($_GET['type']) ? strtolower($_GET['type']) : null;

//Getting Current Moderator Type From the report Type
$moderatorType = null;

foreach($BUCKYS_GLOBALS['reportObjectTypes'] as $mtype => $row)
{
    if(in_array($reportType, $row))
    {
        $moderatorType = $mtype;
    }    
}

if($moderatorType == null)
    buckys_redirect('/index.php', MSG_PERMISSION_DENIED, MSG_TYPE_ERROR);

if($moderatorType == MODERATOR_FOR_COMMUNITY && !BuckysModerator::isModerator($BUCKYS_GLOBALS['user']['userID'], MODERATOR_FOR_COMMUNITY))
    buckys_redirect('/index.php', MSG_PERMISSION_DENIED, MSG_TYPE_ERROR);
if($moderatorType == MODERATOR_FOR_FORUM && !BuckysModerator::isModerator($BUCKYS_GLOBALS['user']['userID'], MODERATOR_FOR_FORUM))
    buckys_redirect('/index.php', MSG_PERMISSION_DENIED, MSG_TYPE_ERROR);
if($moderatorType == MODERATOR_FOR_TRADE && !BuckysModerator::isModerator($BUCKYS_GLOBALS['user']['userID'], MODERATOR_FOR_TRADE))
    buckys_redirect('/index.php', MSG_PERMISSION_DENIED, MSG_TYPE_ERROR);

if(isset($_REQUEST['action']))
{
    if($_REQUEST['action'] == 'delete-objects')
    {
        BuckysReport::deleteObjects($_REQUEST['reportID'], $reportType, $moderatorType);
        buckys_redirect('/reported.php?type=' . $reportType, MSG_REPORTED_OBJECT_REMOVED);
    }else if($_REQUEST['action'] == 'approve-objects'){                    
        BuckysReport::approveObjects($_REQUEST['reportID'], $reportType, $moderatorType);
        buckys_redirect('/reported.php?type=' . $reportType, MSG_REPORTED_OBJECT_APPROVED);
    }else if($_REQUEST['action'] == 'ban-users'){            
        BuckysReport::banUsers($_REQUEST['reportID'], $reportType, $moderatorType);
        buckys_redirect('/reported.php?type=' . $reportType, MSG_BAN_USERS);
    }
    exit;
}



$page = isset($_GET['page']) ? $_GET['page'] : 1;
$totalCount = BuckysReport::getReportedObjectCount($reportType);

//Init Pagination Class
$pagination = new Pagination($totalCount, BuckysReport::$COUNT_PER_PAGE, $page);
$page = $pagination->getCurrentPage();

$objects = BuckysReport::getReportedObject($reportType, $page, BuckysReport::$COUNT_PER_PAGE);

buckys_enqueue_stylesheet('account.css');
buckys_enqueue_stylesheet('moderator.css');
buckys_enqueue_stylesheet('moderator.css');
buckys_enqueue_stylesheet('prettify.css');

buckys_enqueue_javascript('prettyprint/run_prettify.js?skin=default&amp;');
buckys_enqueue_javascript('reported.js');

$BUCKYS_GLOBALS['content'] = 'reported';

//Reported Object Type Label
$reportLabel = array(
    'post' => array('Post', 'Posts'),
    'comment' => array('Comment', 'Comments'),
    'message' => array('Message', 'Messages'),
    'topic' => array('Topic', 'Topics'),
    'reply' => array('Reply', 'Replies')
);

$BUCKYS_GLOBALS['title'] = "Manage Reported " . $reportLabel[$reportType][1] . " - BuckysRoom";

require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php");  
