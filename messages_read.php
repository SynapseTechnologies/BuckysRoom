<?php
require(dirname(__FILE__) . '/includes/bootstrap.php');

//If the user is not logged in, redirect to the index page
if( !($userID = buckys_is_logged_in()) )
{
    buckys_redirect('/index.php');
}

$userData = BuckysUser::getUserBasicInfo($userID);

if( isset($_POST['action']) )
{    
    //Check the user id is same with the current logged user id
    if($_POST['userID'] != $userID)    
    {
        echo 'Invalid Request!';
        exit;
    }
    //Delete Message
    if( $_POST['action'] == 'delete_message' )   
    {       
        if( !BuckysMessage::deleteMessages($_POST['messageID']) )
        {
            buckys_redirect('/messages_inbox.php', "Error: " . $db->getLastError(), MSG_TYPE_ERROR);
        }else{
            buckys_redirect('/messages_inbox.php', MSG_MESSAGE_REMOVED, MSG_TYPE_SUCCESS);
        }
        exit;        
    }
    
    //Delete Message Foreer
    if( $_POST['action'] == 'delete_forever' )   
    {       
        if( !BuckysMessage::deleteMessagesForever($_POST['messageID']) )
        {
            buckys_redirect('/messages_inbox.php', "Error: " . $db->getLastError(), MSG_TYPE_ERROR);
        }else{
            buckys_redirect('/messages_inbox.php', MSG_MESSAGE_REMOVED, MSG_TYPE_SUCCESS);
        }
        exit;        
    }    
    
}

if( isset($_GET['message']) )
    $message = BuckysMessage::getMessage($_GET['message']);

if( !isset($_GET['message']) || !$message )
    buckys_redirect('/messages_inbox.php');

//Make Message as read
BuckysMessage::changeMessageStatus($message['messageID'], 'read');

//Getting Next Message ID and Prev Message ID
if( $message['is_trash'] == 1 )
{
    $msgType = 'trash';
}else if( $message['receiver'] == $userID ){
    $msgType = 'inbox';
}else if( $message['sender'] == $userID ){
    $msgType = 'sent';
}

$nextID = BuckysMessage::getNextID($userID, $message['messageID'], $msgType);
$prevID = BuckysMessage::getPrevID($userID, $message['messageID'], $msgType);

buckys_enqueue_stylesheet('account.css');
buckys_enqueue_stylesheet('info.css');
buckys_enqueue_stylesheet('messages.css');

buckys_enqueue_javascript('messages.js');

$BUCKYS_GLOBALS['content'] = 'messages_read';

$BUCKYS_GLOBALS['title'] = "Read Message - BuckysRoom";

require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php");  
