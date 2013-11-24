<?php
/**
* Post Manage Class
*/

class BuckysPost
{
    public static $post_per_page = 5;
    public static $images_per_page = 30;
    public static $IMAGES_PER_PAGE_FOR_MANAGE_PHOTOS_PAGE = 15;
    public static $COUNT_PER_PAGE_TEXT = 30;
    public static $COUNT_PER_PAGE_IMAGE = 60;
    public static $COUNT_PER_PAGE_VIDEO = 40;
    
    const INDEPENDENT_POST_PAGE_ID = 0; // When this post doesn't belong to a page, then the post's page ID will be 0;
    
    /**
    * Getting all posts that were published by the user's friends
    * 
    * 
    * @param mixed $userID
    * @return Array
    */
    public function getUserPostsStream($userID, $lastDate = null)
    {
        global $db;
        
        $userID = intval($userID);
        
        $pageID = BuckysPost::INDEPENDENT_POST_PAGE_ID; 
        
        $pageCondition = "";
        
        //Add 0 for independent posts
        $pageIDs = array(BuckysPost::INDEPENDENT_POST_PAGE_ID);
        
        //Add users pages
        $myPages = BuckysPage::getPagesByUserId($userID);        
        foreach($myPages as $p)
            $pageIDs[] = $p['pageID'];
        
        //Add Follower pages            
        $pageFollowerIns = new BuckysPageFollower();
        $followedPageList = $pageFollowerIns->getPagesByFollowerID($userID);
        foreach($followedPageList as $pageData) {
            $pageIDs[] = $pageData['pageID'];
        }
        
        $pageCondition = " p.pageID in (" . implode(", ", $pageIDs) . ")";
        
        //Page Limit Query
        $limit_query = ' LIMIT ' . BuckysPost::$post_per_page;
        
        $query = $db->prepare("
            SELECT p.* FROM " . TABLE_POSTS . " AS p WHERE p.poster=%d AND " . $pageCondition . ($lastDate != null ? ' AND p.post_date < "' . $lastDate . '"' : '') . "
            UNION DISTINCT
            SELECT p.* FROM " . TABLE_POSTS . " AS p
            LEFT JOIN " . TABLE_FRIENDS . " AS f ON p.poster=f.userFriendID and f.status=1
            WHERE f.userID=%d AND " . $pageCondition . ($lastDate != null ? ' AND p.post_date < "' . $lastDate . '"' : '') . "
            ORDER BY post_date DESC " . $limit_query . "
        ", $userID, $userID);
        
        
        $rows = $db->getResultsArray($query);
        
        foreach($rows as $idx=>$row)
        {
            //Getting full name
            $query = "SELECT firstName, lastName FROM " . TABLE_USERS . " WHERE userID=" . $row['poster'];
            $urow = $db->getRow($query);
            $rows[$idx]['posterFullName'] = $urow['firstName'] . " " . $urow['lastName'];
            //Getting Liked
            $query = "SELECT likeID FROM " . TABLE_POSTS_LIKES . " WHERE postID=" . $row['postID'] . " AND userID=" . $userID;
            $liked = $db->getVar($query);
            $rows[$idx]['likeID'] = $liked;
            //Getting Reported
            $query = $db->prepare("SELECT reportID FROM " . TABLE_REPORTS . " WHERE reporterID=%d AND objectID=%d and objectType='post'", $userID, $row['postID']);
            $reportID = $db->getVar($query);
            $rows[$idx]['reportID'] = $reportID;
            
        }
        return $rows;
    }
    
    
    /**
    * Get Posts Or Post
    * 
    * 
    * @param integer $userID : Poster
    * @param integer $loggedUserID : viewer
    * @param boolean $canViewPrivate
    * @param integer $postID
    */
    public function getPostsByUserID($userID, $loggedUserID = null, $pageID=BuckysPost::INDEPENDENT_POST_PAGE_ID, $canViewPrivate = false, $postID = null, $lastDate = null)
    {
        global $db;
        
        
        $limit = BuckysPost::$post_per_page;
        
        $userID = intval($userID);
        
        //Getting Page Parameter
        if( isset($_GET['page']) && buckys_not_null($_GET['page']) )
            $page = intval($_GET['page']);    
        else
            $page = 1;
            
        //Page Limit Query
        $limit_query = ' LIMIT ' . (($page - 1) * $limit) . ", " . $limit;
        
        if( buckys_not_null($loggedUserID) ) //Get All posts that were posted by $userID
        {
            $query = $db->prepare('SELECT p.*, CONCAT(u.firstName, " ", u.lastName) as posterFullName, pl.likeID, r.reportID FROM ' . TABLE_POSTS . ' as p
                                LEFT JOIN ' . TABLE_USERS . ' as u ON p.poster = u.userID                                 
                                LEFT JOIN ' . TABLE_POSTS_LIKES . ' as pl ON pl.postID = p.postID AND pl.userID = %d
                                LEFT JOIN ' . TABLE_REPORTS . ' as r ON r.objectType="post" AND r.objectID=p.postID AND r.reporterID= %d
                                WHERE p.poster=%d AND p.pageID=%d' , $loggedUserID, $loggedUserID, $userID, $pageID);
            if( !$canViewPrivate )
                $query .= " AND p.visibility=1 ";
        }else{ //Get Only Public Posts
            $query = $db->prepare('SELECT p.*, CONCAT(u.firstName, " ", u.lastName) as posterFullName, 0 as likedID, 0 as reportID FROM ' . TABLE_POSTS . ' as p
                                LEFT JOIN ' . TABLE_USERS . ' as u ON p.poster = u.userID                                                                              
                                WHERE p.poster= %d AND p.visibility=1 AND p.pageID=%d', $userID, $pageID);
        }
        
        if(!buckys_check_user_acl(USER_ACL_ADMINISTRATOR))
            $query .= ' AND p.post_status=1 ';
        //If Post ID is set, get only one post
        if( $postID != null )
            $query .= $db->prepare(' AND p.postID=%d', $postID);
        /*else
            $query .= ' AND p.type !="image" ';*/
        
        if( $lastDate != null ){
            $lastDate = date('Y-m-d H:i:s', strtotime($lastDate));
            $query .= ' AND p.post_date < "' . $lastDate . '"';
        }
        
        $query .= ' ORDER BY p.post_date DESC ' . $limit_query;
        
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    /**
    * Get photos
    * 
    * 
    * @param int $userID
    * @param int $loggedUserID
    * @param int $pageID
    * @param boolean $canViewPrivate
    * @param int $postID
    * @param int $albumID
    * @param int $limit
    * @param string $lastDate
    * @return Indexed
    */
    public function getPhotosByUserID($userID, $loggedUserID = null, $pageID=BuckysPost::INDEPENDENT_POST_PAGE_ID, $canViewPrivate = false, $postID = null, $albumID = null, $limit = null, $lastDate = null)
    {
        global $db;
        
        /*if( !$limit )
            $limit = BuckysPost::$post_per_page;*/
        
        $userID = intval($userID);
        
        //Getting Page Parameter
        if( isset($_GET['page']) && buckys_not_null($_GET['page']) )
            $page = intval($_GET['page']);    
        else
            $page = 1;
            
        //Page Limit Query
        if($limit)
            $limit_query = ' LIMIT ' . (($page - 1) * $limit) . ", " . $limit;
        
        if( buckys_not_null($loggedUserID) && $canViewPrivate ) //Get All posts that were posted by $userID
        {
            $query = 'SELECT p.*, CONCAT(u.firstName, " ", u.lastName) as posterFullName, pl.likeID, pa.album_id FROM ' . TABLE_POSTS . ' as p
                                LEFT JOIN ' . TABLE_USERS . ' as u ON p.poster = u.userID                                 
                                LEFT JOIN ' . TABLE_ALBUMS_PHOTOS . ' as pa ON pa.post_id = p.postID                                 
                                LEFT JOIN ' . TABLE_POSTS_LIKES . ' as pl ON pl.postID = p.postID AND pl.userID = ' . $userID . '
                                WHERE p.poster= ' . $userID . ' AND p.pageID=' . $pageID;
        }else{ //Get Only Public Posts
            $query = 'SELECT p.*, CONCAT(u.firstName, " ", u.lastName) as posterFullName, pl.likeID, pa.album_id FROM ' . TABLE_POSTS . ' as p
                                LEFT JOIN ' . TABLE_USERS . ' as u ON p.poster = u.userID                                 
                                LEFT JOIN ' . TABLE_ALBUMS_PHOTOS . ' as pa ON pa.post_id = p.postID          
                                LEFT JOIN ' . TABLE_POSTS_LIKES . ' as pl ON pl.postID = p.postID AND pl.userID = ' . $userID . '
                                WHERE p.poster= ' . $userID . ' AND p.visibility=1 ' . ' AND p.pageID=' . $pageID;
        }
        $query .= ' AND p.type="image" ';
        
        //If Post ID is set, get only one post
        if( $postID != null )
            $query .= ' AND p.postID=' . $postID;
        
        //AlbumID Query
        if($albumID != null)
        {
            $aPhotos = BuckysAlbum::getPhotos($albumID);
            
            $apIds = array(0);
            foreach($aPhotos as $a)
                $apIds[] = $a['postID'];
            $query .= ' AND p.postID in (' . implode(', ', $apIds) . ')';
        }
        
        if( $lastDate != null ){
            $lastDate = date('Y-m-d H:i:s', strtotime($lastDate));
            $query .= ' AND p.post_date < "' . $lastDate . '"';
        }
        
        $query .= ' ORDER BY p.post_date DESC ' . $limit_query;
        
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
    /**
    * Get Number of photos
    * 
    * 
    * @param integer $profileID
    * @param integer $pageID
    * @param integer $albumID
    * @return one
    */
    public function getNumberOfPhotosByUserID($profileID, $pageID=BuckysPost::INDEPENDENT_POST_PAGE_ID, $albumID = null)
    {
        global $db;
        
        $userID = buckys_is_logged_in();        
        
        if( buckys_not_null($userID) && ($userID == $profileID || BuckysFriend::isFriend($profileID, $userID)) )
        {
            $query = $db->prepare("SELECT count(DISTINCT(p.postID)) FROM " . TABLE_POSTS . " as p LEFT JOIN " . TABLE_ALBUMS_PHOTOS . " as pa ON pa.post_id = p.postID WHERE p.type='image' AND p.poster=%d AND pageID=%d", $profileID, $pageID);
        }else{
            $query = $db->prepare("SELECT count(DISTINCT(p.postID)) FROM " . TABLE_POSTS . " as p LEFT JOIN " . TABLE_ALBUMS_PHOTOS . " as pa ON pa.post_id = p.postID WHERE p.type='image' AND p.poster=%d AND p.visibility=1 AND pageID=%d", $profileID, $pageID);
        }
        
        if( buckys_not_null($albumID) )
            $query .= $db->prepare(" AND pa.album_id=%d", $albumID);
        
        $count = $db->getVar($query);
        
        return $count;
    }
    
    /**
    * Check that the postID and PosterID are correct
    * 
    * 
    * @param Int $postID
    * @param Int $posterID
    */
    public function checkPostID($postID, $posterID = null)
    {
        global $db;
        
        if( $posterID == null )
            $query = $db->prepare("SELECT postID FROM " . TABLE_POSTS . " WHERE postID=%s", $postID);
        else
            $query = $db->prepare("SELECT postID FROM " . TABLE_POSTS . " WHERE postID=%s AND poster=%s", $postID, $posterID);
            
        $rs = $db->getVar($query);
        
        return $rs ? true : false;
    }
    
    /**
    * Save Post
    * @param array $data
    */
    public function savePost($userID, $data)
    {
        global $db;
          
        $now = date('Y-m-d H:i:s');
        
        $type = isset($data['type']) ? $data['type'] : 'text';
        
        if( !in_array($type, array('text', 'image', 'video')) )
        {
            $type = 'text';
        }
        
        $data['pageID'] = isset($data['pageID']) && is_numeric($data['pageID'])?$data['pageID']:BuckysPost::INDEPENDENT_POST_PAGE_ID;
        
        /*$pattern = "/\[youtube.*\](.*)\[\/youtube\]/i";
        if(preg_match_all($pattern, $data['content'], $matches))
            $type = 'video';*/
        if( $type == 'text'){
            if( trim($data['content']) == '' )
            {
                buckys_add_message(MSG_CONTENT_IS_EMPTY, MSG_TYPE_ERROR);
                return false;
            }
            
            //Strip tags, and change url to clickable
            $data['content'] = strip_tags($data['content']);
            
            $newId = $db->insertFromArray(TABLE_POSTS, array('poster' => $userID, 'pageID'=>$data['pageID'], 'content'=>$data['content'], 'type' => $type, 'youtube_url'=>'', 'post_date' => $now, 'visibility' => $data['post_visibility']));
            
            if( !$newId )
            {
                buckys_add_message($db->getLastError(), MSG_TYPE_ERROR);
                return false;
            }
        }else if( $type == 'video' ){
            //Check Youtube URL is Valid or Not
            if( !buckys_validate_youtube_url($data['youtube_url']) )
            {
                buckys_add_message(MSG_INVALID_YOUTUBE_URL, MSG_TYPE_ERROR);
                return false;
            }
            $newId = $db->insertFromArray(TABLE_POSTS, array('poster' => $userID, 'pageID'=>$data['pageID'],  'content'=>$data['content'], 'type' => $type, 'youtube_url' => $data['youtube_url'], 'post_date' => $now, 'visibility' => $data['post_visibility']));
            if( !$newId )
            {
                buckys_add_message($db->getLastError(), MSG_TYPE_ERROR);
                return false;
            }
        }else if( $type == 'image' ){       
            $newId = BuckysPost::savePhoto($userID, $data);
        }
        
        if( !$newId )
        {
            return false;
        }
        switch($type)
        {
            case 'image':
//                buckys_add_message(MSG_PHOTO_UPLOADED_SUCCESSFULLY, MSG_TYPE_SUCCESS);        
                break;
            case 'video':
                buckys_add_message(MSG_NEW_VIDEO_CREATED, MSG_TYPE_SUCCESS);        
                break;
            case 'text':
            default:
                buckys_add_message(MSG_NEW_POST_CREATED, MSG_TYPE_SUCCESS);        
                break;
        }
        
        
        return $newId;
    }
    
    /**
    * Remove Post and Comment
    * 
    * 
    * @param mixed $userID
    * @param mixed $postID
    */
    public function deletePost($userID, $postID)
    {
        global $db;
        
        $query = $db->prepare("SELECT postID, type, image FROM " . TABLE_POSTS . " WHERE postID=%s AND poster=%s", $postID, $userID);
        $row = $db->getRow($query);
        
        if( $row )
        {
            $db->query( 'DELETE FROM ' . TABLE_POSTS . " WHERE postID=" . $row['postID'] );
            $db->query( 'DELETE FROM ' . TABLE_COMMENTS . " WHERE postID=" . $row['postID'] );
            $db->query( 'DELETE FROM ' . TABLE_ALBUMS_PHOTOS . " WHERE post_id=" . $row['postID'] );
            $db->query( 'DELETE FROM ' . TABLE_ACTIVITES . " WHERE objectID=" . $row['postID'] );
            $db->query( 'DELETE FROM ' . TABLE_REPORTS . " WHERE objectType='post' AND objectID=" . $row['postID'] );
            $db->query( 'DELETE FROM ' . TABLE_POSTS_LIKES . " WHERE postID=" . $row['postID'] );
            $db->query( 'DELETE FROM ' . TABLE_POSTS_HITS . " WHERE postID=" . $row['postID'] );
            //Remove Image
            if( $row['type'] == 'image' )
            {
                @unlink(DIR_FS_PHOTO . "users/" . $userID . "/resized/" . $row['image']);
                @unlink(DIR_FS_PHOTO . "users/" . $userID . "/original/" . $row['image']);
                @unlink(DIR_FS_PHOTO . "users/" . $userID . "/thumbnail/" . $row['image']);
                
                //Remove From Albums
                $db->query('DELETE FROM ' . TABLE_ALBUMS_PHOTOS . ' WHERE post_id=' . $row['postID']);
                
                $user = BuckysUser::getUserData($userID);
                if($user['thumbnail'] == $row['image']) //If current image is a profile image, remove it from the profile image
                {
                    BuckysUser::updateUserFields($userID, array('thumbnail' => ''));
                }
                
            }
            return true;
        }else{
            return false;
        }
    }
    
    /**
    * Save Post
    * 
    *     
    * @param mixed $data
    */
    public function savePhoto($userID, $data)
    {
        global $db, $BUCKYS_GLOBALS;
                
        //Check the Photo File Name
        if( !isset($data['file']) || strpos($data['file'], "../") !== false ||  !file_exists(DIR_FS_PHOTO_TMP . $data['file']) )
        {
            buckys_add_message(MSG_FILE_UPLOAD_ERROR, MSG_TYPE_ERROR);
            return false;
        }
        
        $data['pageID'] = isset($data['pageID']) && is_numeric($data['pageID'])?$data['pageID']:BuckysPost::INDEPENDENT_POST_PAGE_ID;
        
        // Validate the file type
        $fileParts = pathinfo($data['file']);
        if( !in_array(strtolower($fileParts['extension']), $BUCKYS_GLOBALS['imageTypes']) ) {
            buckys_add_message(MSG_INVALID_PHOTO_TYPE, MSG_TYPE_ERROR);
            return false;
        }        
        
        //Validate File Size
        list($width, $height, $type, $attr) = getimagesize(DIR_FS_PHOTO_TMP . $data['file']);
        if($width > MAX_IMAGE_WIDTH || $height > MAX_IMAGE_HEIGHT)
        {
            buckys_add_message(MSG_PHOTO_MAX_SIZE_ERROR, MSG_TYPE_ERROR);
            return false;
        }
        
        
        //Checking File Size and move it from the tmp folder to the user photo folder and resize it.
        if($data['post_visibility'] == 2)
        {
            //Calc Ratio using real image width
            $ratio = floatval($width / $data['width']);            
            $sourceWidth = ($data['x2'] - $data['x1']) * $ratio;            
            
            BuckysPost::moveFileFromTmpToUserFolder($userID, $data['file'], PROFILE_IMAGE_WIDTH, PROFILE_IMAGE_HEIGHT,$data['x1'] * $ratio, $data['y1'] * $ratio, $sourceWidth, $sourceWidth);
            if ($data['pageID'] == BuckysPost::INDEPENDENT_POST_PAGE_ID) {
                //Update User Profile Field
                BuckysUser::updateUserFields($userID, array('thumbnail' => $data['file']));
                $is_profile = 1;
            }
            else {
                //Update Page Profile field
                $pageIns = new BuckysPage();
                $pageIns->updateData($data['pageID'], array('logo' => $data['file']));
                $is_profile = 1;
            }
            
        }else{
            if($width > MAX_POST_IMAGE_WIDTH){
                $height = $height * (MAX_POST_IMAGE_WIDTH / $width);
                $width = MAX_POST_IMAGE_WIDTH;
            }
            if($height > MAX_POST_IMAGE_HEIGHT){
                $width = $width * (MAX_POST_IMAGE_HEIGHT / $height);
                $height = MAX_POST_IMAGE_HEIGHT;
            }
            
            //Create normal image
            BuckysPost::moveFileFromTmpToUserFolder($userID, $data['file'], $width, $height, 0, 0);
            $is_profile = 0;
        }
        
        
        $now = date('Y-m-d H:i:s');
        
        $newId = $db->insertFromArray(TABLE_POSTS, array('poster' => $userID, 'pageID'=>$data['pageID'], 'content'=>$data['content'], 'type' => 'image', 'post_date' => $now, 'image'=>$data['file'], 'visibility' => $data['post_visibility'] > 0 ? 1 : 0, 'is_profile' => $is_profile));
        if( !$newId )
        {
            buckys_add_message($db->getLastError(), MSG_TYPE_ERROR);
            return false;
        }
        
        //Assign Photo to Album
        if( isset($data['album']) && $data['album'] != '' )
        {
            if( !BuckysAlbum::checkAlbumOwner($data['album'], $userID) )
            {
                buckys_add_message(MSG_INVALID_ALBUM_ID, MSG_TYPE_ERROR);
            }else{
                BuckysAlbum::addPhotoToAlbum( $data['album'], $newId );
            }
            
        }
        buckys_add_message(MSG_PHOTO_UPLOADED_SUCCESSFULLY);
        
        return $newId;
    }
    
    /**
    * Create Profile image using already uploaded images
    * 
    * 
    * @param Array $photo
    * @param Array $data
    */
    public function createProfileImage($photo, $data)
    {
        global $db;
        
        $orgFile = DIR_FS_PHOTO . "users/" . $photo['poster'] . "/original/" . $photo['image'];
        $targetFile = DIR_FS_PHOTO . "users/" . $photo['poster'] . "/resized/" . $photo['image'];
        
        list($width, $height, $type, $attr) = getimagesize($orgFile);
    
        //Calc Ratio using real image width
        $ratio = floatval($width / 576);            
        $sourceWidth = ($data['x2'] - $data['x1']) * $ratio;            
        
        BuckysPost::resizeImage($photo['poster'], $photo['image'],PROFILE_IMAGE_WIDTH, PROFILE_IMAGE_HEIGHT,$data['x1'] * $ratio, $data['y1'] * $ratio, $sourceWidth, $sourceWidth);
        
        $db->updateFromArray(TABLE_POSTS, array('is_profile' => 1), array('postID' => $photo['postID']));
        
    }
    
    /**
    * 
    * 
    * Move uploaded file to the user folder from the tmp folder
    * 
    */
    public function moveFileFromTmpToUserFolder($userID, $file, $targetWidth, $targetHeight, $sourceX = null, $sourceY = null, $sourceWidth = null, $sourceHeight = null)
    {
        global $db;
        
        $dir = DIR_FS_PHOTO . "users";        
        //Create users Dir
        if( !is_dir($dir) ){ 
            mkdir($dir, 0777);
            $fp = fopen($dir . "/index.html", "w"); fclose($fp);
        }
        
        $dir = $dir . "/" . $userID;
        //Create User ID Dir
        if( !is_dir($dir) ){            
            mkdir($dir, 0777);
            $fp = fopen($dir . "/index.html", "w"); fclose($fp);
        }
        
        $dir_org = $dir . "/original";
        //Create Original Dir
        if( !is_dir($dir_org) ) {            
            mkdir($dir_org, 0777);
            $fp = fopen($dir_org . "/index.html", "w"); fclose($fp);
        }
        
        $dir_resized = $dir . "/resized";
        //Create Resized Dir
        if( !is_dir($dir_resized) ){ 
            mkdir($dir_resized, 0777);
            $fp = fopen($dir_resized . "/index.html", "w"); fclose($fp);
        }
        
        //Create Thumbnail Dir
        $dir_thumbnail = $dir . "/thumbnail";        
        if( !is_dir($dir_thumbnail) ) {
            mkdir($dir_thumbnail, 0777);
            $fp = fopen($dir_thumbnail . "/index.html", "w"); fclose($fp);
        }
        
        //Move File to the original folder
        $fp1 = fopen(DIR_FS_PHOTO_TMP . $file, "r");
        $fp2 = fopen($dir_org . "/" . $file, "w");
        $buff = fread($fp1, filesize(DIR_FS_PHOTO_TMP . $file));
        fwrite($fp2, $buff);
        fclose($fp1);
        fclose($fp2);
        //Remove Tmp File
        @unlink(DIR_FS_PHOTO_TMP . $file);
        
        //Resize The Image
        BuckysPost::resizeImage($userID, $file, $targetWidth, $targetHeight, $sourceX, $sourceY, $sourceWidth, $sourceHeight);
        
    }
    
    /**
    * Resize Image and create the file on the resized folder
    * 
    * 
    * @param Int $userID
    * @param String $file
    * @param Int $width
    * @param Int $height
    * 
    * @return Resized File name
    */
    public function resizeImage($userID, $file, $destWidth, $destHeight, $sourceX = null, $sourceY = null, $sourceWidth = null, $sourceHeight = null)
    {
        // Get the image size for the current original photo
        list( $currentWidth , $currentHeight, $destType )    = getimagesize( DIR_FS_PHOTO . "users/" . $userID . "/original/" . $file );
        $destType = image_type_to_mime_type($destType);
        
        // Find the correct x/y offset and source width/height. Crop the image squarely, at the center.
        
        if(!$sourceWidth)
            $sourceWidth = $currentWidth;
        if(!$sourceHeight)
            $sourceHeight = $currentHeight;
        
        
        //Create Thumbnail;
        BuckysPost::createThumbnail($userID, $file);
        
        $destPath = DIR_FS_PHOTO . "users/" . $userID . "/resized/" . $file;                
        return buckys_resize_image( DIR_FS_PHOTO . "users/" . $userID . "/original/" . $file , $destPath , $destType , $destWidth , $destHeight , $sourceX , $sourceY , $sourceWidth , $sourceHeight);
    }
    
    /**
    * create thumbnail
    * 
    * 
    * @param mixed $userID
    * @param mixed $file
    */
    public function createThumbnail($userID, $file)
    {
        list( $currentWidth , $currentHeight, $destType )    = getimagesize( DIR_FS_PHOTO . "users/" . $userID . "/original/" . $file );
        $destType = image_type_to_mime_type($destType);
        
        if( $currentWidth == $currentHeight )
        {
            $sourceX = 0;
            $sourceY = 0;
        }
        else if( $currentWidth > $currentHeight )
        {
            $sourceX            = intval( ( $currentWidth - $currentHeight ) / 2 );
            $sourceY             = 0;
            $currentWidth        = $currentHeight;
        }
        else
        {
            $sourceX        = 0;
            $sourceY        = intval( ( $currentHeight - $currentWidth ) / 2 );
            $currentHeight    = $currentWidth;
        }
        
        
        $destPath = DIR_FS_PHOTO . "users/" . $userID . "/thumbnail/" . $file;
        buckys_resize_image( DIR_FS_PHOTO . "users/" . $userID . "/original/" . $file , $destPath , $destType , IMAGE_THUMBNAIL_WIDTH , IMAGE_THUMBNAIL_HEIGHT , $sourceX , $sourceY , $currentWidth , $currentHeight);
    }
    
    /**
    * Get Post By Id
    * 
    * 
    * @param mixed $photoID
    * @return array
    */
    public function getPostById($id, $pageID=null)
    {
        global $db;
        
        if ($pageID) {
            $query = $db->prepare("SELECT * FROM " . TABLE_POSTS . " WHERE postID=%d AND pageID=%d", $id, $pageID);
        }
        else {
            $query = $db->prepare("SELECT * FROM " . TABLE_POSTS . " WHERE postID=%d", $id);
        }
        
        $row = $db->getRow($query);
        
        return $row;
    }
    
    /**
    * Update Photo Data
    * 
    * 
    * @param Int $userID
    * @param Array $data
    */
    public function updatePhoto($userID, $data)
    {
        global $db;
        
        $photo = BuckysPost::getPostById($data['photoID']);
        
        //Update Content And visibility
        $db->updateFromArray(TABLE_POSTS, array(
            'content' => $data['content'],
            'visibility' => !$data['photo_visibility'] ? 0 : 1            
        ), array('postID' => $data['photoID']));
        
        return true;
    }
    
    /**
    * Like Post
    * 
    * 
    * @param int $userID
    * @param int $postID
    */
    public function likePost($userID, $postID, $action)
    {
        global $db;
        
        $post = BuckysPost::getPostById($postID);
        if( !$post || $post['poster'] ==  $userID )
        {
            buckys_add_message(MSG_INVALID_REQUEST, MSG_TYPE_ERROR);
            return false;
        }
        
        if( $post['visibility'] == 0 && !BuckysFriend::isFriend($userID, $post['poster']) )
        {
            buckys_add_message(MSG_INVALID_REQUEST, MSG_TYPE_ERROR);
            return false;
        }
        
        //Check already like it or not
        $query = $db->prepare("SELECT likeID FROM " . TABLE_POSTS_LIKES . " WHERE userID=%s AND postID=%s", $userID, $postID);
        $likeId = $db->getVar($query);
        
        if( $action == 'likePost' )
        {
            if( $likeId )
            {
                buckys_add_message(MSG_ALREADY_LIKED_POST, MSG_TYPE_ERROR);
                return false;
            }
            
            //Like This post
            $rs = $db->insertFromArray(TABLE_POSTS_LIKES, array('userID' => $userID, 'postID' => $postID));
            
            //Update likes on the posts table
            $query = $db->prepare('UPDATE ' . TABLE_POSTS . ' SET `likes`=`likes` + 1 WHERE postID=%d', $postID);
            $db->query($query);
            
            //Add Activity
            BuckysActivity::addActivity($userID, $postID, 'post', 'like', $rs);
            
            //Increase Hits
            BuckysHit::addHit($postID, $userID);
            
            return $rs;
        }else if( $action == 'unlikePost' ){
            if( !$likeId )
            {
                buckys_add_message(MSG_INVALID_REQUEST, MSG_TYPE_ERROR);
                return false;
            }
            
            $query = $db->prepare("DELETE FROM " . TABLE_POSTS_LIKES . " WHERE userID=%s AND postID=%s", $userID, $postID);
            $db->query($query);
            
            //Update likes on the posts table
            $query = $db->prepare('UPDATE ' . TABLE_POSTS . ' SET `likes`=`likes` - 1 WHERE postID=%d', $postID);
            $db->query($query);
            
            //Increase Hits
            BuckysHit::removeHit($postID, $userID);
            
            return true;
        }
    }
    
    /**
    * Post Likes count
    * 
    * 
    * @param integer $postID
    * @return one
    */
    public function getPostLikesCount($postID)
    {
        global $db;
        
        $query = $db->prepare("SELECT likes FROM " . TABLE_POSTS . " WHERE postID=%d", $postID);
        $count = $db->getVar($query);
        
        return $count;
    }
    
    /**
    * Get Top Posts or Images
    * 
    * 
    * @param String $period
    * @param String $type
    */
    public function getTopPosts($pageID=BuckysPost::INDEPENDENT_POST_PAGE_ID, $period = 'today', $type = 'text', $page = 1, $limit = null)
    {
        global $db;
        
        if( $limit == null )
            $limit = BuckysPost::${COUNT_PER_PAGE . strtoupper("_$type")};
        
        $limit = ($page - 1) * $limit . ", " . $limit;
        
        switch($period)
        {
            case 'today':                
                $query = "
                    SELECT DISTINCT(p.postID), p.content, p.image, p.comments, p.post_date, p.is_profile, u.thumbnail, u.userID, CONCAT(u.firstName, ' ', u.lastName) as userName, p.likes, p.youtube_url FROM " . TABLE_POSTS . " as p
                    LEFT JOIN " . TABLE_USERS . " AS u ON u.userID = p.poster                    
                    WHERE p.post_status=1 AND p.type='" . $type . "' AND p.pageID=".$pageID." AND (p.visibility=1) AND p.post_date > '" . date("Y-m-d 00:00:00") . "'                    
                    ORDER BY p.likes DESC, p.post_date
                    LIMIT $limit
                ";
                break;
            case 'this-week':                
                $cw = date("w");                
                $sDate = date("Y-m-d 00:00:00", time() - $cw * 60 * 60 * 24);
                $query = "
                    SELECT p.postID, p.content, p.image, p.post_date, p.comments, p.is_profile, u.thumbnail, u.userID, CONCAT(u.firstName, ' ', u.lastName) as userName, p.likes, p.youtube_url FROM " . TABLE_POSTS . " as p
                    LEFT JOIN " . TABLE_USERS . " AS u ON u.userID = p.poster   
                    WHERE p.post_status=1 AND p.type='" . $type ."' AND p.pageID=".$pageID." AND (p.visibility=1) AND p.post_date > '" . $sDate . "'                    
                    GROUP BY p.postID
                    ORDER BY likes DESC, p.post_date
                    LIMIT $limit
                ";
                
                break;
            case 'this-month':                
                $sDate = date("Y-m-01 00:00:00");
                $query = "
                    SELECT p.postID, p.content, p.image, p.post_date, p.comments, p.is_profile, u.thumbnail, u.userID, CONCAT(u.firstName, ' ', u.lastName) as userName, p.likes, p.youtube_url FROM " . TABLE_POSTS . " as p
                    LEFT JOIN " . TABLE_USERS . " AS u ON u.userID = p.poster
                    WHERE p.post_status=1 AND p.type='" . $type . "' AND p.pageID=".$pageID." AND (p.visibility=1) AND p.post_date > '" . $sDate . "'                    
                    GROUP BY p.postID
                    ORDER BY likes DESC, p.post_date
                    LIMIT $limit
                ";
                break;
            case 'all':
                $query = "
                    SELECT p.postID, p.content, p.image, p.post_date, p.comments, p.is_profile, u.thumbnail, u.userID, CONCAT(u.firstName, ' ', u.lastName) as userName, p.likes, p.youtube_url FROM " . TABLE_POSTS . " as p
                    LEFT JOIN " . TABLE_USERS . " AS u ON u.userID = p.poster
                    WHERE p.post_status=1 AND p.type='" . $type . "' AND p.pageID=".$pageID." AND (p.visibility=1)
                    GROUP BY p.postID
                    ORDER BY likes DESC, p.post_date
                    LIMIT $limit
                ";
                break;
        }
        
        $rows = $db->getResultsArray($query);
        
        return $rows; 
    }
    
    /**
    * Get number of top posts, videos or images
    */
    public function getNumberOfPosts($pageID=BuckysPost::INDEPENDENT_POST_PAGE_ID, $period = 'all', $type = 'post')
    {
        global $db;
        
        $query = $db->prepare("SELECT count(DISTINCT(p.postID)) FROM " . TABLE_POSTS . " AS p WHERE p.post_status=1 AND p.visibility=1 AND p.type=%s AND pageID=%d", $type, $pageID);
        
        switch($period)
        {
            case 'today':   
                $query .= " AND p.post_date > '" . date('Y-m-d 00:00:00') . "' ";
                break;
            case 'this-week':   
                $cw = date("w");                
                $sDate = date("Y-m-d 00:00:00", time() - $cw * 60 * 60 * 24);
                $query .= " AND p.post_date > '" . $sDate . "' ";
                break;
            case 'this-month':   
                $sDate = date("Y-m-01 00:00:00");
                $query .= " AND p.post_date > '" . $sDate . "' ";
                break;
            
        }
        $count =  $db->getVar($query);
        
        return $count;
    }
    
    /**
    * Get Liked User Data
    * 
    * 
    * @param mixed $postID
    * @return Indexed
    */
    public function getLikedUsers($postID)
    {
        global $db;
        
        $query = $db->prepare("SELECT u.userID, u.firstName, u.thumbnail, u.lastName From " . TABLE_POSTS_LIKES . " as pl LEFT JOIN " . TABLE_USERS . " as u ON u.userID=pl.userID WHERE pl.postID=%s ORDER BY pl.liked_date desc limit 30", $postID);
        $likes = $db->getResultsArray($query);
        
        return $likes;
    }
    
    /**
    * Get posts By PageID
    * 
    * 
    * @param integer $pageID
    * @param integer $status
    */
    public function getPostsByPageID($pageID, $status=null) {
        
        global $db;
        
        if (!is_numeric($pageID))
            return null;
        
        $query = sprintf("SELECT * FROM %s WHERE pageID=%d", TABLE_POSTS, $pageID);
        
        if (is_numeric($status)) {
            $query .= ' post_status=' . $status;
        }
        
        return $db->getResultsArray($query);
        
    }
    
    /**
    * Getting Posts From stats_post table
    * 
    * @param String $type: text, image, video
    */
    public function getPostsFromStats($type)
    {
        global $db;
        
        $query = $db->prepare("SELECT DISTINCT(p.postID), p.content, p.image, p.comments, p.post_date, p.is_profile, u.thumbnail, u.userID, CONCAT(u.firstName, ' ', u.lastName) as userName, p.likes, p.youtube_url FROM " . TABLE_STATS_POST . " as s
                    LEFT JOIN " . TABLE_POSTS . " AS p ON p.postID = s.postID
                    LEFT JOIN " . TABLE_USERS . " AS u ON u.userID = p.poster
                    WHERE s.postType=%s ORDER BY s.sortOrder
                    ", $type);
        $rows = $db->getResultsArray($query);
        
        return $rows;
    }
    
}