<?php
/**
* Including Common functions that will be used whole site
*/

/**
* Add Javascript to $BUCKYS_GLOBAL['javascripts']
* 
* @param String $script
* @param Boolean $is_absolute_path
* @param Int $position
*/
function buckys_enqueue_javascript($script, $is_absolute_path = false, $is_footer = true, $position = null)
{
    global $BUCKYS_GLOBALS;
    
    if( !isset($BUCKYS_GLOBALS['javascripts']) )
        $BUCKYS_GLOBALS['javascripts'] = array();
    
    if( !$is_absolute_path )
        $script = DIR_WS_JS . $script;
    
    //Check already added or not
    foreach($BUCKYS_GLOBALS['javascripts'] as $row)
    {
        if($row['src'] == $script)
            return;
    }
    
    if( $position === null || $position >= count($BUCKYS_GLOBALS['javascripts']) )
    {
        array_push($BUCKYS_GLOBALS['javascripts'], array('src' => $script, 'is_footer'=>$is_footer));
    }else{
        $js = array();
        for($i = 0; $i < count($BUCKYS_GLOBALS['javascripts']); $i++)
        {
            if($i == $position)
                $js[] = array('src' => $script, 'is_footer'=>$is_footer);
            $js[] = $BUCKYS_GLOBALS['javascripts'][$i];
        }
        $BUCKYS_GLOBALS['javascripts'] = $js;
    }
}
/**
* Add Stylesheet to $BUCKYS_GLOBAL['stylesheets']
* 
* @param String $stylesheet
* @param Boolean $is_absolute_path
* @param Int $position
*/
function buckys_enqueue_stylesheet($stylesheet, $is_absolute_path = false, $position = null)
{
    global $BUCKYS_GLOBALS;
    
    if( !isset($BUCKYS_GLOBALS['stylesheets']) )
        $BUCKYS_GLOBALS['stylesheets'] = array();
    
    if( !$is_absolute_path )
        $stylesheet = DIR_WS_CSS . $stylesheet;
    
    if( $position === null || $position >= count($BUCKYS_GLOBALS['stylesheets']) )
    {
        array_push($BUCKYS_GLOBALS['stylesheets'], $stylesheet);
    }else{
        $sh = array();
        for($i = 0; $i < count($BUCKYS_GLOBALS['stylesheets']); $i++)
        {
            if($i == $position)
                $sh[] = $stylesheet;
            $sh[] = $BUCKYS_GLOBALS['stylesheets'][$i];
        }
        $BUCKYS_GLOBALS['stylesheets'] = $sh;
    }
}

/**
* Render Scripts from $BUCKYS_GLOBALS['javascripts'] variable
* 
*/
function buckys_render_javascripts($is_footer = true)
{
    global $BUCKYS_GLOBALS;
    
    if(isset($BUCKYS_GLOBALS['javascripts']))
    {
        if(!is_array($BUCKYS_GLOBALS['javascripts']))
            $BUCKYS_GLOBALS['javascripts'] = array($BUCKYS_GLOBALS['javascripts']);
//        $BUCKYS_GLOBALS['javascripts'] = array_unique($BUCKYS_GLOBALS['javascripts']);
        
        foreach($BUCKYS_GLOBALS['javascripts'] as $row)
        { 
            if($row['is_footer'] != $is_footer)
                continue;
                
            echo "<script type='text/javascript' src='" . $row['src'] . "' ></script>" . PHP_EOL;
        }
    }
}



function buckys_render_stylesheet()
{ 
    global $BUCKYS_GLOBALS;
    
    if(isset($BUCKYS_GLOBALS['stylesheets']))
    {
        if(!is_array($BUCKYS_GLOBALS['stylesheets']))
            $BUCKYS_GLOBALS['stylesheets'] = array($BUCKYS_GLOBALS['stylesheets']);
        $BUCKYS_GLOBALS['stylesheets'] = array_unique($BUCKYS_GLOBALS['stylesheets']);
        foreach($BUCKYS_GLOBALS['stylesheets'] as $src)
        {
            echo "<link rel='stylesheet' type='text/css' href='" . $src . "' >" . PHP_EOL;
        }
    }
}



