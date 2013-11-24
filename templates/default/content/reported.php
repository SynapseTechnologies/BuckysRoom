<?php
if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
}  
?>
<section id="main_section">    
    <?php buckys_get_panel('account_links'); ?>    
    
    <section id="right_side">
        <section id="right_side_padding" class="user-info-section">
            <h2 class="titles">
                Reported <?php echo $reportLabel[$reportType][1]; ?>                
            </h2>
            <form method="post" id="reportedform" action="/reported.php?type=<?php echo $reportType?>" style="padding-top:0px;">
                <?php render_result_messages() ?>
                <?php
                    if( count($objects) == 0 )
                    {
                ?>
                    <div class="tr noborder">                            
                        Nothing to see here.
                    </div>
                <?php
                    }else{
                ?>
                <?php $pagination->renderPaginate('/reported.php?type=' . $reportType . "&", count($objects)); ?>
                <div class="table">
                    <div class="thead">
                        <div class="td td-chk"><input type="checkbox" id="chk_all" name="objectID[]" /></div>
                        <div class="td td-user">User</div>
                        <div class="td td-content">
                            <?php
                                echo $reportLabel[$reportType][0];
                            ?>
                        </div>
                        <div class="td td-action">Actions</div>
                        <div class="td td-reporter">Reported By</div>
                        <div class="clear"></div>
                    </div>
                    <?php
                        foreach($objects as $i=>$row){
                    ?>
                    <div class="tr <?php echo $i == count($objects) - 1 ? 'noborder' : ''?>">
                        <div class="td td-chk"><input type="checkbox" id="chk<?php echo $row['messageID']?>" name="reportID[]" value="<?php echo $row['reportID']?>" /></div>
                        <div class="td td-user">
                            <a href="/profile.php?user=<?php echo $row['ownerID']?>">
                                <img src="<?php echo BuckysUser::getProfileIcon(array('thumbnail'=>$row['ownerThumb'], 'userID'=>$row['ownerID']))?>" />
                            </a>
                        </div>
                        <div class="td td-content">
                            <?php 
                                if($reportType == 'post'){
                                    echo buckys_process_post_content($row);
                                }else if($reportType == 'message'){
                                    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;To: <a href='/profile.php?user=" . $row['receiverID'] . "'>" . $row['receiverName'] . "</a><br />";
                                    echo "&nbsp;&nbsp;&nbsp;&nbsp;From: <a href='/profile.php?u=" . $row['senderID'] . "'>" . $row['senderName'] . "</a><br />";
                                    echo "Subject: <b>" . $row['subject'] . "</b><br />";
                                    echo '<p class="message-body">' . $row['content'] . '</p>';
                                }else if($reportType == 'topic'){
                                    echo '<h3>' . $row['title'] . '</h3>';
                                    echo BuckysForumTopic::_convertBBCodeToHTML($row['content']);
                                }else if($reportType == 'reply'){
                                    echo '<h3>Topic: ' . $row['title'] . '</h3>';
                                    echo BuckysForumTopic::_convertBBCodeToHTML($row['content']);
                                }else{
                                    echo $row['content'];
                                }
                            ?>
                            &nbsp;
                        </div>
                        <div class="td td-action">
                            <?php
                                /*switch($reportType)
                                {
                                    case 'post':
                                        $viewLink = '/posts.php?user= ' . $row['ownerID'] . '&post=' . $row['objectID'];
                                        break;
                                    case 'message':
                                        $viewLink = '/messages_read.php?message= ' . $row['objectID'];
                                        break;
                                    case 'topic':
                                        $viewLink = '/forum/topic.php?id= ' . $row['objectID'];
                                        break;
                                    
                                    
                                }
                                <a href="<?php echo $viewLink?>" target="_blank">View <?php echo $reportLabel[$reportType][0]?></a><br />
                                */
                            ?>                            
                                   
                            <a href="/reported.php?action=delete-objects&reportID=<?php echo $row['reportID']?>&type=<?php echo $reportType?>">Delete <?php echo $reportLabel[$reportType][0];?></a><br />
                            <a href="/reported.php?action=approve-objects&reportID=<?php echo $row['reportID']?>&type=<?php echo $reportType?>">Approve <?php echo $reportLabel[$reportType][0];?></a><br />
                            <a href="/reported.php?action=ban-users&reportID=<?php echo $row['reportID']?>&type=<?php echo $reportType?>">Ban User</a><br />
                        </div>
                        <div class="td td-reporter">
                            <a href="/profile.php?user=<?php echo $row['reporterID']?>">
                                <img src="<?php echo BuckysUser::getProfileIcon(array('thumbnail'=>$row['reporterThumb'], 'userID'=>$row['reporterID']))?>" />
                            </a>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <?php
                        }
                    ?>
                </div>                
                <div class="btn-row">
                    <input type="button" class="redButton" value="Delete <?php echo $reportLabel[$reportType][1];?>" id="delete-objects" />
                    <input type="button" class="redButton" value="Approve <?php echo $reportLabel[$reportType][1];?>" id="approve-objects" />
                    <input type="button" class="redButton" value="Ban Users" id="ban-users" />
                </div>
                <input type="hidden" name="action" value="" />
                <input type="hidden" name="type" value="<?php echo $reportType?>" />
                <?php } ?>
            </form>
        </section>
    </section>
</section>