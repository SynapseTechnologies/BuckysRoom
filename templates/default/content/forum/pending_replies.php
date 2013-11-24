<?php
if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
}  
?>
<section id="main_section">
    <section id="main_content">
        <?php render_result_messages() ?>
        <h2 class="titles">Pending Replies</h2>
        <form name="pendingrepliesform" id="pendingrepliesform" action="/forum/pending_replies.php" method="post">
            <input type="hidden" name="action" value="" />            
            <table cellpadding="0" cellspacing="0" class="forumlist">
                <?php if(count($replies) > 0){ ?>
                <thead>
                    <tr>
                        <th width="20"><input type="checkbox" id="chk_all" /></th>
                        <th>Topic</th>
                        <th>Reply</th>
                        <th>User</th>
                        <th>Created Date</th>
                        <th>Action</th>
                    </tr>
                </thead>   
                <tfoot>
                    <tr>
                        <td colspan="6"><?php echo $pagination->renderPaginate('/forum/pending_replies.php?', count($replies))?></td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <input type="button" id="approve-btn" value="Approve" class="redButton" />
                            <input type="button" id="delete-btn" value="Delete" class="redButton" />
                        </td>
                    </tr>
                </tfoot>             
                <tbody>                  
                    <?php 
                    foreach($replies as $row){ 
                        $trow = BuckysForumTopic::getTopic($row['topicID']);
                    ?>
                    <tr>
                        <td class="td-chk"><input type="checkbox" name="rid[]" value="<?php echo $row['replyID']?>" /></td>
                        <td><a href="/forum/topic.php?id=<?php echo $trow['topicID']?>"><?php echo $trow['topicTitle']?></a></td>
                        <td><?php echo BuckysForumTopic::_convertBBCodeToHTML($row['replyContent']) ?></td>
                        <td><a href="/profile.php?user=<?php echo $row['creatorID']?>"><?php echo $row['creatorName']?></a></td>
                        <td><?php echo buckys_format_date($row['createdDate'])?></td>
                        <td><a href="#" class="approve-reply">Approve</a> | <a href="#" class="delete-reply">Delete</a></td>
                    </tr>
                    <?php } ?>                                      
                </tbody>            
                <?php }else{ ?>                    
                <tbody>
                    <tr>
                        <td colspan="6">Nothing to see here</td>
                    </tr>
                </tbody>
                <?php } ?>                    
            </table>       
        </form>
    </section>
</section>
<script type="text/javascript">
    jQuery(document).ready(function(){        
        var formObj = jQuery('#pendingrepliesform');
        //Approve Topic
        formObj.find('.approve-reply').click(function(){
            formObj.find('input[type="checkbox"]').prop('checked', false);
            jQuery(this).parent().parent().find('.td-chk input[type="checkbox"]').prop('checked', true);
            formObj.find('input[name="action"]').val('approve-reply');            
            formObj.submit();
            
            return false;
        })
        
        formObj.find('#approve-btn').click(function(){
            if(jQuery('#pendingrepliesform .td-chk input[type="checkbox"]:checked').size() < 1)
            {
                showMessage(formObj, 'No data selected!', true);
                return false;
            }
            formObj.find('input[name="action"]').val('approve-reply');            
            formObj.submit();            
        })
        
        //Delete Topic
        formObj.find('.delete-reply').click(function(){
            formObj.find('input[type="checkbox"]').prop('checked', false);
            jQuery(this).parent().parent().find('.td-chk input[type="checkbox"]').prop('checked', true);
            formObj.find('input[name="action"]').val('delete-reply');        
            if(confirm('Are you sure that you want to delete this reply?'))            
                formObj.submit();
            
            return false;
        })
        
        formObj.find('#delete-btn').click(function(){
            if(jQuery('#pendingrepliesform .td-chk input[type="checkbox"]:checked').size() < 1)
            {
                showMessage(formObj, 'No data selected!', true);
                return false;
            }
            if(confirm('Are you sure that you want to delete the selected replies?'))
            {
                formObj.find('input[name="action"]').val('delete-reply');            
                formObj.submit();
            }
        })
        
    })
</script>