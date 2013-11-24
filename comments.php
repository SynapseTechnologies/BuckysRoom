<?php
/**
* Add/Delete Comments
*/

require(dirname(__FILE__) . '/includes/bootstrap.php');

$userID = buckys_is_logged_in();

if( isset($_POST['action']) )
{    
    
    //Save Comment
    if( $_POST['action'] == 'save-comment' )
    {
        if( !$userID )
        {
            echo MSG_INVALID_REQUEST;
            exit;
        }
        $postID = $_POST['postID'];
        $comment = $_POST['comment'];
        
        //If comment is empty, show error
        if( trim($comment) == '')
        {
            echo MSG_COMMENT_EMPTY;
            exit;
        }
        //if Post Id was not set, show error
        if( !$postID )
        {
            echo MSG_INVALID_REQUEST;
            exit;
        }
        
        //Check the post id is correct
        if( !BuckysPost::checkPostID($postID) )
        {
            echo MSG_POST_NOT_EXIST;
            exit;
        }
        
        $post = BuckysPost::getPostById($postID);
        if( $post['visibility'] == 0 && $userID != $post['poster'] && !BuckysFriend::isFriend($userID, $post['poster']) )
        {
            //Only Friends can leave comments to private post
            echo MSG_INVALID_REQUEST;
            exit;
        }
        
        
        //If error, show it
        if( !($commentID = BuckysComment::saveComments($userID, $postID, $comment)) )
        {
            echo $db->getLastError();
            exit;
        }else{
            //Show Results
            header('Content-type: application/xml');
            
            $newComment = BuckysComment::getComment($commentID);
            $newCount = BuckysComment::getPostCommentsCount($postID);
            
            
            render_result_xml(
                array(
                    'newcomment' => render_single_comment( $newComment, $userID, true ),
                    'count' => $newCount > 1 ? ($newCount . " comments") : ($newCount . " comment")
                )
            );
            exit;
        }
    }
    
    //Getting More Comments
    if( $_POST['action'] == 'get-comments' )    
    {
        $postID = $_POST['postID'];
        $lastDate = $_POST['last'];
        $comments = BuckysComment::getPostComments($postID, $lastDate);
        //Show Results
        header('Content-type: application/xml');
        $commentsHTML = '';
        foreach($comments as $comment)
        {
            $commentsHTML .= render_single_comment( $comment, $userID, true );
            $lastDate = $comment['posted_date'];
        }
        $result = array('comment' => $commentsHTML);
        
        render_result_xml(
                array(
                    'comment' => $commentsHTML,
                    'lastdate' => $lastDate,
                    'hasmore' => ($commentsHTML != '' && BuckysComment::hasMoreComments($postID, $lastDate)) ? 'yes' : 'no'
                )
            );
    }
}else if($_GET['action']){
    //Delete Post
    if( $_GET['action'] == 'delete-comment' ){
        if( !$userID )
        {
            echo MSG_INVALID_REQUEST;
            exit;
        }        
        $postID = $_GET['postID'];
        $commentID = $_GET['commentID'];
        $cUserID = $_GET['userID'];
        
        if( !BuckysComment::deleteComment($userID, $commentID) )
        {
            echo 'Invalid Request';
        }else{
            header('content-type: application/xml');
            $newCount = BuckysComment::getPostCommentsCount($postID);
            
            render_result_xml(array('commentcount' => $newCount > 1 ? ($newCount . " comments") : ($newCount . " comment")));
        }
        exit;
    }
}