/**
* Check if current user is logged in
* @return loggedin = true, else false
*/
function buckys_is_logged_in()
{
    global $db;
    
    if(isset($_SESSION['userID']))
    {
        $userID = $_SESSION['userID'];
        //Check the UserId exits in the database
        $query = $db->prepare("SELECT userID, status FROM users WHERE userID=%s AND `status` != " . BuckysUser::STATUS_USER_DELETED, $userID);
        $urow = $db->getRow($query);
        
        if(!$urow) //If userid doesn't exist in the database, remove it from the session
        {
            $_SESSION['userID'] = null;
            unset($_SESSION['userID']);
            return false;
        }else if($urow['status'] != 1){
            $_SESSION['userID'] = null;
            unset($_SESSION['userID']);
            buckys_add_message(MSG_ACCOUNT_BANNED, MSG_TYPE_ERROR);
            return false;
        }
        return $urow['userID'];
    }else{
        return buckys_check_cookie_for_login();
        
    }
}

/**
* Check Cookie values for keep me signed in
* 
*/
function buckys_check_cookie_for_login()
{
    global $db;
    
    if(isset($_COOKIE['bkuid0']) && isset($_COOKIE['bkuid1']) && isset($_COOKIE['bkuid2']))
    {
        $cuId =  base64_decode($_COOKIE['bkuid0']);
        $cuId1 =  base64_decode($_COOKIE['bkuid1']);
        $salt =  base64_decode($_COOKIE['bkuid2']);
        $encrypted = md5($salt . $cuId . $salt);
        if($cuId1 == $encrypted)
        {
            $query = $db->prepare("SELECT userID FROM users WHERE userID=%s AND status=1", $cuId);
            $userID = $db->getVar($query);
            
            if($userID)
            {
                $_SESSION['userID'] = $userID;
                //Init Some Session Values
                $_SESSION['converation_list'] = array();    
                return $userID;
            }            
        }
        
        //Remove Cookies
        setcookie('bkuid0', null, time() - 1000, "/", "buckysroom.com");
        setcookie('bkuid1', null, time() - 1000, "/", "buckysroom.com");
        setcookie('bkuid2', null, time() - 1000, "/", "buckysroom.com");
        
    }
    
    return false;
}

/**
* Check that the current user acl
* 
* @param Int $acl
*/
function buckys_check_user_acl($acl)
{
    global $BUCKYS_GLOBALS;
    
    if($BUCKYS_GLOBALS['user']['aclLevel'] >= $acl)
        return true;
    else
        return false;
}

/**
* Check that the current user is forum moderator
* 
*/
function buckys_is_forum_moderator()
{
    global $BUCKYS_GLOBALS;
    
    if(!buckys_check_user_acl(USER_ACL_MODERATOR))
        return false;
    
    //If Administrator, return true    
    if(buckys_check_user_acl(USER_ACL_ADMINISTRATOR))
        return true;
        
    if(!BuckysModerator::isModerator($BUCKYS_GLOBALS['user']['userID'], MODERATOR_FOR_FORUM))
        return false;
        
    return true;
}
/**
* Check that the current user is Community moderator
* 
*/
function buckys_is_community_moderator()
{
    global $BUCKYS_GLOBALS;
    
    if(!buckys_check_user_acl(USER_ACL_MODERATOR))
        return false;
        
    if(!BuckysModerator::isModerator($BUCKYS_GLOBALS['user']['userID'], MODERATOR_FOR_COMMUNITY))
        return false;
        
    return true;
}
/**
* Check that the current user is trade moderator
* 
*/
function buckys_is_trade_moderator()
{
    global $BUCKYS_GLOBALS;
    
    if(!buckys_check_user_acl(USER_ACL_MODERATOR))
        return false;
        
    if(!BuckysModerator::isModerator($BUCKYS_GLOBALS['user']['userID'], MODERATOR_FOR_TRADE))
        return false;
        
    return true;
}


/**
* Redirect to the url
* If $msg is not null, set the message to the session
* 
* @param String $url
* @param String $msg
* @param int $msg_type: MSG_TYPE_SUCCESS(1)=success, MSG_TYPE_ERROR(0)=error, MSG_TYPE_NOTIFY(2)=notification
*/
function buckys_redirect($url, $msg = null, $msg_type = MSG_TYPE_SUCCESS)
{
    if($msg)        
    {
        buckys_add_message($msg, $msg_type);
    }
    header("Location: " . $url);
    exit;
}

