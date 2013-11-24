<?php
if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
}  


$forumTopicData = null;

if ($view['action_type'] == 'create') {
    $view['action_url'] = '/forum/create_topic.php';
    $view['action_name'] = 'create-topic';
    $view['page_title'] = 'Create a New Topic';
}
else if ($view['action_type'] == 'edit') {
    $view['action_url'] = '/forum/edit_topic.php';
    $forumTopicData = $view['forum_data'];
    $view['page_title'] = 'Edit Topic';
    $view['action_name'] = 'edit-topic';
}




?>
<section id="main_section">
    <section id="main_content">
        <div class="breadcrumb"></div>
        <?php render_result_messages() ?>
        <h2 class="titles"><?php echo $view['page_title'];?></h2>
        <form name="newtopicform" id="newtopicform" action="<?php echo $view['action_url'];?>" method="post">
            <input type="hidden" name="action" value="<?php echo$view['action_name'];?>" />
            <?php if ($forumTopicData) :?>
                <input type="hidden" name="id" value="<?php echo $forumTopicData['topicID']?>" />
            <?php endif;?>
            
            <table cellpadding="0" cellspacing="0" class="forumentry">
                <tr>
                    <td class="label">Title: </td>
                    <td><input type="text" id="title" name="title" maxlength="500" value="<?php if ($forumTopicData) echo $forumTopicData['topicTitle'];?>" autocomplete="off" class="input" /> </td>
                </tr>
                <tr>
                    <td class="label">Category: </td>
                    <td>
                        <select style="padding:2px;" name="category" id="category" class="select" autocomplete="off">
                            <option value="">- Select a Category -</option>
                        <?php foreach($categories as $cat){ ?>            
                            <optgroup label="<?php echo $cat['categoryName']?>"></optgroup>
                            <?php 
                                foreach($cat['children'] as $idx=>$subCat){ 
                                    $selected = '';
                                    if ($forumTopicData && $forumTopicData['categoryID'] == $subCat['categoryID']) 
                                        $selected = 'selected="selected"';
                                    
                                    if ($subCat['categoryID'] == $curCatID)
                                        $selected = 'selected="selected"';
                                    
                            ?>
                            <option value="<?php echo $subCat['categoryID']?>" <?php echo $selected;?> >&nbsp;<?php echo $subCat['categoryName']?></option>
                            <?php } ?>                                            
                        <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <textarea cols="20" id="topic-content" name="content" rows="12" class="textarea"><?php if ($forumTopicData) echo BuckysForumTopic::_convertBBCodeToHTML($forumTopicData['topicContent']);?></textarea>
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
        var topicEditor = jQuery('#topic-content').cleditor({
            width: '100%', 
            height: '400px',
            docCSSFile: '/css/main.css;/css/editor/jquery.cleditor.css;/css/forum.css',
            controls: "bold italic underline | link bullets numbering image code"
        })[0];
        
        jQuery('#newtopicform').submit(function(){
            var isValid = true;
            if(jQuery.trim(jQuery('#newtopicform #title').val()) == '')
            {
                jQuery('#newtopicform #title').addClass('input-error');
                isValid = false;
            }
            if(jQuery.trim(jQuery('#newtopicform #category').val()) == '')
            {
                jQuery('#newtopicform #category').addClass('select-error');
                isValid = false;
            }
            
            if(!isValid)
            {
                showMessage(jQuery(this), 'All fields are required.', true);
                return false;
            }
            
            text = jQuery(topicEditor.$frame[0].contentDocument).find('body').text();
                        
            if(jQuery.trim(text) == '' && jQuery(topicEditor.$frame[0].contentDocument).find('body').find('img, a').size() < 1)
            {
                showMessage(jQuery(this), 'All fields are required.', true)                
                topicEditor.$frame[0].contentWindow.focus();  
                isValid = false;
            }
            
            return isValid;
        })
        
    })
</script>