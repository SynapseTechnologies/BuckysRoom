<?php
require(dirname(__FILE__) . '/includes/bootstrap.php');

//If the user is not logged in, redirect to the index page
if( !($userID = buckys_is_logged_in()) )
{
    buckys_redirect('/index.php');
}

//Getting UserData from Id
$userData = BuckysUser::getUserEducations($userID);


if( isset($_POST['action']) )
{    
    //Check the user id is same with the current logged user id
    if($_POST['userID'] != $userID)    
    {
        echo 'Invalid Request!';
        exit;
    }
    
    
    //Save Address
    if( $_POST['action'] == 'save_education' )   
    {       
        $data = array();
        for( $i = 0; $i < count($_POST['schoolname']); $i++ )
        {
            $data[] = array(
                'name' => $_POST['schoolname'][$i],
                'start' => $_POST['from'][$i],
                'end' => $_POST['to'][$i],
                'visibility' => $_POST['visibility'][$i]
            );
        }
        //Update User Phone numbers
        if( BuckysUser::updateUserEducationInfo($userID, $data) )
        {
            echo 'Success';
        }else{
            echo $db->getLastError();
        }
        
        exit;
        
    }
    
}

buckys_enqueue_stylesheet('account.css');
buckys_enqueue_stylesheet('info.css');

buckys_enqueue_javascript('info.js');

$BUCKYS_GLOBALS['content'] = 'info_education';

$BUCKYS_GLOBALS['title'] = "Education - BuckysRoom";

require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php");  
