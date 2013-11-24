<?php
require(dirname(__FILE__) . '/includes/bootstrap.php');

//Getting Current User ID
$userID = buckys_is_logged_in();

//Getting User ID from Parameter
$profileID = isset($_GET['user']) ? $_GET['user'] : 0;

//When displaying page's photo
$showPagePhotoFlag = false;
$paramPageID = BuckysPost::INDEPENDENT_POST_PAGE_ID;

$pageData = null;
if (isset($_GET['pid'])) {
    $paramPageID = $_GET['pid'];
    $pageIns = new BuckysPage();
    $pageData = $pageIns->getPageByID($paramPageID);
    if ($pageData) {
        $profileID = $pageData['userID'];
        $showPagePhotoFlag = true;
    }
    
}

//If the parameter is null, goto homepage 
if(!$profileID)
    buckys_redirect('/index.php');

//Getting UserData from Id
$userData = BuckysUser::getUserData($profileID);

//Goto Homepage if the userID is not correct
if( !buckys_not_null($userData) || !BuckysUser::checkUserID($profileID, true) )
{
    buckys_redirect('/index.php');
}

if (!$showPagePhotoFlag) {
    //if logged user can see all resources of the current user
    $canViewPrivate = $userID == $profileID || BuckysFriend::isFriend($userID, $profileID) || BuckysFriend::isSentFriendRequest($profileID, $userID);

    $photos = BuckysPost::getPhotosByUserID($profileID, $userID, $paramPageID, $canViewPrivate, isset($_GET['post']) ? $_GET['post'] : null, isset($_GET['albumID']) ? $_GET['albumID'] : null, BuckysPost::$images_per_page);            

    $albums = BuckysAlbum::getAlbumsByUserId($profileID);
    
    //Display
    $BUCKYS_GLOBALS['title'] = trim($userData['firstName'] . ' ' . $userData['lastName']) . "'s Photos - BuckysRoom";
    $view['photo_type'] = 'profile';
    
    buckys_enqueue_stylesheet('profile.css');
    buckys_enqueue_stylesheet('posting.css');

    buckys_enqueue_javascript('posts.js');


}
else {
    //Show page photos
    //if logged user can see all resources of the current user
    
    $photos = BuckysPost::getPhotosByUserID($profileID, null, $paramPageID, false, isset($_GET['post']) ? $_GET['post'] : null, isset($_GET['albumID']) ? $_GET['albumID'] : null, BuckysPost::$images_per_page);            

    //$albums = BuckysAlbum::getAlbumsByUserId($profileID);
    
    
    //Display
    $BUCKYS_GLOBALS['title'] = trim($pageData['title']) . "'s Photos - BuckysRoom";
    $view['photo_type'] = 'page';
    $view['pageData'] = $pageData;
    
    buckys_enqueue_stylesheet('account.css');
    buckys_enqueue_stylesheet('stream.css');
    buckys_enqueue_stylesheet('posting.css');
    buckys_enqueue_stylesheet('uploadify.css');
    buckys_enqueue_stylesheet('jquery.Jcrop.css');
    buckys_enqueue_stylesheet('page.css');

    buckys_enqueue_javascript('uploadify/jquery.uploadify.js');
    buckys_enqueue_javascript('jquery.Jcrop.js');
    buckys_enqueue_javascript('jquery.color.js');

    buckys_enqueue_javascript('posts.js');
    buckys_enqueue_javascript('add_post.js');
    buckys_enqueue_javascript('page.js');
    

}


$BUCKYS_GLOBALS['content'] = 'photos';
require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php");  