/**
* Encrypt Plain
* 
* @param string $plain
*/
function buckys_encrypt_password($plain)
{
    //encryption algorithm goes here
}

/**
* Validate a plain password with an encrypted password
* 
* @param mixed $plain
* @param mixed $encrypted
*/
function buckys_validate_password($plain, $encrypted)
{
    if( buckys_not_null($plain) && buckys_not_null($encrypted) )   
    {
        $stack = explode( ':', $encrypted );
        if( sizeof($stack) != 2 ) 
            return false;
        if( md5($stack[1] . $plain) == $stack[0] )
        {
            return true;
        }
    }
    return false;
}

/**
* check the value is null or not
* 
* @param mixed $value
*/
function buckys_not_null($value)
{
    if( is_array($value) )
    {
        if( sizeof($value) > 0 )
        {
            return true;
        }else{
            return false;
        }
    }else{
        if( (is_string($value) || is_int($value)) && ($value != '') && (strlen(trim($value)) > 0) )
        {
            return true;
        }else{
            return false;
        }
    }
}

/**
* Get User Full Info By Email
* 
* @param mixed $email
*/
function buckys_get_user_by_email($email)
{
    global $db;
    
    $query = $db->prepare('SELECT * FROM users WHERE email=%s AND `status` != ' . BuckysUser::STATUS_USER_DELETED, $email);
    $row = $db->getRow($query);

    return $row;
}

/**
* Save the message to the session
* 
* @param String $msg
* @param int $msg_type: MSG_TYPE_SUCCESS(1)=success, MSG_TYPE_ERROR(0)=error, MSG_TYPE_NOTIFY(2)=notification
*/
function buckys_add_message($msg, $msg_type = MSG_TYPE_SUCCESS)
{
    if( !isset($_SESSION['message']) )
    {
        $_SESSION['message'] = array();
    }
    $_SESSION['message'][] = array( 'type'=>$msg_type, 'message'=>$msg );
}

//Getting Result Messages 
function buckys_get_messages()
{
    ob_start();
    render_result_messages();
    $msg = ob_get_contents();
    ob_end_clean();
    return $msg;
}

//Create Image Object
function buckys_image_open($file , $type)
{
    // @rule: Test for JPG image extensions
    if( function_exists( 'imagecreatefromjpeg' ) && ( ( $type == 'image/jpg') || ( $type == 'image/jpeg' ) || ( $type == 'image/pjpeg' ) ) )
    {
        $im    = @imagecreatefromjpeg( $file );

        if( $im !== false ) { return $im; }
    }
    
    // @rule: Test for png image extensions
    if( function_exists( 'imagecreatefrompng' ) && ( ( $type == 'image/png') || ( $type == 'image/x-png' ) ) )
    {
        $im    = @imagecreatefrompng( $file );

        if( $im !== false ) { return $im; }
    }

    // @rule: Test for png image extensions
    if( function_exists( 'imagecreatefromgif' ) && ( ( $type == 'image/gif') ) )
    {
        $im    = @imagecreatefromgif( $file );

        if( $im !== false ) { return $im; }
    }
    
    if( function_exists( 'imagecreatefromgd' ) )
    {
        # GD File:
        $im = @imagecreatefromgd($file);
        if ($im !== false) { return true; }
    }

    if( function_exists( 'imagecreatefromgd2' ) )
    {
        # GD2 File:
        $im = @imagecreatefromgd2($file);
        if ($im !== false) { return true; }
    }

    if( function_exists( 'imagecreatefromwbmp' ) )
    {
        # WBMP:
        $im = @imagecreatefromwbmp($file);
        if ($im !== false) { return true; }
    }

    if( function_exists( 'imagecreatefromxbm' ) )
    {
        # XBM:
        $im = @imagecreatefromxbm($file);
        if ($im !== false) { return true; }
    }

    if( function_exists( 'imagecreatefromxpm' ) )
    {
        # XPM:
        $im = @imagecreatefromxpm($file);
        if ($im !== false) { return true; }
    }
    
    // If all failed, this photo is invalid
    return false;
}


