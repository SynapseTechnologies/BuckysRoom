<?php
require(dirname(__FILE__) . '/includes/bootstrap.php');

//Getting Current User ID
if( !buckys_check_user_acl(USER_ACL_REGISTERED) )
{
    buckys_redirect('/index.php', MSG_NOT_LOGGED_IN_USER, MSG_TYPE_ERROR);
}

$moderatorType = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
if( !in_array($moderatorType, $BUCKYS_GLOBALS['moderatorTypes']) )
{
    //Redirect to community moderator page
    buckys_redirect('/moderator.php?type=community');
}

//Choose Moderator
if( isset($_GET['action']) && $_GET['action'] == 'choose-moderator')
{
    //Confirm that the user is administrator
    if( !buckys_check_user_acl(USER_ACL_ADMINISTRATOR) )    
    {
        buckys_redirect('/moderator.php?type=' . $moderatorType, MSG_PERMISSION_DENIED, MSG_TYPE_ERROR);
    }
    
    //Check the url parameters is correct 
    if( !isset($_GET['id']) || !isset($_GET['idHash']) || !buckys_check_id_encrypted($_GET['id'], $_GET['idHash']) )
    {
        buckys_redirect('/moderator.php?type=' . $moderatorType, MSG_INVALID_REQUEST, MSG_TYPE_ERROR);
    }
    
    BuckysModerator::chooseModerator($_GET['id']);
    buckys_redirect('/moderator.php?type=' . $moderatorType);
}

//Process Actions
if(isset($_POST['action']))
{
    if($_POST['action'] == 'apply_candidate')
    {
        $newID = BuckysModerator::applyCandidate($userID, $moderatorType, $_POST['moderator_text']);
        buckys_redirect('/moderator.php?type=' . $moderatorType, MSG_APPLY_JOB_SUCCESSFULLY);
    }
    
    if($_POST['action'] == 'thumb-up' || $_POST['action'] == 'thumb-down')
    {
        if(!$_POST['candidateID'] || !$_POST['candidateIDHash'] || !buckys_check_id_encrypted($_POST['candidateID'], $_POST['candidateIDHash']))
        {
            $data = array('status'=>'error', 'message'=>MSG_INVALID_REQUEST);            
        }else{
            $result = BuckysModerator::voteCandidate($userID, $_POST['candidateID'], $_POST['action'] == 'thumb-up' ? true : false);
            if(is_int($result))
            {
                $data = array('status'=>'success', 'message'=>MSG_THANKS_YOUR_VOTE, 'votes' => ($result > 0 ? "+" : "") . $result);            
            }else{
                $data = array('status'=>'error', 'message'=>$result);            
            }
        }
        
        render_result_xml($data);
        exit;
    }
    
}

//Get Remaind Date
$timeOffset = strtotime('next Monday') - time();
$remindTimeString = '';
if($timeOffset / (60 * 60 * 24) >= 1)
{
    $day = floor($timeOffset / (60 * 60 * 24));    
    $remindTimeString = $day . ' day' . ($day > 1 ? 's ' : ' ');
    $timeOffset = $timeOffset % (60 * 60 * 24);
}

if($timeOffset / (60 * 60) >= 1)
{
    $hour = floor($timeOffset / (60 * 60));    
    $remindTimeString .= $hour . ' hour' . ($hour > 1 ? 's ' : ' ');
    $timeOffset = $timeOffset % (60 * 60);
}

if($remindTimeString == '')
{
    $remindTimeString = ceil($timeOffset / 60);
    if($remindTimeString > 1)
        $remindTimeString .= ' minutes';
    else
        $remindTimeString .= ' minute';
}

if($remindTimeString == '')
{
    $remindTimeString = $timeOffset;
    if($remindTimeString > 1)
        $remindTimeString .= ' seconds';
    else
        $remindTimeString .= ' second';
}

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$totalCount = BuckysModerator::getCandidatesCount($moderatorType);

//Getting Current Moderator
$currentModerator = BuckysModerator::getModerator($moderatorType);

//Init Pagination Class
$pagination = new Pagination($totalCount, BuckysModerator::$CANDIDATES_PER_PAGE, $page);
$page = $pagination->getCurrentPage();

$candidates = BuckysModerator::getCandidates($moderatorType, $page);

buckys_enqueue_stylesheet('account.css');
buckys_enqueue_stylesheet('moderator.css');

buckys_enqueue_javascript('moderator.js');

$BUCKYS_GLOBALS['content'] = 'moderator';


$BUCKYS_GLOBALS['title'] = "Moderator - BuckysRoom";

//if logged user can see all resources of the current user

require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php");  
