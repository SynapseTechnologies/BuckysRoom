<?php
if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
}  
?>
<section id="main_section">
    <section id="main_content">
        <?php render_result_messages() ?>        
            <h2 class="titles">My Posts</h2>
            <table cellpadding="0" cellspacing="0" class="forumlist">
                <?php if(count($topics) > 0){ ?>
                <thead>
                    <tr>
                        <th>                            
                            <div class="post-sort-nav">
                                <a href="/forum/myposts.php?type=all" <?php echo $listType == 'all' ? 'class="current"' : ''?>>All</a>
                                &middot;
                                <a href="/forum/myposts.php?type=responded" <?php echo $listType == 'responded' ? 'class="current"' : ''?>>Topics Responded To</a>
                                &middot;
                                <a href="/forum/myposts.php?type=started" <?php echo $listType == 'started' ? 'class="current"' : ''?>>Topics Started</a>
                            </div>
                        </th>
                        <th>Category</th>
                        <th>Author</th>
                        <th class="td-counts" style="padding-right:10px;">Replies</th>
                        <th>Last Post</th>
                    </tr>
                </thead>   
                <tfoot>
                    <tr>
                        <td colspan="5"><?php echo $pagination->renderPaginate('/forum/myposts.php?type=' . $listType . "&", count($topics))?></td>
                    </tr>                    
                </tfoot>             
                <tbody>                  
                    <?php foreach($topics as $row){ ?>
                    <tr>
                        <td style="width:60%;padding-right:10px;"><a href="/forum/topic.php?id=<?php echo $row['topicID']?>"><?php echo $row['topicTitle']?></a></td>
                        <td style="padding-right:10px;"><a href="/forum/category.php?id=<?php echo $row['categoryID']?>"><?php echo $row['categoryName']?></a></td>
                        <td style="padding-right:10px;"><a style="font-weight:bold;" href="/profile.php?u=<?php echo $row['creatorID']?>"><?php echo $row['creatorName']?></a></td>
                        <td class="td-counts"><?php echo $row['replies']?></td>
                        <td>
                            <?php
                                if($row['lastReplierID'] > 0){ 
                            ?>
                                <a style="font-weight:bold;" href="/profile.php?u=<?php echo $row['lastReplierID']?>"><?php echo $row['lastReplierName']?></a>                            
                            <?php
								echo '<span style="color:#999999;">';
                                echo buckys_format_date($row['lastReplyDate']);
								echo '</span>';
                                }else{
                            ?>
                                <a style="font-weight:bold;" href="/profile.php?u=<?php echo $row['creatorID']?>"><?php echo $row['creatorName']?></a>                            
                            <?php
								echo '<span style="color:#999999;">';
                                echo buckys_format_date($row['createdDate']);
								echo '</span>';
                                }
                            ?>
                        </td>
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
    </section>
</section>