//Resize Image
function buckys_resize_image($srcPath, $destPath, $destType, $destWidth, $destHeight, $sourceX = 0, $sourceY = 0, $currentWidth=0, $currentHeight=0)
{
    $imgQuality    = 320;
    $pngQuality = ($imgQuality - 100) / 11.111111;
    $pngQuality = round(abs($pngQuality));
    
    // See if we can grab image transparency
    $image                = buckys_image_open( $srcPath , $destType );
    $transparentIndex    = imagecolortransparent( $image );

    // Create new image resource
    $image_p            = ImageCreateTrueColor( $destWidth , $destHeight );
    $background            = ImageColorAllocate( $image_p , 255, 255, 255 );
    
    // test if memory is enough
    if($image_p == FALSE)
    {
        echo 'Image resize fail. Please increase PHP memory';
        return false;
    } 
    
    // Set the new image background width and height
    $resourceWidth        = $destWidth;
    $resourceHeight        = $destHeight;
    
    if(empty($currentHeight) && empty($currentWidth))
    {
        list($currentWidth , $currentHeight) = getimagesize( $srcPath );
    }
    // If image is smaller, just copy to the center
    $targetX = 0;
    $targetY = 0;

    // If the height and width is smaller, copy it to the center.
    if( $destType != 'image/jpg' &&    $destType != 'image/jpeg' && $destType != 'image/pjpeg' )
    {
        if( ($currentHeight < $destHeight) && ($currentWidth < $destWidth) )
        {
            $targetX = intval( ($destWidth - $currentWidth) / 2);
            $targetY = intval( ($destHeight - $currentHeight) / 2);
    
            // Since the 
             $destWidth = $currentWidth;
             $destHeight = $currentHeight;
        }
    }
    
    // Resize GIF/PNG to handle transparency
    if( $destType == 'image/gif' )
    {
        $colorTransparent = imagecolortransparent($image);
        imagepalettecopy($image, $image_p);
        imagefill($image_p, 0, 0, $colorTransparent);
        imagecolortransparent($image_p, $colorTransparent);
        imagetruecolortopalette($image_p, true, 256);
        imagecopyresized($image_p, $image, $targetX, $targetY, $sourceX, $sourceY, $destWidth , $destHeight , $currentWidth , $currentHeight );
    }
    else if( $destType == 'image/png' || $destType == 'image/x-png')
    {
        // Disable alpha blending to keep the alpha channel
        imagealphablending( $image_p , false);
        imagesavealpha($image_p,true);
        $transparent        = imagecolorallocatealpha($image_p, 255, 255, 255, 127);
        
        imagefilledrectangle($image_p, 0, 0, $resourceWidth, $resourceHeight, $transparent);
        imagecopyresampled($image_p , $image, $targetX, $targetY, $sourceX, $sourceY, $destWidth, $destHeight, $currentWidth, $currentHeight);
    }
    else
    {
        // Turn off alpha blending to keep the alpha channel
        imagealphablending( $image_p , false );
        imagecopyresampled( $image_p , $image, $targetX, $targetY, $sourceX, $sourceY, $destWidth , $destHeight , $currentWidth , $currentHeight );
    }

    // Output

    // Test if type is png
    if( $destType == 'image/png' || $destType == 'image/x-png' )
    {
        imagepng($image_p, $destPath);
    }
    elseif ( $destType == 'image/gif')
    {        
        imagegif( $image_p, $destPath );
    }
    else
    {
        // We default to use jpeg
        imagejpeg($image_p, $destPath, $imgQuality);
    }

}

function buckys_trunc_content($content, $length = null)
{
    //remove Youtube Url
    $pattern = "/\[youtube.*\](.*)\[\/youtube\]/i";
    $content = preg_replace($pattern, '$1', $content);
    if($length != null && strlen($content) > $length)
    {
        return substr($content, 0, $length) . "...";
    }else{
        return $content;
    }
}

function buckys_get_youtube_video_id($url)
{
    $url = str_replace('&amp;', '&', $url);
    if( strpos($url, 'http://www.youtube.com/embed/') !== false ) // If Embed URL
    {
        return str_replace('http://www.youtube.com/embed/', '', $url);
    }                                                      
    parse_str( parse_url( $url, PHP_URL_QUERY ), $array_of_vars );
    return $array_of_vars['v'];
}

