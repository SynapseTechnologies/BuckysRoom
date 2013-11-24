<?php
if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
}  

if (isset($view['action_type']) && $view['action_type'] == 'create') {
    $view['page_title'] = 'Post a Reply';
    $view['action_url'] = '/forum/post_reply.php?id=' . $topic['topicID'];
    $view['action'] = 'post-reply';
}
else if (isset($view['action_type']) && $view['action_type'] == 'edit') {
    $view['page_title'] = 'Edit Post Reply';
    $view['action_url'] = '/forum/post_reply.php?id=' . $topic['topicID'];
    $view['action'] = 'edit-post-reply';
}
else {
    $view['action_type'] = null;
}

?>
<section id="main_section">
    <section id="main_content">
        <?php render_result_messages() ?>
        <h2 class="titles"><?php echo $view['page_title'];?></h2>
        <form name="postreplyform" id="postreplyform" action="<?php echo $view['action_url'];?>" method="post">
            <input type="hidden" name="action" value="<?php echo $view['action'];?>" />            
            <input type="hidden" name="topicID" value="<?php echo $topic['topicID'] ?>" />
            <?php if ($view['action_type'] == 'edit'):?>
                <input type="hidden" name="replyID" value="<?php echo $view['replyID']; ?>" />
            <?php endif;?>
            
            <table cellpadding="0" cellspacing="0" class="forumentry">
                <tr>
                    <td class="label">Topic: </td>
                    <td><?php echo $topic['topicTitle']?></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <textarea cols="20" id="reply-content" name="content" rows="12" class="textarea"><?php if (isset($view['replyData'])) echo BuckysForumTopic::_convertBBCodeToHTML($view['replyData']['replyContent']);?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <input type="submit" value="Post" class="redButton" />
                    </td>
                </tr>                
            </table>            
        </form>
    </section>
</section>
<script type="text/javascript">
    jQuery(document).ready(function(){
        var postEditor = jQuery('#reply-content').cleditor({
            width: '100%', 
            height: '400px',
            docCSSFile: '/css/main.css;/css/editor/jquery.cleditor.css;/css/forum.css',
            controls: "bold italic underline | link bullets numbering image code"
        })[0];
        
        jQuery('#postreplyform').submit(function(){
            var isValid = true;
            
            text = jQuery(postEditor.$frame[0].contentDocument).find('body').text();
                        
            if(jQuery.trim(text) == '' && jQuery(topicEditor.$frame[0].contentDocument).find('body').find('img, a').size() < 1)
            {
                showMessage(jQuery(this), 'Please write something hoss.', true)                
                postEditor.$frame[0].contentWindow.focus();  
                isValid = false;
            }
            
            return isValid;
        })
    })
</script>