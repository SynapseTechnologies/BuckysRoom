<?php
/**
* Upload Photo Using Jquery uploadify plugin
*/
require(dirname(dirname(__FILE__)) . '/includes/bootstrap.php');
    
if (!empty($_FILES)) {
    $tempFile = $_FILES['Filedata']['tmp_name'];
    $targetPath = DIR_FS_TRADE_IMG_TMP; // temp files
    if( !is_dir($targetPath) )
    {
        mkdir($targetPath, 0777);
    }
    
    // Validate the file type
    $fileParts = pathinfo($_FILES['Filedata']['name']);
    
    //Check the file extension
    if ( in_array(strtolower($fileParts['extension']), $BUCKYS_GLOBALS['imageTypes']) ) {
        
        //Check Image Size
        list($width, $height, $type, $attr) = getimagesize($tempFile);
        if($width > MAX_IMAGE_WIDTH || $height > MAX_IMAGE_HEIGHT)
        {
            echo json_encode(array('success'=>0, 'msg'=>MSG_PHOTO_MAX_SIZE_ERROR));    
        }else{
            $targetFileName = md5(uniqid()) . "." . $fileParts['extension'];
            $targetFile = $targetPath . $targetFileName;
        
            move_uploaded_file($tempFile,$targetFile);
            
            echo json_encode(array('success'=>1, 'file'=>$targetFileName));    
        }
        
    } else {
        echo json_encode(array('success'=>0, 'msg'=>MSG_INVALID_PHOTO_TYPE));    
    }
}