function buckys_sendmail($to, $toName, $subject, $body)
{
    require_once(DIR_FS_INCLUDES . "phpMailer/class.phpmailer.php");
    
    $mail = new PHPMailer();
    $mail->AddAddress($to, $toName);
    $mail->SetFrom('support@buckysroom.com', 'Buckysroom');
    $mail->Subject = $subject;
    $mail->Body = $body;
    
    $mail->IsSMTP();
    $mail->SMTPAuth = true; 
    $mail->Host = 'yyyy';
    $mail->Username = 'xxxxx';
    $mail->Password = 'zzzzz';
    
    $mail->Send();
}

/**
* Include Panel
* 
* @param String $panel
*/
function buckys_get_panel($panel)
{
    global $BUCKYS_GLOBALS;
    
    if( file_exists(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/panel/" . $panel . ".php") )
    {
        require_once(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/panel/" . $panel . ".php");
    }
}

/**
* Validate the Youtube Video Id
* 
* @param mixed $youtubeID
*/
function buckys_validate_youtube_url($youtubeURL)
{   
    $youtubeID = trim(buckys_get_youtube_video_id($youtubeURL));
    
    if( !$youtubeID )
        return false;
        
    $url = 'http://gdata.youtube.com/feeds/api/videos/' . $youtubeID;
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    if( strtolower(trim($result)) == 'invalid id' )
        return false;
    else 
        return true;
}

/**
* Store Session before redirect
* 
*/
function buckys_exit()
{
    buckys_session_close();
    exit;
}

/**
* Encript ID for security
* 
*/
function buckys_encrypt_id($gID)
{
    if(!isset($_SESSION['user_encrypt_salt']))
    {
        $salt = '';
        for ($i = 0; $i < 20; $i++)
        {
            $salt .= mt_rand();
        }
        
        $salt = md5($salt);
                    
        $_SESSION['user_encrypt_salt'] = $salt;
    }else{            
        $salt = $_SESSION['user_encrypt_salt'];
    }
    
    $encrypted = md5($salt . $gID . $salt);
    
    return $encrypted;
}

/**
* Check ID Encrypted Value 
*/
function buckys_check_id_encrypted($gID, $encrypted)
{
    if( !isset($_SESSION['user_encrypt_salt']) )
    {
        return false;
        /*if( $userID != $encrypted )
            return false;
        else
            return true;*/
    }else{
        if(buckys_encrypt_id($gID) == $encrypted)
            return true;
        else
            return false;
    }
}


/**
* pagination
* 
* @param array $records
* @param int $currentPageNum
* @param string $baseUrl
* @param int $recordPerPage
*/
function buckys_trade_pagination($records, $baseUrl, $currentPageNum, $recordPerPage) {
    
    global $BUCKYS_GLOBALS;
    
    $newRecords = array();
    
    if (!isset($currentPageNum) || !is_numeric($currentPageNum) || $currentPageNum <= 0)
        $currentPageNum = 1;
    
    if (count($records) > 0) {
        
        $totalPages = intval(count($records) / $recordPerPage);
        
        if (count($records) % $recordPerPage > 0)
            $totalPages++;
        
        if ($currentPageNum > $totalPages) {
            $currentPageNum = $totalPages;
            
            if ($currentPageNum == 0)
                $currentPageNum = 1;
            
            //=================== Page should be redirected ===================//
            $newPageUrl = $_SERVER['REQUEST_URI'];  
                      
            $newPageUrlArr = parse_url($newPageUrl);    
            $newPageUrl = $newPageUrlArr['path'];
            $argStr = $newPageUrlArr['query'];
            
            parse_str($argStr, $outResult);            
            $newArgList = array();            
            if (count($outResult) > 0) {                
                foreach($outResult as $key=>$val) {
                    if ($key != 'page')
                        $newArgList[] = $key . '=' . $val;                    
                }                
                $newArgList[] = 'page=' . $currentPageNum;
            }
            
            $newPageUrl .= '?' . implode('&', $newArgList);
            
            buckys_redirect($newPageUrl); //redirect
            //-------------------------------------------------------------------//
            
        }
        
        
        $startIndex = ($currentPageNum - 1) * $recordPerPage;
        $endIndex = $currentPageNum * $recordPerPage;
        
        if (count($records) < $endIndex) {
            $endIndex = count($records);
        }
        
        
        /* new index for records */
        foreach($records as $recData) {
            $newRecords[] = $recData;
        }
        $records = $newRecords;
        $newRecords = array();
        
        
        
        for ($idx = $startIndex; $idx < $endIndex; $idx++) {
            $newRecords[] = $records[$idx];
        }
        
        
        
        $parsedUrl = parse_url($baseUrl);
        if ($parsedUrl['query'] == '') {
            $baseUrl = rtrim($baseUrl, '?') . '?';
        }
        else {
            $baseUrl = rtrim($baseUrl, '&') . '&';
        }
        
        $BUCKYS_GLOBALS['tradePagination'] = array(
                                                    'startIndex'    => $startIndex + 1,
                                                    'endIndex'      => $endIndex, 
                                                    'totalRecords'  => count($records),
                                                    'totalPages'    => $totalPages,
                                                    'currentPage'   => $currentPageNum,
                                                    'baseUrl'       => $baseUrl,
                                                    'currentRecords' => count($newRecords)
                                                );
        
        
    }
    
    
    return $newRecords;
    
}

/**
* Get trade item first image's thumb.
* 
* @param string $imageString : formated as follows /images/trade/ .... | ....| ....
*/
function buckys_trade_get_item_thumb($imageString) {
    
    $imageList = array();
    if ($imageString != '')
        $imageList = explode("|", $imageString);
        
        
    $thumbFileName = '';
    
    if (count($imageList) > 0) {
        
        $thumbPathInfo = pathinfo($imageList[0]);
        $thumbFileName = $thumbPathInfo['dirname'] . "/" . $thumbPathInfo['filename'] . TRADE_ITEM_IMAGE_THUMB_SUFFIX . "." . $thumbPathInfo['extension'];
    }
    else {
        $thumbFileName = '/images/trade/no-image-thumb.jpg';
    }
    
    return $thumbFileName;
    
}

/**
* Get time left in format of 5d 4h
* 
* @param mixed $createdTimeStr
*/
function buckys_trade_get_item_time_left($createdTimeStr) {
    
    $timeLeftStr = '';
    $timeLeft = TRADE_ITEM_LIFETIME * 3600 * 24 + strtotime($createdTimeStr) - time();
    
    
    
    if ($timeLeft > 0) {
        $dayLeft = intval($timeLeft / 3600 / 24);
        
        if ($dayLeft > 0) {
            $timeLeftStr = $dayLeft . "d ";
            $timeLeft -= $dayLeft * 3600 * 24;
            $hourLeft = intval($timeLeft / 3600);
            
            $timeLeftStr .= $hourLeft . "h";
        }
        else {
            
            $hourLeft = intval($timeLeft / 3600);
            
            $timeLeftStr = $hourLeft . "h ";
            
            $timeLeft -= $hourLeft * 3600;
            $minLeft = intval($timeLeft / 60);
            $timeLeftStr .= $minLeft . "m ";
        }
        
    }
    else {
        $timeLeftStr = '0';
    }
    
    return $timeLeftStr;
}
/**
* Get time ago
* @param string $createdTimeStr : time
*/
function buckys_trade_get_item_time_past($createdTimeStr) {
    
    $timePastStr = '';
                        
    $timePast = time() - strtotime($createdTimeStr);
    
    if ($timePast > 0) {
        $dayPast = intval($timePast / 3600 / 24);
        
        if ($dayPast > 0) {
            $timePastStr = $dayPast . "d ";
            $timePast -= $dayPast * 3600 * 24;
            $hourPast = intval($timePast / 3600);
            
            $timePastStr .= $hourPast . "h";
        }
        else {
            
            $hourPast = intval($timePast / 3600);
            $timePastStr = $hourPast . "h ";
            
            $timePast -= $hourPast * 3600;
            $minPast = intval($timePast / 60);
            $timePastStr .= $minPast . "m ";
        }
        
    }
    else {
        $timePastStr = '0';
    }
    
    return $timePastStr;
}

/**
* Get Trade Item Search URL
* 
* @param string $query
* @param string $catStr
* @param string $locationStr
* @param string $sort
* @param mixed $page
*/
function buckys_trade_search_url($query, $catStr, $locationStr, $sort, $userID, $page = null) {
    
    $query = trim($query);
    $catStr = trim($catStr);
    $locationStr = trim($locationStr);
    $sort = trim($sort);
    
    $tmpParamList = array();
    if ($query != '') {
        $tmpParamList[] = 'q=' . urlencode($query);
    }
    if ($catStr != '') {
        $tmpParamList[] = 'cat=' . urlencode($catStr);
    }
    if ($locationStr != '') {
        $tmpParamList[] = 'loc=' . urlencode($locationStr);
    }    
    if ($sort != '') {
        $tmpParamList[] = 'sort=' . urlencode($sort);
    }
    if ($userID != '') {
        $tmpParamList[] = 'user=' . urlencode($userID);
    }
    
    
    if (count($tmpParamList) > 0)
        $paginationUrlBase = '/trade/search.php?' . implode('&', $tmpParamList);
    else
        $paginationUrlBase = '/trade/search.php';
        
    return $paginationUrlBase;
    
}

/**
* Get Page & People Search URL
* 
* @param string $query
* @param string $type
*/
function buckys_pp_search_url($query, $type, $sort, $addLastSymFlag = true) {
    
    $query = trim($query);
    $type = trim($type);
    
    $tmpParamList = array();
    if ($query != '') {
        $tmpParamList[] = 'q=' . urlencode($query);
    }
    if ($type != '') {
        $tmpParamList[] = 'type=' . urlencode($type);
    }
    if ($sort != '') {
        $tmpParamList[] = 'sort=' . urlencode($sort);
    }
    
    if (count($tmpParamList) > 0) {
        $paginationUrlBase = '/search.php?' . implode('&', $tmpParamList) . ($addLastSymFlag ? '&': '');
    }
        
    else {
        $paginationUrlBase = '/search.php' . $addLastSymFlag? '?': '';
    }
        
        
    return $paginationUrlBase;
    
}


/**
* Get country by ID
* 
* @param integer $countryID
*/
function buckys_trade_get_country_name($countryID) {
    $countryIns = new BuckysCountry();
    $countryData = $countryIns->getCountryById($countryID);
    
    if ($countryData) {
        return $countryData['country_title'];
    }
    return;
}


function get_secure_string($str, $urlDecode=false) {
    
    if ($urlDecode) {
        return trim(urldecode(strip_tags($str)));
    }
    else {
        return trim(strip_tags($str));
    }
    
}

function get_secure_integer($str) {
    if (is_numeric($str))
        return intval($str);
    else
        return null;
}

function buckys_truncate_string($string, $length, $stripHTML = true)
{
    if($stripHTML == true)
        $string = strip_tags($string);
    
    $offset = 3;
    
    if(strlen($string) < $length - $offset)
        return $string;
        
    return substr($string, 0, $length - $offset) . '...';
}

/**
* It will find URL in string, and change them to be clickable (add them to <a> tag)
* 
* @param string $text
* @param string $targetWindow
*/
function buckys_make_links_clickable($text, $targetWindow='_blank') {
    return  preg_replace(
     array(
       '/(?(?=<a[^>]*>.+<\/a>)
             (?:<a[^>]*>.+<\/a>)
             |
             ([^="\']?)((?:https?|ftp|bf2|):\/\/[^<> \n\r]+)
         )/iex',
       '/<a([^>]*)target="?[^"\']+"?/i',
       '/<a([^>]+)>/i',
       '/(^|\s)(www.[^<> \n\r]+)/iex',
       '/(([_A-Za-z0-9-]+)(\\.[_A-Za-z0-9-]+)*@([A-Za-z0-9-]+)
       (\\.[A-Za-z0-9-]+)*)/iex'
       ),
     array(
       "stripslashes((strlen('\\2')>0?'\\1<a href=\"\\2\">\\2</a>\\3':'\\0'))",
       '<a\\1',
       '<a\\1 target="'.targetWindow.'">',
       "stripslashes((strlen('\\2')>0?'\\1<a href=\"http://\\2\">\\2</a>\\3':'\\0'))",
       "stripslashes((strlen('\\2')>0?'<a href=\"mailto:\\0\">\\0</a>':'\\0'))"
       ),
       $text
   );
}