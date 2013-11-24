<?php
/**
* Initation File
* Including All base classes as well as config file
*/

//Include Config file
require_once(dirname(__FILE__) . "/config.php");

//Auto load classes
function __autoload($className)
{
    if(file_exists(DIR_FS_CLASSES . "class." . $className . ".php"))
        include DIR_FS_CLASSES . "class." . $className . ".php";
}

require_once(DIR_FS_INCLUDES . "messages.php");
require_once(DIR_FS_INCLUDES . "tables.php");

$db = new Database_Mysqli(DATABASE_HOST, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);

require_once(DIR_FS_FUNCTIONS . "session.php");
require_once(DIR_FS_FUNCTIONS . "general.php");
require_once(DIR_FS_FUNCTIONS . "view.php");

//Session Start
buckys_session_start();

//Init Global Values
buckys_enqueue_javascript('jquery-1.9.0.js', false, false);
buckys_enqueue_javascript('site.js');

buckys_enqueue_stylesheet('main.css');

$BUCKYS_GLOBALS['template'] = DEFAULT_THEME;
$BUCKYS_GLOBALS['layout'] = 'layout';
$BUCKYS_GLOBALS['headerType'] = 'default';

//Define User Acl Constants
BuckysUserAcl::defineAclConstants();

//Set User Data into Global Variable
if( !($userID = buckys_is_logged_in()) )
{   
    $BUCKYS_GLOBALS['user'] = array(
        'userID' => 0,
        'user_type' => 'Public',
        'aclLevel' => 0,
        'aclName' => 'Public'
    );        
}else{
    $BUCKYS_GLOBALS['user'] = BuckysUser::getUserBasicInfo($userID);
}


if(buckys_is_logged_in()){
    buckys_enqueue_stylesheet('footer.css');
    buckys_enqueue_stylesheet('jquery-ui/jquery-ui.css');
    
    buckys_enqueue_javascript('jquery-ui.min.js');    
    buckys_enqueue_javascript('jquery.contextMenu.js');
    buckys_enqueue_javascript('private_messenger.js');
}
